<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button\BulkOperation;

/**
 * X-Payments bulk operation stop button widget
 */
class Stop extends \XLite\View\Button\Regular
{
    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'stop_bulk_operation';
    }

    /**
     * Get operation
     *
     * @return string
     */
    protected function getOperation()
    {
        return \XPay\XPaymentsCloud\Model\BulkOperation::OPERATION_CAPTURE;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_FORM_PARAMS]->appendValue(
            array('operation' => $this->getOperation())
        );

        $this->widgetParams[static::PARAM_ID] = new \XLite\Model\WidgetParam\TypeString('Button ID', 'stop-bulk-operation');
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'btn regular-button always-enabled hidden';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Stop';
    }
}
