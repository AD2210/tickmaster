<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class,[
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array_combine(Ticket::STATUSES, Ticket::STATUSES),
                'placeholder' => 'Choisir un statut'
            ])
            ->add('priority', ChoiceType::class,[
                'choices' => array_combine(Ticket::PRIORITIES, Ticket::PRIORITIES),
                'placeholder' => 'Choisir une priorité'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
