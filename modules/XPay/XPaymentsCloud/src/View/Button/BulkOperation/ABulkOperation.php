<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Button\BulkOperation;

/**
 * X-Payments bulk operation button widget
 */
abstract class ABulkOperation extends \XLite\View\Button\Regular
{
    /**
     * Get operation
     *
     * @return string
     */
    abstract protected function getOperation();

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'add_bulk_operation';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue(
            array('operation' => $this->getOperation())
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'btn regular-button more-action hide-on-disable hidden';
    }
}
