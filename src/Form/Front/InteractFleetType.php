<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use App\Entity\Fleet;

class InteractFleetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'list',
                EntityType::class,
                array(
                    'class' => Fleet::class,
                    'label' => 'form.fleet',
                    'query_builder' => function (EntityRepository $er) use($options) {
                        return $er->createQueryBuilder('f')
                            ->join('f.user', 'u')
                            ->where('u.id = :user')
                            ->andWhere('f.flightTime is null')
                            ->andWhere('f.fightAt is null')
                            ->setParameters(array('user' => $options['user']))
                            ->orderBy('f.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'attr'  => array(
                        'placeholder' => 'form.fleet',
                        'class' => 'form-control',
                    ),
                    'required' => true,
                    'mapped' => false,
                )
            )
            ->add(
                'flightType',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                array(
                    'choices' => $this->getFlightType(),
                    'label' => 'form.flightType',
                    'attr'  => array(
                        'placeholder' => 'form.flightType',
                        'class' => 'form-control select2',
                    ),
                    'required' => true
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.sendFleet'));
    }

    protected function getFlightType()
    {
        return array(
            'normal' => '1',
            'decharger et revenir' => '2',
            'coloniser' => '3',
            'envahir' => '4',
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('user'));
        $resolver->setDefaults(
            array(
                'data_class'         => null,
                'translation_domain' => 'front_fleet',
            )
        );
    }
}