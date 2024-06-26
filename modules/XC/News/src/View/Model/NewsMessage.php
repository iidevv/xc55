<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\View\Model;

/**
 * News message view model
 */
class NewsMessage extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'date' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Date',
            self::SCHEMA_LABEL    => 'Date',
            \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => false,
            self::SCHEMA_REQUIRED => false,
        ],
        'name' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'News title',
            self::SCHEMA_REQUIRED => true,
        ],
        'body' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL    => 'Content',
            self::SCHEMA_REQUIRED => true,
            self::SCHEMA_TRUSTED_PERMISSION => true,
        ],
        'brief_description' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Brief description',
            self::SCHEMA_REQUIRED => true,
            \XLite\View\FormField\Input\Text::PARAM_ATTRIBUTES => [
                'maxlength' => '300'
            ]
        ],
        'enabled' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Enabled',
            self::SCHEMA_REQUIRED => false,
        ],
        'cleanURL' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\CleanURL',
            self::SCHEMA_LABEL    => 'Clean URL',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_HELP     => 'Human readable and SEO friendly web address for the page.',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_OBJECT_CLASS_NAME  => 'XC\News\Model\NewsMessage',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_OBJECT_ID_NAME     => 'id',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_ID                 => 'cleanurl',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_EXTENSION          => \XLite\Model\Repo\CleanURL::CLEAN_URL_DEFAULT_EXTENSION,
        ],
        'meta_title' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'News page title',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_COMMENT  => 'Leave blank to use news title as Page Title.',
        ],
        'meta_tags' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Meta keywords',
            self::SCHEMA_REQUIRED => false,
        ],
        'meta_desc_type' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\MetaDescriptionType',
            self::SCHEMA_LABEL    => 'Meta description',
            self::SCHEMA_REQUIRED => false,
        ],
        'meta_desc' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => '',
            self::SCHEMA_REQUIRED => true,
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW =>  [
                    'meta_desc_type' => ['C'],
                ]
            ],
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
     * getFieldBySchema
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        if ($name === 'meta_title') {
            $data[static::SCHEMA_PLACEHOLDER] = static::t('Default');
        }

        if ($name === 'cleanURL') {
            $cleanUrlExt = \XLite\Model\Repo\CleanURL::isNewsUrlHasExt() ? \XLite\Model\Repo\CleanURL::CLEAN_URL_DEFAULT_EXTENSION : '';

            if (
                $this->getModelObject()
                && $this->getModelObject()->getCleanURL()
                && \XLite\Model\Repo\CleanURL::isNewsUrlHasExt()
                && !preg_match('/.html$/', $this->getModelObject()->getCleanURL())
            ) {
                $cleanUrlExt = '';
            }

            $data[\XLite\View\FormField\Input\Text\CleanURL::PARAM_EXTENSION] = $cleanUrlExt;
        }

        return parent::getFieldBySchema($name, $data);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XC\News\Model\NewsMessage
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')->find($this->getModelId())
            : null;

        return $model ?: new \XC\News\Model\NewsMessage();
    }

    /**
     * Validate date field
     *
     * @param \XLite\View\FormField\AFormField $field      Form field object
     * @param array                            $formFields
     *
     * @return array
     */
    protected function validateFormFieldDateValue($field, $formFields)
    {
        $result = [true, null];

        $data = $this->getRequestData();

        if (!empty($data['date'])) {
            $value = (int) \XLite\Core\Converter::parseFromJsFormat($data['date']);

            if (0 >= $value || $value > 2147483647) {
                $result = [
                    false,
                    static::t('[field] year must be between 1970 and 2038', ['field' => static::t('Date')])
                    ];
            }
        }

        return $result;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XC\News\View\Form\Model\NewsMessage';
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
                \XLite\View\Button\AButton::PARAM_STYLE => 'action regular-main-button',
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
            \XLite\Core\TopMessage::addInfo('The news message has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The news message has been added');
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
        if (isset($data['date']) && !is_numeric($data['date'])) {
            $data['date'] = \XLite\Core\Converter::parseFromJsFormat($data['date']);
        }

        parent::setModelProperties($data);
    }

    /**
     * Rollback model if data validation failed
     *
     * @return void
     */
    protected function rollbackModel()
    {
        $urls = $this->getModelObject()->getCleanURLs();
        /** @var \XLite\Model\CleanURL $url */
        foreach ($urls as $url) {
            if (!$url->isPersistent()) {
                \XLite\Core\Database::getEM()->remove($url);
            }
            \XLite\Core\Database::getEM()->detach($url);
        }

        parent::rollbackModel();
    }
}
