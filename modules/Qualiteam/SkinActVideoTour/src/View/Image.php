<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View;

use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Model\WidgetParam\TypeString;

/**
 * Class image
 * @Extender\Mixin
 */
class Image extends \XLite\View\Image
{
    use VideoTourTrait;

    /**
     * @var string
     */
    public const PARAM_YOUTUBE_ID = 'youtubeId';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_YOUTUBE_ID => new TypeString('Youtube Id', ''),
        ];
    }

    /**
     * Get youtube id
     *
     * @return string
     */
    public function getYoutubeId()
    {
        return $this->getParam(self::PARAM_YOUTUBE_ID);
    }

    /**
     * Get youtube preview image url
     *
     * @return string
     */
    public function getYoutubePreviewImageUrl()
    {
        return sprintf("https://img.youtube.com/vi/%s/mqdefault.jpg",
            $this->getYoutubeId()
        );
    }

    /**
     * Get image URL
     *
     * @return string
     */
    public function getURL()
    {
        $url = parent::getURL();

        if (
            $url
            && $this->useDefaultImage
            && $this->getYoutubeId()
        ) {
            $url = $this->getYoutubePreviewImageUrl();
            $this->useDefaultImage = false;
        }

        return $url;
    }
}