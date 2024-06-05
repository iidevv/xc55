<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

use XLite\View\FormField\Input\PriceOrPercent;

/**
 * Settings dialog model widget
 */
abstract class AShippingSettings extends \XLite\View\Model\Settings
{
    public const FIELD_CARRIER_SERVICE = 'carrierService';

    /**
     * Single service
     *
     * @var \XLite\Model\Shipping\Method
     */
    protected $singleService;

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $this->sections['taxesAndHandling'] = 'Taxes & Handling';
    }

    /**
     * Get schema fields
     *
     * @param string $section
     *
     * @return array
     */
    public function getSchemaFieldsForSection($section)
    {
        if ($section === 'taxesAndHandling') {
            $schemaFields = [
                'handlingFee' => [
                    self::SCHEMA_CLASS => PriceOrPercent::class,
                    self::SCHEMA_LABEL => 'Handling fee',
                ],
                'taxClass'    => [
                    self::SCHEMA_CLASS     => \XLite\View\FormField\Select\TaxClass::class,
                    self::SCHEMA_LABEL     => 'Tax class',
                    self::SCHEMA_LINK_HREF => $this->buildURL('tax_classes'),
                    self::SCHEMA_LINK_TEXT => 'Tax classes',

                ],
            ];
        } else {
            $schemaFields = parent::getSchemaFieldsForSection($section);
        }

        return $schemaFields;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'model/shipping_settings.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'model/shipping_settings.css';

        return $list;
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFields()
    {
        $list = [];

        if ($this->hasSingleService()) {
            $list[static::FIELD_CARRIER_SERVICE] = [
                static::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text',
                static::SCHEMA_LABEL => static::t('Carrier service name'),
            ];
        }

        return $list + parent::getSchemaFields();
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
        return $name === static::FIELD_CARRIER_SERVICE && $this->hasSingleService()
            ? $this->getSingleService()->getName()
            : parent::getModelObjectValue($name);
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
        if ($name === 'handlingFee') {
            $value = $this->getMethod()->getHandlingFee();
        } elseif ($name === 'taxClass') {
            $value = $this->getMethod()->getTaxClass();
        } else {
            $value = parent::getDefaultFieldValue($name);
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
        $isNeededToUpdateMethod = false;

        if (isset($data['handlingFee'])) {
            $this->getMethod()->setHandlingFee($data['handlingFee']);

            $isNeededToUpdateMethod = true;
            unset($data['handlingFee']);
        }

        if (isset($data['taxClass'])) {
            $taxClass = \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->find($data['taxClass']);

            if ($taxClass || ($data['taxClass'] === '0')) {
                $this->getMethod()->setTaxClass($taxClass);
                $isNeededToUpdateMethod = true;
            }

            unset($data['taxClass']);
        }

        if ($isNeededToUpdateMethod) {
            $this->getMethod()->update();
        }

        parent::setModelProperties($data);

        if (isset($data[static::FIELD_CARRIER_SERVICE]) && $this->hasSingleService()) {
            $carrierService = $this->getSingleService();
            $carrierService->setName($data[static::FIELD_CARRIER_SERVICE]);

            $carrierService->update();
        }
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result                     = parent::getFormButtons();
        $result['shipping_methods'] = new \XLite\View\Button\SimpleLink(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to shipping methods'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action shipping-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('shipping_methods'),
            ]
        );

        return $result;
    }

    /**
     * Returns processor id
     *
     * @return string
     */
    protected function getProcessorId()
    {
        return \XLite::getController()->getProcessorId();
    }

    /**
     * Returns shipping method
     *
     * @return null|\XLite\Model\Shipping\Method
     */
    protected function getMethod()
    {
        return \XLite::getController()->getMethod();
    }

    /**
     * Returns single service
     *
     * @return boolean|\XLite\Model\Shipping\Method
     */
    protected function getSingleService()
    {
        if ($this->singleService === null) {
            $this->singleService = false;

            $method          = $this->getMethod();
            $carrierServices = $method->getChildrenMethods();
            if (count($carrierServices) === 1) {
                $this->singleService = $carrierServices[0];
            }
        }

        return $this->singleService;
    }

    /**
     * Check if carrier has single service
     *
     * @return boolean
     */
    protected function hasSingleService()
    {
        return (bool) $this->getSingleService();
    }

    /**
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsible($section)
    {
        return $this->isShowSectionHeader($section);
    }

    /**
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsed($section)
    {
        return $this->isShowSectionHeader($section);
    }
}
