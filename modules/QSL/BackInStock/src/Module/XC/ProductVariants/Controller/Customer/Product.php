<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class Product extends \XLite\Controller\Customer\Product
{
    /**
     * Product variant
     *
     * @var \XC\ProductVariants\Model\ProductVariant
     */
    protected $variant;

    /**
     * Return current product variant Id
     *
     * @return integer
     */
    public function getVariantId()
    {
        return \XLite\Core\Request::getInstance()->variant_id;
    }

    /**
     * Alias
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    public function getProductVariant()
    {
        if (!isset($this->variant)) {
            $this->variant = $this->defineProductVariant();
        }

        return $this->variant;
    }

    /**
     * Define product variant
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    protected function defineProductVariant()
    {
        return \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
            ->findOneBy([
                'variant_id' => $this->getVariantId()
            ]);
    }

    /**
     * Add back in stock variant record
     */
    protected function doActionAddBack2stockVariantRecord()
    {
        $result = $this->processAddVariantStockRecord() && $this->processAddVariantPriceRecord();

        if (\XLite\Core\Request::getInstance()->isAJAX()) {
            $this->set('silent', true);
            $this->suppressOutput = true;
            $this->translateTopMessagesToHTTPHeaders();
            if ($result) {
                \XLite\Core\Event::back2stockrecordadded();
                $this->setSilenceClose(true);
            }
        }
    }

    /**
     * Returns product stock record by specific set of conditions
     *
     * @return \QSL\BackInStock\Model\Record || null
     */
    protected function getProductStockRecordByVariantSet()
    {
        $product = $this->getProduct();
        $variant = $this->getProductVariant();

        return \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
            ->getWaitedRecordByVariantSet(
                $product,
                $variant,
                \XLite\Core\Auth::getInstance()->getProfile(),
                \XLite\Core\Request::getInstance()->getBackInStockVariantCookie($product, $variant)
            );
    }

    /**
     * Returns product price record by specific set of conditions
     *
     * @return \QSL\BackInStock\Model\RecordPrice || null
     */
    protected function getProductPriceRecordByVariantSet()
    {
        $product = $this->getProduct();
        $variant = $this->getProductVariant();

        return \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
            ->getWaitedRecordByVariantSet(
                $product,
                $variant,
                \XLite\Core\Auth::getInstance()->getProfile(),
                \XLite\Core\Request::getInstance()->getBackInStockVariantPriceCookie($product, $variant)
            );
    }

    /**
     * Process add variant stock record
     *
     * @return boolean
     */
    protected function processAddVariantStockRecord()
    {
        $result = true;

        if (\XLite\Core\Request::getInstance()->notify_stock && \XLite\Core\Config::getInstance()->QSL->BackInStock->allowStockNotification) {
            if ($record = $this->getProductStockRecordByVariantSet()) {
                \XLite\Core\Event::back2stockrecordaddederror();
                \XLite\Core\TopMessage::addWarning(
                    'You have already signed up to get notified when this product is back in stock.'
                );
            } elseif (
                !\XLite\Core\Auth::getInstance()->getProfile()
                && !filter_var(\XLite\Core\Request::getInstance()->email, FILTER_VALIDATE_EMAIL)
            ) {
                \XLite\Core\TopMessage::addError('A valid email address is required');
            } else {
                $record = $this->assembleBackInStockVariantRecord();
                \XLite\Core\Database::getEM()->persist($record);
                \XLite\Core\Database::getEM()->flush();

                \XLite\Core\TopMessage::addInfo(
                    'You are signed up to get notified when the product X is back in stock.',
                    ['product' => $this->getProduct()->getName()]
                );
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Process add variant price record
     *
     * @return boolean
     */
    protected function processAddVariantPriceRecord()
    {
        $result = true;
        if (\XLite\Core\Request::getInstance()->notify_price && \XLite\Core\Config::getInstance()->QSL->BackInStock->allowPriceNotification) {
            if ($record = $this->getProductPriceRecordByVariantSet()) {
                \XLite\Core\Event::back2stockpricerecordaddederror();
                \XLite\Core\TopMessage::addWarning(
                    'You have already signed up to get notified when this product price is drop.'
                );
            } elseif (
                !\XLite\Core\Auth::getInstance()->getProfile()
                && !filter_var(\XLite\Core\Request::getInstance()->email, FILTER_VALIDATE_EMAIL)
            ) {
                \XLite\Core\TopMessage::addError('A valid email address is required');
            } else {
                $record = $this->assembleBackInStockVariantPriceRecord();
                \XLite\Core\Database::getEM()->persist($record);
                \XLite\Core\Database::getEM()->flush();

                \XLite\Core\TopMessage::addInfo(
                    'You are signed up to get notified when the product X price is drop.',
                    ['product' => $this->getProduct()->getName()]
                );
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Remove back in stock variant record
     */
    protected function doActionRemoveBack2stockVariantRecord()
    {
        $record = $this->getProductStockRecordByVariantSet();
        $recordPrice = $this->getProductPriceRecordByVariantSet();

        if ($record || $recordPrice) {
            if ($record) {
                \XLite\Core\Database::getEM()->remove($record);
            }
            if ($recordPrice) {
                \XLite\Core\Database::getEM()->remove($recordPrice);
            }
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\Event::back2stockrecordremoved();
            \XLite\Core\TopMessage::addInfo(
                'You have been unsubscribed from notifications for the product X.',
                ['product' => $this->getProduct()->getName()]
            );
        } else {
            \XLite\Core\Event::back2stockrecordremovederror();
            \XLite\Core\TopMessage::addWarning('You have not been subscribed to notifications for this product.');
        }

        if (\XLite\Core\Request::getInstance()->isAJAX()) {
            $this->set('silent', true);
            $this->translateTopMessagesToHTTPHeaders();
        } else {
            $this->setReturnURL($this->getURL());
        }
    }

    /**
     * Assemble back in stock variant record
     *
     * @return \QSL\BackInStock\Model\Record
     */
    protected function assembleBackInStockVariantRecord()
    {
        $record = new \QSL\BackInStock\Model\Record();
        $record->setProduct($this->getProduct());
        $record->setVariant($this->getProductVariant());
        $record->setCurrentQuantity($this->getProduct()->getPublicAmount());
        $record->setLanguage($this->getCurrentLanguage());
        $record->generateHash();

        if (\XLite\Core\Request::getInstance()->quantity) {
            $record->setQuantity(max(1, (float)\XLite\Core\Request::getInstance()->quantity));
        }

        $record->setEmail(\XLite\Core\Request::getInstance()->email);
        if (\XLite\Core\Auth::getInstance()->getProfile()) {
            $record->setProfile(\XLite\Core\Auth::getInstance()->getProfile());
        } else {
            \XLite\Core\Request::getInstance()->setBackInStockVariantCookie($record);
        }

        return $record;
    }

    /**
     * Assemble back in stock variant record (price)
     *
     * @return \QSL\BackInStock\Model\RecordPrice
     */
    protected function assembleBackInStockVariantPriceRecord()
    {
        $record = new \QSL\BackInStock\Model\RecordPrice();
        $record->setProduct($this->getProduct());
        $variant = $this->getProductVariant();
        $record->setVariant($variant);
        $record->setCurrentPrice($variant->getPrice());
        $record->setLanguage($this->getCurrentLanguage());
        $record->generateHash();

        if (\XLite\Core\Request::getInstance()->price) {
            $record->setPrice(max(0, (int)\XLite\Core\Request::getInstance()->price));
        }

        $record->setEmail(\XLite\Core\Request::getInstance()->email);
        if (\XLite\Core\Auth::getInstance()->getProfile()) {
            $record->setProfile(\XLite\Core\Auth::getInstance()->getProfile());
        } else {
            \XLite\Core\Request::getInstance()->setBackInStockVariantPriceCookie($record);
        }

        return $record;
    }
}
