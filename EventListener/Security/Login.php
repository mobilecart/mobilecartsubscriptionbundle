<?php

namespace MobileCart\SubscriptionBundle\EventListener\Security;

use MobileCart\CoreBundle\CartComponent\ArrayWrapper;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use Symfony\Component\EventDispatcher\Event;

class Login
{
    protected $entityService;

    protected $cartSessionService;

    protected $event;

    protected function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    protected function getEvent()
    {
        return $this->event;
    }

    public function getReturnData()
    {
        return $this->getEvent()->getReturnData()
            ? $this->getEvent()->getReturnData()
            : [];
    }

    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    public function getEntityService()
    {
        return $this->entityService;
    }

    /**
     * @param $cartSessionService
     * @return $this
     */
    public function setCartSessionService($cartSessionService)
    {
        $this->cartSessionService = $cartSessionService;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCartSessionService()
    {
        return $this->cartSessionService;
    }

    public function onLoginSuccess(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

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

                $this->getCartSessionService()->getCart()->getCustomer()
                    ->set('subscription_customer', $subCustomerData)
                    ->set('subscription', $subData);
            }
        }
    }
}
