<?php

namespace MobileCart\SubscriptionBundle\Twig\Extension;

class FrontendExtension extends \Twig_Extension
{
    /**
     * @var
     */
    protected $cartSessionService;

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
     * @param $cartSessionService
     * @return $this
     */
    public function setCartSessionService($cartSessionService)
    {
        $this->cartSessionService = $cartSessionService;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCartSessionService()
    {
        return $this->cartSessionService;
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
        if (!$this->getCartSessionService()->getCart()) {
            return false;
        }

        if (!$this->getCartSessionService()->getCart()->getCustomer()) {
            return false;
        }

        if (!$this->getCartSessionService()->getCart()->getCustomer()->getSubscriptionCustomer()) {
            return false;
        }

        return $this->getCartSessionService()->getCart()->getCustomer()
            ->getSubscriptionCustomer()
            ->getIsActive();
    }
}
