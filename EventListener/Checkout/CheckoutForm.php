<?php

namespace MobileCart\SubscriptionBundle\EventListener\Checkout;

use MobileCart\CoreBundle\Payment\CollectPaymentMethodRequest;
use MobileCart\CoreBundle\Payment\PaymentMethodServiceInterface;
use MobileCart\SubscriptionBundle\Entity\Subscription;
use Symfony\Component\EventDispatcher\Event;
use MobileCart\CoreBundle\Constants\EntityConstants as CoreEntityConstants;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

class CheckoutForm
{
    protected $entityService;

    protected $checkoutSessionService;

    protected $event;

    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    public function getEntityService()
    {
        return $this->entityService;
    }

    public function setCheckoutSessionService($checkoutSession)
    {
        $this->checkoutSessionService = $checkoutSession;
        return $this;
    }

    public function getCheckoutSessionService()
    {
        return $this->checkoutSessionService;
    }

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

    public function onCheckoutForm(Event $event)
    {
        if ($event->getSingleStep()) {
            return false;
        }

        $this->setEvent($event);
        $returnData = $this->getReturnData();

        $cartSession = $this->getCheckoutSessionService()
            ->getCartSessionService();

        $cart = $cartSession->getCart();
        $customer = $cart->getCustomer();

        $subItems = [];
        if ($cart->getItems()) {
            foreach($cart->getItems() as $cartItem) {
                if ($cartItem->getSubscription()) {
                    $subItems[] = $cartItem;
                }
            }
        }

        if (!$subItems) {
            return false;
        }

        $subscriptionCustomers = $customer->getId()
            ? $this->getEntityService()->findBy(EntityConstants::SUBSCRIPTION_CUSTOMER, ['customer' => $customer->getId()])
            : [];

        $existingSubscriptionIds = [];
        $subscriptionTokens = [];
        if ($subscriptionCustomers) {
            foreach($subscriptionCustomers as $subscriptionCustomer) {
                $subId = $subscriptionCustomer->getSubscription()->getId();
                $existingSubscriptionIds[] = $subId;
                if ($subscriptionCustomer->getCustomerToken()) {
                    $subscriptionTokens[$subId] = $subscriptionCustomer->getCustomerToken()->getId();
                }
            }
        }

        if ($existingSubscriptionIds) {
            foreach($subItems as $subItem) {
                $sub = $subItem->getSubscription();
                if (in_array($sub->getId(), $existingSubscriptionIds)) {
                    // todo : handle multiple subscriptions, throw exception if necessary
                    // todo : ensure this isn't a tokenized payment, in which case, the order would contain the same items in the cart
                    continue;
                }
            }
        }

        // figure out payment action
        $actions = [];
        $extPlanId = '';
        foreach($subItems as $subItem) {
            $sub = $subItem->getSubscription();
            switch($sub->getPaymentHandler()) {
                case Subscription::HANDLER_SERVER_AUTO:

                    if (
                        in_array($sub->getId(), $existingSubscriptionIds)
                        && isset($subscriptionTokens[$sub->getId()])
                    ) {
                        $action = PaymentMethodServiceInterface::ACTION_PURCHASE_STORED_TOKEN;
                        if (!in_array($action, $actions)) {
                            $actions[] = $action;
                        }
                    } else {
                        $action = PaymentMethodServiceInterface::ACTION_CREATE_TOKEN;
                        if (!in_array($action, $actions)) {
                            $actions[] = $action;
                        }
                    }

                    break;
                case Subscription::HANDLER_SERVER_MANUAL:
                    // todo : this could be a manual stored token payment

                    break;
                case Subscription::HANDLER_SERVICE_EXTERNAL:

                    if ($sub->getExternalPlanId()) {
                        $extPlanId = $sub->getExternalPlanId();
                        $action = PaymentMethodServiceInterface::ACTION_PURCHASE_AND_SUBSCRIBE_RECURRING;
                        if (!in_array($action, $actions)) {
                            $actions[] = $action;
                        }
                    }

                    break;
                default:

                    break;
            }
        }

        if ($actions) {
            $action = $actions[0]; // todo : handle multiple payments for multiple subscriptions, etc

            $paymentMethodRequest = new CollectPaymentMethodRequest();
            $paymentMethodRequest->setAction($action);

            if ($extPlanId) {
                $event->setExternalPlanId($extPlanId);
                $paymentMethodRequest->setExternalPlanId($extPlanId);
                $email = $this->getCheckoutSessionService()
                    ->getCartSessionService()
                    ->getCustomer()
                    ->getEmail();

                if (!isset($returnData['payment_data'])) {
                    $returnData['payment_data'] = [];
                }

                // this is handled in CheckoutSubmitOrder
                $returnData['payment_data']['external_plan_id'] = $extPlanId;
                $returnData['payment_data']['email'] = $email;
            }

            $event->setCollectPaymentMethodRequest($paymentMethodRequest);
        }

        $event->setReturnData($returnData);
    }
}
