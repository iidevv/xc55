<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Controller\Admin;

/**
 * Magic 360 page controller (Product modify section)
 */

use Qualiteam\SkinActMagicImages\Main;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    /**
     * Get pages
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        if (!$this->isNew()) {
            $list['magic360'] = static::t('SkinActMagicImages magic 360 images');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if (!$this->isNew()) {
            $list['magic360'] = Main::getModulePath() . '/product/tab.twig';
        }

        return $list;
    }
}
