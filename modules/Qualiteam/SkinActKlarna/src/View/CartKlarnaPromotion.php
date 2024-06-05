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
 * @ListChild (list="cart.panel.box", weight="1000")
 */
class CartKlarnaPromotion extends AView
{
    use KlarnaTrait;

    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/klarna_promotion.twig';
    }

    public function getPurchaseTotal(): string
    {
        $total = (string)$this->getCart()->getTotal();
        return str_replace('.', '', $total);
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/cart/klarna_promotion.less';

        return $list;
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && Container::getContainer()->get('klarna.configuration')->isEnabled();
    }
}