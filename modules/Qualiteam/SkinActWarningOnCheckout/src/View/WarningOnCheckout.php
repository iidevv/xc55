<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWarningOnCheckout\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild(list="checkout.review.selected.placeOrder", weight="395")
 */
class WarningOnCheckout extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActWarningOnCheckout/body.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActWarningOnCheckout/style.less';

        return $list;
    }

    public function isVisible()
    {
        return parent::isVisible()
            && !empty(\XLite\Core\Config::getInstance()->Qualiteam->SkinActWarningOnCheckout->url_warning);
    }

    public function getWarningText()
    {
        return static::t('SkinActWarningOnCheckout prop 65 warning for california residents', [
            'URL' => \XLite\Core\Config::getInstance()->Qualiteam->SkinActWarningOnCheckout->url_warning,
        ]);
    }
}