<?php

namespace App\Form;

use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>'off']])
            ->add('contenu', TextareaType::class,['attr'=>['class'=>'form-control summernote', 'rows'=>"5"]])
            ->add('ordre', ChoiceType::class,[
                'attr'=>['class'=>'form-control'],
                'choices' => ['1'=>1, 2=>2, 3=>3, 4=>4, 5=>5]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Departement::class,
        ]);
    }
}
