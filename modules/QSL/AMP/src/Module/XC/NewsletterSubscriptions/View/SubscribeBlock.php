<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Module\XC\NewsletterSubscriptions\View;

/**
 * SubscribeBlock
 */
class SubscribeBlock extends \XC\NewsletterSubscriptions\View\SubscribeBlock
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/modules/XC/NewsletterSubscriptions/form/subscribe.twig';
    }

    /**
     * AMP-mode styles
     *
     * NOTE: Use this method instead of getCSSFiles for AMP page styles
     *
     * .less files are merged with modules/QSL/AMP/styles/initialize.less by default
     *
     * @return array
     */
    protected function getAmpCSSFiles()
    {
        return [
            [
                'file' => 'modules/QSL/AMP/modules/XC/NewsletterSubscriptions/form/styles.less',
            ],
        ];
    }
}
