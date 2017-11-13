<?php

namespace MobileCart\SubscriptionBundle\EventListener\Subscription;

use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionDelete
 * @package MobileCart\SubscriptionBundle\EventListener\Subscription
 */
class SubscriptionDelete
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

    public function onSubscriptionDelete(CoreEvent $event)
    {
        $entity = $event->getEntity();
        $this->getEntityService()->remove($entity);
    }
}
