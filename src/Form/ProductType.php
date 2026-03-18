<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome',
                'translation_domain' => 'messages',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descrição',
                'translation_domain' => 'messages',
            ])
            ->add('image', UrlType::class, [
                'label' => 'URL da imagem',
                'required' => false,
                'translation_domain' => 'messages',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Preço',
                'currency' => 'BRL',
                'translation_domain' => 'messages',
            ])
            ->add('stockQuantity', IntegerType::class, [
                'label' => 'Estoque',
                'translation_domain' => 'messages',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
