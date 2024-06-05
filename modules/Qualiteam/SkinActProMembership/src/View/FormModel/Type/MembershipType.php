<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActProMembership\View\FormModel\Type;

use Symfony\Component\Form\FormBuilderInterface;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After ("QSL\MembershipProducts")
 */
class MembershipType extends \QSL\MembershipProducts\View\FormModel\Type\MembershipType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('type');

        $builder->add(
            'type',
            'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
            [
                'label' => false,
                'show_label_block' => false,
                'choices' => [
                   // static::t('Unlimited') => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_NONE,
                    static::t('Day') => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_DAY,
                    static::t('Week') => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_WEEK,
                    static::t('Month') => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_MONTH,
                    static::t('Year') => \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_YEAR,
                ],
                'placeholder' => false,
            ]
        );
    }

}