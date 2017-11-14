<?php

namespace MobileCart\SubscriptionBundle\EventListener\Security;

use Symfony\Component\EventDispatcher\Event;
use MobileCart\CoreBundle\CartComponent\ArrayWrapper;
use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

/**
 * Class Login
 * @package MobileCart\SubscriptionBundle\EventListener\Security
 */
class Login
{
    /**
     * @var \MobileCart\CoreBundle\Service\CartService
     */
    protected $cartService;

    /**
     * @return \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    public function getEntityService()
    {
        return $this->getCartService()->getEntityService();
    }

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

    public function onLoginSuccess(CoreEvent $event)
    {
        $returnData = $event->getReturnData();

        if ($event->getIsCustomer()) {

            // load subscription_customer row
            $subCustomer = $this->getEntityService()->findOneBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
                'customer' => $event->getUser()->getId(),
            ]);

            if ($subCustomer) {

                $subCustomer->setIsLoggedIn(1);
                $this->getEntityService()->persist($subCustomer);

                $subCustomerData = new ArrayWrapper($subCustomer->getData());
                $subData = new ArrayWrapper($subCustomer->getSubscription()->getData());

                $returnData['subscription_customer'] = $subCustomerData;
                $returnData['subscription'] = $subData;

                $event->setReturnData($returnData);

                $this->getCartService()->getCart()->getCustomer()
                    ->set('subscription_customer', $subCustomerData)
                    ->set('subscription', $subData);
            }
        }
    }
}
