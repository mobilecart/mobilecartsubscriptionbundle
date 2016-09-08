<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

class SubscriptionCustomerDelete
{

    protected $entityService;

    protected $event;

    protected $router;

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

    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    public function getEntityService()
    {
        return $this->entityService;
    }

    public function onSubscriptionCustomerDelete(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

        $entity = $event->getEntity();
        $this->getEntityService()->remove($entity, EntityConstants::SUBSCRIPTION_CUSTOMER);

        $event->getRequest()->getSession()->getFlashBag()->add(
            'success',
            'Subscription Successfully Deleted!'
        );

        $response = new RedirectResponse($this->getRouter()->generate('cart_admin_subscription_customer', []));

        $event->setReturnData($returnData)
            ->setResponse($response);
    }
}
