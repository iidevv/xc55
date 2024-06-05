<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Google reCAPTCHA module settings.
 * @Extender\Mixin
 */
class Settings extends \XLite\View\Model\ModuleSettings
{
    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        switch ($optionName = $option->getName()) {
            case 'google_recaptcha_theme':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_HIDE => [
                        'google_recaptcha_api' => [
                            \QSL\reCAPTCHA\View\FormField\Select\Version::API_V3,
                        ],
                    ],
                ];
                break;

            case 'google_recaptcha_size':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_HIDE => [
                        'google_recaptcha_api' => [
                            \QSL\reCAPTCHA\View\FormField\Select\Version::API_V3,
                        ],
                    ],
                ];
                break;

            case 'google_recaptcha_throttling':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_SHOW => [
                        'google_recaptcha_api' => [
                            \QSL\reCAPTCHA\View\FormField\Select\Version::API_V3,
                        ],
                    ],
                ];
                break;
        }

        if ($optionName === 'google_recaptcha_newsletter') {
            $cell[static::SCHEMA_DEPENDENCY] = [
                static::DEPENDENCY_SHOW => [
                    'google_recaptcha_api' => [
                        \QSL\reCAPTCHA\View\FormField\Select\Version::API_V3,
                    ],
                ],
            ];
        }

        if (
            strpos($optionName, "_min_score") > 0
            || strpos($optionName, "_fallback") > 0
        ) {
            $togglerOptionName = str_replace('_min_score', '', $optionName);
            $togglerOptionName = str_replace('_fallback', '', $togglerOptionName);

            // make all thoese options only available when API v3 selected:
            $cell[static::SCHEMA_DEPENDENCY] = [
                static::DEPENDENCY_SHOW => [
                    'google_recaptcha_api' => [
                        \QSL\reCAPTCHA\View\FormField\Select\Version::API_V3,
                    ],

                ],
            ];

            // … AND when appropriate form is enabled:
            if ($optionName !== 'google_recaptcha_min_score' && $togglerOptionName !== $optionName) {
                $cell[static::SCHEMA_DEPENDENCY][static::DEPENDENCY_SHOW]["google_{$togglerOptionName}"] = true;
            }
        }

        return $cell;
    }
}
