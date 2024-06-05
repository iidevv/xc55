<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\View\FormField\Input;

use Qualiteam\SkinActColorSwatchesFeature\Main;
use Qualiteam\SkinActColorSwatchesFeature\Traits\ColorSwatchesTrait;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Database;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\WidgetParam\TypeObject;
use XLite\Model\WidgetParam\TypeString;
use XLite\Model\Attribute;

/**
 * Class ship date
 */
class ShipDate extends \XLite\View\FormField\Input\Text
{
    use ColorSwatchesTrait;

    public const PARAM_ATTRIBUTE  = 'attribute';
    public const PARAM_VALUE_ID   = 'valueid';

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/form_field/input/shipdate.less';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ATTRIBUTE => new TypeObject('Attribute', null, false, Attribute::class),
            static::PARAM_VALUE_ID  => new TypeString('Value IDs', null),
        ];
    }

    /**
     * @return string
     */
    protected function getId(): string
    {
        return $this->getAttribute()
            ? $this->getAttribute()->getId()
            : 'NEW_ID';
    }

    /**
     * @return mixed
     */
    protected function getAttribute(): mixed
    {
        return $this->getParam(static::PARAM_ATTRIBUTE);
    }

    /**
     * @return string
     */
    protected function getFieldName(): string
    {
        return $this->getAttribute()
            ? 'attributeValue[' . $this->getId() . '][shipdate][' . ($this->getParam(static::PARAM_VALUE_ID) ?: '0') . ']'
            : 'newValue[' . $this->getId() . '][shipdate][' . ($this->getParam(static::PARAM_VALUE_ID) ?: '0') . ']';
    }

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes(): array
    {
        $list = parent::getCommonAttributes();
        $list['name'] = $this->getFieldName();

        return $list;
    }

    /**
     * @return string
     */
    public function getFieldId(): string
    {
        return $this->getParam(static::PARAM_VALUE_ID)
            ? ('shipdate-' . $this->getParam(static::PARAM_VALUE_ID))
            : 'shipdate-new-id';
    }

    /**
     * @return bool
     */
    protected function isVisible(): bool
    {
        return ExecuteCached::executeCachedRuntime(function () {
            return $this->getAttribute()
                && Database::getRepo('QSL\ColorSwatches\Model\Swatch')->isAvailable();
        }, ['isSwatchVisible', $this->getAttribute()]);
    }

    /**
     * @return bool
     */
    protected function getDefaultParamFieldOnly(): bool
    {
        return true;
    }

    protected function getDefaultPlaceholder()
    {
        return static::t('SkinActColorSwatchesFeature ship date');
    }

    /**
     * @return string
     */
    protected function getValueContainerClass(): string
    {
        return parent::getValueContainerClass() . ' shipdate-field';
    }

    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if ($this->getParam(static::PARAM_VALUE_ID)) {
            $value = Database::getRepo(AttributeValueSelect::class)
                ->find($this->getParam(static::PARAM_VALUE_ID));

            if (
                $value instanceof AttributeValueSelect
                && Main::isModuleColorSwatchesEnabled()
                && $value->getSwatch()
            ) {
                $this->setValue($value->getShipdate());
            }
        }
    }
}
