<?php

namespace MobileCart\SubscriptionBundle\EventListener\Subscription;

use Symfony\Component\EventDispatcher\Event;
use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionAdminForm
 * @package MobileCart\SubscriptionBundle\EventListener\Subscription
 */
class SubscriptionAdminForm
{
    /**
     * @var \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    protected $entityService;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $formTypeClass = '';

    /**
     * @var \MobileCart\CoreBundle\Service\ThemeConfig
     */
    protected $themeConfig;

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
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @return $this
     */
    public function setFormFactory(\Symfony\Component\Form\FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @param string $formTypeClass
     * @return $this
     */
    public function setFormTypeClass($formTypeClass)
    {
        $this->formTypeClass = $formTypeClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return $this->formTypeClass;
    }

    /**
     * @param \MobileCart\CoreBundle\Service\ThemeConfig $themeConfig
     * @return $this
     */
    public function setThemeConfig(\MobileCart\CoreBundle\Service\ThemeConfig $themeConfig)
    {
        $this->themeConfig = $themeConfig;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\ThemeConfig
     */
    public function getThemeConfig()
    {
        return $this->themeConfig;
    }

    public function onSubscriptionAdminForm(CoreEvent $event)
    {
        $form = $this->getFormFactory()->create($this->getFormTypeClass(), $event->getEntity(), [
            'action' => $event->getFormAction(),
            'method' => $event->getFormMethod(),
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

        $event->setReturnData('form', $form);
        $event->setReturnData('form_sections', $formSections);
    }
}
