<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => [
                        'ROLE_USER' => 'ROLE_USER',
                        'ROLE_EDITOR' => 'ROLE_EDITOR',
                        'ROLE_ADMIN' => 'ROLE_ADMIN',
                    ],
                    'multiple' => true,
                    // Affichage des éléments sous forme de cases à cocher
                    'expanded' => true
                ]
            )
            // ->add('password')
            // ->add('plainPassword', PasswordType::class, [

            //     // On indique à Symfony que la propriété 'plainPassword'
            //     // n'est pas liée (mapped) à l'entité User
            //     'mapped' => false
            // ])
            ->add('firstname')
            ->add('lastname');

        // On va se brancher à un évènement PRE_SET_DATA
        // Pour afficher le champ password en fonction du contexte
        // dans lequel on se trouve :
        // - à la création : on affiche le champ
        // - à l'édition : on affiche pas le champ

        // $button.addEventListener('click', app.handleClick)
        // handleClick(event)

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // ON récupère les données de l'utilisateur que l'on s'apprete
            // à créer ou à éditer
            $user = $event->getData();
            $form = $event->getForm();

            // Si on est dans le cas d'une création de compte utilisateur
            // Alors on ajoute le champs password
            if ($user->getId() === null) {
                $form->add('plainPassword', PasswordType::class, [

                    // On indique à Symfony que la propriété 'plainPassword'
                    // n'est pas liée (mapped) à l'entité User
                    'mapped' => false
                ]);
            }
            // dd($user, $form);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
