<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\FormField\Select;

class Swatch extends \XLite\View\FormField\Select\ASelect
{
    public const PARAM_ATTRIBUTE  = 'attribute';
    public const PARAM_VALUE_ID   = 'valueid';

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ColorSwatches/form_field/swatch.css';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/ColorSwatches/form_field/swatch.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ATTRIBUTE => new \XLite\Model\WidgetParam\TypeObject('Attribute', null, false, 'XLite\Model\Attribute'),
            static::PARAM_VALUE_ID  => new \XLite\Model\WidgetParam\TypeString('Value ID', null),
        ];
    }

    /**
     * @inheritdoc
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if ($this->getParam(static::PARAM_VALUE_ID)) {
            $value = \XLite\Core\Database::getRepo('\XLite\Model\AttributeValue\AttributeValueSelect')
                ->find($this->getParam(static::PARAM_VALUE_ID));
            if ($value && $value->getSwatch()) {
                $this->setValue($value->getSwatch()->getId());
            }
        }
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [0 => ''];
        foreach ($this->getSwatches() as $swatch) {
            $list[$swatch->getId()] = $swatch->getName();
        }

        return $list;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();
        if ($this->getValue() && isset($list[0])) {
            unset($list[0]);
        }

        return $list;
    }

    /**
     * @return \QSL\ColorSwatches\Model\Swatch[]
     */
    protected function getSwatches()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(static function () {
            return \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')
                ->findAllActive();
        }, ['all_active_swatches']);
    }

    /**
     * @return string
     */
    protected function getId()
    {
        return $this->getAttribute()
            ? $this->getAttribute()->getId()
            : 'NEW_ID';
    }

    /**
     * @return mixed
     */
    protected function getAttribute()
    {
        return $this->getParam(static::PARAM_ATTRIBUTE);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return parent::getName()
            ? parent::getName()
            : $this->getFieldName();
    }

    /**
     * @return string
     */
    protected function getFieldName()
    {
        return $this->getAttribute()
            ? 'attributeValue[' . $this->getId() . '][swatch][' . ($this->getParam(static::PARAM_VALUE_ID) ?: '0') . ']'
            : 'newValue[' . $this->getId() . '][swatch][' . ($this->getParam(static::PARAM_VALUE_ID) ?: '0') . ']';
    }

    /**
     * @return string
     */
    public function getFieldId()
    {
        return $this->getParam(static::PARAM_VALUE_ID)
            ? ('swatch-' . $this->getParam(static::PARAM_VALUE_ID))
            : 'swatch-new-id';
    }

    /**
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Swatch');
    }

    /**
     * @return string
     */
    protected function getDefaultValue()
    {
        return \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')
            ->getDefaultSwatchId();
    }

    /**
     * @return bool
     */
    protected function getDefaultParamFieldOnly()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(function () {
            return ($this->getAttribute() && $this->getAttribute()->getProduct()
                    || $this->getAttribute() === null)
                && \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')->isAvailable();
        }, ['isSwatchVisible', $this->getAttribute()]);
    }

    /**
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' swatch-selector';
    }

    /**
     * @param mixed $value
     * @param mixed $text
     *
     * @return array
     */
    protected function getOptionAttributes($value, $text)
    {
        $attributes = parent::getOptionAttributes($value, $text);
        foreach ($this->getSwatches() as $swatch) {
            if ($swatch->getId() == $value) {
                $attributes['data-color'] = $swatch->getColor();
                $attributes['data-image'] = $swatch->getImage()
                    ? $swatch->getImage()->getFrontURL()
                    : null;
                break;
            }
        }

        return $attributes;
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/QSL/ColorSwatches/form_field/swatch.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }
}
