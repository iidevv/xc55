<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;


/**
 * @Extender\Mixin
 */
class TemporaryFile extends \XLite\Model\TemporaryFile
{
    public function renewMimesForReview()
    {
        $images = [
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
        ];

        $videos = [
            'video/mp4' => 'mp4',
            'video/ogg' => 'ogv',
            'video/webm' => 'webm',
        ];

        $mimes = [];

        if (Config::getInstance()->XC->Reviews->allow_upload_photos) {
            $mimes = array_merge($mimes, $images);
        }

        if (Config::getInstance()->XC->Reviews->allow_upload_videos) {
            $mimes = array_merge($mimes, $videos);
        }


        static::$types = $mimes;
    }
}