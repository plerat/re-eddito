<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class, [
                'required' => false,
            ])
            ->add('media', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '10m',
                        extensions: ['mp4', 'avi', 'jpg', 'jpeg', 'gif', 'png', 'webp'],
                        extensionsMessage: 'Please upload a valid media file : mp4, avi, jpg, jpeg, gif, png. Size max = 10 mo',
                    )

                ],
            ])
            ->addEventListener(
            FormEvents::POST_SUBMIT,
            [$this, 'onPostSubmit']
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }


    public function onPostSubmit(PostSubmitEvent $event): void
    {
        $form = $event->getForm();
        $post = $event->getData();

        $content = $post?->getContent();
        $mediaFile = $form->get('media')->getData();

        if (!$content && !$mediaFile) {
            $form->addError(new FormError('Veuillez renseigner un contenu ou un média.'));
        }

    }
}
