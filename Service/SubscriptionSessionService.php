<?php

namespace MobileCart\SubscriptionBundle\Service;

use MobileCart\CoreBundle\Constants\EntityConstants;
use MobileCart\SubscriptionBundle\Constants\EntityConstants as SubEntityConstants;

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
}
