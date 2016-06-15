<?php

namespace MobileCart\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionPaymentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('payment_date', 'datetime')
            ->add('payment_method')
            ->add('base_currency')
            ->add('base_amount')
            ->add('currency')
            ->add('amount')
            ->add('is_approved')
            ->add('subscription')
            ->add('customer')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MobileCart\SubscriptionBundle\Entity\SubscriptionPayment'
        ));
    }
}
