<?php

namespace App\Form;

use App\Entity\ProgrammeActivite;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->proramme =$options['programme'];
        $programme = $this->proramme;

        $builder
            ->add('date', TextType::class,['attr'=>['class'=>'form-control datepicker', 'autocomplete'=>"off"]])
            ->add('description', TextareaType::class,['attr'=>['class'=>'form-control summernote', 'rows'=>5]])
            ->add('lieu', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>"off"]])
            ->add('reportage', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>"off"], 'required'=>false])
            ->add('programme', EntityType::class,[
                'attr'=>['class'=>'form-control'],
                'class'=> 'App\Entity\Programme',
                'query_builder' => function(EntityRepository $er) use($programme){
                    return $er->findProgramme($programme);
                },
                'choice_label' => 'id',
                'label' => '',
                'required'=>true,
                'multiple' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProgrammeActivite::class,
            'programme' => null
        ]);
    }
}
