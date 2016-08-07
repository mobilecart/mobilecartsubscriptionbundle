<?php

namespace MobileCart\SubscriptionBundle\EventListener\Checkout;

use Symfony\Component\EventDispatcher\Event;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

class OrderSubmitSuccess
{
    protected $entityService;

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

    public function onOrderSubmitSuccess(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

        $cart = $event->getCart();
        if (!$cart->hasItems()) {
            return;
        }

        $customerToken = $event->getCustomerToken();
        if (!$customerToken) {
            return;
        }

        $customer = $event->getOrder()->getCustomer();

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
                ->setCustomerToken($customerToken)
                ->setIsActive(1);

            $this->getEntityService()->persist($subscriptionCustomer);
            break; // assuming a single subscription
        }

    }
}
