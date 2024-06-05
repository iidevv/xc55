<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Model;

/**
 * Banner view model
 */
class Banner extends \XLite\View\Model\AModel
{
    /**
     * Shema default
     *
     * @var   array
     */
    protected $schemaDefault = [
        'location'       => [
            self::SCHEMA_CLASS    => 'QSL\Banner\View\FormField\Select\SelectLocation',
            self::SCHEMA_LABEL    => 'Location',
            self::SCHEMA_REQUIRED => false,
        ],
        'position'       => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Position',
            self::SCHEMA_REQUIRED => false,
        ],
        'title'          => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Banner name',
            self::SCHEMA_REQUIRED => true,
        ],
        'categories'     => [
            self::SCHEMA_CLASS              => 'XLite\View\FormField\Select\Categories',
            self::SCHEMA_LABEL              => 'Categories',
            self::SCHEMA_REQUIRED           => false,
        ],
        'products_pages' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Display on products pages',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_HELP     => 'If you enable this option the banner will be shown on all the products\' pages of the selected category/ies. The option is ignored if no category is selected.',
        ],
        'memberships'    => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Memberships',
            self::SCHEMA_LABEL    => 'Memberships',
            self::SCHEMA_HELP     => 'Banners can be limited to customers with these membership levels',
            self::SCHEMA_REQUIRED => false,
        ],
        'home_page'      => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Show on home page',
            self::SCHEMA_REQUIRED => false,
        ],
        'navigation'     => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Show banner\'s pagination',
            self::SCHEMA_REQUIRED => false,
        ],
        'arrows'         => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Show navigation arrows',
            self::SCHEMA_REQUIRED => false,
        ],
        'parallax'       => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Display this banner as parallax block',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_HELP     => 'Please, choose an image for the parallax block in  \'Banner images settings\' section. If you do not set some image for parallax, then the first image from this banner will be used for parallax effect. <br/><br/> HTML-banners will not be displayed in parallax block.',
        ],
        'width'          => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Width, px',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_HELP     => 'Set this parameter to "0" or leave this field empty to let the module calculate banner width automatically',
        ],
        'height'         => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Height, px',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_HELP     => 'Set this parameter to "0" or leave this field empty to let the module calculate banner height automatically',
        ],
        'delay'          => [
            self::SCHEMA_CLASS                                     => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL                                     => 'Animation speed, in seconds',
            self::SCHEMA_REQUIRED                                  => false,
            self::SCHEMA_VALUE                                     => '3',
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_MIN => 1,
        ],
        'timeout'        => [
            self::SCHEMA_CLASS                                     => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL                                     => 'Delay, in seconds',
            self::SCHEMA_REQUIRED                                  => false,
            self::SCHEMA_VALUE                                     => '4',
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_MIN => 0,
        ],
        'effect'         => [
            self::SCHEMA_CLASS    => 'QSL\Banner\View\FormField\Select\SelectEffect',
            self::SCHEMA_LABEL    => 'Rotation effect',
            self::SCHEMA_REQUIRED => false,
        ],
        'enabled'        => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Enabled',
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
     * @return \QSL\Banner\Model\Banner
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('QSL\Banner\Model\Banner')->find($this->getModelId())
            : null;

        return $model ?: new \QSL\Banner\Model\Banner();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\QSL\Banner\View\Form\Model\Banner';
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
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ($this->currentAction != 'add') {
            \XLite\Core\TopMessage::addInfo('The banner has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The banner has been added');
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
        foreach (['enabled', 'home_page', 'navigation', 'parallax'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = !empty($data[$field]) ? 1 : 0;
            }
        }

        $categories  = $data['categories'] ?? [];
        $memberships = $data['memberships'] ?? null;
        unset($data['categories'], $data['memberships'], $data['pages']);

        parent::setModelProperties($data);

        /** @var \QSL\Banner\Model\Banner $entity */
        $model = $this->getModelObject();

        // Remove old links
        foreach ($model->getCategories() as $category) {
            $category->getBanners()->removeElement($model);
        }
        //$model->getCategories()->clear();
        $model->clearCategories();

        if (isset($categories) && $categories) {
            // Add new links
            foreach ($categories as $cid) {
                $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($cid);
                if ($category) {
                    $model->addCategories($category);
                    $category->addBanner($model);
                }
            }
        }

        // Memberships
        foreach ($model->getMemberships() as $m) {
            $m->getBanners()->removeElement($model);
        }
        //$model->getMemberships()->clear();
        $model->clearMemberships();

        if (is_array($memberships)) {
            foreach ($memberships as $id) {
                $m = \XLite\Core\Database::getRepo('XLite\Model\Membership')->find($id);
                if ($m) {
                    $model->addMemberships($m);
                    $m->addBanners($model);
                }
            }
        }
    }
}
