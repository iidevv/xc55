<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Model;

/**
 * Provider view model
 */
class Provider extends \XLite\View\Model\Base\Simple
{
    public const SECTION_SETTINGS = 'settings';

    protected $updateMessage = 'The entity has been updated';

    protected $createMessage = 'The entity has been added';

    protected $entityClass = 'QSL\OAuth2Client\Model\Provider';

    protected $schemaDefault = [
        'linkName'            => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL => 'Link name',
        ],
        'tooltip'             => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL => 'Tooltip',
        ],
        'enabled'             => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\OnOffWithoutOffLabel',
            self::SCHEMA_LABEL => 'Enabled',
        ],
        'display_in_header'   => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\OnOffWithoutOffLabel',
            self::SCHEMA_LABEL => 'Display in site header',
        ],
        'display_in_checkout' => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\OnOffWithoutOffLabel',
            self::SCHEMA_LABEL => 'Display on the checkout page',
        ],
    ];

    /**
     * Settings schema
     *
     * @var array
     */
    protected $schemaSettings = [];

    /**
     * @inheritdoc
     */
    public function __construct(array $params = [], array $sections = [])
    {
        $this->schemaSettings = $this->getModelObject()->getWrapper()->getFormFields();
        $this->sections += [static::SECTION_SETTINGS => static::t('Settings')];

        parent::__construct($params);
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/OAuth2Client/provider/controller.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getFormClass()
    {
        return '\QSL\OAuth2Client\View\Form\Model\Provider';
    }

    /**
     * @inheritdoc
     */
    protected function validateFields(array $data, $section)
    {
        parent::validateFields($data, $section);

        if ($section == static::SECTION_SETTINGS && empty($this->errorMessages)) {
            $this->validateSettings($data);
        }
    }

    /**
     * Validate settings
     *
     * @param array $data Section data
     */
    protected function validateSettings(array $data)
    {
        $fields = $data[static::SECTION_PARAM_FIELDS];

        $errors = $this->getModelObject()->getWrapper()->validate($fields);
        foreach ($errors as $error) {
            $this->addErrorMessage(
                $error['name'],
                $error['message'],
                $data
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function setModelProperties(array $data)
    {
        /** @var \QSL\OAuth2Client\Core\Wrapper\AWrapper $wrapper */ #nolint
        $wrapper = $this->getModelObject()->getWrapper();

        foreach ($data as $name => $value) {
            if ($wrapper->isSetting($name)) {
                $this->getModelObject()->setSetting($name, $value);
                unset($data[$name]);
            } elseif ($wrapper->isCustomProperty($name)) {
                $wrapper->setCustomProperty($name, $value);
                unset($data[$name]);
            }
        }

        parent::setModelProperties($data);
    }

    /**
     * @inheritdoc
     */
    protected function getModelObjectValue($name)
    {
        /** @var \QSL\OAuth2Client\Core\Wrapper\AWrapper $wrapper */ #nolint
        $wrapper = $this->getModelObject()->getWrapper();

        if ($wrapper->isSetting($name)) {
            $result = $this->getModelObject()->getSetting($name);
        } elseif ($wrapper->isCustomProperty($name)) {
            $result = $wrapper->getCustomProperty($name);
        } else {
            $result = parent::getModelObjectValue($name);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getButtonPanelClass()
    {
        return '\QSL\OAuth2Client\View\StickyPanel\Model\Provider';
    }

    /**
     * @inheritdoc
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['back'] = new \XLite\View\Button\SimpleLink(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => 'Back to list',
                \XLite\View\Button\Link::PARAM_LOCATION => static::buildURL('oauth2_client_providers'),
            ]
        );

        return $result;
    }
}
