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
     * @return array
     */
    public function getSortableFields()
    {
        return [
            'id' => 'ID',
            'customer_name' => 'Customer Name',
            'email' => 'Email', // including customer.email because we join tables in SubscriptionCustomerSearch
            'parent_subscription_customer_id' => 'Parent Subscription',
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
        ];
    }

    /**
     * @return mixed|string
     */
    public function getSearchField()
    {
        return 'customer_name';
    }

    /**
     * @return int|mixed
     */
    public function getSearchMethod()
    {
        return self::SEARCH_METHOD_LIKE;
    }

}
