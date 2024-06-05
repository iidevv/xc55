<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\Product\AttributeValue\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Attribute value (Select)
 * @Extender\Mixin
 */
class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/QSL/ColorSwatches/product/attribute_value/select/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/QSL/ColorSwatches/product/attribute_value/select/controller.js';

        return $list;
    }

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return (
            \XLite\Core\Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER
            && $this->isColorSwatches()
            && $this->getAttrValue()
        )
            ? 'modules/QSL/ColorSwatches/product/attribute_value/select/selectbox.twig'
            : parent::getTemplate();
    }

    /**
     * Check - this attribute is color swatches or not
     *
     * @return boolean
     */
    protected function isColorSwatches()
    {
        return $this->getAttribute()->isColorSwatchesAttribute();
    }

    /**
     * Check - this attribute is color swatches or not
     *
     * @return boolean
     */
    protected function isShowSelector()
    {
        return $this->getAttribute()->isShowSelector($this->getProduct());
    }

    /**
     * @inheritdoc
     */
    protected function getAttrValue()
    {
        $result = parent::getAttrValue();

        if ($this->isColorSwatches()) {
            foreach ($result as $k => $v) {
                if (!$v->detectSwatch()) {
                    unset($result[$k]);
                }
            }
        }

        return $result;
    }

    /**
     * Get swatch box attributes
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Attribute value
     *
     * @return array
     */
    protected function getSwatchAttributes(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $swatch = $value->detectSwatch();

        $attributes = [
            'class'      => ['block-value'],
            'data-color' => $swatch->getColor(),
            'data-image' => $swatch->getImage() ? $swatch->getImage()->getFrontURL() : null,
        ];

        if ($this->isShowSelector()) {
            $attributes = $attributes + [
                    'data-option-id' => $value->getId(),
                ];
        }

        if ($this->isSelectedValue($value)) {
            $attributes['class'][] = 'selected';
        }

        if (!$this->isAttributeValueAvailable($value)) {
            $attributes['class'][] = 'cs-disabled';
        }

        return $attributes;
    }

    /**
     * Is attribute value available for
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Attribute value
     *
     * @return boolean
     */

    protected function isAttributeValueAvailable(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        return (method_exists($value, 'isVariantAvailable') && $value->isVariantAvailable())
            || !method_exists($value, 'isVariantAvailable');
    }

    /**
     * Get swatch link (sub box) attributes
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Attribute value
     *
     * @return array
     */
    protected function getSwatchLinkAttributes(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $swatch     = $value->detectSwatch();
        $attributes = [
            'href'      => 'javascript:void(0);',
            'class'     => [],
            'data-name' => $this->getOptionTitle($value),
        ];

        if ($swatch->getColor()) {
            $attributes['style'] = ' background-color: #' . $swatch->getColor() . ';';
        }

        // Modifiers in popover
        $result = [];
        foreach ($value::getModifiers() as $field => $v) {
            $modifier = $this->getAbsoluteModifierValue($value, $field);
            if ($modifier != 0) {
                $result[] = \XLite\Model\AttributeValue\AttributeValueSelect::formatModifier($modifier, $field);
            }
        }
        if ($result) {
            $attributes['data-toggle']    = 'popover';
            $attributes['data-content']   = implode(', ', $result);
            $attributes['data-placement'] = 'bottom';
            $attributes['data-trigger']   = 'focus';
            $attributes['data-html']      = 'true';
            $attributes['data-trigger']   = 'hover';
        }

        return $attributes;
    }

    /**
     * @inheritdoc
     */
    protected function getModifierTitleForSwatch(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        return $value->detectSwatch()
            ? null
            : parent::getModifierTitle($value);
    }

    /**
     * Get swatch's image
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Value
     *
     * @return \QSL\ColorSwatches\Model\Image\Swatch
     */
    protected function getSwatchImage(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $swatch = $value->detectSwatch();

        return $swatch ? $swatch->getImage() : null;
    }

    /**
     * Get swatch's image URL
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Value
     *
     * @return string
     */
    protected function getSwatchImageURL(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $image = $this->getSwatchImage($value);

        return $image ? $image->getFrontURL() : null;
    }

    /**
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value
     *
     * @return array
     */
    protected function getSwatchInputAttributes(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $result = [
            'class'             => 'form-control',
            'type'              => 'radio',
            'name'              => $this->getName(),
            'value'             => $value->getId(),
            'data-attribute-id' => $this->getAttribute()->getId(),
        ];

        if ($this->isSelectedValue($value)) {
            $result['checked'] = 'checked';
        }

        if ($this->showPlaceholderOption()) {
            $result['required'] = 'required';
        }

        return $result;
    }

    /**
     * Get option attributes
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Value
     *
     * @return array
     */
    protected function getRadioAttributes(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $result = [
            'value'             => $value->getId(),
            'data-attribute-id' => $this->getAttribute()->getId(),
            'name'              => $this->getName(),
            'type'              => 'radio',
        ];

        if ($this->isSelectedValue($value)) {
            $result['checked'] = 'checked';
        }

        foreach ($value::getModifiers() as $field => $options) {
            $modifier = $value->getAbsoluteValue($field);
            if ($modifier !== 0) {
                $result['data-modifier-' . $field] = $modifier;
            }
        }

        return $result;
    }
}
