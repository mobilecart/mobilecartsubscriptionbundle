<?php

namespace MobileCart\SubscriptionBundle\Service;

use MobileCart\CoreBundle\CartComponent\ArrayWrapper;
use MobileCart\SubscriptionBundle\Entity\SubscriptionCustomer;

class SubscriptionSessionService
{
    protected $cartSessionService;

    public function setCartSessionService($cartSessionService)
    {
        $this->cartSessionService = $cartSessionService;
        return $this;
    }

    public function getCartSessionService()
    {
        return $this->cartSessionService;
    }

    /**
     * @return bool
     */
    public function getIsSubscribed()
    {
        if (!$this->getCartSessionService()->getCart()) {
            return false;
        }

        if (!$this->getCartSessionService()->getCart()->getCustomer()) {
            return false;
        }

        if (!$this->getCartSessionService()->getCart()->getCustomer()->getSubscriptionCustomer()) {
            return false;
        }

        return $this->getCartSessionService()->getCart()->getCustomer()
            ->getSubscriptionCustomer()
            ->getIsActive();
    }

    /**
     * @param SubscriptionCustomer $subscriptionCustomer
     * @return $this
     */
    public function setSubscriptionCustomer(SubscriptionCustomer $subscriptionCustomer)
    {
        $subCustomerData = new ArrayWrapper($subscriptionCustomer->getData());
        $subData = new ArrayWrapper($subscriptionCustomer->getSubscription()->getData());

        // making this more explicit since its not always working
        $customer = $this->getCartSessionService()->getCart()->getCustomer();
        $customer->set('subscription_customer', $subCustomerData)
            ->set('subscription', $subData);

        $this->getCartSessionService()->setCustomer($customer);

        return $this;
    }
}
