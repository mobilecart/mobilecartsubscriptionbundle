<?php

namespace MobileCart\SubscriptionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MobileCart\CoreBundle\Repository\CartRepositoryInterface;

/**
 * SubscriptionPaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubscriptionPaymentRepository
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
            'created_at' => 'Created At',
            'payment_method' => 'Payment Method',
            'is_approved' => 'Is Approved',
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
                'code'  => 'payment_method',
                'label' => 'Payment Method',
                'type'  => 'string',
            ],
            [
                'code'  => 'created_at',
                'label' => 'Created At',
                'type'  => 'string',
            ],
            [
                'code'  => 'is_approved',
                'label' => 'Is Approved',
                'type'  => 'number',
            ],
            [
                'code'  => 'base_currency',
                'label' => 'Base Currency',
                'type'  => 'string',
            ],
        ];
    }

    /**
     * @return mixed|string
     */
    public function getSearchField()
    {
        return 'name';
    }

    /**
     * @return int|mixed
     */
    public function getSearchMethod()
    {
        return self::SEARCH_METHOD_LIKE;
    }
}
