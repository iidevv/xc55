<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Module\QSL\Banner\View\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use XCart\Extender\Mapping\Extender;

/**
 * Banner box widget for story builder. Is used as a data provider for vue component
 *
 * @Extender\Depend ("QSL\Banner")
 */
class BannerBoxVueTemplate extends \QSL\Banner\View\Customer\BannerBox
{
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            $this->getDir() . LC_DS . 'panel_parts/banners/file_uploader/style.less',
        ]);
    }

    public function getJSFiles()
    {
        return [];
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/layout_editor';
    }

    protected function getDefaultTemplate()
    {
        return $this->getDir() . LC_DS . 'panel_parts/banners/banner_vue_template.twig';
    }

    /**
     * @return array|ArrayCollection
     */
    protected function getBannerImages()
    {
        $bannersCollection = new ArrayCollection();
        $bannerSlides      = $this->getBanner()->getBannerSlide()->toArray();
        $data              = array_merge($bannerSlides, []);

        usort($data, static function ($a, $b) {
            return $a->getPosition() - $b->getPosition();
        });

        foreach ($data as $key) {
            $bannersCollection[] = $key;
        }

        return $bannersCollection;
    }
}
