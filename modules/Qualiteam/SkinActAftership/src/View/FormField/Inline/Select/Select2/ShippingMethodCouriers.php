<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\FormField\Inline\Select\Select2;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;

/**
 * Get selector
 */
class ShippingMethodCouriers extends \XLite\View\FormField\Inline\Base\Single
{
    use AftershipTrait;

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass(): string
    {
        return \Qualiteam\SkinActAftership\View\FormField\Select\Select2\ShippingMethodCouriers::class;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/order/inline/select/style.less';

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getModulePath() . '/order/inline/select/script.js';

        return $list;
    }
}