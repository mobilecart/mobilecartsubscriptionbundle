<?php

namespace MobileCart\SubscriptionBundle\Entity;

use \Doctrine\Common\Collections\Collection;

class RecursiveSubscriptionCustomerIterator implements \RecursiveIterator
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return (!$this->data->current()->getChildSubscriptionCustomers()->isEmpty());
    }

    /**
     * @return RecursiveSubscriptionCustomerIterator|\RecursiveIterator
     */
    public function getChildren()
    {
        return new RecursiveSubscriptionCustomerIterator($this->data->current()->getChildSubscriptionCustomers());
    }

    public function current()
    {
        return $this->data->current();
    }

    public function next()
    {
        $this->data->next();
    }

    public function key()
    {
        return $this->data->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->data->current() instanceof SubscriptionCustomer;
    }

    public function rewind()
    {
        $this->data->first();
    }
}
