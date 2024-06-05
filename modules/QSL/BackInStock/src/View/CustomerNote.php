<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View;

/**
 * Customer note
 */
class CustomerNote extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * Already created record flag
     *
     * @var boolean
     */
    protected $alreadyCreated;

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/QSL/BackInStock/customer_note.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $product = \XLite::getController() instanceof \XLite\Controller\Customer\Product
            ? \XLite::getController()->getProduct()
            : null;

        $this->widgetParams += [
            static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product',
                $product,
                false,
                'XLite\Model\Product'
            ),
        ];
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BackInStock/customer_note.twig';
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        $product = $this->getProduct();

        $result = [
            'class' => [
                'back2stock-box',
            ],
            'data-post-url' => $this->buildURL(
                'product',
                'add_back2stock_record',
                [
                    'product_id' => $product->getProductId()
                ]
            ),
            'data-product-id' => $product->getProductId(),
        ];

        $result['class'][] = $this->isRecordAlreadyCreated()
            ? 'saved'
            : 'non-saved';

        $result['class'][] = (
                $this->countPriceRecords()
                || $this->countRecords()
                || $this->isRecordAlreadyCreated()
                || $this->isPriceRecordAlreadyCreated()
            )
            ? 'visible'
            : 'hidden';

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
        if (!isset($this->alreadyCreated) || $force) {
            /** @var \QSL\BackInStock\Model\Record $record */
            $record = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
                ->getWaitedRecordBySet(
                    $this->getParam(static::PARAM_PRODUCT),
                    \XLite\Core\Auth::getInstance()->getProfile(),
                    \XLite\Core\Request::getInstance()->getBackInStockCookie($this->getParam(static::PARAM_PRODUCT))
                );
            /** @var \QSL\BackInStock\Model\RecordPrice $recordPrice */
            $recordPrice = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                ->getWaitedRecordBySet(
                    $this->getParam(static::PARAM_PRODUCT),
                    \XLite\Core\Auth::getInstance()->getProfile(),
                    \XLite\Core\Request::getInstance()->getBackInStockPriceCookie($this->getParam(static::PARAM_PRODUCT))
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
        $count = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')
            ->countSumWaiting(
                $this->getParam(static::PARAM_PRODUCT)
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
        if (!isset($this->alreadyCreated) || $force) {
            /** @var \QSL\BackInStock\Model\RecordPrice $record */
            $record = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
                ->getRecordBySet(
                    $this->getParam(static::PARAM_PRODUCT),
                    \Xlite\Core\Auth::getInstance()->getProfile(),
                    \XLite\Core\Request::getInstance()->getBackInStockCookie($this->getParam(static::PARAM_PRODUCT))
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
        $count = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')
            ->countWaiting(
                $this->getParam(static::PARAM_PRODUCT)
            );

        return $this->isRecordAlreadyCreated()
            ? $count - 1
            : $count;
    }

    /**
     * Return URL for cancel notification subscription link
     *
     * @return string
     */
    protected function getCancelNotificationSubscriptionURL()
    {
        $product = $this->getProduct();

        return \XLite\Core\Converter::buildURL(
            'product',
            'remove_back2stock_record',
            [
                'product_id' => $product->getProductId()
            ]
        );
    }
}
