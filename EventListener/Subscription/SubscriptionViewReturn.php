<?php

namespace MobileCart\SubscriptionBundle\EventListener\Subscription;

use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionViewReturn
 * @package MobileCart\SubscriptionBundle\EventListener\Subscription
 */
class SubscriptionViewReturn
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

    public function onSubscriptionViewReturn(CoreEvent $event)
    {
        $template = $event->getEntity()->getCustomTemplate()
            ? $event->getEntity()->getCustomTemplate()
            : 'Subscription:view.html.twig';

        $event->setResponse($this->getThemeService()->render(
            'frontend',
            $template,
            $event->getReturnData()
        ));
    }
}
