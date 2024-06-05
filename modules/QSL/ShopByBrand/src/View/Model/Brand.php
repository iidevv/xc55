<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Model;

/**
 * Brand view model
 */
class Brand extends \XLite\View\Model\AModel
{
    /**
     * Shema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'name'            => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Brand name',
            self::SCHEMA_REQUIRED => true,
        ],
        'image'           => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\FileUploader\Image',
            self::SCHEMA_LABEL    => 'Brand logo',
            self::SCHEMA_REQUIRED => false,
        ],
        'description'     => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL    => 'Brand description',
            self::SCHEMA_REQUIRED => false,
        ],
        'meta_title'      => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Brand page title',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_COMMENT  => 'Leave blank to use brand name as Page Title.',
        ],
        'metaKeywords'    => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Meta keywords',
            self::SCHEMA_REQUIRED => false,
        ],
        'metaDescription' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Meta description',
            self::SCHEMA_REQUIRED => false,
        ],
        'cleanURL'        => [
            self::SCHEMA_CLASS                                                 => 'XLite\View\FormField\Input\Text\CleanURL',
            self::SCHEMA_LABEL                                                 => 'Clean URL',
            self::SCHEMA_REQUIRED                                              => false,
            self::SCHEMA_HELP                                                  => 'Human readable and SEO friendly web address for the page.',
            // Make sure the class name doesn't have a leading hash!
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_OBJECT_CLASS_NAME => 'QSL\ShopByBrand\Model\Brand',
            \XLite\View\FormField\Input\Text\CleanURL::PARAM_OBJECT_ID_NAME    => 'brand_id',
        ],
    ];

    /**
     * Return current model ID
     *
     * @return int
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->brand_id;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/ShopByBrand/brand/edit.css';

        return $list;
    }

    /**
     * This object will be used if another one is not pased
     *
     * @return \QSL\ShopByBrand\Model\Brand
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->find($this->getModelId())
            : null;

        return $model ?: new \QSL\ShopByBrand\Model\Brand();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'QSL\ShopByBrand\View\Form\Model\Brand';
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
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        return $result;
    }

    /**
     * Add top message
     */
    protected function addDataSavedTopMessage()
    {
        if ($this->currentAction != 'create') {
            \XLite\Core\TopMessage::addInfo('The brand has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The brand has been added');
        }
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     */
    protected function setModelProperties(array $data)
    {
        parent::setModelProperties($data);

        if (isset($data['name'])) {
            if ($this->getModelId()) {
                // Update existing attribute option
                $this->getModelObject()->getOption()->map(['name' => $data['name']]);
            } else {
                // Create new attribute option
                $attributeOption = new \XLite\Model\AttributeOption();
                $attributeOption->setAttribute(
                    \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findBrandAttribute()
                );
                $attributeOption->setName($data['name']);
                \XLite\Core\Database::getEM()->persist($attributeOption);
                $this->getModelObject()->setOption($attributeOption);
            }
        }
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $id = $this->getModelObject()->getBrandId();
        if ($id) {
            $this->schemaDefault['image'][\XLite\View\FormField\Image::PARAM_OBJECT_ID] = $id;
            $image                                                                      = $this->getDefaultModelObject()->getImage();
            if ($image) {
                $this->schemaDefault['image'][\XLite\View\FormField\Image::PARAM_FILE_OBJECT_ID] = $image->getId();
            }
        }

        return $this->getFieldsBySchema($this->schemaDefault);
    }
}
