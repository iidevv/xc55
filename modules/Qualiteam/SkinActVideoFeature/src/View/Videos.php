<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View;

use Qualiteam\SkinActVideoFeature\Model\EducationalVideo;
use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeObject;

/**
 * Videos list
 */
class Videos extends \XLite\View\AView
{
    const PARAM_VIDEOS = 'videos';
    const PARAM_SHUFFLE = 'isShuffle';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_VIDEOS => new TypeObject(
                'Videos',
                null,
                false,
                EducationalVideo::class
            )
        ];

        $this->widgetParams += [
            static::PARAM_SHUFFLE => new TypeBool(
                'isShuffle',
                true
            )
        ];
    }

    protected function getEducationalVideos()
    {
        return $this->getParam(static::PARAM_VIDEOS);
    }

    protected function isShuffleVideos()
    {
        return $this->getParam(static::PARAM_SHUFFLE);
    }

    protected function getVideos()
    {
        $videos = $this->getEducationalVideos();

        if (!is_array($videos)) {
            $items = [];
            foreach ($videos as $video) {
                if ($video->getVideo()->getEnabled()) {
                    $items[] = $video->getVideo();
                }
            }

            $videos = $items;
        }

        return $this->isShuffleVideos() ? $this->getShuffleVideos($videos) : $videos;
    }

    protected function getShuffleVideos(array $videos): array
    {
        shuffle($videos);
        return $videos;
    }

    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActVideoFeature/items_list/educational_videos';
    }

    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/videos.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActVideoFeature/css/less/videos.less';
        return $list;
    }
}