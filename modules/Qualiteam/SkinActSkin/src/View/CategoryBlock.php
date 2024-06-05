<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View;

use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;

/**
 * Category widget
 *
 * @ListChild ("layout.main.center.top", zone="customer", weight="100")
 */
class CategoryBlock extends AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $targets = parent::getAllowedTargets();

        $targets[] = 'category';

        return $targets;
    }

    protected function getDefaultTemplate()
    {
        return 'category_block/body.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible();
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'css/less/category-block.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        return $list;
    }

    public function getBlockBg() {
        return $this->getCategory()->getBgColor() ?: '';
    }
}
