<?php

namespace MobileCart\SubscriptionBundle\Entity;

use \Doctrine\Common\Collections\Collection;

class RecursiveSubscriptionCustomerIterator implements \RecursiveIterator
{

    private $_data;

    public function __construct(Collection $data)
    {
        $this->_data = $data;
    }

    public function hasChildren()
    {
        return (!$this->_data->current()->getChildSubscriptionCustomers()->isEmpty());
    }

    public function getChildren()
    {
        return new RecursiveSubscriptionCustomerIterator($this->_data->current()->getChildSubscriptionCustomers());
    }

    public function current()
    {
        return $this->_data->current();
    }

    public function next()
    {
        $this->_data->next();
    }

    public function key()
    {
        return $this->_data->key();
    }

    public function valid()
    {
        return $this->_data->current() instanceof SubscriptionCustomer;
    }

    public function rewind()
    {
        $this->_data->first();
    }
}
