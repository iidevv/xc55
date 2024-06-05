<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class CustomerNote extends \QSL\BackInStock\View\CustomerNote
{
    /**
     * Widget parameter names
     */
    public const PARAM_VARIANT = 'variant';

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $variant = \XLite::getController() instanceof \XLite\Controller\Customer\Product
            ? \XLite::getController()->getProductVariant()
            : null;

        $this->widgetParams += [
            static::PARAM_VARIANT => new \XLite\Model\WidgetParam\TypeObject(
                'Variant',
                $variant,
                false,
                'XC\ProductVariants\Model\ProductVariant'
            ),
        ];
    }

    /**
     * Get product variant
     *
     * @return \XLite\Model\ProductVariant
     */
    protected function getProductVariant()
    {
        return $this->getParam(static::PARAM_VARIANT);
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        $result = parent::getContainerTagAttributes();

        $product = $this->getProduct();
        $variant = $this->getProductVariant();

        if ($variant) {
            $result['data-post-url'] = $this->buildURL(
                'product',
                'add_back2stock_variant_record',
                [
                    'product_id' => $product->getProductId(),
                    'variant_id' => $variant->getVariantId()
                ]
            );
            $result['data-variant-id'] = $variant->getVariantId();
        }

        return $result;
    }

    /**
     * Check - record already created for specified product or not
     *
     * @param bool $force Force check OPTIONAL
     *
     * @return boolean
     */
    protected function isRecordAlreadyCreated($force = false)
    {
        $variant = $this->getProductVariant();

        if (!$variant) {
            return parent::isRecordAlreadyCreated($force);
        }

        if (!isset($this->alreadyCreated) || $force) {
            /** @var \QSL\BackInStock\Model\Record $record */
            $record = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
                ->getRecordByVariantSet(
                    $this->getParam(static::PARAM_PRODUCT),
                    $this->getParam(static::PARAM_VARIANT),
                    \XLite\Core\Auth::getInstance()->getProfile(),
                    \XLite\Core\Request::getInstance()->getBackInStockVariantCookie(
                        $this->getParam(static::PARAM_PRODUCT),
                        $this->getParam(static::PARAM_VARIANT)
                    )
                );
            /** @var \QSL\BackInStock\Model\RecordPrice $recordPrice */
            $recordPrice = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                ->getRecordByVariantSet(
                    $this->getParam(static::PARAM_PRODUCT),
                    $this->getParam(static::PARAM_VARIANT),
                    \XLite\Core\Auth::getInstance()->getProfile(),
                    \XLite\Core\Request::getInstance()->getBackInStockVariantPriceCookie(
                        $this->getParam(static::PARAM_PRODUCT),
                        $this->getParam(static::PARAM_VARIANT)
                    )
                );
            $this->alreadyCreated = ($record && $record->isWaiting()) || ($recordPrice && $recordPrice->isWaiting());
        }

        return $this->alreadyCreated;
    }

    /**
     * Count records
     *
     * @return integer
     */
    protected function countRecords()
    {
        $variant = $this->getProductVariant();

        if (!$variant) {
            return parent::countRecords();
        }

        $count = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
            ->countSumVariantWaiting(
                $this->getParam(static::PARAM_PRODUCT),
                $this->getParam(static::PARAM_VARIANT)
            );

        return $this->isRecordAlreadyCreated()
            ? $count - 1
            : $count;
    }

    /**
     * Check - record already created for specified product or not
     *
     * @param bool $force Force check OPTIONAL
     *
     * @return boolean
     */
    protected function isPriceRecordAlreadyCreated($force = false)
    {
        $variant = $this->getProductVariant();

        if (!$variant) {
            return parent::isPriceRecordAlreadyCreated($force);
        }

        if (!isset($this->alreadyCreated) || $force) {
            /** @var \QSL\BackInStock\Model\RecordPrice $record */
            $record = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                ->getRecordByVariantSet(
                    $this->getParam(static::PARAM_PRODUCT),
                    $this->getParam(static::PARAM_VARIANT),
                    \Xlite\Core\Auth::getInstance()->getProfile(),
                    \XLite\Core\Request::getInstance()->getBackInStockVariantCookie(
                        $this->getParam(static::PARAM_PRODUCT),
                        $this->getParam(static::PARAM_VARIANT)
                    )
                );
            $this->alreadyCreated = $record && $record->isWaiting();
        }

        return $this->alreadyCreated;
    }

    /**
     * Count records
     *
     * @return integer
     */
    protected function countPriceRecords()
    {
        $variant = $this->getProductVariant();

        if (!$variant) {
            return parent::countPriceRecords();
        }

        $count = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
            ->countVariantWaiting(
                $this->getParam(static::PARAM_PRODUCT),
                $this->getParam(static::PARAM_VARIANT)
            );

        return $this->isRecordAlreadyCreated()
            ? $count - 1
            : $count;
    }

    /**
     * Return URL for cancel notification subscription link
     *
     * @return string[]
     */
    protected function getCancelNotificationSubscriptionURL()
    {
        $variant = $this->getProductVariant();

        if (!$variant) {
            return parent::getCancelNotificationSubscriptionURL();
        }

        $product = $this->getProduct();

        return \XLite\Core\Converter::buildURL(
            'product',
            'remove_back2stock_variant_record',
            [
                'product_id' => $product->getProductId(),
                'variant_id' => $variant->getVariantId()
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        $product = $this->getProduct();
        $variant = $this->getProductVariant();

        $result = parent::isVisible();

        if ($variant) {
            $initialParentClass = get_parent_class(parent::class);
            $result = $initialParentClass::isVisible()
                && ($variant = $this->getProductVariant())
                && ($variant->isBackInStockAllowed() || $variant->isPriceDropAllowed());
        }

        if (
            !$variant
            && $product->mustHaveVariants()
        ) {
            // Customer note must be hidden, untill all variant options are selected
            $result = false;
        }

        return $result;
    }
}
