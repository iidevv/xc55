<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\Banner\View\Customer;

/**
 * Banner box widget
 */
class DoubleBannerBox extends \QSL\Banner\View\Customer\BannerBox
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        return [];
    }

    /**
     * Get Banner info
     *
     * @return array()
     */
    protected function getBannerInfo()
    {
        if ($this->isMoreThanOneBanner()) {
            $id = 'slideshow' . $this->getBanner()->getLocation() . $this->getBanner()->getId();

            $bannerid = $this->getBanner()->getId();

            $height = 'calc';

            if ($this->getBanner()->getHeight()) {
                if ($this->getBanner()->getWidth()) {
                    $height = $this->getBanner()->getWidth() . ":" . ($this->getBanner()->getHeight() + 2);
                } else {
                    $height = 'container';
                }
            }

            $list = [
                'id' => $id,
                'data-cycle-slides' => 'div.banner_item',
                'data-cycle-pager' => '#navigation_' . $this->getBanner()->getId(),
                'data-cycle-auto-height' => $height,
                'data-cycle-swipe' => 'true',
                'data-cycle-swipe-fx' => 'scrollHorz'
            ];

            $arrows = $this->getBanner()->getArrows();

            $list['data-cycle-next'] = $arrows ? '#banner_next' . $bannerid : '';
            $list['data-cycle-prev'] = $arrows ? '#banner_prev' . $bannerid : '';
        } else {
            $id = 'slideshow' . $this->getBanner()->getLocation() . $this->getBanner()->getId();
            $list = [
                'id' => $id
            ];
        }

        return $list;
    }
}
