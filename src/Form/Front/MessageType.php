<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;

class MessageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'anonymous',
                CheckboxType::class,
                array(
                    'label' => 'form.name',
                    'attr'  => array(
                        'placeholder' => 'form.name',
                        'class' => '',
                    ),
                    'mapped' => false,
                    'required' => false,
                )
            )
            ->add(
                'user',
                EntityType::class,
                array(
                    'class' => User::class,
                    'label' => 'form.user',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.username', 'ASC');
                    },
                    'choice_label' => 'username',
                    'attr'  => array(
                        'placeholder' => 'form.user',
                        'class' => 'form-control',
                    ),
                    'required' => true,
                    'mapped' => true,
                )
            )
            ->add(
                'title',
                null,
                array(
                    'label' => 'form.title',
                    'attr'  => array(
                        'placeholder' => 'form.title',
                        'class' => 'form-control',
                        'maxlength' => '20',
                        'minlength' => '1',
                        'style' => 'height: 25px'
                    ),
                    'required' => true,
                    'mapped' => true,
                )
            )
            ->add(
                'content',
                'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                array(
                    'label' => 'form.content',
                    'attr'  => array(
                        'placeholder' => 'form.content',
                        'class' => 'form-control',
                        'rows' => 10,
                        'cols' => 75,
                        'maxlength' => '500',
                        'minlength' => '1',
                    ),
                    'required' => true,
                    'mapped' => true,
                )
            )
            ->add(
                'bitcoin',
                null,
                array(
                    'label' => 'form.num',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.num',
                        'class' => 'form-control',
                        'min' => '0',
                        'style' => 'height: 20px'
                    ),
                    'required' => true,
                    'mapped' => true,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.sendMessage'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'App\Entity\Message',
                'translation_domain' => 'front_message',
            )
        );
    }
}