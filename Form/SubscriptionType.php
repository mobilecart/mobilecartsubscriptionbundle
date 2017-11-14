<?php

namespace MobileCart\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * Class SubscriptionType
 * @package MobileCart\SubscriptionBundle\Form
 */
class SubscriptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('payment_amount', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('payment_interval_days', NumberType::class, [
                'required' => true,
                'constraints' => [
                    new GreaterThan(['value' => 0]),
                    new NotBlank(),
                ],
            ])
            ->add('payment_handler', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new GreaterThan(['value' => 0]),
                    new NotBlank(),
                ],
            ])
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
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'subscription';
    }
}
