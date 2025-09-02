<?php

/**
 * Admin Password Type.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formularz edycji hasła.
 */
class AdminPasswordType extends AbstractType
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
            ->add('currentPassword', PasswordType::class, [
                'label' => 'form.admin.current_password.label',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'admin.password.current.not_blank']),
                ],
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'form.admin.new_password.label',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'admin.password.new.not_blank']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'admin.password.new.min_length',
                    ]),
                ],
            ]);
    }

    /**
     * Configuration.
     *
     * @param OptionsResolver $resolver resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
