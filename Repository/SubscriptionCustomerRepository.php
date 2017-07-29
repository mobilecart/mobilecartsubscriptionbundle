<?php

namespace MobileCart\SubscriptionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MobileCart\CoreBundle\Repository\CartRepositoryInterface;

/**
 * SubscriptionCustomerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubscriptionCustomerRepository
    extends EntityRepository
    implements CartRepositoryInterface
{
    /**
     * @return bool
     */
    public function isEAV()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasImages()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getSortableFields()
    {
        return [
            'id' => 'ID',
            'customer_name' => 'Customer Name',
            'email' => 'Email', // including customer.email because we join tables in SubscriptionCustomerSearch
            'parent_subscription_customer_id' => 'Parent Subscription',
            'created_at' => 'Created At',
            'service_account_id' => 'Service Account', // we join tables in SubscriptionCustomerSearch
        ];
    }

    /**
     * @return array
     */
    public function getFilterableFields()
    {
        return [
            [
                'code'  => 'id',
                'label' => 'ID',
                'type'  => 'number',
            ],
            [
                'code'  => 'customer_name',
                'label' => 'Customer Name',
                'type'  => 'string',
            ],
            [
                'code'  => 'parent_subscription_customer_id',
                'label' => 'Parent Subscription',
                'type'  => 'number',
            ],
            [
                'code'  => 'service_account_id',
                'label' => 'Service Account',
                'type'  => 'string',
                'table' => 'customer_token'
            ],
        ];
    }

    /**
     * @return mixed|string
     */
    public function getSearchField()
    {
        return [
            'customer_name',
            [
                'table' => 'customer_token', // we join tables in SubscriptionCustomerSearch
                'column' => 'service_account_id',
            ],
            [
                'table' => 'customer',
                'column' => 'email', // we join tables in SubscriptionCustomerSearch
            ],
        ];
    }

    /**
     * @return int|mixed
     */
    public function getSearchMethod()
    {
        return self::SEARCH_METHOD_LIKE;
    }

}
