<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient;
use Kontrolgruppen\CoreBundle\Entity\Visitation;
use Kontrolgruppen\CoreBundle\Form\Visitation\ClientCompanyType;
use Kontrolgruppen\CoreBundle\Form\Visitation\ClientPersonType;
use Kontrolgruppen\CoreBundle\Repository\ChannelRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Kontrolgruppen\CoreBundle\Repository\ReasonRepository;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Symfony\Component\Form\AbstractType;
// import textclass
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class VisitationType.
 */
class VisitationType extends AbstractType
{
    protected $processTypeRepository;
    protected $reasonRepository;
    protected $serviceRepository;
    protected $channelRepository;
    protected $router;
    protected $translator;

    /**
     * VisitationType constructor.
     *
     * @param ProcessTypeRepository $processTypeRepository
     * @param ReasonRepository      $reasonRepository
     * @param ServiceRepository     $serviceRepository
     * @param ChannelRepository     $channelRepository
     * @param RouterInterface       $router
     * @param TranslatorInterface   $translator
     */
    public function __construct(ProcessTypeRepository $processTypeRepository, ReasonRepository $reasonRepository, ServiceRepository $serviceRepository, ChannelRepository $channelRepository, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->processTypeRepository = $processTypeRepository;
        $this->reasonRepository = $reasonRepository;
        $this->serviceRepository = $serviceRepository;
        $this->channelRepository = $channelRepository;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Visitation $visitation */
        $visitation = $builder->getData();
        // Add client controls on new processes.

        $client = $visitation->getVisitationClient();
        switch ($client->getType()) {
            case AbstractProcessClient::COMPANY:
                $builder
                    ->add('company', ClientCompanyType::class, [
                        'label' => false,
                        'mapped' => false,
                    ]);
                break;

            case AbstractProcessClient::PERSON:
                $builder
                    ->add('person', ClientPersonType::class, [
                        'label' => false,
                        'mapped' => false,
                    ]);
                break;
        }

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Visitation::class,
        ]);
    }
}
