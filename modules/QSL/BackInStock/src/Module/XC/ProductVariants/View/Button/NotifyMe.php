<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class NotifyMe extends \QSL\BackInStock\View\Button\NotifyMe
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
     * Return URL parameters to use in AJAX popup
     *
     * @return string[]
     */
    protected function prepareURLParams()
    {
        $params = parent::prepareURLParams();

        $variant = $this->getProductVariant();
        if ($variant) {
            $params['variant_id'] = $variant->getVariantId();
        }

        return $params;
    }

    /**
     * Check - record already created for specified product or not
     *
     * @return boolean
     */
    protected function isRecordAlreadyCreated()
    {
        $variant = $this->getProductVariant();

        if (!$variant) {
            return parent::isRecordAlreadyCreated();
        }

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

        return ($record && $record->isWaiting()) || ($recordPrice && $recordPrice->isWaiting());
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        $result = parent::isVisible();

        if ($variant = $this->getProductVariant()) {
            $initialParentClass = get_parent_class(parent::class);
            $result = $initialParentClass::isVisible()
                && ($variant->isBackInStockAllowed() || $variant->isPriceDropAllowed())
                && !$this->getProduct()->getIsAvailableForBackorder();
        }

        if (!$variant && $this->getProduct()->mustHaveVariants()) {
            // Button must be hidden, until all variant options are selected
            $result = false;
        }

        return $result;
    }
}
