<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\FormField\Input;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XLite\Core\Config;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Class shipping method mandatory checkbox
 */
class ShippingMethodMandatoryCheckbox extends \XLite\View\FormField\Input\Checkbox implements ProviderInterface
{
    use AftershipTrait;

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return Config::getInstance()->General->smsv_text_checkbox;
    }

    /**
     * Get js files
     *
     * @return array
     */
    public function getJSFiles(): array
    {
        $list = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/checkout/steps/shipping/parts/shipping_mandatory_checkbox.js';

        return $list;
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels(): array
    {
        return [
            'SkinActAftership you have to accept how shipping for large size items works' => static::t('SkinActAftership you have to accept how shipping for large size items works')
        ];
    }
}