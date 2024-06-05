<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\Model;

/**
 * Survey view model
 */
class Survey extends \XLite\View\Model\AModel
{
    /**
     * Shema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'id' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Survey id',
            self::SCHEMA_REQUIRED => false,
        ],
        'rating' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Rating',
            self::SCHEMA_REQUIRED => false,
        ],
        'status' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Status',
            self::SCHEMA_REQUIRED => false,
        ],
        'emailDate' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Email date',
            self::SCHEMA_REQUIRED => false,
        ],
        'initDate' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Init date',
            self::SCHEMA_REQUIRED => false,
        ],
        'feedbackDate' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Feedback date',
            self::SCHEMA_REQUIRED => false,
        ],
        'feedbackProcessedDate' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Feedback processed date',
            self::SCHEMA_REQUIRED => false,
        ],
        'tags' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Tags',
            self::SCHEMA_REQUIRED => false,
        ],
        'comments' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Comments',
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
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * This object will be used if another one is not pased
     *
     * @return \QSL\CustomerSatisfaction\Model\Survey
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Survey')->find($this->getModelId())
            : null;

        return $model ?: new \QSL\CustomerSatisfaction\Model\Survey();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\QSL\CustomerSatisfaction\View\Form\Model\Survey';
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
        if ($this->currentAction != 'create') {
            \XLite\Core\TopMessage::addInfo('The survey has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The survey has been added');
        }
    }
}
