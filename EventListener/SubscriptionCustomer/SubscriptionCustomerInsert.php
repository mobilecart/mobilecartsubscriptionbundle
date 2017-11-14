<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\EventDispatcher\Event;
use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerInsert
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerInsert
{
    /**
     * @var \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    protected $entityService;

    /**
     * @param \MobileCart\CoreBundle\Service\AbstractEntityService $entityService
     * @return $this
     */
    public function setEntityService(\MobileCart\CoreBundle\Service\AbstractEntityService $entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    public function getEntityService()
    {
        return $this->entityService;
    }

    public function onSubscriptionCustomerInsert(CoreEvent $event)
    {
        $entity = $event->getEntity();
        $entity->setCreatedAt(new \DateTime('now'));
        if (!$entity->getCustomerName()) {
            $entity->setCustomerName('');
        }
        $this->getEntityService()->persist($entity);
    }
}
