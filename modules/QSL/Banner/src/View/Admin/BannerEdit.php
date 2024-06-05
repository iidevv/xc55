<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Admin;

/**
 * Banner add / modify page (widget)
 *
 * @see   ____class_see____
 * @since 1.0.0
 *
 */
class BannerEdit extends \XLite\View\Dialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'banner_edit';

        return $list;
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        // Remove saved to session data
        $this->clearSavedData();
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/Banner';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getBody()
    {
        return $this->getDir() . LC_DS . 'banner_edit.twig';
    }

    /**
     * Remove saved data
     *
     * @return void
     */
    protected function clearSavedData()
    {
        \XLite\Core\Session::getInstance()->bannerSavedData = null;
    }
}
