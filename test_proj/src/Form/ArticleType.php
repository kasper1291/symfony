<?php

namespace App\Form;

use App\Entity\Article;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('author', \Symfony\Component\Form\Extension\Core\Type\TextType::class,  [
                'label' => 'Author',
                'required' => false
            ])
            ->add('title', \Symfony\Component\Form\Extension\Core\Type\TextType::class,  ['label' => 'Title'])
            ->add('content', TextareaType::class,  ['label' => 'Content'])
            ->add('image', FileType::class,  [
                'label' => 'Image',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '24M',
                        'mimeTypes' =>[
                            'image/gif',
                            'image/jpeg',
                            'image/x-png',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image !',
                    ])
                ]
            ])
            ->add('created_at')
            ->add('updated_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
