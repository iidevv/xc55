<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Tabs;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request as Request;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Tabs related to X-Payments Cloud settings page
 * @ListChild (list="admin.center", zone="admin", weight="10")
 */
class Settings extends \XLite\View\Tabs\ATabs
{
    const PARAM_FOR_PAYMENT_METHOD = 'forPaymentMethod';

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'payment_method';
        $list[] = 'module';

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_FOR_PAYMENT_METHOD => new \XLite\Model\WidgetParam\TypeBool(
                'Payment method page flag',
                false,
                false
            ),
        );

    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        switch ($this->getTarget()) {
            case 'module':
                $forXpayments = ('XPay-XPaymentsCloud' == Request::getInstance()->moduleId);
                break;
            case 'payment_method':
                $forXpayments = $this->getPaymentMethod()->isXpayments()
                    && $this->getParam(self::PARAM_FOR_PAYMENT_METHOD);
                break;
            default:
                $forXpayments = true;
                break;
        }

        return $forXpayments
            && parent::isVisible();
    }

    /**
     * Returns tab URL
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        $methodId = (Request::getInstance()->method_id) ?: XPaymentsHelper::getPaymentMethod()->getMethodId();

        switch ($target) {
            case 'module':
                $url = $this->buildUrl($target, '', array('moduleId' => 'XPay-XPaymentsCloud', 'method_id' => $methodId));
                break;
            case 'payment_method':
                $url = $this->buildUrl($target, '', array('method_id' => $methodId));
                break;
            default:
                $url = parent::buildTabURL($target);
                break;
        }

        return $url;
    }

    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        return array( 
            'payment_method' => array(
                'weight' => 100,
                'title'  => static::t('Payment Method'),
                'template' => 'empty.twig',
            ),
            'module' => array( 
                'weight' => 200,
                'title'  => static::t('Store settings'),
                'template' => 'empty.twig',
            ),
        );
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->find(Request::getInstance()->method_id);
    }
}
