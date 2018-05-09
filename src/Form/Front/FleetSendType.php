<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use function Couchbase\defaultEncoder;
use Doctrine\ORM\EntityRepository;
use PMA\libraries\config\Form;
use App\Entity\Planet;

class FleetSendType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'galaxy',
                null,
                array(
                    'label' => 'form.num',
                    'attr'  => array(
                        'placeholder' => 'form.num',
                        'class' => 'form-control',
                        'min' => '1',
                        'max' => '20',
                    ),
                    'required' => true,
                )
            )
            ->add(
                'sector',
                null,
                array(
                    'label' => 'form.num',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.num',
                        'class' => 'form-control',
                        'min' => '1',
                        'max' => '100',
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                )
            )
            ->add(
                'planete',
                null,
                array(
                    'label' => 'form.num',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.num',
                        'class' => 'form-control',
                        'min' => '1',
                        'max' => '25',
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                )
            )
            ->add(
                'planet',
                EntityType::class,
                array(
                    'class' => Planet::class,
                    'label' => 'form.planet',
                    'query_builder' => function (EntityRepository $er) use($options) {
                        return $er->createQueryBuilder('p')
                            ->join('p.user', 'u')
                            ->where('u.id = :user')
                            ->setParameter('user', $options['user'])
                            ->orderBy('p.sector', 'ASC');
                    },
                    'choice_label' => 'name',
                    'attr'  => array(
                        'placeholder' => 'form.planet',
                        'class' => 'form-control',
                    ),
                    'required' => false,
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