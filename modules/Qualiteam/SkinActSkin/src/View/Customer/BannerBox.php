<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use XCart\Extender\Mapping\Extender;

/**
 * Category widget
 *
 * @Extender\Mixin
 */
class BannerBox extends \QSL\Banner\View\Customer\BannerBox
{
    /**
     * @return array|ArrayCollection
     */
    protected function getBannerData()
    {
        $bannersCollection = new ArrayCollection();
        $bannerSlides      = $this->getBanner()->getBannerSlide()->toArray();
        $contents          = $this->getBanner()->getContents()->toArray();

        usort($bannerSlides, static function ($a, $b) {
            return $a->getPosition() - $b->getPosition();
        });

        usort($contents, static function ($a, $b) {
            return $a->getPosition() - $b->getPosition();
        });

        // We've changed just this order of merging banners data.
        // HTML contents should be run first
        $data = array_merge($contents, $bannerSlides);

        foreach ($data as $key) {
            if ($key->getEventCell() == 'C') { //HTML-banners have no "enabled" options
                $bannersCollection[] = $key;
            } elseif ($key->getEnabled()) { //Image banners have "enabled" options
                $bannersCollection[] = $key;
            }
        }

        return $bannersCollection;
    }
}
