<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\StickyPanel\Order\Admin;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Orders list panel
 *
 * @Extender\Mixin
 */
abstract class Search extends \XLite\View\StickyPanel\Order\Admin\Search implements \XLite\Base\IDecorator
{
    /**
     * Check if X-Payment Cloud payment method is enabled
     *
     * @return bool
     */
    protected static function isXpaymentsEnabled()
    {
        return XPaymentsHelper::getPaymentMethod()
            && XPaymentsHelper::getPaymentMethod()->isEnabled();
    }

    /**
     * Define buttons 
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        if (static::isXpaymentsEnabled()) {

            $batchId = \XLite\Core\Database::getRepo('XPay\XPaymentsCloud\Model\BulkOperation')
                ->getActiveBatchId(\XPay\XPaymentsCloud\Model\BulkOperation::OPERATION_CAPTURE);

            if (empty($batchId)) {

                $list['xpayments_bulk_capture'] = $this->getWidget(
                    array(),
                    'XPay\XPaymentsCloud\View\Button\BulkOperation\Capture'
                );

            } else {

                $list['xpayments_bulk_progress'] = $this->getWidget(
                    array(),
                    'XPay\XPaymentsCloud\View\BulkOperation\Progress'
                );

                $list['xpayments_bulk_progress_stop'] = $this->getWidget(
                    array(),
                    'XPay\XPaymentsCloud\View\Button\BulkOperation\Stop'
                );
            }
        }

        return $list;
    }
}
