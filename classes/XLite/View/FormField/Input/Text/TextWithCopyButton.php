<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

use XLite\Model\WidgetParam\TypeString;
use XLite\View\FormField\Input\Text;

class TextWithCopyButton extends Text
{
    public const PARAM_BTN_ID         = 'copyBtnId';
    public const PARAM_BTN_TARGET     = 'copyBtnClipboardTarget';


    protected function getFieldTemplate(): string
    {
        return 'input_with_copy_button.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_BTN_ID     => new TypeString('Copy button ID', 'copy-button'),
            self::PARAM_BTN_TARGET => new TypeString('Copy button clipboard target', '')
        ];
    }

    /**
     * Returns copy button ID parameter value.
     *
     * @return string
     */
    public function getCopyBtnId(): string
    {
        return $this->getParam(self::PARAM_BTN_ID);
    }

    /**
     * Returns HTML selector that detects the field to copy the text from.
     *
     * @return string
     */
    public function getCopyBtnClipboardTarget(): string
    {
        return ( $this->getParam(self::PARAM_BTN_TARGET) ?: '#' . $this->getFieldId() );
    }
}
