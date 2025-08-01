<?php

/**
 * Url Filter Type.
 */

namespace App\Form;

use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formularz do filtrowania skróconych adresów URL.
 */
class UrlFilterType extends AbstractType
{
    /**
     * Obsługa budowania formularza filtrowania URL.
     *
     * @param FormBuilderInterface $builder Obiekt budujący formularz
     * @param array<string, mixed> $options Opcje przekazane do formularza
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'required' => false,
                'label' => 'form.url_filter.email.label',
            ])
            ->add('originalUrl', TextType::class, [
                'required' => false,
                'label' => 'form.url_filter.original_url.label',
            ])
            ->add('shortCode', TextType::class, [
                'required' => false,
                'label' => 'form.url_filter.short_code.label',
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'required' => false,
                'choice_label' => 'name',
                'label' => 'form.url_filter.tag.label',
                'placeholder' => 'form.url_filter.tag.placeholder',
            ]);
    }

    /**
     * Obsługa konfiguracji opcji formularza.
     *
     * @param OptionsResolver $resolver Obiekt konfigurujący opcje
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
        ]);
    }
}
