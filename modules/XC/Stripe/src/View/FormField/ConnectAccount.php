<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\View\FormField;

/**
 * ConnectAccount
 */
class ConnectAccount extends \XLite\View\FormField\AFormField
{
    public const PARAM_PROFILE = 'profile';

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/XC/Stripe/form_field/connect_account/style.less';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PROFILE => new \XLite\Model\WidgetParam\TypeObject('Profile', null, false, 'XLite\Model\Profile'),
        ];
    }

    /**
     * @return bool
     */
    protected function isVendorZone()
    {
        return \XLite\Core\Auth::getInstance()->isVendor();
    }

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_COMPLEX;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/XC/Stripe/form_field/connect_account/field.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getStripeAccountId()
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    protected function getConnectionURL()
    {
        return $this->buildURL('stripe_connect_vendor', 'connect_account');
    }

    /**
     * @return string
     */
    protected function getDisonnectionURL()
    {
        return $this->buildURL('stripe_connect_vendor', 'disconnect_account');
    }
}
