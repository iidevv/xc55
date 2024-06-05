<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\View;

use Qualiteam\SkinActKlarna\Traits\KlarnaTrait;
use XCart\Container;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request;

/**
 * JS checkout options
 *
 * @ListChild (list="jscontainer.js", zone="customer", weight="999999")
 *
 */
class JSCheckout extends \XLite\View\AView
{
    use KlarnaTrait;

    public static function getAllowedTargets()
    {
        return ['checkout'];
    }

    public function getHTML(): string
    {
        return $this->getKlarnaSdkScript();
    }

    protected function getKlarnaSdkScript(): string
    {
        return sprintf('<script async src="%s"></script>',
            Container::getContainer()->getParameter('klarna.url.sdk')
        );
    }

    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/js_options.twig';
    }
}