<?php

namespace MobileCart\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionCustomerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('is_in_free_trial')
            ->add('is_active')
            ->add('is_overdue')
            ->add('payment_reminder_at', 'datetime')
            ->add('is_logged_in')
            ->add('subscription')
            ->add('customer')
            ->add('customer_name')
            ->add('parent_subscription_customer')
            //->add('customer_token', 'text')
        ;
    }

    public function getBlockPrefix()
    {
        return 'subscription_customer';
    }
}
