<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View\Button;

use Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet;
use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XLite\Core\Database;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Product;
use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeObject;
use XLite\View\Button\Link;

class MagicSetLink extends Link
{
    use MagicImagesTrait;

    public const PARAM_ATTRIBUTE_VALUE      = 'attributeValue';
    public const PARAM_VALUE_ID      = 'valueid';
    public const PARAM_PRODUCT              = 'product';
    public const PARAM_IS_COLORBOX_SWATCHES = 'isColorboxSwatches';

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/button/less/magic_set.less';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ATTRIBUTE_VALUE => new TypeObject(
                'Attribute value',
                null,
                false,
                AttributeValueSelect::class,
            ),

            static::PARAM_PRODUCT => new TypeObject(
                'Product',
                null,
                false,
                Product::class
            ),

            static::PARAM_IS_COLORBOX_SWATCHES => new TypeBool(
                'Is colorbox swatches',
                false,
            ),

            static::PARAM_VALUE_ID => new TypeInt(
                'Value id',
                null,
            ),
        ];
    }

    /**
     * Return button text
     *
     * @return string
     */
    protected function getButtonLabel(): string
    {
        return $this->hasAttributeValue()
        && $this->getMagicSwatchesSet()
            ? $this->getEditMagicImageSetLabel()
            : $this->getAddMagicImageSetLabel();
    }

    /**
     * @return bool
     */
    protected function hasAttributeValue(): bool
    {
        return (bool) $this->getAttributeValue();
    }

    /**
     * @return object|null
     */
    public function getAttributeValue(): ?object
    {
        return $this->getParam(self::PARAM_ATTRIBUTE_VALUE);
    }

    /**
     * @return int|null
     */
    public function getAttributeValueId(): ?int
    {
        return $this->getParam(self::PARAM_VALUE_ID);
    }

    /**
     * @return object|null
     */
    protected function getMagicSwatchesSet(): ?object
    {
        return Database::getRepo(MagicSwatchesSet::class)
            ->findOneBy([
                'product'        => $this->getProduct(),
                'attributeValue' => $this->getAttributeValue(),
            ]);
    }

    /**
     * @return \XLite\Model\Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }

    /**
     * @return string
     */
    protected function getEditMagicImageSetLabel(): string
    {
        return static::t('SkinActMagicImages edit 360 images x', [
            'count' => $this->getMagicSwatchesSet()->getImagesCount(),
        ]);
    }

    /**
     * @return string
     */
    protected function getAddMagicImageSetLabel(): string
    {
        return static::t('SkinActMagicImages add 360 images');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible(): bool
    {
        return parent::isVisible()
            && $this->hasAttributeValue()
            && $this->isColorboxSwatches();
    }

    /**
     * @return bool
     */
    public function isColorboxSwatches(): bool
    {
        return $this->getParam(self::PARAM_IS_COLORBOX_SWATCHES);
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass(): string
    {
        return 'magic-set';
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL(): string
    {
        return $this->getMagicSwatchesSet() ? $this->getEditMagicSetUrl() : $this->getAddMagicSetUrl();
    }

    /**
     * @return string
     */
    protected function getEditMagicSetUrl(): string
    {
        return $this->buildURL($this->getTargetController(), '', [
            'id' => $this->getMagicSwatchesSet()->getId(),
        ]);
    }

    /**
     * @return string
     */
    protected function getAddMagicSetUrl(): string
    {
        return $this->buildURL($this->getTargetController(), '', [
            'product_id' => $this->getProduct()->getProductId(),
            'attribute_id' => $this->getAttributeValueId()
        ]);
    }
}