<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\View\FormField\Select\CardNumberDisplayFormat;

/**
 * Additional actions for Module settings page
 *
 * @Extender\Mixin
 */
abstract class Module extends \XLite\Controller\Admin\Module implements \XLite\Base\IDecorator
{
    /**
     * Check if this is the X-Payments Cloud module
     *
     * @return bool
     */
    protected function isXPaymentsCloud()
    {
        return strval($this->getModuleId()) === \Includes\Utils\Module\Module::buildId('XPay', 'XPaymentsCloud');
    }

    /**
     * Update action
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->isXPaymentsCloud()) {

            $config = \XLite\Core\Config::getInstance()->XPay->XPaymentsCloud;
            $requestData = \XLite\Core\Request::getInstance()->getData();

            if (
                CardNumberDisplayFormat::FORMAT_UNMASKED === $config->card_number_display_format
                && CardNumberDisplayFormat::FORMAT_MASKED === $requestData['card_number_display_format']
            ) {
                \XLite\Core\TopMessage::getInstance()->clearTopMessages();
                \XPay\XPaymentsCloud\Logic\ClearCCData\Generator::run($this->assembleClearCCDataOptions());
            }
        }

        parent::doActionUpdate();
    }

    /**
     * Do no action
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if ($this->isXPaymentsCloud()) {

            $request = \XLite\Core\Request::getInstance();

            if ($request->clear_cc_data_completed) {
                \XLite\Core\TopMessage::addInfo('Credit card data has been cleared successfully.');

                $this->setReturnURL(
                    $this->buildURL('module', '', array('moduleId' => 'XPay-XPaymentsCloud'))
                );

            } elseif ($request->clear_cc_data_failed) {
                \XLite\Core\TopMessage::addError('The clearing of credit card data has been stopped.');

                $this->setReturnURL(
                    $this->buildURL('module', '', array('moduleId' => 'XPay-XPaymentsCloud'))
                );
            }
        }
    }

    /**
     * Check - clearing process is not-finished or not
     *
     * @return boolean
     */
    public function isClearCCDataNotFinished()
    {
        return \XPay\XPaymentsCloud\Core\ClearCCData::getInstance()->isClearCCDataNotFinished();
    }

    /**
     * Assemble clear credit cards data options
     *
     * @return array
     */
    protected function assembleClearCCDataOptions()
    {
        return array(
            'include' => \XLite\Core\Request::getInstance()->section,
        );
    }
}
