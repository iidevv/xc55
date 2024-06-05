<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Model;

use XLite\View\FormField\AFormField;

/**
 * Form fields for the Edit Reminder page.
 */
class Reminder extends \XLite\View\Model\AModel
{
    /**
     * Shema default.
     *
     * @var array
     */
    protected $schemaDefault = [
        'name'            => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Reminder name',
            self::SCHEMA_REQUIRED => true,
        ],
        'enabled'         => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Send automatically',
            self::SCHEMA_REQUIRED => false,
        ],
        'cronDelay'       => [
            self::SCHEMA_CLASS                                  => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL                                  => 'Send automatically in (hours)',
            self::SCHEMA_REQUIRED                               => false,
            \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 0,
        ],
        'coupon'          => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\PriceOrPercent',
            self::SCHEMA_LABEL    => 'New coupon amount',
            self::SCHEMA_REQUIRED => false,
        ],
        'couponExpire'    => [
            self::SCHEMA_CLASS                                  => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL                                  => 'Coupon expires in (days)',
            self::SCHEMA_REQUIRED                               => false,
            \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 1,
            self::SCHEMA_DEPENDENCY                             => [
                self::DEPENDENCY_HIDE => [
                    'coupon[price]' => ['', ' ', null, 0, 0.0, '0.', '0', '0.0', '0.00', '00'],
                ],
            ],
        ],
        'couponSingleUse' => [
            self::SCHEMA_CLASS      => \XLite\View\FormField\Input\Checkbox\Simple::class,
            self::SCHEMA_LABEL      => 'Coupon cannot be combined with other coupons',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_HIDE => [
                    'coupon[price]' => ['', ' ', null, 0, 0.0, '0.', '0', '0.0', '0.00', '00'],
                ],
            ],
        ],
        'subject'         => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'E-mail subject (no coupon)',
            self::SCHEMA_REQUIRED => true,
        ],
        'body'            => [
            self::SCHEMA_CLASS     => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL     => 'E-mail body (no coupon)',
            self::SCHEMA_REQUIRED  => true,
            AFormField::PARAM_HELP => 'See our Knowledge Base for more information about customizing reminders.',
        ],
        'couponSubject'   => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'E-mail subject (with coupon)',
            self::SCHEMA_REQUIRED => false,
        ],
        'couponBody'      => [
            self::SCHEMA_CLASS     => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL     => 'E-mail body (with coupon)',
            self::SCHEMA_REQUIRED  => false,
            AFormField::PARAM_HELP => 'See our Knowledge Base for more information about customizing reminders.',
        ],
    ];

    /**
     * Return current model ID from the request.
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->reminder_id;
    }

    /**
     * This object will be used if another one is not passed.
     *
     * @return \QSL\AbandonedCartReminder\Model\Reminder
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('QSL\AbandonedCartReminder\Model\Reminder')->find(
                $this->getModelId()
            )
            : null;

        return $model ?: new \QSL\AbandonedCartReminder\Model\Reminder();
    }

    /**
     * Return name of web form widget class.
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\QSL\AbandonedCartReminder\View\Form\Model\Reminder';
    }

    /**
     * Return list of the "Button" widgets.
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getId() ? 'Update' : 'Create';

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        $result['back_to_list'] = new \XLite\View\Button\SimpleLink(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Email reminders'),
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('cart_reminders'),
            ]
        );

        return $result;
    }

    /**
     * Add top message.
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ($this->currentAction != 'create') {
            \XLite\Core\TopMessage::addInfo('The cart reminder has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The cart reminder has been added');
        }
    }
}
