<?php

namespace MobileCart\SubscriptionBundle\EventListener\Subscription;

use Symfony\Component\EventDispatcher\Event;
use MobileCart\SubscriptionBundle\Form\SubscriptionType;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;

class SubscriptionAdminForm
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

    public function onSubscriptionAdminForm(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

        $entity = $event->getEntity();

        $formType = new SubscriptionType();
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
                    'name',
                    'payment_amount',
                    'payment_interval_days',
                    'payment_handler',
                    'payment_method_code',
                    'external_plan_id',
                    'free_trial_days',
                    'has_shipping',
                    'shipping_method_code',
                    'is_free_shipping',
                    'has_groups',
                    'max_group_size',
                ],
            ],
        ];

        $returnData['form_sections'] = $formSections;
        $returnData['form'] = $form;

        $event->setForm($form)
            ->setReturnData($returnData);
    }
}
