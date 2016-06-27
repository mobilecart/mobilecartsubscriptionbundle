<?php

namespace MobileCart\SubscriptionBundle\EventListener\Cart;

use MobileCart\CoreBundle\CartComponent\ArrayWrapper;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use Symfony\Component\EventDispatcher\Event;

class AddProduct
{
    public $entityService;

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

    public function onCartAddProduct(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();
        $cart = $returnData['cart'];
        $request = $event->getRequest();
        $productId = $request->get('id', '');
        if (!$productId) {
            $productId = $event->get('product_id');
        }

        if ($cartItem = $cart->findItem('product_id', $productId)) {
            if ($cartItem->getSubscriptionId()) {
                $subscription = $this->getEntityService()->find(EntityConstants::SUBSCRIPTION, $cartItem->getSubscriptionId());
                if ($subscription) {
                    $cartItem->setSubscription(new ArrayWrapper($subscription->getData()));
                }
            }
        }
    }
}
