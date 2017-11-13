<?php

namespace MobileCart\SubscriptionBundle\EventListener\Subscription;

use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionNewReturn
 * @package MobileCart\SubscriptionBundle\EventListener\Subscription
 */
class SubscriptionNewReturn
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

    public function onSubscriptionNewReturn(CoreEvent $event)
    {
        $event->setReturnData('template_sections', []);
        $event->setReturnData('entity', $event->getEntity());
        $event->setReturnData('form', $event->getReturnData('form')->createView());

        $event->setResponse($this->getThemeService()->render(
            'subscription_admin',
            'Subscription:new.html.twig',
            $event->getReturnData()
        ));
    }
}
