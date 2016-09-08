<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\EventDispatcher\Event;
use MobileCart\SubscriptionBundle\Form\SubscriptionCustomerType;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

class SubscriptionCustomerAdminForm
{
    protected $entityService;

    protected $formFactory;

    protected $themeConfig;

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

    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    public function getEntityService()
    {
        return $this->entityService;
    }

    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    public function getFormFactory()
    {
        return $this->formFactory;
    }

    public function setThemeConfig($themeConfig)
    {
        $this->themeConfig = $themeConfig;
        return $this;
    }

    public function getThemeConfig()
    {
        return $this->themeConfig;
    }

    public function onSubscriptionCustomerAdminForm(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

        $entity = $event->getEntity();

        $formType = new SubscriptionCustomerType();
        //$formType->setPaymentHandlerOptions([]);
        $form = $this->getFormFactory()->create($formType, $entity, [
            'action' => $event->getAction(),
            'method' => $event->getMethod(),
        ]);

        $formSections = [
            'general' => [
                'label' => 'General',
                'id' => 'general',
                'fields' => [
                    'subscription',
                    'parent_subscription_customer',
                    'customer',
                    'customer_name',
                    //'customer_token',
                    'is_active',
                    'is_in_free_trial',
                    'is_overdue',
                    'payment_reminder_at',
                    'is_logged_in',
                ],
            ],
        ];

        $returnData['form_sections'] = $formSections;
        $returnData['form'] = $form;

        $event->setForm($form)
            ->setReturnData($returnData);
    }
}
