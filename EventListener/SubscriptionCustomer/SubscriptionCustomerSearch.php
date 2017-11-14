<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use MobileCart\CoreBundle\Constants\EntityConstants;
use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerSearch
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerSearch
{
    /**
     * @var \MobileCart\CoreBundle\Service\SearchServiceInterface
     */
    protected $search;

    /**
     * @param \MobileCart\CoreBundle\Service\SearchServiceInterface $search
     * @param $objectType
     * @return $this
     */
    public function setSearch(\MobileCart\CoreBundle\Service\SearchServiceInterface $search, $objectType)
    {
        $this->search = $search->setObjectType($objectType);
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\SearchServiceInterface
     */
    public function getSearch()
    {
        return $this->search;
    }

    public function onSubscriptionCustomerSearch(CoreEvent $event)
    {
        $request = $event->getRequest();
        $search = $this->getSearch()
            ->parseRequest($request)
            ->addJoin('inner', EntityConstants::CUSTOMER, 'id', 'customer_id')
            ->addColumn(EntityConstants::CUSTOMER . '.email')
            ->addJoin('left', EntityConstants::CUSTOMER_TOKEN, 'id', 'customer_token_id')
            ->addColumn(EntityConstants::CUSTOMER_TOKEN . '.service_account_id');

        $event->setReturnData('search', $search);
        $event->setReturnData('result', $search->search());

        if (in_array($search->getFormat(), ['', 'html'])) {
            // for storing the last grid filters in the url ; used in back links
            $request->getSession()->set('cart_admin_subscription_customer', $request->getQueryString());
        }
    }
}
