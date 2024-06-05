<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Base;

use XCart\Extender\Mapping\Extender;

/**
 * Sticky panel
 *
 * @Extender\Mixin
 */
abstract class StickyPanel extends \XLite\View\Base\StickyPanel implements \XLite\Base\IDecorator
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
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (
            \XLite::getController() instanceof \XLite\Controller\Admin\Module
            && $this->isXPaymentsCloud()
        ) {
            $list[] = 'modules/XPay/XPaymentsCloud/js/stickyPanelXpayments.js';
        }

        return $list;
    }
}
