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
* @ListChild (list="product.details.page.info", weight="100")
 */
class ProductKlarnaPromotion extends AView
{
    use KlarnaTrait;

    public function getPurchaseTotal(): string
    {
        $total = (string)$this->getProduct()->getDisplayPrice();
        return str_replace('.', '', $total);
    }

    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/klarna_promotion.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() &&
            Container::getContainer()->get('klarna.configuration')->isEnabled();
    }
}