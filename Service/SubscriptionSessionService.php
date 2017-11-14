<?php

namespace MobileCart\SubscriptionBundle\Service;

use MobileCart\CoreBundle\CartComponent\ArrayWrapper;
use MobileCart\SubscriptionBundle\Entity\SubscriptionCustomer;

/**
 * Class SubscriptionSessionService
 * @package MobileCart\SubscriptionBundle\Service
 */
class SubscriptionSessionService
{
    /**
     * @var \MobileCart\CoreBundle\Service\CartService
     */
    protected $cartService;

    /**
     * @param \MobileCart\CoreBundle\Service\CartService $cartService
     * @return $this
     */
    public function setCartService(\MobileCart\CoreBundle\Service\CartService $cartService)
    {
        $this->cartService = $cartService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\CartService
     */
    public function getCartService()
    {
        return $this->cartService;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    public function getEntityService()
    {
        return $this->getCartService()->getEntityService();
    }

    /**
     * @return bool
     */
    public function getIsSubscribed()
    {
        if (!$this->getCartService()->getCart()) {
            return false;
        }

        if (!$this->getCartService()->getCart()->getCustomer()) {
            return false;
        }

        if (!$this->getCartService()->getCart()->getCustomer()->getSubscriptionCustomer()) {
            return false;
        }

        return (bool) $this->getCartService()->getCart()->getCustomer()
            ->getSubscriptionCustomer()
            ->getIsActive();
    }

    public function getCalcIsInFreeTrial()
    {
        if (!$this->getCartService()->getCart()) {
            return false;
        }

        if (!$this->getCartService()->getCart()->getCustomer()) {
            return false;
        }

        if (!$this->getCartService()->getCart()->getCustomer()->getSubscriptionCustomer()) {
            return false;
        }

        $subCustomer = $this->getCartService()->getCart()->getCustomer()
            ->getSubscriptionCustomer();

        $sub = $this->getCartService()->getCart()->getCustomer()
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

        $this->getCartService()->getCart()->getCustomer()
            ->set('subscription_customer', $subCustomerData)
            ->set('subscription', $subData);

        return $this;
    }
}
