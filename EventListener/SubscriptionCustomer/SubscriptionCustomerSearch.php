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

    protected function getReturnData()
    {
        return $this->getEvent()->getReturnData()
            ? $this->getEvent()->getReturnData()
            : [];
    }

    public function onSubscriptionCustomerSearch(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

        $search = $event->getSearch()
            ->setObjectType($event->getObjectType()) // Important: set this first
            ->parseRequest($event->getRequest())
            ->addJoin('inner', EntityConstants::CUSTOMER, 'id', 'customer_id')
            ->addColumn(EntityConstants::CUSTOMER . '.email')
            ;

        $returnData['search'] = $search;
        $returnData['result'] = $search->search();

        $event->setReturnData($returnData);
    }
}
