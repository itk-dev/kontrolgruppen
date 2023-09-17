<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form\Visitation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
     * VisitationType constructor.
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
        $builder
            ->add('cpr', TextType::class, [
                'label' => 'visitation.form.client_cpr',
                'attr' => [
                    'class' => 'js-input-cpr no-cpr-scanning',
                ],
            ])
            ->add('search', ButtonType::class, [
                'label' => 'visitation.form.search_client_cpr.search',
                'attr' => [
                    'class' => 'btn-primary',
                    // Make a route to the result page and pass the cpr as a parameter. Remember to set the route name in the controller.
                    'data-search-action' => $this->router->generate(
                        'result',
                        [],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'data-search-text' => $this->translator->trans('visitation.form.search_client_cpr.search'),
                    'data-loading-text' => $this->translator->trans('visitation.form.search_client_cpr.loading'),
                ],
            ]);
    }
}
