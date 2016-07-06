<?php

namespace MobileCart\SubscriptionBundle\Twig\Extension;

class FrontendExtension extends \Twig_Extension
{
    /**
     * @var
     */
    protected $subscriptionSessionService;

    /**
     * @var
     */
    protected $session;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'isSubscribed' => new \Twig_Function_Method($this, 'getIsSubscribed', ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [];
    }

    public function getName()
    {
        return 'mobilecart.subscription.frontend';
    }

    /**
     * @param $subscriptionSessionService
     * @return $this
     */
    public function setSubscriptionSessionService($subscriptionSessionService)
    {
        $this->subscriptionSessionService = $subscriptionSessionService;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionSessionService()
    {
        return $this->subscriptionSessionService;
    }

    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }

    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return bool
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionSessionService()->getIsSubscribed();
    }
}
