<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\FormField\Caption;

/**
 * Help text that appears on the module settings page.
 */
class RecaptchaScoreHelp extends \XLite\View\FormField\AFormField
{
    /**
     * Field type.
     */
    public const FIELD_TYPE_RECAPTCHA_SCORE_HELP = 'recaptchaScoreHelp';

    public const PARAM_RECAPTCHA_SCORE_HELP_TEXT = 'recaptchaScoreHelpText';

    /**
     * Returns the field type.
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_RECAPTCHA_SCORE_HELP;
    }

    // /**
    //  * Via this method the widget registers the CSS files which it uses.
    //  * During the viewers initialization the CSS files are collecting into the static storage.
    //  *
    //  * @return array
    //  */
    // public function getCSSFiles()
    // {
    //     return array_merge(
    //         parent::getCSSFiles(),
    //         [
    //             'main/style.css',
    //             $this->getDir() . '/recaptcha_help.css',
    //         ]
    //     );
    // }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/' . $this->getFieldTemplate();
    }

    /**
     * Returns the name of the folder with widget-related files.
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/reCAPTCHA/module_settings/header';
    }

    /**
     * Returns the field template.
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'recaptcha_score_help.twig';
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
            static::PARAM_RECAPTCHA_SCORE_HELP_TEXT => new \XLite\Model\WidgetParam\TypeString('Score Help text'),
        ];
    }

    /**
     * Get min value
     *
     * @return integer
     */
    protected function getRecaptchaScoreHelpText()
    {
        return $this->getParam(self::PARAM_RECAPTCHA_SCORE_HELP_TEXT);
    }
}
