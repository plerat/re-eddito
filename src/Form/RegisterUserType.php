<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
            'email',EmailType::class,[
                'label'=>'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email'
                    ]),
                ]
            ])
            ->add('pseudo',TextType::class,[
                'label'=>'Pseudo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an nickname'
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options' => [
                    'label' => 'Password',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password'
                        ])
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please confirm the password'
                        ])
                    ]
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::Class]);
    }
}
