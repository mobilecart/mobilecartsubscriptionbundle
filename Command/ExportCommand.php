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

class ExportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cart:export:subscription')
            ->addArgument('ids', InputArgument::OPTIONAL, 'CSV of IDs')
            //->addArgument('format', InputArgument::OPTIONAL, 'Format')
            ->setDescription('Export Subscriptions')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command Exports Subscriptions:

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

        $format = 'json';

        $ids = $input->getArgument('ids')
            ? explode(',', $input->getArgument('ids'))
            : [];

        if ($ids) {

            $path = realpath(__DIR__ . '/../../../../export.json');
            $message = "Opening File: " . $path;
            $output->writeln($message);
            $fh = fopen($path, 'w');

            foreach($ids as $id) {

                $customer = $entityService->find('customer', $id);

                $data = $customer->getData();
                $data['hash'] = $customer->getHash();

                $subCustomer = $entityService->findOneBy(SubEntityConstants::SUBSCRIPTION_CUSTOMER, [
                    'customer' => $customer->getId(),
                ]);

                if ($subCustomer) {
                    $data['subscription_customer'] = $subCustomer->getData();
                    $data['subscription'] = $subCustomer->getSubscription()->getData();

                    $customerToken = $entityService->findOneBy('customer_token', [
                        'customer' => $customer->getId(),
                    ]);
                    $data['customer_token'] = $customerToken->getData();
                }

                $out = json_encode($data). "\n";
                fwrite($fh, $out);
                $output->writeln($out);
            }

            fclose($fh);
        }
    }
}
