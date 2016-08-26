<?php

/*
 * This file is part of the Mobile Cart package.
 *
 * (c) Jesse Hanson <jesse@mobilecart.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MobileCart\SubscriptionBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MobileCart\CoreBundle\Constants\EntityConstants;
use MobileCart\SubscriptionBundle\Constants\EntityConstants as SubEntityConstants;

class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cart:import:subscription')
            //->addArgument('ids', InputArgument::OPTIONAL, 'CSV of IDs')
            //->addArgument('format', InputArgument::OPTIONAL, 'Format')
            ->setDescription('Export Subscriptions')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command Imports Subscriptions:

<info>php %command.full_name%</info>

EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityService = $this->getContainer()->get('cart.entity');
        $path = realpath(__DIR__ . '/../../../../export.json');
        $message = "Opening File: " . $path;
        $output->writeln($message);

        $subs = [];

        $fh = fopen($path, 'r');
        if ($fh !== false) {
            while (($line = fgets($fh, 8192)) !== false) {

                $customerData = (array) json_decode($line);
                $output->writeln(print_r($customerData, 1));

                $subCustomerData = [];
                $subData = [];
                $tokenData = [];
                if (isset($customerData['subscription_customer'])) {

                    $subCustomerData = (array) $customerData['subscription_customer'];
                    $subData = (array) $customerData['subscription'];
                    $tokenData = (array) $customerData['customer_token'];
                    unset($customerData['subscription_customer']);
                    unset($customerData['subscription']);
                    unset($customerData['customer_token']);

                    if (isset($subCustomerData['created_at']) && is_object($subCustomerData['created_at'])) {
                        $createdAtObj = $subCustomerData['created_at'];
                        $createdAt = \DateTime::createFromFormat('Y-m-d H:i:s.u', $createdAtObj->date);
                        $subCustomerData['created_at'] = $createdAt;
                    } else {
                        $subCustomerData['created_at'] = null;
                    }
                }

                if (isset($customerData['last_login_at']) && is_object($customerData['last_login_at'])) {
                    $lastloginAtObj = $customerData['last_login_at'];
                    $lastloginAt = \DateTime::createFromFormat('Y-m-d H:i:s.u', $lastloginAtObj->date);
                    $customerData['last_login_at'] = $lastloginAt;
                } else {
                    $customerData['last_login_at'] = null;
                }

                $customer = $entityService->findOneBy('customer', [
                    'email' => $customerData['email']
                ]);

                if ($customer) {
                    $output->writeln("Found Customer: {$customer->getEmail()}");
                } else {
                    $customer = $entityService->getInstance('customer');
                    $customer->fromArray($customerData);
                    $output->writeln("Saving Customer");
                    $entityService->persist($customer);
                    $output->writeln("Saved Customer ID: {$customer->getId()}");
                }

                $customerToken = $entityService->findOneBy('customer_token', [
                    'customer' => $customer->getId(),
                ]);

                if ($tokenData && !$customerToken) {

                    $customerToken = $entityService->getInstance('customer_token');
                    $customerToken->fromArray($tokenData);
                    $customerToken->setCustomer($customer);
                    $entityService->persist($customerToken);
                    $output->writeln("Created Token");
                }

                if (!$subCustomerData) {
                    $output->writeln("No subscription");
                    continue;
                }

                $subCustomer = $entityService->findOneBy('subscription_customer', [
                    'customer' => $customer->getId(),
                ]);

                if ($subCustomer) {

                    if ($customerToken && !$subCustomer->getCustomerToken()) {
                        $subCustomer->setCustomerToken($customerToken);
                        $entityService->persist($subCustomer);
                    }

                    continue;
                }

                $subCustomer = $entityService->getInstance('subscription_customer');

                $subId = $subData['id'];
                $sub = null;
                if (isset($subs[$subId])) {
                    $sub = $subs[$subId];
                } else {
                    $sub = $entityService->find('subscription', $subId);
                    $subs[$subId] = $sub;
                }

                $subCustomer->fromArray($subCustomerData);
                $subCustomer->setSubscription($sub)
                    ->setCustomer($customer);

                if ($subCustomerData['parent_subscription_customer_id'] > 0) {
                    $parentSubscription = $entityService->find('subscription_customer', $subCustomerData['parent_subscription_customer_id']);
                    if ($parentSubscription) {
                        $subCustomer->setParentSubscriptionCustomer($parentSubscription);
                    }
                }

                $entityService->persist($subCustomer);
                $output->writeln("Saved Subscription");

            }
            fclose($fh);
        }
    }
}
