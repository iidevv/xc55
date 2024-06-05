<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField;

use CDev\Paypal\View\FormField\Select\PPCMBannerType;
use CDev\Paypal\Main;

class BannerPreview extends \XLite\View\FormField\AFormField
{
    use PreviewTrait;

    public const FIELD_TYPE_BANNER_PREVIEW = 'banner_preview';

    public const PARAM_IS_DISPLAY_TITLE = 'isDisplayTitle';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_IS_DISPLAY_TITLE => new \XLite\Model\WidgetParam\TypeBool('Is display title', true),
        ];
    }

    /**
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_BANNER_PREVIEW;
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function isDisplayTitle()
    {
        return $this->getParam(static::PARAM_IS_DISPLAY_TITLE);
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/form_field/banner_preview/body.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Paypal/form_field/banner_preview/style.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/Paypal/form_field/banner_preview/script.js';
        $list[] = 'modules/CDev/Paypal/form_field/banner_preview/watcher.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return parent::getDefaultWrapperClass() . ' banner-preview-wrapper';
    }

    /**
     * @return array
     */
    public function getCommentedData()
    {
        $attributes = [
            'data-pp-style-layout' => $this->getSetting('ppcm_banner_type'),
        ];

        $textStyleAttributes = [
            'data-pp-style-logo-type'     => $this->getSetting('ppcm_text_logo_type'),
            'data-pp-style-logo-position' => $this->getSetting('ppcm_text_logo_position'),
            'data-pp-style-text-size'     => $this->getSetting('ppcm_text_size'),
            'data-pp-style-text-color'    => $this->getSetting('ppcm_text_color'),
        ];

        $flexStyleAttributes = [
            'data-pp-style-color' => $this->getSetting('ppcm_flex_color_scheme'),
            'data-pp-style-ratio' => $this->getSetting('ppcm_flex_layout'),
        ];

        if ($this->getSetting('ppcm_banner_type') === PPCMBannerType::PPCM_FLEX) {
            $attributes = array_merge($attributes, $flexStyleAttributes);
        } else {
            $attributes = array_merge($attributes, $textStyleAttributes);
        }

        return [
            'config' => $this->getPaypalCommonConfig(),
            'attributes' => $attributes
        ];
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    protected function getSetting($name)
    {
        return $this->getMethod()->getSetting($name);
    }

    /**
     * @return \XLite\Model\Payment\Method
     */
    protected function getMethod()
    {
        return Main::getPaymentMethod(Main::PP_METHOD_PC);
    }
}
