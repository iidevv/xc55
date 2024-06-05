<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\View;

use Qualiteam\SkinActKlarna\Traits\KlarnaTrait;
use XCart\Container;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Config;
use XLite\View\AView;

/**
 * JS promotion options
 *
 * @ListChild (list="jscontainer.js", zone="customer", weight="999999")
 */
class JSPromotion extends AView
{
    use KlarnaTrait;

    public static function getAllowedTargets()
    {
        return ['cart', 'product'];
    }

    public function getHTML(): string
    {
        return $this->getKlarnaPromotionScript();
    }

    protected function getKlarnaPromotionScript(): string
    {
        return sprintf(
            '<script async data-environment="%s" src="%s" data-client-id="%s"></script>',
            Container::getContainer()->get('klarna.configuration')->getMode(),
            Container::getContainer()->getParameter('klarna.url.promotion'),
            Container::getContainer()->getParameter('klarna.settings.promotion.clientId'),
        );
    }

    protected function isVisible(): bool
    {
        return parent::isVisible()
            && $this->isVisibleSnippet()
            && Container::getContainer()->get('klarna.configuration')->isEnabled();
    }

    protected function isVisibleSnippet(): bool
    {
        return $this->isVisibleOnCart()
            || $this->isVisibleOnProductPage();
    }

    protected function isVisibleOnCart(): bool
    {
        return (bool) Config::getInstance()->General->klarna_cart_page_snippet;
    }

    protected function isVisibleOnProductPage(): bool
    {
        return (bool) Config::getInstance()->General->klarna_product_page_snippet;
    }

    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/js_options.twig';
    }
}