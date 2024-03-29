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

/**
 * Class ClientPersonType.
 */
class ClientPersonType extends AbstractType
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

        $builder
            ->add('cpr', TextType::class, [
                'label' => 'process.form.client_cpr',
                'attr' => [
                    'class' => 'js-input-cpr no-cpr-scanning',
                    'readonly' => null !== $identifier ? true : false,
                ],
                'data' => $identifier,
            ])
            ->add('search', ButtonType::class, [
                'label' => 'process.form.search_client_cpr.search',
                'attr' => [
                    'class' => 'btn-primary',
                    'data-search-action' => $this->router->generate(
                        'process_search_by_cpr',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'data-search-text' => $this->translator->trans('process.form.search_client_cpr.search'),
                    'data-loading-text' => $this->translator->trans('process.form.search_client_cpr.loading'),
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['identifier']);
    }
}
