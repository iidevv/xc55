<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Model;

use XC\ThemeTweaker\Model\Template as ModelTemplate;
use XC\ThemeTweaker\View\FormField\Textarea\CodeMirror;

/**
 * Theme tweaker template view model
 */
class Template extends \XLite\View\Model\AModel
{
    protected $savedData;

    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'body' => [
            self::SCHEMA_CLASS          => 'XC\ThemeTweaker\View\FormField\Textarea\CodeMirror',
            self::SCHEMA_LABEL          => 'Template',
            self::SCHEMA_REQUIRED       => false,
            self::SCHEMA_FIELD_ONLY     => true,
            self::SCHEMA_TRUSTED        => true,
            self::SCHEMA_ATTRIBUTES     => [
                'v-pre' => 'v-pre',
            ],
            CodeMirror::PARAM_CODE_MODE => 'twig',
            CodeMirror::PARAM_COLS      => 130,
        ],
    ];

    /**
     * @inheritdoc
     */
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $this->preprocessSchema();
    }

    /**
     * Add placeholder if a template of an email notification body is edited
     */
    protected function preprocessSchema()
    {
        if (
            \XLite\Core\Request::getInstance()->interface === \XLite::MAIL_INTERFACE
            && ($path = \XLite\Core\Request::getInstance()->template)
            && preg_match('/\/body\.twig/', $path)
        ) {
            $this->schemaDefault['body'][self::SCHEMA_PLACEHOLDER] = static::t("There is no special code for this notification");
        }
    }

    /**
     * Prepare request data by form fields (typecasting)
     *
     * @param array $requestData Request data
     *
     * @return array
     */
    protected function prepareRequestDataByFormFields($requestData)
    {
        $requestData = parent::prepareRequestDataByFormFields($requestData);

        $rawData = \XLite\Core\Request::getInstance()->getPostData(false);
        if (isset($rawData['body'])) {
            $requestData['body'] = $rawData['body'];
        }

        return $requestData;
    }

    protected function validateFields(array $data, $section)
    {
        if (isset($data[static::SECTION_PARAM_FIELDS]['body'])) {
            $value = $data[static::SECTION_PARAM_FIELDS]['body']->getValue();
            $error = \XLite::getController()->validateTemplate(
                $value,
                \XLite::getController()->getTemplateLocalPath()
            );

            if ($error) {
                $this->addErrorMessage('body_invalid', $error['message']);
            }
        }

        parent::validateFields($data, $section);
    }

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
     * Retrieve property from the request or from  model object
     *
     * @param string $name Field/property name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($name)
    {
        $request = \XLite\Core\Request::getInstance();

        $value = null;

        switch ($name) {
            case 'template':
                $value = $request->template;
                break;

            case 'body':
                $rawData = $request->getPostData(false);
                $value   = $rawData['body'] ?? null;
                break;
        }

        return $value ?: parent::getDefaultFieldValue($name);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return ModelTemplate
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo(ModelTemplate::class)->find($this->getModelId())
            : null;

        if (!$model && \XLite\Core\Request::getInstance()->template) {
            $localPath = \XLite\Core\Request::getInstance()->template;
            $model     = \XLite\Core\Database::getRepo(ModelTemplate::class)
                ->findOneByTemplate($localPath);
        }

        return $model ?: new ModelTemplate();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XC\ThemeTweaker\View\Form\Model\Template';
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
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Save changes',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        if (!\XLite\Core\Request::getInstance()->template) {
            $result['templates'] = new \XLite\View\Button\SimpleLink(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Back to templates list',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('theme_tweaker_templates'),
                ]
            );
        }

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
            \XLite\Core\TopMessage::addInfo('The template has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The template has been added');
        }
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
        switch ($name) {
            case 'body':
                $value     = '';
                $localPath = '';

                if (\XLite\Core\Request::getInstance()->template) {
                    $localPath = \XLite\Core\Request::getInstance()->template;
                } elseif ($this->getModelObject()->getId()) {
                    if ($this->getModelObject()->getBody()) {
                        return $this->getModelObject()->getBody();
                    }
                    $localPath = parent::getModelObjectValue('template');

                    if (!$this->getModelObject()->getEnabled()) {
                        $localPath .= '.tmp';
                    }
                }

                $layout    = \XLite\Core\Layout::getInstance();
                $interface = \XLite\Core\Request::getInstance()->interface;
                $zone      = \XLite\Core\Request::getInstance()->zone;

                $tweakerPath = $layout->getTweakerPathByLocalPath($localPath, $interface, $zone);
                $tweakerData = \XLite\Core\Layout::getInstance()->getTweakerContent($tweakerPath, $interface, $zone, false);

                if ($tweakerData) {
                    return $tweakerData;
                }

                if ($localPath) {
                    $value = \Includes\Utils\FileManager::read(\LC_DIR_SKINS . $localPath);

                    if (\XLite\Core\Request::getInstance()->interface === \XLite::INTERFACE_MAIL) {
                        $value = $this->postProcessThemeTweakerMailBody($value);
                    }
                }

                break;

            default:
                $value = parent::getModelObjectValue($name);
                break;
        }

        return $value;
    }

    protected function postProcessThemeTweakerMailBody($value)
    {
        if (preg_match('/^[\s\n]*({#[\s\S]*[^#]#})?([\s\S]*?)$/', $value, $m)) {
            return $m[2];
        }

        return $value;
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
        $body = $data['body'];
        unset($data['body']);

        $data['date'] = LC_START_TIME;

        $localPath = '';
        $layout    = \XLite\Core\Layout::getInstance();

        if (\XLite\Core\Request::getInstance()->template) {
            $localPath = \XLite\Core\Request::getInstance()->template;
        } elseif ($this->getModelObject()->getId()) {
            $localPath = $this->getModelObjectValue('template');
        }

        if ($localPath) {
            $interface = \XLite\Core\Request::getInstance()->interface;
            $zone      = \XLite\Core\Request::getInstance()->zone;

            if ($interface === \XLite::INTERFACE_MAIL) {
                $layout->setMailSkin($zone);
            }

            if ($interface) {
                $tweakerPath = $layout->getTweakerPathByLocalPath($localPath, $interface, $zone);
            } else {
                $tweakerPath = $localPath;
            }

            $this->savedData = [
                'template' => $tweakerPath,
                'body'     => $body,
                'original' => false,
            ];

            $data['template'] = $tweakerPath;
            $data['body']     = $body;
        }

        parent::setModelProperties($data);
    }

    /**
     * @return \XLite\Model\AEntity
     */
    protected function prepareObjectForMapping()
    {
        $result = parent::prepareObjectForMapping();

        $model = \XLite\Core\Database::getRepo(ModelTemplate::class)
            ->findOneByTemplate($this->getSavedData('template'));

        if ($model) {
            $model->setEnabled(true);
            $model->setBody($this->getSavedData('body'));
            $this->widgetParams[static::PARAM_MODEL_OBJECT]->setValue($model);

            $result = $model;
        } else {
            $cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();
            $cacheDriver->delete(\XC\ThemeTweaker\Core\Layout::THEME_TWEAKER_TEMPLATES_CACHE_KEY);
        }

        return $result;
    }

    protected function rollbackModel()
    {
        if (isset($this->savedData)) {
            if (!$this->savedData['original']) {
                \Includes\Utils\FileManager::write($this->savedData['path'], $this->savedData['body']);
            } else {
                \Includes\Utils\FileManager::deleteFile($this->savedData['path']);
            }
        }

        parent::rollbackModel();
    }
}
