<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Carriers\CarrierProvider;

class CalculateShippingRateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $carrierProvider = new CarrierProvider();
        $carriers = $carrierProvider->getAll();

        $choices = [];
        foreach ($carriers as $carrier) {
            $choices[$carrier::NAME] = $carrier::CODE;
        }

        $builder
            ->add('carrier', ChoiceType::class, [
                'choices' => $choices,
                'required' => true,
                'placeholder' => 'Please select carrier',
                'attr' => [
                    'style' => 'width: 500px;',
                ],
                'label' => 'Carrier',
            ])
            ->add('weightKg', NumberType::class, [
                'label' => 'Weight, Kg',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Please enter parcel weight in kg.',
                    'style' => 'width: 500px;',
                ],
            ])
            ->add('calculate', SubmitType::class, [
            'label' => 'Calculate',
        ]);
    }
}
