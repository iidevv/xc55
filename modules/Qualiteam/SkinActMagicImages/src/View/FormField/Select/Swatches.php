<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View\FormField\Select;

use XLite\Model\AttributeValue\AttributeValueSelect;
use Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Product;
use XLite\Model\WidgetParam\TypeObject;
use XLite\View\FormField\Select\Regular;

class Swatches extends Regular
{
    use ExecuteCachedTrait;

    public const PARAM_MAGIC_SET = 'magicSet';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_MAGIC_SET => new TypeObject(
                'Magic set',
                null,
                false,
                MagicSwatchesSet::class
            ),
        ];
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return Request::getInstance()->attribute_id
            ? $this->prepareSingleOption()
            : $this->prepareMultipleOptions();
    }

    protected function prepareSingleOption(): array
    {
        /** @var AttributeValueSelect $attribute */
        $attribute = $this->getPresetAttributeDb();

        $result[$attribute->getId()] = $attribute->getAttributeOption()->getName();

        return $result;
    }

    protected function getPresetAttributeDb(): mixed
    {
        $id = Request::getInstance()->attribute_id;

        return $this->executeCachedRuntime(function () use ($id) {
            return Database::getRepo(AttributeValueSelect::class)
                ->findOneBy(['id' => $id]);
        }, [
            __CLASS__,
            __METHOD__,
            $id,
        ]);
    }

    protected function prepareMultipleOptions(): array
    {
        $set                    = $this->getSet();
        $result[]               = static::t('SkinActMagicImages none');
        $product                = $set ? $set->getProduct() : $this->getProduct();
        $attributeColorSwatches = $product->getColorSwatchAttributes();
        $existColorSwatchesIds  = $this->prepareExistColorSwatchAttributeValueIds($product);

        /** @var \XLite\Model\AttributeValue\AttributeValueSelect $attributeColorSwatch */
        foreach ($attributeColorSwatches as $attributeColorSwatch) {
            if (!in_array($attributeColorSwatch->getId(), $existColorSwatchesIds)) {
                $result[$attributeColorSwatch->getId()] = $attributeColorSwatch->getAttributeOption()->getName();
            }
        }

        return $result;
    }

    /**
     * @return MagicSwatchesSet|null
     */
    protected function getSet(): ?MagicSwatchesSet
    {
        return $this->getParam(static::PARAM_MAGIC_SET);
    }

    /**
     * @return Product|null
     */
    protected function getProduct(): ?Product
    {
        return Database::getRepo(Product::class)
            ->findOneBy([
                'product_id' => Request::getInstance()->product_id,
            ]);
    }

    public function prepareExistColorSwatchAttributeValueIds(Product $product): array
    {
        $result                          = [];
        $existColorSwatchAttributeValues = $this->getExistColorSwatchAttributesValueWithoutCurrentId($product, $this->getViewValue() ?? 0);

        foreach ($existColorSwatchAttributeValues as $a) {
            if ($a->getAttributeValue()) {
                $result[] = $a->getAttributeValue()->getId();
            }
        }

        sort($result);

        return $result;
    }

    protected function getExistColorSwatchAttributesValueWithoutCurrentId(Product $product, int $attributeValueId): ?array
    {
        return Database::getRepo(MagicSwatchesSet::class)
            ->getColorSwatchesAttributeValueWithoutCurrentAttributeValue($product, $attributeValueId);
    }
}