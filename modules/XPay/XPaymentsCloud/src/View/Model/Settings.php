<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Model;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Core\ClearCCData;

/**
 * Settings dialog model widget
 *
 * @Extender\Mixin
 */
abstract class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Check if this is the X-Payments Cloud module
     *
     * @return bool
     */
    protected function isXPaymentsCloud()
    {
        $request = \XLite\Core\Request::getInstance()->getGetData();

        return !empty($request['moduleId'])
            && strval($request['moduleId']) === \Includes\Utils\Module\Module::buildId('XPay', 'XPaymentsCloud');
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        $result = parent::getBody();

        if (
            \XLite::getController() instanceof \XLite\Controller\Admin\Module
            && $this->isXPaymentsCloud()
            && ClearCCData::getInstance()->isClearCCDataNotFinished()
        ) {
            $result = 'modules/XPay/XPaymentsCloud/clear_cc_data/body.twig';
        }

        return $result;
    }
}
