<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerAddSuccess
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerAddSuccess
{
    /**
     * @var \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    protected $entityService;

    /**
     * @var \MobileCart\CoreBundle\Service\ThemeService
     */
    protected $themeService;

    /**
     * @param \MobileCart\CoreBundle\Service\ThemeService $themeService
     * @return $this
     */
    public function setThemeService(\MobileCart\CoreBundle\Service\ThemeService $themeService)
    {
        $this->themeService = $themeService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\ThemeService
     */
    public function getThemeService()
    {
        return $this->themeService;
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

    public function onSubscriptionCustomerAddSuccess(CoreEvent $event)
    {
        $event->setResponse($this->getThemeService()->render(
            'subscription_frontend',
            'SubscriptionCustomer:add_success.html.twig',
            $event->getReturnData()
        ));
    }
}
