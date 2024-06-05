<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Checkout;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * @ListChild (list="checkout_fastlane.sections.place-order.before", weight="200")
 */
class ToSConsent extends \XLite\View\AView implements ProviderInterface
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FastLaneCheckout/sections/tos_consent.twig';
    }

    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/XC/FastLaneCheckout/sections/tos_consent.js';

        return $list;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->General->terms_conditions_confirm_type == 'Clickwrap';
    }

    /**
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'You have to accept terms & conditions' => static::t('You have to accept terms & conditions')
        ];
    }
}
