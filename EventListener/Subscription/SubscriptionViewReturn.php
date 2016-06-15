<?php

namespace MobileCart\SubscriptionBundle\EventListener\Subscription;

use Symfony\Component\EventDispatcher\Event;

class SubscriptionViewReturn
{
    protected $themeService;

    protected $event;

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

    public function setThemeService($themeService)
    {
        $this->themeService = $themeService;
        return $this;
    }

    public function getThemeService()
    {
        return $this->themeService;
    }

    public function onSubscriptionViewReturn(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

        $template = $event->getEntity()->getCustomTemplate()
            ? $event->getEntity()->getCustomTemplate()
            : 'Subscription:view.html.twig';

        $response = $this->getThemeService()
            ->render('frontend', $template, $returnData);

        $event->setReturnData($returnData);
        $event->setResponse($response);
    }
}
