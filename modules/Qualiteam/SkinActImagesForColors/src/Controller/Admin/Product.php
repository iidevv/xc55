<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\Controller\Admin;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    protected function hasSwatches()
    {
        foreach ($this->getProduct()->getEditableAttributes() as $attribute) {
            if ($attribute->isColorSwatchesAttribute()) {
                return true;
            }
        }
        return false;
    }

    public function getPages()
    {
        $pages = parent::getPages();

        if (!$this->isNew() && $this->hasSwatches()) {
            $pages['images_for_colors'] = static::t('SkinActImagesForColors images_for_colors tab');
        }

        return $pages;
    }

    protected function getPageTemplates()
    {
        $templates = parent::getPageTemplates();

        if (!$this->isNew() && $this->hasSwatches()) {
            $templates += [
                'images_for_colors' => 'modules/Qualiteam/SkinActImagesForColors/images_for_colors.twig',
            ];
        }

        return $templates;
    }

}