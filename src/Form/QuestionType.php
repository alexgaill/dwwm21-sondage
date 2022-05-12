<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Sondage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Question',
                'attr' => [
                    'placeholder' => 'Question'
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-3'
                ]
            ])
            // EntityType permet d'informer symfony que l'on souhaite récupérer les informations
            // d'une entité pour générer un select
            ->add('sondage', EntityType::class, [
                // On doit lui indiquer l'entité de référence avec le paramètre class => Obligatoire
                'class' => Sondage::class,
                // On doit lui indiquer quel propriété de l'entité va servir pour afficher les options => Obligatoire
                'choice_label' => 'title'
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Ajouter"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
