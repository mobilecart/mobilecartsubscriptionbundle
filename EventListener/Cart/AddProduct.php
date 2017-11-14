<?php

namespace MobileCart\SubscriptionBundle\EventListener\Cart;

use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\CoreBundle\CartComponent\ArrayWrapper;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

/**
 * Class AddProduct
 * @package MobileCart\SubscriptionBundle\EventListener\Cart
 */
class AddProduct
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

    public function onCartAddProduct(CoreEvent $event)
    {
        $cartItem = $event->get('item');
        if ($cartItem && $cartItem->getSubscriptionId()) {
            $subscription = $this->getEntityService()->find(EntityConstants::SUBSCRIPTION, $cartItem->getSubscriptionId());
            if ($subscription) {

                // set subscription data
                $cartItem->setSubscription(new ArrayWrapper($subscription->getData()));

                // force quantity of 1
                $this->getCartService()
                    ->setProductQty($cartItem->getProductId(), 1)
                    ->updateItemEntityQty($cartItem->getProductId(), 1);
            }
        }
    }
}
