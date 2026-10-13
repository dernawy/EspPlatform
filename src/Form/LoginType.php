<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    /**
     * @var AuthenticationUtils
     */
    private AuthenticationUtils $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils, private UrlGeneratorInterface $urlGenerator)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('username', TextType::class, [
                'translation_domain' => 'messages',
                'help'               => 'app.login.username.help.text',
                'label' => 'app.login.form.username.label',
                'required' => true,

                'attr' => [
                    'autocomplete'   => 'username',
                    'placeholder'     => 'app.login.form.username.label',
                ],
                'constraints' => [
                    new NotBlank([
                        'message'     => 'app.login.username.error.blank.message'
                    ]),

                    new Length([
                        'min'         => 6,
                        'max'         => 12,
                        'minMessage'  => 'The username to short, the minimum length is ({{ limit }}) characters, the current username length is ({{ value_length }}) character(s).',
                        'maxMessage'  => 'The username to long, the maximum length is ({{ limit }}) characters, the current username length is ({{ value_length }}) character(s).'
                    ])
                ]
            ])

            ->add('password', PasswordType::class, [
                'translation_domain' => 'messages',
                'help'               => 'app.login.password.help.text',
                'label' => 'app.login.form.password.label',
                'required' => true,
                'attr' => [
                    'autocomplete'   => 'current-password',
                    'placeholder'     => 'app.login.form.password.label',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'app.login.password.error.blank.message'
                    ])
                ]
            ])

            ->add('remember_me', CheckboxType::class, [
                'translation_domain' => 'messages',
                'label'              => 'app.login.form.remember.me.label',
                'mapped'             => false,
            ])

            ->add('login', SubmitType::class, [
                'label' => 'app.login.form.login.button.label'
            ]);




        $authUtils = $this->authenticationUtils;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($authUtils) {

            // get the login error if there is one
            $error = $authUtils->getLastAuthenticationError();

            if ($error) {
                $event->getForm()->addError(new FormError($error->getMessage()));
            }

            $event->setData(
                array_replace((array)$event->getData(), array(
                    'username' => $authUtils->getLastUsername(),
                ))
            );
        });
    }

    public function onAuthenticationSuccess(): ?Response
    {

        return new RedirectResponse($this->urlGenerator->generate('all_app_home'));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /* Note: the form's csrf_token_id must correspond to that for the form login
         * listener in order for the CSRF token to validate successfully.
         */

        $resolver->setDefaults(array(
            'data_class'         => null,
            'csrf_field_name'    => '_csrf_token',
            'csrf_token_id'      => 'authenticate',
            'translation_domain' => 'messages'
        ));

    }
}
