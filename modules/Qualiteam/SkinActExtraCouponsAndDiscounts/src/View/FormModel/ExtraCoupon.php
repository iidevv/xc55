<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\View\FormModel;

use Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts;
use XLite\Core\Database;
use XLite\Core\Translation;
use XLite\View\Button\AButton;
use XLite\View\Button\Submit;
use XLite\View\FormModel\Type\Select2Type;
use XLite\View\FormModel\Type\TextareaAdvancedType;

class ExtraCoupon extends \XLite\View\FormModel\AFormModel
{
    protected function getTarget()
    {
        return 'extra_coupon';
    }

    protected function getAction()
    {
        return 'update';
    }

    protected function getActionParams()
    {
        $identity = $this->getDataObject()->default->identity;

        return $identity ? ['id' => $identity] : [];
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/form_model/style.less',
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $types = [
            '%' => Translation::lbl('SkinActExtraCouponsAndDiscounts percent')->translate(),
            '$' => Translation::lbl('SkinActExtraCouponsAndDiscounts X off', ['currency' => \XLite::getInstance()->getCurrency()->getCurrencySymbol()])->translate(),
        ];

        $schema = [
            self::SECTION_DEFAULT => [
                'title' => [
                    'label'       => static::t('SkinActExtraCouponsAndDiscounts tab title'),
                    'required'    => false,
                    'position'    => 100,
                ],
                'stamp_text_1' => [
                    'label'       => static::t('SkinActExtraCouponsAndDiscounts stamp text line 1 field'),
                    'required'    => false,
                    'position'    => 200,
                ],
                'stamp_text_2' => [
                    'label'       => static::t('SkinActExtraCouponsAndDiscounts stamp text line 2 field'),
                    'required'    => false,
                    'position'    => 300,
                ],
                'coupon_code' => [
                    'label'       => static::t('SkinActExtraCouponsAndDiscounts orange text line field'),
                    'required'    => true,
                    'position'    => 400,
                    'constraints' => [
                        'XLite\Core\Validator\Constraints\MaxLength'       => [
                            'length'  => 16,
                            'message' =>
                                static::t('SkinActExtraCouponsAndDiscounts X length must be less then Y', [
                                    'field'  => static::t('SkinActExtraCouponsAndDiscounts orange text line field'),
                                    'length' => 16
                                ]),
                        ],
                        'Symfony\Component\Validator\Constraints\NotBlank' => [
                            'message' => static::t('SkinActExtraCouponsAndDiscounts this field is required'),
                        ],
                    ],

                ],
                'type' => [
                    'label'       => static::t('SkinActExtraCouponsAndDiscounts discount type field'),
                    'type'        => Select2Type::class,
                    'choices'     => array_flip($types),
                    'required'    => true,
                    'position'    => 500,
                ],
                'value' => [
                    'label'       => static::t('SkinActExtraCouponsAndDiscounts discount amount field'),
                    'required'    => true,
                    'position'    => 600,
                ],
                'description' => [
                    'label'       => static::t('SkinActExtraCouponsAndDiscounts description field'),
                    'type'        => TextareaAdvancedType::class,
                    'required'    => false,
                    'position'    => 700,
                ],
                'additional_content' => [
                    'label'       => static::t('SkinActExtraCouponsAndDiscounts additional content field'),
                    'type'        => TextareaAdvancedType::class,
                    'required'    => false,
                    'position'    => 800,
                ],
            ],
        ];

        return $schema;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result   = parent::getFormButtons();
        $identity = $this->getDataObject()->default->identity;

        $label = $identity
            ? static::t('SkinActExtraCouponsAndDiscounts update coupon')
            : static::t('SkinActExtraCouponsAndDiscounts create coupon');

        $result['submit'] = new Submit(
            [
                AButton::PARAM_LABEL    => $label,
                AButton::PARAM_BTN_TYPE => 'regular-main-button',
                AButton::PARAM_STYLE    => 'action',
            ]
        );

        return $result;
    }

    protected function getExtraCouponEntity()
    {
        return Database::getRepo(ExtraCouponsAndDiscounts::class)->find($this->getDataObject()->default->identity);
    }
}