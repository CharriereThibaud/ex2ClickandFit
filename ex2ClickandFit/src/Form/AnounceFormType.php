<?php

namespace App\Form;

use App\Entity\Anounce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnounceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title', TextType::class, [
                'attr' => ['placeholder' => 'Titre de votre annonce'],
                'label' => "Titre"
            ])
            ->add('Description', TextareaType::class, [
                'attr' => ['placeholder' => 'Description de votre annonce'],
                'required' => false,
            ])
            ->add('Photo', FileType::class, [
                'label' => "Image",
                'required' => false,
                'mapped' => false
            ])
            ->add('PostalCode', TextType::class, [
                'attr' => ['placeholder' => 'Lieux de votre annonce'],
                'label' => "Code postal"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Anounce::class,
        ]);
    }
}
