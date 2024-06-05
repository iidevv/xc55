<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\Order\Details\Admin;

/**
 * Order shipments widget
 */
class Shipments extends \XLite\View\AView
{
    /**
     * Widget constants
     */
    public const TEMPLATES_DIR = 'modules/XC/CanadaPost/shipments';

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = self::TEMPLATES_DIR . '/style.css';
        $list[] = self::TEMPLATES_DIR . '/popup_box.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = self::TEMPLATES_DIR . '/controller.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/xcart.popup.js';
        $list[static::RESOURCE_JS][] = 'js/xcart.popup_button.js';

        return $list;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return self::TEMPLATES_DIR . '/shipments.twig';
    }

    // {{{ Shipment control buttons

    /**
     * Check - display "Print packing slip" button or not
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return boolean
     */
    public function displayPrintPackingSlipButton(\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $result = false;

        if (
            isset($parcel)
            && $parcel::STATUS_CREATED == $parcel->getStatus()
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Check - display "Create shipment" button or not
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return boolean
     */
    public function displayCreateShipmentButton(\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $result = false;

        if (
            isset($parcel)
            && $parcel::STATUS_PROPOSED == $parcel->getStatus()
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Check - display "Void shipment" button or not
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return boolean
     */
    public function displayVoidShipmentButton(\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $result = false;

        if (
            isset($parcel)
            && $parcel::STATUS_CREATED == $parcel->getStatus()
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Check - display "Transmit shipment" button or not
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return boolean
     */
    public function displayTransmitShipmentButton(\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $result = false;

        if (
            isset($parcel)
            && $parcel::STATUS_CREATED == $parcel->getStatus()
            && $parcel::QUOTE_TYPE_CONTRACTED == \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type
            && $parcel::QUOTE_TYPE_CONTRACTED == $parcel->getQuoteType()
        ) {
            $result = true;
        }

        return $result;
    }

    // }}}

    // {{{ Get parcels warnings

    /**
     * Get parcel warning messages
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return mixed
     */
    public function getParcelWarnings(\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $warnings = [];

        if (isset($parcel)) {
            if (
                !$parcel->areAPICallsAllowed()
                && $parcel::STATUS_CREATED == $parcel->getStatus()
            ) {
                $warnings[] = [
                    'message' => static::t('Parcel is cannot be voided or transmitted - wrong quote type'),
                ];
            }

            if (
                $parcel::STATUS_CREATED == $parcel->getStatus()
                && $parcel::QUOTE_TYPE_NON_CONTRACTED == $parcel->getQuoteType()
                && $parcel::QUOTE_TYPE_CONTRACTED == \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type
            ) {
                $warnings[] = [
                    'message' => static::t('Parcel is cannot be transmitted - wrong quote type'),
                ];
            }
        }

        return (empty($warnings)) ? null : $warnings;
    }

    // }}}

    /**
     * Check - are the only options for Contracted shipment must be displayed (i.e. for "Create Shipment" request)
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return boolean
     */
    public function displayOnlyContractedOptions(\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        return (
            (
                $parcel::QUOTE_TYPE_CONTRACTED == $parcel->getQuoteType()
                && $parcel::STATUS_PROPOSED != $parcel->getStatus()
            ) || (
                $parcel::QUOTE_TYPE_CONTRACTED == \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type
                && $parcel::STATUS_PROPOSED == $parcel->getStatus()
            )
        );
    }

    /**
     * Return JS parameters
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return array
     */
    public function getParcelJSParams(\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        return [
            'parcel_id' => $parcel->getId(),
            'status'    => $parcel->getStatus(),
        ];
    }

    /**
     * Check - is notification "On shipment" enabled or not
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel model
     *
     * @return boolean
     */
    public function isNotifyOnShipment(\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        return (
            $parcel->isDeliveryToPostOffice()
            || $parcel->getNotifyOnShipment()
        );
    }

    /**
     * Return text
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Shipment\Link $link
     *
     * @return string
     */
    protected function getRemovedItemsWarning($link)
    {
        return static::t(
            'Some of the items in the parcel were removed. To view all the items, including the removed ones, see the document.',
            [
                'docUrl' => $link->getUrl(),
                'docTitle' => $link->getLinkTitle()
            ]
        );
    }
}
