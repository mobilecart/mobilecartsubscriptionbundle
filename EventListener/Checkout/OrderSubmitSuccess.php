<?php

namespace MobileCart\SubscriptionBundle\EventListener\Checkout;

use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

/**
 * Class OrderSubmitSuccess
 * @package MobileCart\SubscriptionBundle\EventListener\Checkout
 */
class OrderSubmitSuccess
{
    /**
     * @var \MobileCart\SubscriptionBundle\Service\SubscriptionSessionService
     */
    protected $subscriptionSessionService;

    /**
     * @return \MobileCart\CoreBundle\Service\CartService
     */
    public function getCartService()
    {
        return $this->getSubscriptionSessionService()->getCartService();
    }

    /**
     * @return \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    public function getEntityService()
    {
        return $this->getCartService()->getEntityService();
    }

    /**
     * @param \MobileCart\SubscriptionBundle\Service\SubscriptionSessionService $subscriptionSessionService
     * @return $this
     */
    public function setSubscriptionSessionService(\MobileCart\SubscriptionBundle\Service\SubscriptionSessionService $subscriptionSessionService)
    {
        $this->subscriptionSessionService = $subscriptionSessionService;
        return $this;
    }

    /**
     * @return \MobileCart\SubscriptionBundle\Service\SubscriptionSessionService
     */
    public function getSubscriptionSessionService()
    {
        return $this->subscriptionSessionService;
    }

    public function onOrderSubmitSuccess(CoreEvent $event)
    {
        $cart = $event->getCart();
        if (!$cart->hasItems()) {
            return;
        }

        $customerToken = $event->getCustomerToken();
//        if (!$customerToken) {
//            return;
//        }

        $customer = $event->getOrder()->getCustomer();
        if (!$customer) {
            return;
        }

        foreach($cart->getItems() as $item) {

            if (!$item->getSubscriptionId()) {
                continue;
            }

            $subscription = $this->getEntityService()->find(EntityConstants::SUBSCRIPTION, $item->getSubscriptionId());
            if (!$subscription) {
                continue;
            }

            $customerName = $event->getOrder()->getBillingName();
            if (!$customerName) {
                $customerName = '';
            }

            $subscriptionCustomer = $this->getEntityService()->getInstance(EntityConstants::SUBSCRIPTION_CUSTOMER);
            $subscriptionCustomer->setSubscription($subscription)
                ->setCustomer($customer)
                ->setCreatedAt(new \DateTime('now'))
                ->setCustomerName($customerName)
                ->setIsActive(true);

            if ($customerToken) {
                $subscriptionCustomer->setCustomerToken($customerToken);
            }

            $this->getEntityService()->persist($subscriptionCustomer);

            $this->getSubscriptionSessionService()->setSubscriptionCustomer($subscriptionCustomer);

            //break; // assuming a single subscription
        }

    }
}
