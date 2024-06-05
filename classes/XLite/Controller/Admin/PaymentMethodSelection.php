<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Payment method selection  controller
 */
class PaymentMethodSelection extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Constructor
     *
     * @param array $params Constructor parameters
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['search']);
    }

    /**
     * Get session cell name for pager widget
     *
     * @return string
     */
    public function getPagerSessionCell()
    {
        return parent::getPagerSessionCell() . '_' . md5(microtime(true));
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Payment methods');
    }

    /**
     * Return true if 'Install' link should be displayed
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function isDisplayInstallModuleLink(\XLite\Model\Payment\Method $method)
    {
        return $method->getModuleName()
            && !$this->isModuleEnabled($method);
    }

    /**
     * Returns URL to payment module
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getPaymentModuleURL(\XLite\Model\Payment\Method $method)
    {
        return $method->getModulePageURL();
    }

    /**
     * Return payment methods type which is provided to the widget
     *
     * @return string
     */
    protected function getPaymentType()
    {
        return \XLite\Core\Request::getInstance()->{\XLite\View\Button\Payment\AddMethod::PARAM_PAYMENT_METHOD_TYPE};
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = parent::getSearchParams();

        $searchParams[\XLite\View\Pager\APager::PARAM_PAGE_ID] = 1;

        return $searchParams;
    }

    /**
     * Return true if payment method's module is enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method model object
     *
     * @return boolean
     */
    protected function isModuleEnabled(\XLite\Model\Payment\Method $method)
    {
        return (bool) $method->getProcessor();
    }
}
