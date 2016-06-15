<?php

namespace MobileCart\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('payment_amount')
            ->add('payment_interval_days')
            ->add('payment_handler')
            ->add('payment_method_code')
            ->add('external_plan_id')
            ->add('free_trial_days')
            ->add('has_shipping')
            ->add('shipping_method_code')
            ->add('is_free_shipping')
            ->add('has_groups')
            ->add('max_group_size')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MobileCart\SubscriptionBundle\Entity\Subscription'
        ));
    }
}
