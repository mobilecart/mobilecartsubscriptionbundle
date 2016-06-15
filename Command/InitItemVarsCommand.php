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

class InitItemVarsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cart:sub:itemvars')
            ->setDescription('Create Default Variants for Subscrptions')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command manages creating default Variants for Subscriptions:

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

        $code = EntityConstants::PRODUCT;

        $varSet = $entityService->findOneBy(EntityConstants::ITEM_VAR_SET, [
            'object_type' => $code,
        ]);

        if (!$varSet) {

            $name = "Default Product";

            $varSet = $entityService->getInstance(EntityConstants::ITEM_VAR_SET);
            $varSet->setName($name)
                ->setObjectType($code);

            $entityService->persist($varSet);

            $message = "Created ItemVarSet: {$name} for Object Type: {$code}";
            $output->writeln($message);
        }

        // Add Variants

        // subscription_id
        $varCode = 'subscription_id';
        $itemVar = $entityService->findOneBy(EntityConstants::ITEM_VAR, [
            'code' => $varCode,
        ]);

        if (!$itemVar) {
            $itemVar = $entityService->getInstance(EntityConstants::ITEM_VAR);
            $itemVar->setCode($varCode)
                ->setName('Subscription')
                ->setUrlToken($varCode)
                ->setDatatype(EntityConstants::INT)
                ->setFormInput(EntityConstants::INPUT_NUMBER)
                ;

            $entityService->persist($itemVar);

            $message = "Created ItemVar: Subscription , subscription_id";
            $output->writeln($message);
        }

        $itemVarSetVar = $entityService->findOneBy(EntityConstants::ITEM_VAR_SET_VAR,[
            'item_var_set' => $varSet->getId(),
            'item_var' => $itemVar->getId(),
        ]);

        if (!$itemVarSetVar) {
            $itemVarSetVar = $entityService->getInstance(EntityConstants::ITEM_VAR_SET_VAR);
            $itemVarSetVar->setItemVarSet($varSet)
                ->setItemVar($itemVar)
            ;

            $entityService->persist($itemVarSetVar);

            $message = "Created ItemVarSetVar: Subscription , subscription_id";
            $output->writeln($message);
        }

        $message = "Finished Execution";
        $output->writeln($message);
    }
}
