<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCustomerViewReturn
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerViewReturn
{
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

    public function onSubscriptionCustomerViewReturn(CoreEvent $event)
    {
        $template = $event->getEntity()->getCustomTemplate()
            ? $event->getEntity()->getCustomTemplate()
            : 'SubscriptionCustomer:view.html.twig';

        $event->setResponse($this->getThemeService()->render(
            'frontend',
            $template,
            $event->getReturnData()
        ));
    }
}
