<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * Yes/No FlipSwitch
 */
class PaymentMethod extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    public const PARAM_METHOD_ID = 'methodId';
    public const PARAM_TITLE = 'title';

    /**
     * Returns default param value
     *
     * @return string
     */
    protected function getDefaultOnLabel()
    {
        return static::t('Active');
    }

    /**
     * Returns default param value
     *
     * @return string
     */
    protected function getDefaultOffLabel()
    {
        return static::t('Inactive');
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
            self::PARAM_TITLE => new \XLite\Model\WidgetParam\TypeString('Tooltip', ''),
            self::PARAM_METHOD_ID => new \XLite\Model\WidgetParam\TypeString('Payment method id', null),
        ];
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/checkbox/payment_method.twig';
    }

    /**
     * @return string
     */
    protected function getTooltipMessage()
    {
        if ($this->isDisabled() && $this->getDisabledTitle()) {
            return $this->getDisabledTitle();
        }

        if ($this->getParam(static::PARAM_TITLE)) {
            return $this->getParam(static::PARAM_TITLE);
        }

        return null;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        if ($this->getParam(static::PARAM_METHOD_ID)) {
            $list['data-method-id'] = $this->getParam(static::PARAM_METHOD_ID);
        }

        return $list;
    }
}
