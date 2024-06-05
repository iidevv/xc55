<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Customer;

use Doctrine\Common\Collections\ArrayCollection;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\View\CacheableTrait;

/**
 * Banner box widget
 *
 */
class BannerBox extends \XLite\View\AView
{
    use ExecuteCachedTrait;
    use CacheableTrait;

    /**
     * Widget parameter names
     */
    public const PARAM_BANNER_ID = 'banner_id';

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
     * @return array
     */
    public function getCSSFiles()
    {
        $result = parent::getCSSFiles();

        $result[] = [
            'file'  => $this->getDir() . LC_DS . 'style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $result;
    }


    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . LC_DS . 'lib' . LC_DS . 'jquery.cycle2.min.js';
        $list[] = $this->getDir() . LC_DS . 'lib' . LC_DS . 'jquery.cycle2.flip.js';
        $list[] = $this->getDir() . LC_DS . 'lib' . LC_DS . 'jquery.cycle2.swipe.min.js';
        $list[] = $this->getDir() . LC_DS . 'lib' . LC_DS . 'jquery.cycle2.scrollVert.js';
        $list[] = $this->getDir() . LC_DS . 'lib' . LC_DS . 'jquery.cycle2.tile.js';
        $list[] = $this->getDir() . LC_DS . 'script.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . LC_DS . 'body.twig';
    }

    /**
     * @return mixed
     */
    protected function getBanner()
    {
        return $this->widgetParams[self::PARAM_BANNER_ID]->getObject();
    }


    /**
     * @return array|ArrayCollection
     */
    protected function getBannerData()
    {
        $bannersCollection = new ArrayCollection();
        $bannerSlides      = $this->getBanner()->getBannerSlide()->toArray();
        $contents          = $this->getBanner()->getContents()->toArray();
        $data              = array_merge($bannerSlides, $contents);

        usort($data, static function ($a, $b) {
            return $a->getPosition() - $b->getPosition();
        });

        foreach ($data as $key) {
            if ($key->getEventCell() == 'C') { //HTML-banners have no "enabled" options
                $bannersCollection[] = $key;
            } elseif ($key->getEnabled()) { //Image banners have "enabled" options
                $bannersCollection[] = $key;
            }
        }

        return $bannersCollection;
    }


    /**
     * Define widget parameters
     *
     * @return integer
     */
    protected function isMoreThanOneBanner()
    {
        return count($this->getBannerData()) > 1;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_BANNER_ID => new \QSL\Banner\Model\WidgetParam\Banner('Banner Id', 0, true)
        ];
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

            $class = 'cycle-slideshow';

            $effect = $this->getBanner()->getEffect();

            $delay  = $this->getBanner()->getDelay();

            $timeout  = $this->getBanner()->getTimeout();

            $bannerid = $this->getBanner()->getId();

            $height = 'calc';

            if ($this->getBanner()->getHeight()) {
                if ($this->getBanner()->getWidth()) {
                    $height = $this->getBanner()->getWidth() . ":" . ($this->getBanner()->getHeight() + 2);
                } else {
                    $height = 'container';
                }
            }

            $tileVertical = "true";

            switch ($effect) {
                case 'tileSlideHorz':
                    $tileVertical = "false";
                    $effect = 'tileSlide';
                    break;
                case 'tileBlindHorz':
                    $tileVertical = "false";
                    $effect = 'tileBlind';
                    break;
                case 'tileSlideVert':
                    $effect = 'tileSlide';
                    break;
                case 'tileBlindVert':
                    $effect = 'tileBlind';
                    break;
                default:
                    break;
            }

            $list = [
                'id' => $id,
                'class' => $class,
                'data-cycle-fx' => $effect,
                'data-cycle-speed'  => $delay * 1000,
                'data-cycle-timeout'  => $timeout * 1000,
                'data-cycle-tile-vertical' => $tileVertical,
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

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache TTL (seconds)
     *
     * @return integer
     */
    protected function getCacheTTL()
    {
        return 3600;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = $this->widgetParams[self::PARAM_BANNER_ID];

        return $list;
    }
}
