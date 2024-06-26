<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Invoice item attribute values
 *
 * @ListChild (list="invoice.item.name", weight="50")
 * @ListChild (list="invoice.item.name", weight="20", zone="admin")
 * @ListChild (list="invoice.item.name", weight="50", interface="mail", zone="common")
 * @ListChild (list="invoice.item.name", weight="20", interface="pdf", zone="common")
 * @ListChild (list="order.items.item.name", weight="20", zone="admin")
 */
class InvoiceAttributeValues extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_ITEM = 'item';
    public const PARAM_DISPLAY_VARIATIVE = 'displayVariative';

    /**
     * Returns displayVariative parameter value
     *
     * @return string
     */
    public function getDisplayVariative()
    {
        return $this->getParam(self::PARAM_DISPLAY_VARIATIVE);
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'invoice_attribute_values/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'invoice_attribute_values/body.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Order item', null, false, '\\XLite\\\Model\\OrderItem'),
            self::PARAM_DISPLAY_VARIATIVE => new \XLite\Model\WidgetParam\TypeString('Display variative', ''),
        ];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getParam(self::PARAM_ITEM)->hasAttributeValues();
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\OrderItem\AttributeValue
     *
     * @return string|integer
     */
    protected function getAttributeId(\XLite\Model\OrderItem\AttributeValue $attrValue)
    {
        $attribute = $attrValue->getAttributeValue() ? $attrValue->getAttributeValue()->getAttribute() : null;

        if (!$attribute && $attrValue->getAttributeId()) {
            $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($attrValue->getAttributeId());
        }

        return $attribute ? $attribute->getId() : $attrValue->getAttributeValueId();
    }

    /**
     * Get value container attributes
     *
     * @param \XLite\Model\OrderItem\AttributeValue $attrValue Attribute value
     *
     * @return array
     */
    protected function getValueContainerAttributes(\XLite\Model\OrderItem\AttributeValue $attrValue)
    {
        $attributes = [
            'class' => ['av-' . $this->getAttributeId($attrValue)],
        ];

        if ($this->needFade($attrValue)) {
            $attributes['class'][] = 'text';
            $attributes['title'] = $attrValue->getActualValue();
            $attributes['data-toggle'] = 'tooltip';
            $attributes['data-placement'] = 'bottom';
        } elseif ($this->isYesNo($attrValue)) {
            $attributes['class'][] = 'yes-no';
        }

        return $attributes;
    }

    /**
     * Check - need right fade or not
     *
     * @param \XLite\Model\OrderItem\AttributeValue $attrValue Attribute value
     *
     * @return boolean
     */
    protected function needFade(\XLite\Model\OrderItem\AttributeValue $attrValue)
    {
        return $attrValue->getAttributeValue() instanceof \XLite\Model\AttributeValue\AttributeValueText;
    }

    /**
     * Check - attribute is YanNo type or not
     *
     * @param \XLite\Model\OrderItem\AttributeValue $attrValue Attribute value
     *
     * @return boolean
     */
    protected function isYesNo(\XLite\Model\OrderItem\AttributeValue $attrValue)
    {
        return $attrValue->getAttributeValue() instanceof \XLite\Model\AttributeValue\AttributeValueCheckbox;
    }

    /**
     * Get plain values
     *
     * @return array
     */
    protected function getPlainValues()
    {
        $list = [];

        foreach ($this->getParam(self::PARAM_ITEM)->getAttributeValues() as $av) {
            if (!$this->needFade($av)) {
                $list[] = $av;
            }
        }

        return $list;
    }

    /**
     * Get text values
     *
     * @return array
     */
    protected function getTextValues()
    {
        $list = [];

        foreach ($this->getParam(self::PARAM_ITEM)->getAttributeValues() as $av) {
            if ($this->needFade($av)) {
                $list[] = $av;
            }
        }

        return $list;
    }

    /**
     * Get Yes/No values
     *
     * @return array
     */
    protected function getYesNoValues()
    {
        $list = [];

        foreach ($this->getParam(self::PARAM_ITEM)->getAttributeValues() as $av) {
            if ($this->isYesNo($av)) {
                $list[] = $av;
            }
        }

        return $list;
    }

    /**
     * Return true if current page is in invoice mode
     *
     * @return boolean
     */
    protected function isInvoiceMode()
    {
        return \XLite\Core\Request::getInstance()->mode == 'invoice';
    }

    /**
     * Get attribute value to display with order item
     *
     * @return string
     */
    protected function getDisplayOptionValues()
    {
        $result = [];

        foreach ($this->getParam(self::PARAM_ITEM)->getAttributeValues() as $av) {
            $result[] = $av->getValue();
        }

        return implode(' / ', $result);
    }
}
