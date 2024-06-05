<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Model;

/**
 * Record view model
 */
class Record extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'date' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Date',
            self::SCHEMA_REQUIRED => false,
        ],
        'state' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'State',
            self::SCHEMA_REQUIRED => false,
        ],
        'sentDate' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Sent date',
            self::SCHEMA_REQUIRED => false,
        ],
        'email' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Email',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * @inheritdoc
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')->find($this->getModelId())
            : null;

        return $model ?: new \QSL\BackInStock\Model\Record();
    }

    /**
     * @inheritdoc
     */
    protected function getFormClass()
    {
        return '\QSL\BackInStock\View\Form\Model\Record';
    }

    /**
     * @inheritdoc
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getId() ? 'Update' : 'Create';

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => $label,
                \XLite\View\Button\AButton::PARAM_STYLE => 'action',
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
        if ($this->currentAction !== 'create') {
            \XLite\Core\TopMessage::addInfo('The record has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The record has been added');
        }
    }
}
