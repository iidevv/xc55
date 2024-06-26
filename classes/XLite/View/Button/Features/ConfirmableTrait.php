<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Features;

/**
 * Confirmable button trait
 */
trait ConfirmableTrait
{
    /**
     * @return string
     */
    protected static function getConfirmWidgetParamName()
    {
        return 'confirm';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getConfirmationText()
    {
        return $this->getParam(static::getConfirmWidgetParamName()) ?? $this->getDefaultConfirmationText();
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        $code = parent::getDefaultJSCode();

        if ($this->getConfirmationText()) {
            $code = 'if (confirm(\'' . static::t($this->getConfirmationText()) . '\')) { ' . $code .
                ' } else { core.trigger(\'confirm_reject\', this) }';
        }

        return $code;
    }

    /**
     * getDefaultConfirmationText
     *
     * @return string
     */
    protected function getDefaultConfirmationText()
    {
        return 'Do you really want to perform action with the selected items?';
    }
}
