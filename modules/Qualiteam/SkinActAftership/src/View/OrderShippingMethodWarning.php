<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="order.shipping.method", weight="200", zone="admin")
 */
class OrderShippingMethodWarning extends \XLite\View\AView
{
    use AftershipTrait;

    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'order';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/order/shipping_method_warning_message.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/order/shipping_method_warning_message.less';

        return $list;
    }

    /**
     * Get warning text
     *
     * @return string
     */
    protected function getWarningText(): string
    {
        return static::t('SkinActAftership warning message');
    }
}