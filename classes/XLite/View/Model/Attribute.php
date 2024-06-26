<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

use XLite\Core\Database;
use XLite\View\FormField\Select\RadioButtonsList\Attribute\CheckboxAddToNew as CheckboxAddToNewRadioList;

/**
 * Attribute view model
 */
class Attribute extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'name'            => [
            self::SCHEMA_CLASS    => \XLite\View\FormField\Input\Text::class,
            self::SCHEMA_LABEL    => 'Attribute',
            self::SCHEMA_REQUIRED => true,
        ],
        'attribute_group' => [
            self::SCHEMA_CLASS    => \XLite\View\FormField\Select\AttributeGroups::class,
            self::SCHEMA_LABEL    => 'Attribute group',
            self::SCHEMA_REQUIRED => false,
        ],
        'type'            => [
            self::SCHEMA_CLASS    => \XLite\View\FormField\Select\AttributeTypes::class,
            self::SCHEMA_LABEL    => 'Type',
            self::SCHEMA_REQUIRED => false,
        ],
        'displayMode'            => [
            self::SCHEMA_CLASS    => \XLite\View\FormField\Select\AttributeDisplayMode::class,
            self::SCHEMA_LABEL    => 'Display as',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'type' => \XLite\Model\Attribute::TYPE_SELECT,
                ],
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
     * Defines the CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'attribute/style.css';

        return $list;
    }

    protected function isGlobalAttribute()
    {
        return !$this->getProductClass();
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $this->preprocessFormFieldsForSectionDefault();

        return $this->getFieldsBySchema($this->schemaDefault);
    }

    /**
     * Preprocess schemaDefault
     *
     * @return void
     */
    protected function preprocessFormFieldsForSectionDefault()
    {
        if ($this->isGlobalAttribute()) {
            $this->schemaDefault['type'][\XLite\View\FormField\Select\AttributeTypes::PARAM_EXCLUDE_HIDDEN] = false;
        }

        if ($this->getModelObject()->getId()) {
            $this->schemaDefault['type'][self::SCHEMA_COMMENT]
                = 'Before editing attributes specific for the chosen type you should save the changes';

            if (
                $this->getModelObject()->getAttributeValuesCount()
                || (
                    $this->getModelObject()->getProductClass()
                    && $this->getModelObject()->getProductClass()->getProductsCount()
                )
            ) {
                $this->schemaDefault['type'][self::SCHEMA_COMMENT] = 'Attribute data will be lost. warning text';
            }

            if ($this->getModelObject()->getType() !== \XLite\Model\Attribute::TYPE_HIDDEN) {
                $this->schemaDefault['display_above'] = [
                    self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\OnOff',
                    self::SCHEMA_LABEL => 'Display option above the price',
                ];
            }

            if (
                in_array(
                    $this->getModelObject()->getType(),
                    [
                    \XLite\Model\Attribute::TYPE_SELECT,
                    \XLite\Model\Attribute::TYPE_HIDDEN,
                    ]
                )
            ) {
                $this->schemaDefault['values_title'] = [
                    self::SCHEMA_CLASS => 'XLite\View\FormField\Separator\Regular',
                    self::SCHEMA_LABEL => 'Attribute values',
                ];
                $this->schemaDefault['values'] = [
                    self::SCHEMA_CLASS                                    => 'XLite\View\FormField\ItemsList',
                    self::SCHEMA_LABEL                                    => false,
                    \XLite\View\FormField\ItemsList::PARAM_LIST_CLASS     => 'XLite\View\ItemsList\Model\AttributeOption',
                    \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS => 'custom-field type-' . $this->getModelObject()->getType(),
                ];

                if (
                    $this->getModelObject()->getAttributeOptions()->count()
                    && $this->getModelObject()->getType() !== \XLite\Model\Attribute::TYPE_HIDDEN
                ) {
                    $this->schemaDefault['applySortingGlobally'] = [
                        self::SCHEMA_CLASS                                  => 'XLite\View\FormField\Input\Checkbox',
                        self::SCHEMA_FIELD_ONLY                             => true,
                        \XLite\View\FormField\Input\Checkbox::PARAM_CAPTION => static::t('Apply sorting globally'),
                    ];
                }
            }

            if (
                $this->getModelObject()->getType() == \XLite\Model\Attribute::TYPE_CHECKBOX
            ) {
                $this->schemaDefault['isSelectable'] = [
                    static::SCHEMA_CLASS                                    => '\XLite\View\FormField\Input\Checkbox\YesNo',
                    static::SCHEMA_LABEL                                    => 'Buyers can select an option',
                    \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS => 'custom-field',
                    \XLite\View\FormField\AFormField::PARAM_HELP            => 'Whether customers should be able to select an option they require when ordering the product, or the information is provided purely as part of product specification',
                ];
                $this->schemaDefault['addToNew'] = [
                    static::SCHEMA_CLASS                                    => '\XLite\View\FormField\Select\RadioButtonsList\Attribute\CheckboxAddToNew',
                    static::SCHEMA_LABEL                                    => 'Default attribute value',
                    static::SCHEMA_DEPENDENCY                               => [
                        static::DEPENDENCY_SHOW => [
                            'isSelectable' => [0],
                        ],
                    ],
                    \XLite\View\FormField\AFormField::PARAM_HELP            => 'This value will be added to new products or class assigns automatically',
                    \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS   => 'custom-field type-' . $this->getModelObject()->getType(),
                ];
            }
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
        $data['attribute_group'] = \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')
            ->find($data['attribute_group']);

        if (!isset($data['addToNew'])) {
            $data['addToNew'] = [];
        } elseif ($this->getModelObject()->getType() == \XLite\Model\Attribute::TYPE_CHECKBOX) {
            if ($data['isSelectable']) {
                $data['addToNew'] = [0,1];
            } elseif ($data['addToNew'] === CheckboxAddToNewRadioList::NO_VALUE_OPTION) {
                $data['addToNew'] = [];
            } else {
                $data['addToNew'] = [$data['addToNew']];
            }
        }

        parent::setModelProperties($data);

        $this->getModelObject()->setProductClass($this->getProductClass());
    }

    /**
     * Change model object value
     *
     * @param string $name Object value name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        if (
            $this->getModelObject()->getType() == \XLite\Model\Attribute::TYPE_CHECKBOX
            && $name == 'isSelectable'
        ) {
            $value = parent::getModelObjectValue('addToNew');

            return $value && count($value) === 2 ? 1 : 0;
        }

        return parent::getModelObjectValue($name);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Attribute
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Model\Attribute();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Model\Attribute';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass()
               . (
               $this->getModelObject()->getId()
                   ? ' edit-attribute-dialog attribute-type-' . $this->getModelObject()->getType()
                   : ''
               );
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return null;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getId() ? 'Save changes' : 'Next wizard';
        $style = 'action ' . ($this->getModelObject()->getId() ? 'save' : 'next');

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => $style,
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
            \XLite\Core\TopMessage::addInfo('The attribute has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('The attribute has been added');
        }
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionUpdate()
    {
        parent::postprocessSuccessActionUpdate();

        if (
            $this->getModelObject()->getType() == \XLite\Model\Attribute::TYPE_SELECT
            && $this->getRequestData('applySortingGlobally')
        ) {
            foreach ($this->getModelObject()->getAttributeOptions() as $option) {
                \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueSelect')
                    ->updatePositionByOption($option);
            }
        }
    }

    /**
     * @return boolean
     */
    public function showErrorsViaTopMessage()
    {
        return false;
    }

    /**
     * @param array  $data    Current section data
     * @param string $section Current section name
     */
    protected function validateFields(array $data, $section)
    {
        parent::validateFields($data, $section);

        $attribute = $this->getModelObject();
        $attributeRepo = Database::getRepo(\XLite\Model\Attribute::class);

        $cnd = new \XLite\Core\CommonCell();
        $cnd->{$attributeRepo::SEARCH_PRODUCT_CLASS} = $attribute->getProductClass();
        $cnd->{$attributeRepo::SEARCH_ATTRIBUTE_GROUP} = $attribute->getAttributeGroup();
        $cnd->{$attributeRepo::SEARCH_NAME} = $attribute->getName();
        $cnd->{$attributeRepo::SEARCH_PRODUCT} = null;

        if ($attribute->getId()) {
            $cnd->{$attributeRepo::SEARCH_EXCLUDING_ID} = $attribute->getId();
        }

        $sameAttributesCount = $attributeRepo->search($cnd, true);

        if ($sameAttributesCount) {
            $this->addErrorMessage(
                'name',
                static::t('This attribute already exists')
            );
        }
    }
}
