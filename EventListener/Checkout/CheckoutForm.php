<?php

namespace MobileCart\SubscriptionBundle\EventListener\Checkout;

use MobileCart\CoreBundle\Payment\CollectPaymentMethodRequest;
use MobileCart\CoreBundle\Payment\PaymentMethodServiceInterface;
use MobileCart\SubscriptionBundle\Entity\Subscription;
use Symfony\Component\EventDispatcher\Event;
use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\CoreBundle\Constants\EntityConstants as CoreEntityConstants;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

/**
 * Class CheckoutForm
 * @package MobileCart\SubscriptionBundle\EventListener\Checkout
 */
class CheckoutForm
{
    /**
     * @var \MobileCart\CoreBundle\Service\CheckoutSessionService
     */
    protected $checkoutSessionService;

    /**
     * @return \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    public function getEntityService()
    {
        return $this->getCheckoutSessionService()->getCartService()->getEntityService();
    }

    /**
     * @param \MobileCart\CoreBundle\Service\CheckoutSessionService $checkoutSession
     * @return $this
     */
    public function setCheckoutSessionService(\MobileCart\CoreBundle\Service\CheckoutSessionService $checkoutSession)
    {
        $this->checkoutSessionService = $checkoutSession;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\CheckoutSessionService
     */
    public function getCheckoutSessionService()
    {
        return $this->checkoutSessionService;
    }

    public function onCheckoutForm(CoreEvent $event)
    {
        if ($event->getSingleStep()) {
            return false;
        }

        $returnData = $event->getReturnData();

        // Gather subscription items
        $cartSession = $this->getCheckoutSessionService()->getCartService();

        $cart = $cartSession->getCart();
        if (!$cart) {
            $cart = $cartSession->initCart()->getCart();
        }

        $customer = $cart->getCustomer();

        $subItems = [];
        if ($cart->getItems()) {
            foreach($cart->getItems() as $cartItem) {
                if ($cartItem->getSubscription()) {
                    $subItems[] = $cartItem;
                }
            }
        }

        // nothing to do without subscription items
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
                    ->getCartService()
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
