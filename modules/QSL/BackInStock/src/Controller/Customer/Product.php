<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Customer\Product
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->widget === '\QSL\BackInStock\View\CustomerBox'
            ? ''
            : parent::getTitle();
    }

    /**
     * Add back in stock record
     */
    protected function doActionAddBack2stockRecord()
    {
        $stockRecord = $this->processAddStockRecord();
        $priceRecord = $this->processAddPriceRecord();

        if (\XLite\Core\Request::getInstance()->isAJAX()) {
            $this->set('silent', true);
            $this->suppressOutput = true;
            $this->translateTopMessagesToHTTPHeaders();
            if ($stockRecord || $priceRecord) {
                \XLite\Core\Event::back2stockrecordadded();
            } else {
                \XLite\Core\Event::back2stockrecordclosed();
            }

            $this->setSilenceClose(true);
        }
    }

    /**
     * Returns product stock record by specific set of conditions
     *
     * @return \QSL\BackInStock\Model\Record || null
     */
    protected function getProductStockRecordBySet()
    {
        $product = $this->getProduct();

        return \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
            ->getWaitedRecordBySet(
                $product,
                \XLite\Core\Auth::getInstance()->getProfile(),
                \XLite\Core\Request::getInstance()->getBackInStockCookie($product)
            );
    }

    /**
     * Returns product price record by specific set of conditions
     *
     * @return \QSL\BackInStock\Model\RecordPrice || null
     */
    protected function getProductPriceRecordBySet()
    {
        $product = $this->getProduct();

        return \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
            ->getWaitedRecordBySet(
                $product,
                \XLite\Core\Auth::getInstance()->getProfile(),
                \XLite\Core\Request::getInstance()->getBackInStockPriceCookie($product)
            );
    }

    /**
     * Process add stock record
     *
     * @return boolean
     */
    protected function processAddStockRecord()
    {
        $result = false;

        if (\XLite\Core\Request::getInstance()->notify_stock && \XLite\Core\Config::getInstance()->QSL->BackInStock->allowStockNotification) {
            if ($record = $this->getProductStockRecordBySet()) {
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
                $record = $this->assembleBackInStockRecord();
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
     * Process add price record
     *
     * @return boolean
     */
    protected function processAddPriceRecord()
    {
        $result = false;

        if (\XLite\Core\Request::getInstance()->notify_price && \XLite\Core\Config::getInstance()->QSL->BackInStock->allowPriceNotification) {
            if ($record = $this->getProductPriceRecordBySet()) {
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
                $record = $this->assembleBackInStockPriceRecord();
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
     * Remove back in stock record
     */
    protected function doActionRemoveBack2stockRecord()
    {
        $record = $this->getProductStockRecordBySet();
        $recordPrice = $this->getProductPriceRecordBySet();

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
     * Assemble back in stock record
     *
     * @return \QSL\BackInStock\Model\Record
     */
    protected function assembleBackInStockRecord()
    {
        $record = new \QSL\BackInStock\Model\Record();
        $record->setProduct($this->getProduct());
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
            \XLite\Core\Request::getInstance()->setBackInStockCookie($record);
        }

        return $record;
    }

    /**
     * Assemble back in stock record (price)
     *
     * @return \QSL\BackInStock\Model\RecordPrice
     */
    protected function assembleBackInStockPriceRecord()
    {
        $record = new \QSL\BackInStock\Model\RecordPrice();
        $record->setProduct($this->getProduct());
        $record->setCurrentPrice($this->getProduct()->getPrice());
        $record->setLanguage($this->getCurrentLanguage());
        $record->generateHash();

        if (\XLite\Core\Request::getInstance()->price) {
            $record->setPrice(max(0, (int)\XLite\Core\Request::getInstance()->price));
        }

        $record->setEmail(\XLite\Core\Request::getInstance()->email);
        if (\XLite\Core\Auth::getInstance()->getProfile()) {
            $record->setProfile(\XLite\Core\Auth::getInstance()->getProfile());
        } else {
            \XLite\Core\Request::getInstance()->setBackInStockPriceCookie($record);
        }

        return $record;
    }
}
