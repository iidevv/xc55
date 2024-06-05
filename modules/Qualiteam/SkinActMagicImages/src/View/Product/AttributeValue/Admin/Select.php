<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View\Product\AttributeValue\Admin;

use Qualiteam\SkinActMagicImages\Main;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class select
 * @Extender\After("Qualiteam\SkinActColorSwatchesFeature")
 * @Extender\Mixin
 */
class Select extends \XLite\View\Product\AttributeValue\Admin\Select
{
    /**
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return Main::getModulePath() . '/product/manage_attribute_value/select/body.twig';
    }
}