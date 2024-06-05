<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Module\XC\ThemeTweaker\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use QSL\Banner\Model\Banner;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
class LayoutEdit extends \XC\ThemeTweaker\Controller\Admin\LayoutEdit
{
    /**
     * Array index which is used by layout editor, may contain banner/slide/image ids
     */
    public const BANNER_SLIDE_IMAGE_KEY = 'banner_slide_image_';

    /**
     * Applies layout editor changeset to the view lists repo
     */
    protected function doActionResetLayout()
    {
        parent::doActionResetLayout();
        if (Request::getInstance()->preset) {
            $controller = new \QSL\Banner\Controller\Admin\CacheManagement();
            $controller->rebuildViewLists();
        }
    }

    /**
     * Applies layout editor changes. Save banners
     */
    protected function doActionApplyChanges()
    {
        parent::doActionApplyChanges();

        $postData = Request::getInstance()->getData();
        $postData = array_filter(
            $postData,
            static fn($item, $key) => strpos($key, static::BANNER_SLIDE_IMAGE_KEY) === 0,
            ARRAY_FILTER_USE_BOTH
        );

        $shouldReload   = false;
        $bannersAdded   = false;
        $bannersDeleted = false;

        // remove banners
        foreach ($postData as $origBannerId => $bannerPostData) {
            if (isset($bannerPostData['delete_banner'])) {
                $bannerId       = str_replace(static::BANNER_SLIDE_IMAGE_KEY, '', $origBannerId);
                $bannerToDelete = \XLite\Core\Database::getRepo(Banner::class)->find($bannerId);
                \XLite\Core\Database::getEM()->persist($bannerToDelete);
                $bannerToDelete->delete();
                unset($postData[$origBannerId]);
                $bannersDeleted = $shouldReload = true;
            }
        }

        // change banner location
        $mapsLocation = [
            'center'             => 'StandardTop',
            'center.bottom'      => 'StandardBottom',
            'layout.bottom.wide' => 'WideBottom',
            'sidebar.first'      => 'MainColumn',
            'sidebar.second'     => 'SecondaryColumn',
            'layout.top.wide'    => 'WideTop',
        ];
        foreach ($postData as $origBannerId => $bannerPostData) {
            if (isset($bannerPostData['change_banner_type'])) {
                $location = $mapsLocation[$bannerPostData['change_banner_type']] ?? '';

                if ($location) {
                    $bannerId       = str_replace(static::BANNER_SLIDE_IMAGE_KEY, '', $origBannerId);
                    $bannerToChange = \XLite\Core\Database::getRepo(Banner::class)->find($bannerId);

                    if ($bannerToChange) {
                        $bannerToChange->setLocation($location);
                        \XLite\Core\Database::getEM()->persist($bannerToChange);
                        $bannersDeleted = $shouldReload = true;
                    }
                    unset($postData[$origBannerId]['change_banner_type']);
                }
            }
        }

        // add banners
        foreach ($postData as $bannerId => $bannerPostData) {
            if (isset($bannerPostData['add_banner'])) {
                if (!isset($maxId)) {
                    $maxId = (\XLite\Core\Database::getRepo(Banner::class)->getLastId() ?: 0);
                }
                $maxId += 1;

                $bannerToAdd = new \QSL\Banner\Model\Banner();
                $bannerToAdd->setTitle('Banner #' . $maxId);
                $bannerToAdd->setLocation($this->getLocationBasedOnPreset($bannerPostData['add_banner']));
                $bannerToAdd->setEffect('fade');
                $maxBannerOrderBy = $maxBannerOrderBy ?? (\XLite\Core\Database::getRepo(Banner::class)->getLastOrderBy() ?: 0);
                if ((int) $maxBannerOrderBy > 0) {
                    $maxBannerOrderBy += 10;
                    $bannerToAdd->setPosition($maxBannerOrderBy);
                }
                \XLite\Core\Database::getEM()->persist($bannerToAdd);
                unset($postData[$bannerId]);
                $shouldReload = true;
                $bannersAdded = true;
            }
        }

        // add new slides/images
        foreach ($postData as $htmlSlideId => $bannerPostData) {
            $bannerPostData = array_pop($bannerPostData) ?: [];
            if (!is_array($bannerPostData)) {
                continue;
            }
            foreach ($bannerPostData as $newSlideData) {
                if (
                    isset($newSlideData['alt'])
                    && isset($newSlideData['is_delete'])
                    && !empty($newSlideData['temp_id'])
                ) {
                    $bannerId = str_replace(static::BANNER_SLIDE_IMAGE_KEY, '', $htmlSlideId);
                    // this is a new slide to add to existing banner. $bannerId is bannerId now
                    $objBannerSlide = new \QSL\Banner\Model\BannerSlide();

                    $maxOrderBy = $maxOrderBy
                        ?? (\XLite\Core\Database::getRepo('QSL\Banner\Model\BannerSlide')->getLastOrderBy() ?: 0);
                    if ((int) $maxOrderBy > 0) {
                        $maxOrderBy += 10;
                        $objBannerSlide->setPosition($maxOrderBy);
                    }

                    if (!isset($banners[$bannerId])) {
                        $banners[$bannerId] = \XLite\Core\Database::getRepo(Banner::class)->find($bannerId);
                    }

                    $objBannerSlide->setBanner($banners[$bannerId]);
                    $objBannerSlide->processFiles('image', $newSlideData);

                    unset($postData[$htmlSlideId]);
                    $shouldReload = $shouldFlush = true;
                }
            }
        }

        // modify slides
        foreach ($postData as $htmlSlideId => $bannerPostData) {
            // Only banner slides should be here
            if (
                ($bannerSlideId = str_replace(static::BANNER_SLIDE_IMAGE_KEY, '', $htmlSlideId))
                && ($objBannerSlide = \XLite\Core\Database::getRepo('QSL\Banner\Model\BannerSlide')->find($bannerSlideId))
                && $this->updateDeleteBannerSlide($bannerPostData, $objBannerSlide)
            ) {
                $shouldReload = true;
            }
        }

        if ($bannersAdded || $bannersDeleted) {
            $controller = new \QSL\Banner\Controller\Admin\CacheManagement();
            $controller->rebuildViewLists();

            \XLite\Core\Database::getEM()->flush();
        }

        if ($shouldReload) {
            if (!empty($shouldFlush)) {
                \XLite\Core\Database::getEM()->flush();
            }
            $this->setReturnURL($this->getReturnURL());
            $this->setHardRedirect(true);
            // else block is not used to not overwrite parent behavior
        }
    }

    /**
     * @param                           $bannerPostData
     * @param \XLite\Model\AEntity|null $objBannerSlide
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    protected function updateDeleteBannerSlide($bannerPostData, $objBannerSlide): bool
    {
        $isChanged = false;
        $data      = $bannerPostData;

        $isDeleteSlide = isset($data['is_delete_slide']) ? $data['is_delete_slide'] === 'true' : false;
        if ($isDeleteSlide) {
            \XLite\Core\Database::getEM()->remove($objBannerSlide);
            \XLite\Core\Database::getEM()->flush();

            return true;
        }

        if (isset($data['alt']) && $objBannerSlide->getImage()) {
            $objBannerSlide->getImage()->setAlt($data['alt']);
            $isChanged = true;
        }

        if (isset($data['link'])) {
            $objBannerSlide->setLink($data['link']);
            $isChanged = true;
        }

        if (isset($data['disableSlide'])) {
            $objBannerSlide->setEnabled($data['disableSlide'] ? 0 : 1);
            $isChanged = true;
        }

        if (isset($data['slidePosition'])) {
            $objBannerSlide->setPosition((int) $data['slidePosition']);
            $isChanged = true;
        }

        if (!empty($data['temp_id']) || !empty($data['is_delete'])) {
            if (!empty($data['is_delete']) && $data['is_delete'] === 'true') {
                $data['delete'] = true;
            }
            $isChanged = true;
            $objBannerSlide->processFiles('image', $data);
        }

        if ($isChanged) {
            \XLite\Core\Database::getEM()->persist($objBannerSlide);
            \XLite\Core\Database::getEM()->flush();
        }

        return $isChanged;
    }

    protected function getLocationBasedOnPreset($presetString)
    {
        // see skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/layout_groups.js
        $nameMap = [
            'one'   => ['WideTop', 'StandardTop', 'WideBottom'],
            'left'  => ['WideTop', 'MainColumn', 'StandardTop', 'WideBottom'],
            'right' => ['WideTop', 'StandardTop', 'SecondaryColumn', 'WideBottom'],
            'three' => ['WideTop', 'MainColumn', 'StandardTop', 'SecondaryColumn', 'WideBottom'],
        ];

        $parts = explode('.', $presetString) + ['', '', 0];

        return $nameMap[$parts[0]][$parts[2]] ?? 'StandardTop';
    }
}
