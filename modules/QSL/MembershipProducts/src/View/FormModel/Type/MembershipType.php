<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\View\FormModel\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\FormModel\Type\Base\AType;

class MembershipType extends AType
{
    public static function getThemeFile()
    {
        return 'modules/QSL/MembershipProducts/form_model/type/membership_type.twig';
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'compound' => true,
            ]
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'value',
            'XLite\View\FormModel\Type\PatternType',
            [
                'label'             => false,
                'show_label_block'  => false,
                'inputmask_pattern' => [
                    'alias'      => 'integer',
                    'rightAlign' => false,
                    'min'        => 1,
                ],
            ]
        );
        $builder->add(
            'type',
            'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
            [
                'label'            => false,
                'show_label_block' => false,
                'choices'          => [
                    static::t('Unlimited') => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_NONE,
                    static::t('Day')       => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_DAY,
                    static::t('Week')      => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_WEEK,
                    static::t('Month')     => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_MONTH,
                    static::t('Year')      => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_YEAR,
                ],
                'placeholder'      => false,
            ]
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
    }
}
