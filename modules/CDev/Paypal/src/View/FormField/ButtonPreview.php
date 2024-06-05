<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField;

class ButtonPreview extends \XLite\View\FormField\AFormField
{
    use PreviewTrait;

    public const FIELD_TYPE_BUTTON_PREVIEW = 'button_preview';

    /**
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_BUTTON_PREVIEW;
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/form_field/button_preview/body.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Paypal/form_field/button_preview/style.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/Paypal/form_field/button_preview/script.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return parent::getDefaultWrapperClass() . ' button-preview-wrapper';
    }

    /**
     * @return array
     */
    public function getCommentedData()
    {
        return [
            'config' => $this->getPaypalCommonConfig()
        ];
    }
}
