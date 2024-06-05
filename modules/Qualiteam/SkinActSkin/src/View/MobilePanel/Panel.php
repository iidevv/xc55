<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\MobilePanel;

use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;

/**
 * @ListChild (list="layout.main", weight=500, zone="customer")
 */
class Panel extends AView
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            ['js/mobile-panel.js']
        );
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
            $list = parent::getCSSFiles();
            $list[] = [
                'file'  => 'css/less/mobile-panel.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
            return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/mobile-panel/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isCheckoutLayout();
    }
}