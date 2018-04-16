<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AllyImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'imageFile',
                'Vich\UploaderBundle\Form\Type\VichImageType',
                array(
                    'label' => 'form.changeImageFile',
                    'required' => false,
                    'attr'  => array(
                        'class'       => '',
                        'accept'      => '.jpg,.jpeg,.bmp,.png',
                        'autocomplete' => 'off',
                        'data-max-size' => '1'
                    )
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.image'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'App\Entity\Ally',
                'translation_domain' => 'front_ally',
            )
        );
    }
}