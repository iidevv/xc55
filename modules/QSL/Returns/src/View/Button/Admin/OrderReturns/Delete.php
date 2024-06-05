<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Button\Admin\OrderReturns;

/**
 * Order returns methods
 */
class Delete extends \XLite\View\Button\Regular
{
    /**
     * Widget parameter names
     */
    public const PARAM_CONFIRMATION = 'confirm';

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'delete_order_returns';
    }

    /**
     * getDefaultConfirmationText
     *
     * @return string
     */
    protected function getDefaultConfirmationText()
    {
        return 'Do you really want to delete returns?';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_CONFIRMATION => new \XLite\Model\WidgetParam\TypeString(
                'Confirmation text',
                $this->getDefaultConfirmationText(),
                true
            ),
        ];
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        $code = parent::getDefaultJSCode();

        if ($this->getParam(self::PARAM_CONFIRMATION)) {
            $code = 'if (confirm(\'' . static::t($this->getParam(self::PARAM_CONFIRMATION)) . '\')) { ' . $code . ' }';
        }

        return $code;
    }
}
