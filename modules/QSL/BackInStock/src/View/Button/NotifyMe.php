<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Button;

use QSL\BackInStock\Main;

/**
 * Notify me button
 */
class NotifyMe extends \XLite\View\Button\PopupButton
{
    /**
     * Widget parameter names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/QSL/BackInStock/customer_box.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        if (Main::isCurrentSkin('XC-CrispWhiteSkin')) {
            $list[] = [
                'file'  => 'modules/QSL/BackInStock/modules/XC/CrispWhiteSkin/customer_box.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/BackInStock/button/notify_me.js';

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
     * Return URL parameters to use in AJAX popup
     *
     * @return string[]
     */
    protected function prepareURLParams()
    {
        return [
            'target'     => 'product',
            'product_id' => $this->getProduct()->getProductId(),
            'widget'     => '\QSL\BackInStock\View\CustomerBox',
            'popup'      => '1',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getClass()
    {
        $result = parent::getClass() . ' popup-notify-me';

        if ($this->isRecordAlreadyCreated()) {
            $result .= ' already-created';
        }

        return $result;
    }

    /**
     * Check - record already created for specified product or not
     *
     * @return boolean
     */
    protected function isRecordAlreadyCreated()
    {
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
        return ($record && $record->isWaiting()) || ($recordPrice && $recordPrice->isWaiting());
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultLabel()
    {
        return 'Notify me';
    }

    /**
     * Default withoutClose value
     *
     * @return boolean
     */
    protected function getDefaultWithoutCloseState()
    {
        return true;
    }
}
