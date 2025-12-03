<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

final class LoginUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Password'
            ])
            ->add('_remember_me', CheckboxType::class, [
                'required' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
