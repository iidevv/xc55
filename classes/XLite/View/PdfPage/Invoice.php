<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\PdfPage;

/**
 * Pdf test page
 */
class Invoice extends \XLite\View\APdfPage
{
    /**
     * Widget parameter names
     */
    public const PARAM_ORDER = 'order';

    /**
     * language can be requested before pdf generation
     */
    public $languageCode;

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Get pdf language
     *
     * @return string
     */
    public function getLanguageCode()
    {
        if ($this->languageCode) {
            return $this->languageCode;
        } elseif (
            $this->getZone() === \XLite::ZONE_CUSTOMER
            && $this->getOrder()
            && $this->getOrder()->getProfile()
        ) {
            return $this->getOrder()->getProfile()->getLanguage();
        } else {
            return parent::getLanguageCode();
        }
    }

    /**
     * @param $languageCode
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;
    }
    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                'XLite\Model\Order'
            ),
        ];
    }

    /**
     * Returns PDF document title
     *
     * @return string
     */
    public function getDocumentTitle()
    {
        return $this->getOrder()
            ? 'Order ' . $this->getOrder()->getPrintableOrderNumber() . ' invoice'
            : 'Order invoice';
    }


    /**
     * Page Html template path
     *
     * @return string
     */
    public function getPdfStylesheets()
    {
        return array_merge(
            parent::getPdfStylesheets(),
            [
                'order/invoice/common.less',
                'order/invoice/style.less',
                'order/invoice/print.css',
            ]
        );
    }

    /**
     * Page Html template path
     *
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'order/invoice/page.twig';
    }
}
