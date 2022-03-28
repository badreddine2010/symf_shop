<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles',ChoiceType::class,['choices'=>[
                'utilisateur' => "ROLE_USER",
                'administrateur' => "ROLE_ADMIN"
            ],
            'expanded'=> true,
            'multiple'=> true,
            'label'=>"Roles"
            ])
            ->add('password')
            ->add('firstName')
            ->add('name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
