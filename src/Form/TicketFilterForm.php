<?php
namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketFilterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices'  => array_combine(Ticket::STATUSES, Ticket::STATUSES),
                'placeholder' => 'Tous statuts',
                'required' => false,
            ])
            ->add('priority', ChoiceType::class, [
                'choices'  => array_combine(Ticket::PRIORITIES, Ticket::PRIORITIES),
                'placeholder' => 'Toutes priorités',
                'required' => false,
            ])
            ->add('createdFrom', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('createdTo', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('updatedFrom', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('updatedTo', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('sort', ChoiceType::class, [
                'choices' => [
                    'Priorité'        => 't.priority',
                    'Date création'   => 't.createdAt',
                    'Date mise à jour'=> 't.updatedAt',
                ],
                'placeholder' => 'Tri par',
                'required' => false,
            ])
            ->add('direction', ChoiceType::class, [
                'choices'  => ['Croissant' => 'ASC', 'Descroissant' => 'DESC'],
                'placeholder' => 'Ordre',
                'required' => false,
            ])
            ->add('filter', SubmitType::class, ['label' => 'Appliquer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}