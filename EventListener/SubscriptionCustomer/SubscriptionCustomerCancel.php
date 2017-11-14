<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\HttpFoundation\RedirectResponse;
use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerCancel
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerCancel
{
    /**
     * @var \MobileCart\SubscriptionBundle\Service\SubscriptionService
     */
    protected $subscriptionService;

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
     * @param \MobileCart\SubscriptionBundle\Service\SubscriptionService $subscriptionService
     * @return $this
     */
    public function setSubscriptionService(\MobileCart\SubscriptionBundle\Service\SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        return $this;
    }

    /**
     * @return \MobileCart\SubscriptionBundle\Service\SubscriptionService
     */
    public function getSubscriptionService()
    {
        return $this->subscriptionService;
    }

    public function onSubscriptionCustomerCancel(CoreEvent $event)
    {
        if ($this->getSubscriptionService()->cancelRecurringSubscription($event->getEntity())) {
            $event->getRequest()->getSession()->getFlashBag()->add(
                'success',
                'Subscription Successfully Canceled!'
            );
        }

        $event->setResponse(new RedirectResponse($this->getRouter()->generate(
            'cart_admin_subscription_customer',
            []
        )));
    }
}
