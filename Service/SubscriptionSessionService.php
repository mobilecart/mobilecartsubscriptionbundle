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


    public function getCalcIsInFreeTrial()
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

        $subCustomer = $this->getCartSessionService()->getCart()->getCustomer()
            ->getSubscriptionCustomer();

        $sub = $this->getCartSessionService()->getCart()->getCustomer()
            ->getSubscription();

        if ($sub->getFreeTrialDays()) {
            $freeTrialDays = (int) $sub->getFreeTrialDays();
            $startDate = $subCustomer->getCreatedAt();


        }

        return false;
    }

    /**
     * @param SubscriptionCustomer $subscriptionCustomer
     * @return $this
     */
    public function setSubscriptionCustomer(SubscriptionCustomer $subscriptionCustomer)
    {
        $subCustomerData = new ArrayWrapper($subscriptionCustomer->getData());
        $subData = new ArrayWrapper($subscriptionCustomer->getSubscription()->getData());

        $this->getCartSessionService()->getCart()->getCustomer()
            ->set('subscription_customer', $subCustomerData)
            ->set('subscription', $subData);

        return $this;
    }
}
