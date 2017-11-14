<?php

namespace MobileCart\SubscriptionBundle\EventListener\Admin;

use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class AdminNavigation
 * @package MobileCart\SubscriptionBundle\EventListener\Admin
 */
class AdminNavigation
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @return $this
     */
    public function setRouter(\Symfony\Component\Routing\RouterInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param CoreEvent $event
     */
    public function onAdminNavigation(CoreEvent $event)
    {
        $event->get('menu')->addChild('Subscriptions', [
            'route' => 'cart_admin_subscription',
            'uri'   => $this->getRouter()->generate('cart_admin_subscription', []),
            'extras' => [
                'safe_label' => true,
            ]
        ]);

        $event->get('menu')->addChild('Subscribers', [
            'route' => 'cart_admin_subscription_customer',
            'uri'   => $this->getRouter()->generate('cart_admin_subscription_customer', []),
            'extras' => [
                'safe_label' => true,
            ]
        ]);
    }
}
