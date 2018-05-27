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

class MarketType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
                'bitcoin',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'style' => 'height: 20px',
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                )
            )
            ->add(
                'worker',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'style' => 'height: 20px',
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                )
            )
            ->add(
                'soldier',
                null,
                array(
                    'label' => 'form.nbr',
                    'data' => 0,
                    'attr'  => array(
                        'placeholder' => 'form.nbr',
                        'class' => 'form-control',
                        'min' => '0',
                        'style' => 'height: 20px',
                        'autocomplete' => 'off',
                    ),
                    'required' => true,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.buyMarket'));
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
                'translation_domain' => 'front_market',
            )
        );
    }
}