<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\View\Model;

use XLite\Core\Database;
use XLite\View\FormField\AFormField;
use XLite\View\FormField\Textarea\Advanced;

/**
 * Category Question view model
 */
class CategoryQuestion extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'question' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Question',
            self::SCHEMA_REQUIRED => true,
        ],
        'answer'   => [
            self::SCHEMA_CLASS              => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL              => 'Answer',
            self::SCHEMA_REQUIRED           => true,
            self::SCHEMA_TRUSTED_PERMISSION => true,
            Advanced::PARAM_STYLE           => 'answer-body-content',
        ]
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
     * getFieldBySchema
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        return parent::getFieldBySchema($name, $data);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? Database::getRepo('Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion')->find($this->getModelId())
            : null;

        if (!$model) {
            $model = new \Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion();
            $model->setPosition(Database::getRepo('Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion')->getMaxPosition() + 10);
        }

        return $model;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\Qualiteam\SkinActCategoryDescriptionAndFaq\View\Form\Model\CategoryQuestion';
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        return $this->getFieldsBySchema($this->schemaDefault);
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['save'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Save',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        $result['save_and_close'] = new \XLite\View\Button\Regular(
            [
                \XLite\View\Button\AButton::PARAM_LABEL  => 'Save & Close',
                \XLite\View\Button\AButton::PARAM_STYLE  => 'action',
                \XLite\View\Button\Regular::PARAM_ACTION => 'updateAndClose',
            ]
        );

        return $result;
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
        if (isset($data['enabled'])) {
            $data['enabled'] = !empty($data['enabled']) ? 1 : 0;
        }

        parent::setModelProperties($data);
    }

    /**
     * Save form fields in session
     *
     * @param mixed $data Data to save
     *
     * @return void
     */
    protected function saveFormData($data)
    {
        parent::saveFormData($data);
    }

    /**
     * Rollback model if data validation failed
     *
     * @return void
     */
    protected function rollbackModel()
    {
        parent::rollbackModel();
    }
}
