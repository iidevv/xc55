<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Model;

/**
 * Question view model
 */
class Question extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'private' => [
            self::SCHEMA_CLASS    => 'QSL\ProductQuestions\View\FormField\Select\QuestionType',
            self::SCHEMA_LABEL    => 'Question type',
            self::SCHEMA_REQUIRED => false,
        ],
        'name' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Name',
            self::SCHEMA_REQUIRED => true,
        ],
        'email' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Email',
            self::SCHEMA_REQUIRED => false,
        ],
        'product' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Model\ProductSelector',
            self::SCHEMA_LABEL    => 'Product',
            self::SCHEMA_REQUIRED => true,
        ],
        'question' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Question',
            self::SCHEMA_REQUIRED => true,
        ],
        'answer' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Answer',
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
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ProductQuestions/question/style.css';

        return $list;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \QSL\ProductQuestions\Model\Question
     */
    protected function getDefaultModelObject()
    {
        /** @var \QSL\ProductQuestions\Model\Question $model */
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('QSL\ProductQuestions\Model\Question')->find(
                $this->getModelId()
            )
            : null;

        return $model ?: new \QSL\ProductQuestions\Model\Question();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\QSL\ProductQuestions\View\Form\Model\Question';
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

        $result['questions-list'] = $this->getWidget(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to product questions'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action questions-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('product_questions'),
            ],
            \XLite\View\Button\SimpleLink::class
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
            \XLite\Core\TopMessage::addInfo('The question has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The question has been added');
        }
    }


    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        if (isset($data['product']) && !is_object($data['product'])) {
            $data['product'] = \XLite\Core\Database::getRepo('XLite\Model\Product')->find((int) $data['product']);
        }

        parent::setModelProperties($data);
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        $value = parent::getModelObjectValue($name);
        if ($name === 'product') {
            $value = is_object($value) ? $value->getProductId() : 0;
        }

        return $value;
    }
}
