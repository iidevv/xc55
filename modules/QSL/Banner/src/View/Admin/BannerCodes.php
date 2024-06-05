<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Admin;

/**
 * Banners list (widget)
 *
 */
class BannerCodes extends \XLite\View\Dialog
{
    /**
     * Banners list cache
     *
     * @var   \Doctrine\ORM\PersistentCollection|null
     */
    protected $bannerCodesCache = null;

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
     * Get banner contents
     *
     * @return \Doctrine\ORM\PersistentCollection|void
     */
    public function getBannerContents()
    {

        return $this->getBanner()->getContents();
    }

    /**
     * @return int
     */
    public function getBannerContentsCount()
    {
        return count($this->getBannerContents());
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
        return $this->getDir() . LC_DS . 'banner_codes.twig';
    }
}
