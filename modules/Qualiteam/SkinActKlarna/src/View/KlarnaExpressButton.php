<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\View;

use Qualiteam\SkinActKlarna\Traits\KlarnaTrait;
use XCart\Container;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\View\AView;

/**
 * @ListChild (list="cart.panel.totals", weight="900")
 */
class KlarnaExpressButton extends AView
{
    use KlarnaTrait;

    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/cart/klarna_express_button.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/cart/klarna_express_button.less';

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getModulePath() . '/cart/klarna_express_button.js';

        return $list;
    }

    public function isVisible()
    {
        return parent::isVisible()
            && Auth::getInstance()->isLogged()
            && Container::getContainer()->get('klarna.configuration')->isEnabled();
    }
}