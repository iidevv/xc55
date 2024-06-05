<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\Shipping;

/**
 * Offline shipping method view model
 */
class Offline extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'name'         => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Shipping method name',
            self::SCHEMA_REQUIRED => true,
        ],
        'deliveryTime' => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL => 'Delivery time',
            self::SCHEMA_HELP  => 'deliveryTime.help',
        ],
        'handlingFee'  => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\PriceOrPercent',
            self::SCHEMA_LABEL => 'Handling fee',
        ],
        'taxClass'     => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Select\TaxClass',
            self::SCHEMA_LABEL => 'Tax class',
        ],
        'tableType'    => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Select\ShippingTableType',
            self::SCHEMA_LABEL => 'Table based on',
            self::SCHEMA_HELP  => 'tableType.help',
        ],
        'shippingZone' => [
            self::SCHEMA_CLASS     => 'XLite\View\FormField\Select\ShippingZone',
            self::SCHEMA_LABEL     => 'Address zone',
            self::SCHEMA_LINK_TEXT => 'Manage zones',
        ],
    ];

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->methodId;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($this->getModelId())
            : null;

        $model = $model ?: new \XLite\Model\Shipping\Method();
        if (!$model->getAdded()) {
            $model->setEnabled(true);
        };
        $model->setAdded(true);
        $model->setProcessor('offline');

        return $model;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Shipping\Offline';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->isPersistent() ? 'Update' : 'Create';

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
     * Perform some operations when creating fields list by schema
     *
     * @param string $name Node name
     * @param array  $data Field description
     *
     * @return array
     */
    protected function getFieldSchemaArgs($name, array $data)
    {
        $data = parent::getFieldSchemaArgs($name, $data);

        if ($name === 'shippingZone') {
            $data[\XLite\View\FormField\Select\ShippingZone::PARAM_METHOD] = $this->getModelObject();
        }

        if ($name === 'taxClass') {
            $data[self::SCHEMA_LINK_HREF] = $this->buildURL('tax_classes');
            $data[self::SCHEMA_LINK_TEXT] = 'Tax classes';
        }

        return $data;
    }

    /**
     * Returns used zones
     * @return array
     * @todo: add runtime cache
     *
     */
    protected function getUsedZones()
    {
        $list = [];

        $shippingMethod = $this->getModelObject();
        if ($shippingMethod && $shippingMethod->getShippingMarkups()) {
            foreach ($shippingMethod->getShippingMarkups() as $markup) {
                if ($markup->getZone()) {
                    if (!isset($list[$markup->getZone()->getZoneId()])) {
                        $list[$markup->getZone()->getZoneId()] = 1;
                    } else {
                        $list[$markup->getZone()->getZoneId()]++;
                    }
                }
            }
        }

        return $list;
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
        $taxClass = \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->find($data['taxClass']);

        if (!$taxClass && $data['taxClass'] !== '0') {
            unset($data['taxClass']);
        } else {
            $data['taxClass'] = $taxClass;
        }

        unset($data['shippingZone']);

        parent::setModelProperties($data);
    }

    /**
     * Preparing data for shippingZone param
     *
     * @param array $data Field description
     *
     * @return array
     */
    protected function prepareFieldParamsShippingZone($data)
    {
        $data[static::SCHEMA_LINK_HREF] = $this->buildURL('zones');

        return $data;
    }
}
