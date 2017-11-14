<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerNewReturn
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerNewReturn
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

    public function onSubscriptionCustomerNewReturn(CoreEvent $event)
    {
        $event->setReturnData('entity', $event->getEntity());
        $event->setReturnData('form', $event->getReturnData('form')->createView());
        $event->setReturnData('template_sections', []);

        $event->setResponse($this->getThemeService()->render(
            'subscription_admin',
            'SubscriptionCustomer:new.html.twig',
            $event->getReturnData()
        ));
    }
}
