<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use MobileCart\CoreBundle\Constants\EntityConstants;
use Symfony\Component\EventDispatcher\Event;

class SubscriptionCustomerSearch
{
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

    public function onSubscriptionCustomerSearch(Event $event)
    {
        $this->setEvent($event);
        $returnData = $event->getReturnData();
        $request = $event->getRequest();

        $search = $event->getSearch()
            ->setObjectType($event->getObjectType()) // Important: set this first
            ->parseRequest($event->getRequest())
            ->addJoin('inner', EntityConstants::CUSTOMER, 'id', 'customer_id')
            ->addColumn(EntityConstants::CUSTOMER . '.email')
            ->addJoin('left', EntityConstants::CUSTOMER_TOKEN, 'id', 'customer_token_id')
            ->addColumn(EntityConstants::CUSTOMER_TOKEN . '.service_account_id');

        $returnData['search'] = $search;
        $returnData['result'] = $search->search();

        $event->setReturnData($returnData);

        if (in_array($search->getFormat(), ['', 'html'])) {
            // for storing the last grid filters in the url ; used in back links
            $request->getSession()->set('cart_admin_subscription_customer', $request->getQueryString());
        }
    }
}
