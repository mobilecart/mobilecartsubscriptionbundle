<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerEditReturn
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerEditReturn
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

    public function onSubscriptionCustomerEditReturn(CoreEvent $event)
    {
        $event->setReturnData('entity', $event->getEntity());
        $event->setReturnData('form', $event->getReturnData('form')->createView());
        $event->setReturnData('template_sections', []);

        $event->setResponse($this->getThemeService()->render(
            'subscription_admin',
            'SubscriptionCustomer:edit.html.twig',
            $event->getReturnData()
        ));
    }
}
