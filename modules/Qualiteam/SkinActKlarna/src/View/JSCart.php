<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\View;

use Qualiteam\SkinActKlarna\Traits\KlarnaTrait;
use XCart\Container;
use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;

/**
 * JS cart options
 *
 * @ListChild (list="jscontainer.js", zone="customer", weight="999999")
 */
class JSCart extends AView
{
    use KlarnaTrait;

    public static function getAllowedTargets()
    {
        return ['cart'];
    }

    public function getHTML()
    {
        return $this->getKlarnaExpressButtonScript();
    }

    protected function getKlarnaExpressButtonScript(): string
    {
        return sprintf(
            '<script async data-environment="%s" src="%s" data-id="%s"></script>',
            Container::getContainer()->get('klarna.configuration')->getMode(),
            Container::getContainer()->getParameter('klarna.url.express-button'),
            Container::getContainer()->getParameter('klarna.settings.express-button.clientId'),
        );
    }

    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/js_options.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() &&
            Container::getContainer()->get('klarna.configuration')->isEnabled();
    }
}