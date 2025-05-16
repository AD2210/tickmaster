<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SettingsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $settings = $options['data']->getSettings();

        $builder
            ->add('theme', ChoiceType::class, [
                'choices'  => [
                    'Clair' => 'light',
                    'Sombre' => 'dark',
                ],
                'label'    => 'Thème de l\'interface',
                'mapped'   => false,
                'data'     => $settings['theme'] ?? 'light',
            ])
            ->add('showCharts', CheckboxType::class, [
                'label'    => 'Afficher les graphiques',
                'mapped'   => false,
                'required' => false,
                'data'     => $settings['showCharts'] ?? true,
            ])
            ->add('barColor', ChoiceType::class, [
                'choices'  => [
                    'Bleu'     => '#0d6efd',
                    'Gris'     => '#6c757d',
                    'Vert'     => '#198754',
                    'Jaune'    => '#ffc107',
                    'Rouge'    => '#dc3545',
                ],
                'label'         => 'Couleur du bar graph',
                'mapped'        => false,
                'data'          => $settings['barColor'] ?? '#0d6efd',
                'expanded'      => true,
                'multiple'      => false,
                'choice_attr'   => function($choice, $key, $value) {
                    return ['style' => "background-color:{$value}; width:20px; height:20px; display:inline-block; margin-right:8px;"];
                },
            ])
            ->add('pieContrast', ChoiceType::class, [
                'choices'  => [
                    'Faible contraste' => 'low',
                    'Fort contraste'   => 'high',
                ],
                'label'    => 'Contraste du pie graph',
                'mapped'   => false,
                'data'     => $settings['pieContrast'] ?? 'high',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
