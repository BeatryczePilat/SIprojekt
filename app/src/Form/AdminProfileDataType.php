<?php

/**
 * Admin Profile Data Type.
 */

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formularz edycji danych.
 */
class AdminProfileDataType extends AbstractType
{
    /**
     * Builder.
     *
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
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
                'attr' => ['class' => 'form-control'],
            ]);
    }

    /**
     * Configuration.
     *
     * @param OptionsResolver $resolver resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
