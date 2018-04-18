<?php

namespace App\Form\Front;

use App\Entity\Grade;
use function Couchbase\defaultEncoder;
use Doctrine\ORM\EntityRepository;
use PMA\libraries\config\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserAttrGradeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'grade',
                EntityType::class,
                array(
                    'class' => Grade::class,
                    'label' => 'form.grade',
                    'query_builder' => function (EntityRepository $er) use($options) {
                        return $er->createQueryBuilder('g')
                            ->join('g.ally', 'a')
                            ->where('a.id = :id')
                            ->setParameter('id', $options['allyId'])
                            ->orderBy('g.placement', 'ASC');
                    },
                    'choice_label' => 'name',
                    'attr'  => array(
                        'placeholder' => 'form.grade',
                        'class' => 'form-control',
                    ),
                    'required' => true,
                    'mapped' => false,
                )
            )
            ->add('sendForm', SubmitType::class, array('label' => 'form.send'));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('allyId'));
        $resolver->setDefaults(
            array(
                'data_class'         => null,
                'translation_domain' => 'front_grade',
            )
        );
    }
}