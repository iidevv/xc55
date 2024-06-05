<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View;

class FilterBySubstring extends \XLite\View\AView
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['brands']);
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/ShopByBrand/filter/filter_by_substring';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return (bool) \XLite\Core\Config::getInstance()->QSL->ShopByBrand->show_filter_by_substring;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    protected function getBrandNameSubstring()
    {
        return ($this->getFirstLetter() !== null) || ($this->getSubstring() === null)
            ? ''
            : $this->getSubstring();
    }
}
