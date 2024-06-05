<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\FormField\Input\Checkbox;

/**
 * On/Off FlipSwitch
 */
class AttributeIsColor extends \XLite\View\FormField\Input\Checkbox\OnOffWithoutOffLabel
{
    public const PARAM_ATTRIBUTE = 'attribute';

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ATTRIBUTE => new \XLite\Model\WidgetParam\TypeObject('Attribute', null, false, 'XLite\Model\Attribute'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'is_color[' . $this->getAttribute()->getId() . ']';
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->getAttribute()->isColorSwatchesAttribute();
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return static::t('Color swatches');
    }

    /**
     * Get attribute
     *
     * @return \XLite\Model\Attribute
     */
    protected function getAttribute()
    {
        return $this->getParam(static::PARAM_ATTRIBUTE);
    }
}
