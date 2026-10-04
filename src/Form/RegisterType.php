<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('full_name', TextType::class, [
                'translation_domain' => 'messages',
                'help'               => 'app.text.new.user.register.form.fullname.message',
                'label'              => 'app.register.form.fullname.label',
                'required'           => true,
                'attr' => [
                    'placeholder'     => 'app.text.new.user.register.form.placeholder.fullname.text',
                     'autocomplete'   => 'given-name'
                ],

                'constraints' => [

                    new NotBlank([
                        'message'     => 'The name field must not be empty.'
                    ]),

                    new Length([
                        'min'         => 3,
                        'max'         => 50,
                        'minMessage'  => 'The name to short, the minimum length is ({{ limit }}) characters, the current first name length is ({{ value_length }}) character(s).',
                        'maxMessage'  => 'The  name to long, the maximum length is ({{ limit }}) characters, the current first name length is ({{ value_length }}) character(s).'
                    ])
                ]
            ])

            ->add('username', TextType::class, [
                'translation_domain'  => 'messages',
                'help'                => 'app.text.new.user.register.form.username.message',
                'label'               => 'app.register.form.username.label',
                'required'            => true,
                'attr' => [
                    'placeholder'     => 'app.text.new.user.register.form.placeholder.username.text',
                ],
                'constraints' => [
                    new NotBlank([
                        'message'     => 'The username field must not be empty.'
                    ]),

                    new Length([
                        'min'         => 6,
                        'max'         => 14,
                        'minMessage'  => 'The username to short, the minimum length is ({{ limit }}) characters, the current username length is ({{ value_length }}) character(s).',
                        'maxMessage'  => 'The username to long, the maximum length is ({{ limit }}) characters, the current username length is ({{ value_length }}) character(s).'
                    ])
                ]
            ])

            ->add('user_email', EmailType::class, [
                'translation_domain' => 'messages',
                'help'               => 'app.text.new.user.register.form.email.message',
                'required'           => true,
                'label'              => 'app.register.form.email.label',
                'attr' => [
                    'autocomplete'   => 'email',
                    'placeholder'    => 'app.text.new.user.register.form.placeholder.email.text',
                ],

                'constraints' => [
                    new NotBlank([
                        'message'    => 'The email field must not be empty.'
                    ]),
                ]
            ])

            ->add('password', RepeatedType::class, [
                'translation_domain'  => 'messages',
                'type'                => PasswordType::class,
                'required'            => true,
                'invalid_message'     => 'The password fields must match.',
                'options' => [
                    'attr' => [
                        'placeholder' => 'app.text.new.user.register.form.placeholder.password.text',
                        'autocomplete'   => 'secret'
                    ]
                ],

                'first_options'  => [
                    'label'           => 'app.register.form.password.label',
                    'label_attr'=> [
                        'placeholder' => 'app.text.new.user.register.form.placeholder.password.text',
                        'class'       => 'pl-input-label-rtl pl-input-label'
                    ],
                    'help'            => 'app.text.new.user.register.form.password.message',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'The password field must not be empty.'
                        ])
                    ]
                ],
                'second_options' => [
                    'label'           => 'app.register.form.repassword.label',
                    'label_attr'=> [
                        'placeholder' => 'app.text.new.user.register.form.placeholder.repassword.text',
                        'class'       => 'pl-input-label-rtl'
                    ],
                    'help'            => 'app.text.new.user.register.form.repassword.message',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'The repeated password field must not be empty.'
                        ])
                    ]
                ]
            ])

            ->add('phone', TelType::class, [
                'translation_domain' => 'messages',
                'help'               => 'app.text.new.user.register.form.phone.message',
                'label'              => 'app.register.form.phone.label',
                'required'           => true,
                'label_attr'=> [
                    'class'          => 'pl-input-label'
                ],
                'attr' => [

                    'placeholder'    => 'app.text.new.user.register.form.placeholder.phone.text',
                ],
                'constraints' => [
                    new NotBlank([
                        'message'    => 'The phone number field must not be empty.'
                    ]),
                ]
            ])

            ->add('termsAccepted', CheckboxType::class, array(
                'translation_domain' => 'messages',
                'label'              => 'app.text.new.user.register.form.terms.message',
                'mapped'             => false,
                'constraints' => [
                    new IsTrue([
                        'message'     => 'You should agree to our terms'
                    ])
                ]
            ))

            ->add('register', SubmitType::class, ['label' => 'app.text.new.user.register.form.button.register.text'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Users::class,
            'translation_domain' => 'messages'
        ]);
    }
}
