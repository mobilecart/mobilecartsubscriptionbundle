<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

class SubscriptionCustomerCancel
{

    protected $subscriptionService;

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

    protected function getReturnData()
    {
        return $this->getEvent()->getReturnData()
            ? $this->getEvent()->getReturnData()
            : [];
    }

    public function setSubscriptionService($subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        return $this;
    }

    public function getSubscriptionService()
    {
        return $this->subscriptionService;
    }

    public function onSubscriptionCustomerCancel(Event $event)
    {
        $this->setEvent($event);
        if ($this->getSubscriptionService()->cancelRecurringSubscription($event->getEntity())) {
            $event->getRequest()->getSession()->getFlashBag()->add(
                'success',
                'Subscription Successfully Canceled!'
            );
        }
    }
}
