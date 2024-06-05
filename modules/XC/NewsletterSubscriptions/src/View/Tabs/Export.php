<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs related to export page
 * @Extender\Mixin
 */
class Export extends \XLite\View\Tabs\Export
{
    /**
     * Return widget default template
     *
     * @return array<string, array<string, string|int>>
     */
    protected function defineSections()
    {
        return parent::defineSections() + [
                'XC\NewsletterSubscriptions\Logic\Export\Step\NewsletterSubscribers' => [
                    'label' => 'Subscribers',
                    'position' => 120
                ],
            ];
    }
}
