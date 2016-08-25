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

class ProcessStripeWebhooksCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cart:webhooks:stripe')
            ->setDescription('Process Stripe Webhooks for Subscrptions')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command Processes Webhooks for Subscriptions:

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

        $webhooks = $entityService->findBy(EntityConstants::WEBHOOK_LOG, [
            'is_processed' => 0,
        ]);

        if ($webhooks) {
            foreach($webhooks as $webhook) {

                $obj = json_decode($webhook->getRequestBody());
                if (!is_object($obj) || !isset($obj->type)) {
                    continue;
                }

                switch($obj->type) {
                    case 'customer.subscription.deleted':

                        $serviceAccountId = $obj->data->object->customer;
                        $customerToken = $entityService->findOneBy(EntityConstants::CUSTOMER_TOKEN, [
                            'service_account_id' => $serviceAccountId,
                        ]);

                        if (!$customerToken) {
                            continue;
                        }

                        $subCustomer = $entityService->findOneBy(SubEntityConstants::SUBSCRIPTION_CUSTOMER,[
                            'customer_token' => $customerToken->getId(),
                        ]);

                        if (!$subCustomer) {
                            continue;
                        }

                        $output->writeln("Deleting Subscription and Token: " . $subCustomer->getCustomer()->getEmail());

                        $entityService->remove($subCustomer);
                        $entityService->remove($customerToken);

                        break;
                    case 'charge.failed':

                        break;
                    case 'invoice.payment.failed':

                        break;
                    default:

                        break;
                }
            }
        }

        $message = "Finished Execution";
        $output->writeln($message);
    }
}
