<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Titlu',
            'required' => true,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Titlul nu poate fi gol.',
                ]),
                new Assert\Length([
                    'min' => 4,
                    'max' => 20,
                    'minMessage' => 'Introduceti cel puțin {{ limit }} caractere pentru titlu.',
                    'maxMessage' => 'Introduceti cel mult {{ limit }} caractere pentru titlu.',
                ]),
            ],
        ])
            ->add('description', TextType::class, [
                'label' => 'Descriere',
                'required' => false,
                'attr' => [
                    'rows' => 5, 
                    'cols' => 40, 
                ],
            ])
            ->add('dueDate', DateType::class, [
                'label' => 'Data limită',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
