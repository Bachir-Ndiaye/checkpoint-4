<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('type')
            //->add('path')
            ->add('path', FileType::class, [
                'label' => '(PDF, Images, Audio or Video files)',
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/bmp',
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                            'image/webp',
                            'video/x-msvideo',
                            'video/mpeg',
                            'video/ogg',
                            'video/webm',
                            'video/3gpp',
                            'video/3gpp2'
                        ],
                        'mimeTypesMessage' => 'Merci de téléverser une photo ou une vidéo valide.',
                    ])
                ],

            ])
            ->add('users',EntityType::class, [
                "class" => User::class,
                'choice_label' => 'lastname',
                'multiple' => true
            ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
