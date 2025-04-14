<?php

namespace App\Form;

use App\Entity\Task;
use Gedmo\Uploadable\MimeType\MimeTypeGuesser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Name Can not be blank']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'atleaset {{ limit }}',
                        'max' => 5,
                        'maxMessage' => 'atleast {{ limit }}'
                    ])
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Gender Can not be null']),
                ],
                'choices' => [
                    'Male' => 0,
                    'Female' => 1
                ],
                'placeholder' => 'Select Gender'
            ])
            ->add('hobby', ChoiceType::class, [
                'required' => false,
                'multiple'=>true,
                'expanded'=>true,
                'constraints' => [
                    new NotBlank(['message' => 'please choose hobby'])
                ],
                'choices' => [
                    'games' => 'games',
                    'travel' => 'travel',
                    'reading' => 'reading'
                ]
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'please upload image']),
                    new Image([
                        'maxSize' => '5M',
                        'mimeTypes' =>
                        [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage'=>'please upload a valid image'
                    ])
                ],

            ])
            ->add('role',ChoiceType::class,[
                'required'=>false,
                'constraints'=>[
                    new NotBlank(['message'=>'please choose role'])
                ],
                'choices'=>[
                    'admin'=>0,
                    'employee'=>1
                ],
                'placeholder'=>'choose Role',
            ])
            // ->add('created_at')
            // ->add('updated_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
