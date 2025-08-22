<?php

/**
 * Admin Profile Type.
 */

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Formularz do edycji profilu administratora.
 */
class AdminProfileType extends AbstractType
{
    /**
     * Buduje formularz profilu administratora.
     *
     * @param FormBuilderInterface $builder Obiekt budujący formularz
     * @param array<string, mixed> $options Opcje formularza
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.admin.email.label',
                'required' => true,
            ])
            ->add('nickname', TextType::class, [
                'label' => 'admin.login_data.nickname',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('currentPassword', PasswordType::class, [
                'label' => 'form.admin.current_password.label',
                'mapped' => false,
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'form.admin.new_password.label',
                'required' => false,
                'mapped' => false,
            ]);
    }

    /**
     * Konfiguruje opcje formularza.
     *
     * @param OptionsResolver $resolver Obiekt konfigurujący opcje
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
