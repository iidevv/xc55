<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\Model;

use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\View\Button\AButton;
use XLite\View\Button\Submit;
use XLite\View\FormField\Input\Checkbox\Enabled;
use XLite\View\FormField\Input\Text;
use XLite\View\FormField\Input\Text\Integer;
use XLite\View\FormField\Input\Text\Price;
use XLite\View\Model\AModel;

/**
 * Subscription view model
 */
class Subscription extends AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'failedTries'  => [
            self::SCHEMA_CLASS    => Integer::class,
            self::SCHEMA_LABEL    => 'Failed tries',
            self::SCHEMA_REQUIRED => false,
        ],
        'successTries' => [
            self::SCHEMA_CLASS    => Integer::class,
            self::SCHEMA_LABEL    => 'Success tries',
            self::SCHEMA_REQUIRED => false,
        ],
        'startDate'    => [
            self::SCHEMA_CLASS    => Integer::class,
            self::SCHEMA_LABEL    => 'Start date',
            self::SCHEMA_REQUIRED => false,
        ],
        'plannedDate'  => [
            self::SCHEMA_CLASS    => Integer::class,
            self::SCHEMA_LABEL    => 'Planned date',
            self::SCHEMA_REQUIRED => false,
        ],
        'realDate'     => [
            self::SCHEMA_CLASS    => Integer::class,
            self::SCHEMA_LABEL    => 'Real date',
            self::SCHEMA_REQUIRED => false,
        ],
        'status'       => [
            self::SCHEMA_CLASS    => Text::class,
            self::SCHEMA_LABEL    => 'Status',
            self::SCHEMA_REQUIRED => false,
        ],
        'type'         => [
            self::SCHEMA_CLASS    => Text::class,
            self::SCHEMA_LABEL    => 'Type',
            self::SCHEMA_REQUIRED => false,
        ],
        'number'       => [
            self::SCHEMA_CLASS    => Integer::class,
            self::SCHEMA_LABEL    => 'Number',
            self::SCHEMA_REQUIRED => false,
        ],
        'period'       => [
            self::SCHEMA_CLASS    => Text::class,
            self::SCHEMA_LABEL    => 'Period',
            self::SCHEMA_REQUIRED => false,
        ],
        'reverse'      => [
            self::SCHEMA_CLASS    => Enabled::class,
            self::SCHEMA_LABEL    => 'Reverse',
            self::SCHEMA_REQUIRED => false,
        ],
        'periods'      => [
            self::SCHEMA_CLASS    => Integer::class,
            self::SCHEMA_LABEL    => 'Periods',
            self::SCHEMA_REQUIRED => false,
        ],
        'fee'          => [
            self::SCHEMA_CLASS    => Price::class,
            self::SCHEMA_LABEL    => 'Fee',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return Request::getInstance()->id;
    }

    /**
     * This object will be used if another one is not pased
     *
     * @return \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription
     */
    protected function getDefaultModelObject()
    {
        $repo = Database::getRepo(\Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription::class);

        $model = $this->getModelId()
            ? $repo->find($this->getModelId())
            : null;

        return $model ?: new \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return \Qualiteam\SkinActXPaymentsSubscriptions\View\Form\ItemsList\Subscription::class;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getId() ? 'Update' : 'Create';

        $result['submit'] = new Submit(
            [
                AButton::PARAM_LABEL => $label,
                AButton::PARAM_STYLE => 'action',
            ]
        );

        return $result;
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ('create' != $this->currentAction) {
            TopMessage::addInfo('The subscription has been updated');

        } else {
            TopMessage::addInfo('The subscription has been added');
        }
    }
}
