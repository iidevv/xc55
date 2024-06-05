<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\Product\Details\Customer;

use Qualiteam\SkinActMagicImages\Main;
use XCart\Extender\Mapping\Extender as Extender;
use XCart\Extender\Mapping\ListChild;

/**
 * Image
 *
 * @ListChild (list="product.details.page.image.photo", weight="10")
 * @ListChild (list="product.details.quicklook.image", weight="10")
 *
 * @Extender\Mixin
 */
class Image extends \XLite\View\Product\Details\Customer\Image
{
    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        if (static::QUICKLOOK_PAGE === $this->viewListName) {
            return parent::getTemplate();
        }

        $tool = static::getToolObj('SkinActMagicImages');
        if ($tool->params->checkValue('enable-effect', 'Yes', 'product')) {
            if (static::hasProductSpin($this->getProduct())) {
                return Main::getModulePath() . '/templates/image.twig';
            }
        }

        return parent::getTemplate();
    }
}
