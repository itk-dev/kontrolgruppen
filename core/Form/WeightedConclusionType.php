<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Kontrolgruppen\CoreBundle\Entity\WeightedConclusion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WeightedConclusionType.
 */
class WeightedConclusionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('argumentsFor', CKEditorType::class, [
                'config' => ['toolbar' => 'editor'],
            ])
            ->add('argumentsAgainst', CKEditorType::class, [
                'config' => ['toolbar' => 'editor'],
            ])
            ->add('conclusion', CKEditorType::class, [
                'config' => ['toolbar' => 'editor'],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WeightedConclusion::class,
        ]);
    }
}
