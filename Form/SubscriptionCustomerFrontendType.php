<?php

namespace MobileCart\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriptionCustomerFrontendType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', 'text')
            ->add('last_name', 'text')
            ->add('email', 'text',[
                'required' => 1,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('billing_phone')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MobileCart\CoreBundle\Entity\Customer',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'customer';
    }
}
