<?php

namespace App\Form;

use App\Entity\Cardinal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CardinalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('annee', TextType::class,['attr'=>['class'=>'form-control', 'placeholder'=>"", 'autocomplete'=>"off"]])
            ->add('theme', TextType::class,['attr'=>['class'=>'form-control', 'placeholder'=>"", 'autocomplete'=>"off"]])
            ->add('titre', TextType::class,['attr'=>['class'=>'form-control', 'placeholder'=>"", 'autocomplete'=>"off"]])
            ->add('message', TextareaType::class,['attr'=>['class'=>'form-control summernote', 'rows'=>"5"]])
            ->add('photo', FileType::class,[
                'mapped'=>false,
                'required' =>false,
                'constraints'=>[
                    new File([
                        'maxSize' => '100000k',
                        'mimeTypes' =>[
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => "Votre fichier doit être de type image"
                    ])
                ],
                'attr'=>['onchange'=>'getFileInfo()'],
                'label' => "Photo du Cardinal "
            ])
            //->add('createdAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cardinal::class,
        ]);
    }
}
