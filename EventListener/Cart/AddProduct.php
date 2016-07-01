<?php

namespace MobileCart\SubscriptionBundle\EventListener\Cart;

use MobileCart\CoreBundle\CartComponent\ArrayWrapper;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AddProduct
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

    public function setCartSessionService($cartSessionService)
    {
        $this->cartSessionService = $cartSessionService;
        return $this;
    }

    public function getCartSessionService()
    {
        return $this->cartSessionService;
    }

    public function onCartAddProduct(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();
        $cart = $returnData['cart'];
        $request = $event->getRequest();
        $format = $request->get('format', '');

        $productId = $event->getProductId()
            ? $event->getProductId()
            : $request->get('id', '');

        if ($cartItem = $cart->findItem('product_id', $productId)) {
            if ($cartItem->getSubscriptionId()) {
                $subscription = $this->getEntityService()->find(EntityConstants::SUBSCRIPTION, $cartItem->getSubscriptionId());
                if ($subscription) {
                    $cartItem->setSubscription(new ArrayWrapper($subscription->getData()));
                    $removed = false;
                    foreach($cart->getItems() as $item) {
                        if ($item->getProductId() != $productId) {
                            $cart->removeProductId($item->getProductId()); // todo : add a config option for this
                            $removed = true;
                        }
                    }

                    if ($removed) {

                        $cart = $this->getCartSessionService()
                            ->setCart($cart)
                            ->collectShippingMethods()
                            ->collectTotals()
                            ->getCart();

                        $cartEntity = $event->getCartEntity();

                        // update db
                        $cartEntity->setJson($cart->toJson());
                        $this->getEntityService()->persist($cartEntity);
                        $returnData['cart'] = $cart;

                        $response = '';
                        switch($format) {
                            case 'json':
                                $response = new JsonResponse($returnData);
                                break;
                            default:
                                $params = [];
                                $route = 'cart_view';
                                $url = $this->getRouter()->generate($route, $params);
                                $response = new RedirectResponse($url);
                                break;
                        }

                        $event->setReturnData($returnData);
                        $event->setResponse($response);
                    }
                }
            } else {
                if ($cart->getItems()) {
                    $removed = false;
                    foreach($cart->getItems() as $item) {
                        if ($item->getSubscriptionId()) {
                            $cart->removeProductId($item->getProductId());
                            $removed = true;
                        }
                    }

                    if ($removed) {

                        $cart = $this->getCartSessionService()
                            ->setCart($cart)
                            ->collectShippingMethods()
                            ->collectTotals()
                            ->getCart();

                        $cartEntity = $event->getCartEntity();

                        // update db
                        $cartEntity->setJson($cart->toJson());
                        $this->getEntityService()->persist($cartEntity);
                        $returnData['cart'] = $cart;
                    }
                }
            }
        }
    }
}
