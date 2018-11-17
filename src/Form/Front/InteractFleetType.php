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
                [
                    'class' => Fleet::class,
                    'label' => 'form.fleet',
                    'query_builder' => function (EntityRepository $er) use($options) {
                        return $er->createQueryBuilder('f')
                            ->join('f.user', 'u')
                            ->where('u.id = :user')
                            ->andWhere('f.flightTime is null')
                            ->andWhere('f.fightAt is null')
                            ->setParameters(['user' => $options['user']])
                            ->orderBy('f.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'attr'  => [
                        'placeholder' => 'form.fleet',
                        'class' => 'game-input',
                    ],
                    'required' => true,
                    'mapped' => false,
                ]
            )
            ->add(
                'flightType',
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                [
                    'choices' => $this->getFlightType(),
                    'label' => 'form.flightType',
                    'attr'  => [
                        'placeholder' => 'form.flightType',
                        'class' => 'game-input select2',
                    ],
                    'required' => true
                ]
            )
            ->add('sendForm', SubmitType::class, ['label' => 'form.sendFleet', 'attr' => ['class' => 'confirm-button']]);
    }

    protected function getFlightType()
    {
        return [
            'Normal' => '1',
            'Décharger et revenir' => '2',
            'Coloniser' => '3',
            'Envahir' => '4',
        ];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['user']);
        $resolver->setDefaults(
            [
                'data_class'         => null,
                'translation_domain' => 'front_fleet',
            ]
        );
    }
}