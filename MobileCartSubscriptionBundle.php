<?php

namespace MobileCart\SubscriptionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use MobileCart\SubscriptionBundle\Constants\EntityConstants as SubEntityConstants;

class MobileCartSubscriptionBundle extends Bundle
{
    public function boot()
    {
        $this->container->get('cart.entity')
            ->addObjectRepository(
                SubEntityConstants::SUBSCRIPTION,
                $this->container->getParameter('cart.repo.subscription')
            )
            ->addObjectRepository(
                SubEntityConstants::SUBSCRIPTION_CUSTOMER,
                $this->container->getParameter('cart.repo.subscription_customer')
            );

        $this->container->get('cart.theme.config')
            ->setTheme(
                'subscription_admin',
                'admin',
                'MobileCartSubscriptionBundle:Admin/',
                'bundles/mobilecartsubscription'
            )
            ->setTheme(
                'subscription_frontend',
                'frontend',
                'MobileCartSubscriptionBundle:Frontend/',
                'bundles/mobilecartsubscription'
            )
            ->setTheme(
                'subscription_email',
                'email',
                'MobileCartSubscriptionBundle:Frontend/',
                'bundles/mobilecartsubscription'
            )
            ->addAdminEditRoute(SubEntityConstants::SUBSCRIPTION, 'cart_admin_subscription_edit')
            ->addAdminEditRoute(SubEntityConstants::SUBSCRIPTION_CUSTOMER, 'cart_admin_subscription_customer_edit')
        ;


    }
}
