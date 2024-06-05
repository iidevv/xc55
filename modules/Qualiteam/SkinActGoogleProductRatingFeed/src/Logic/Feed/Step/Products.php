<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Step;

use Qualiteam\SkinActGoogleProductRatingFeed\Main;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;
use XLite\Model\Attribute;

/**
 * @Extender\Mixin
 */
class Products extends Reviews
{
    protected function getReviewRecord(): array
    {
        $list = parent::getReviewRecord();

        return Main::sliceArray($list, 'is_spam', $this->getProductsTag());
    }

    protected function getProductsTag(): array
    {
        return [
            'products' => $this->getProductTag(),
        ];
    }

    protected function getProductTag(): array
    {
        return [
            'product' => $this->prepareProductTag(),
        ];
    }

    protected function prepareProductTag(): array
    {
        return array_merge(
            $this->prepareProductIdsTag(),
            $this->prepareProductNameTag(),
            $this->prepareProductUrlTag(),
        );
    }

    protected function prepareProductIdsTag(): array
    {
        return [
            'product_ids' => array_merge(
                $this->prepareGtinsTag(),
                $this->prepareSkusTag(),
            ),
        ];
    }

    protected function prepareGtinsTag(): array
    {
        return [
            'gtins' => $this->getGtinTag(),
        ];
    }

    protected function getGtinTag(): array
    {
        return [
            'gtin' => $this->getMappedField(
                'google_rating_gtin_field',
            ),
        ];
    }

    protected function getMappedField($name)
    {
        return $this->mapProductField(
            \XLite\Core\Config::getInstance()->Qualiteam->SkinActGoogleProductRatingFeed->{$name},
        );
    }

    protected function mapProductField($mapped)
    {
        $value         = '';
        $valuePriority = 4;

        $fields = unserialize($mapped);
        if (is_array($fields)) {
            foreach (unserialize($mapped) as $field) {
                if (preg_match('/^attr:([\d]+)$/', $field, $matches)) {
                    $attribute = \XLite\Core\Database::getRepo(Attribute::class)->find((int) $matches[1]);
                    $priority  = $attribute->getProductClass() ? 1 : 2;
                    $v         = $attribute ? $this->getAttributeValue($attribute) : null;
                } else {
                    $priority = 3;
                    $v        = $this->getFieldValue($field);
                }

                if ($v && ($priority < $valuePriority)) {
                    $value         = $v;
                    $valuePriority = $priority;
                }
            }
        }

        return $value;
    }

    public function getAttributeValue(\XLite\Model\Attribute $attribute)
    {
        $value = $attribute->getAttributeValue($this->getProduct(), true);

        return (is_array($value) && !empty($value)) ? array_pop($value) : null;
    }

    public function getFieldValue($field)
    {
        return call_user_func([$this, 'get' . ucfirst($field)]);
    }

    protected function prepareSkusTag(): array
    {
        return [
            'skus' => $this->prepareSku(),
        ];
    }

    protected function prepareSku(): array
    {
        return [
            'sku' => $this->getSku()
        ];
    }

    protected function getSku(): ?string
    {
        return $this->getProduct()->getSku();
    }

    protected function prepareProductNameTag(): array
    {
        return [
            'product_name' => $this->getProductName(),
        ];
    }

    protected function getProductName(): string
    {
        return $this->getProduct()->getName();
    }

    protected function prepareProductUrlTag(): array
    {
        return [
            'product_url' => $this->getProductUrl(),
        ];
    }

    protected function getProductUrl(): string
    {
        return Main::getShopURL(
            Converter::buildCleanURL(
                'product',
                '',
                ['product_id' => $this->getProduct()->getProductId()],
            )
        );
    }
}