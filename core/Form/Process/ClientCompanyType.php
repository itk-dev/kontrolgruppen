<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form\Process;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ClientCompanyType.
 */
class ClientCompanyType extends AbstractType
{
    protected $router;
    protected $translator;

    /**
     * ProcessType constructor.
     *
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $identifier = $options['identifier'] ?? null; // Get the identifier
        $pNumbers = $options['pNumbers'] ?? [];

        $builder
            ->add('cvr', TextType::class, [
                'label' => 'process.form.client_cvr',
                'attr' => [
                    'class' => 'js-input-cvr no-cvr-scanning',
                    'readonly' => null !== $identifier ? true : false,
                ],
                'data' => $identifier,
            ])
            ->add('search', ButtonType::class, [
                'label' => 'process.form.search_client_cvr.search',
                'attr' => [
                    'class' => 'btn-primary',
                    'data-search-action' => $this->router->generate(
                        'process_search_by_cvr',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'data-search-text' => $this->translator->trans('process.form.search_client_cvr.search'),
                    'data-loading-text' => $this->translator->trans('process.form.search_client_cvr.loading'),
                ],
            ])
            ->add('pNumber', ChoiceType::class, [
                'label' => 'process.form.p_number',
                'choices' => array_combine($pNumbers, $pNumbers),
                'placeholder' => 'process.form.p_number_placeholder',
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setDefined(['identifier']);
        $resolver->setDefined(['identifier','pNumbers']);
    }
}
