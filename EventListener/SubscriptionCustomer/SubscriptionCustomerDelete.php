<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\HttpFoundation\RedirectResponse;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerDelete
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerDelete
{
    /**
     * @var \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    protected $entityService;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @return $this
     */
    public function setRouter(\Symfony\Component\Routing\RouterInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

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

    public function onSubscriptionCustomerDelete(CoreEvent $event)
    {
        $entity = $event->getEntity();
        $this->getEntityService()->remove($entity, EntityConstants::SUBSCRIPTION_CUSTOMER);

        $event->getRequest()->getSession()->getFlashBag()->add(
            'success',
            'Subscription Successfully Deleted!'
        );

        $event->setResponse(new RedirectResponse($this->getRouter()->generate(
            'cart_admin_subscription_customer',
            []
        )));
    }
}
