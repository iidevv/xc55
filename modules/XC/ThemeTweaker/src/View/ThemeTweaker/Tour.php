<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\ThemeTweaker;

use XCart\Extender\Mapping\ListChild;

/**
 * Panel tour widget
 *
 * @ListChild(list="themetweaker-tour", weight="100")
 */
class Tour extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker_panel/tour';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/tour.twig';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/tiny-tour/tour.min.js';
        $list[] = $this->getDir() . '/tour.js';

        return $list;
    }
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/tiny-tour/tour.min.css';
        $list[] = [
            'file'  => $this->getDir() . '/tour.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        // Not implemented for storefront builder, see ECOM-274
        return false
            && parent::isVisible()
            && \XC\ThemeTweaker\Core\ThemeTweaker::getInstance()->isInLayoutMode()
            && !$this->isTourShown();
    }

    /**
     * Check - is product tour shown already
     *
     * @return boolean
     */
    protected function isTourShown()
    {
        return \XLite\Core\Config::getInstance()->XC->ThemeTweaker->tour_shown;
    }
}
