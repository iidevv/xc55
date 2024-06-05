<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Front page view model
 */
class FrontPage extends \XLite\View\Model\Category
{
    /**
     * We add 'Root category listings format' widget into the default section
     *
     * @param array $params
     * @param array $sections
     */
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $schema = [];

        foreach ($this->schemaDefault as $name => $value) {
            $schema[$name] = $value;

            if ($name === 'description') {
                $schema['root_category_look'] = [
                    self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\RootCategoriesLook',
                    self::SCHEMA_LABEL    => 'Root category listings format',
                    self::SCHEMA_REQUIRED => false,
                ];
            }
        }

        if (\XLite::getController()->getTarget() === 'seo_homepage_settings') {
            $schema['meta_desc'][self::SCHEMA_DEPENDENCY] = [];
            $schema['name'][self::SCHEMA_LABEL]           = 'Title';
            $schema['meta_tags'][self::SCHEMA_LABEL]      = 'Keywords';

            $schema['section_description'] = [
                self::SCHEMA_CLASS => 'XLite\View\FormField\SeoSettings\AboutSection',
            ];

            $schema['search_preview'] = [
                self::SCHEMA_CLASS                                 => 'XLite\View\FormField\Label',
                self::SCHEMA_LABEL                                 => 'Search preview',
                self::SCHEMA_REQUIRED                              => false,
                \XLite\View\FormField\Label\ALabel::PARAM_UNESCAPE => true,
            ];
        }
        $schema['name'][self::SCHEMA_HELP]           = 'Page title displayed in search engines.';
        $schema['meta_desc_type'][self::SCHEMA_HELP] = 'Description shown to users on your homepage and displayed in search engines.';
        $schema['meta_tags'][self::SCHEMA_HELP]      = 'Keywords or phrases relevant to your page, which may increase your rankings based on relevance.';

        $this->schemaDefault = $schema;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        if (\XLite::getController()->getTarget() === 'seo_homepage_settings') {
            $list[] = 'front_page/seo_homepage_settings.js';
        }

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        if (\XLite::getController()->getTarget() === 'seo_homepage_settings') {
            $list[] = 'front_page/seo_homepage_settings.less';
        }

        return $list;
    }

    /**
     * Get default value for the field
     *
     * @param string $fieldName Field service name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($fieldName)
    {
        $value = parent::getDefaultFieldValue($fieldName);

        if (
            $fieldName === 'root_category_look'
            && !$value
        ) {
            $value = \XLite\Core\Config::getInstance()->General->subcategories_look;
        }

        if ($fieldName === 'section_description') {
            $value = static::t('Updating your homepage title, meta description, and keywords with store-relevant words');
        } elseif ($fieldName === 'search_preview') {
            $shop_url         = \XLite::getInstance()->getShopURL();
            $company_name     = "<a class='company-name' href='{$shop_url}'>" . $this->getDefaultModelObject()->getName() . '</a>';
            $company_descr    = "<span class='company-descr'>" . $this->getDefaultModelObject()->getMetaDesc() . '</span>';
            $company_keywords = "<span class='company-keywords'>" . $this->getDefaultModelObject()->getMetaTags() . '</span>';
            $value            = "<span class='company-url'>{$shop_url}</span><br />{$company_name}<br />{$company_descr}<br />{$company_keywords}";
        }

        return $value;
    }

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();
    }

    protected function getHeaderText()
    {
        if (\XLite::getController()->getTarget() === 'seo_homepage_settings') {
            return static::t('Homepage Title, Meta Description and Keywords');
        } else {
            return parent::getHeaderText();
        }
    }

    /**
     * Return list of form fields objects by schema
     *
     * @param array $schema Field descriptions
     *
     * @return array
     */
    protected function getFieldsBySchema(array $schema)
    {
        $ordered_fields = $this->getIncludedFields();
        if (\XLite::getController()->getTarget() === 'seo_homepage_settings') {
            $allowed_fields     = [
                'name',
                'meta_desc_type',
                'meta_desc',
                'useCustomOG',
                'ogMeta',
                'meta_tags',
            ];
            $new_ordered_fields = ['section_description'];
            foreach ($allowed_fields as $name) {
                if (in_array($name, $ordered_fields)) {
                    $new_ordered_fields[] = $name;
                }
            }
            $new_ordered_fields[] = 'search_preview';
            $ordered_fields       = $new_ordered_fields;
        }

        $newSchema = [];
        foreach ($ordered_fields as $name) {
            if (!empty($schema[$name])) {
                $newSchema[$name] = $schema[$name];
            }
        }

        return parent::getFieldsBySchema($newSchema);
    }

    /**
     * Get included fields
     *
     * @return array
     */
    protected function getIncludedFields()
    {
        return [
            'show_title',
            'name',
            'description',
            'root_category_look',
            'meta_title',
            'meta_tags',
            'meta_desc_type',
            'meta_desc',
        ];
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Category
     */
    protected function getDefaultModelObject()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Model\FrontPage';
    }

    /**
     * Add top message
     */
    protected function addDataSavedTopMessage()
    {
        \XLite\Core\TopMessage::addInfo('The front page has been updated');
    }
}
