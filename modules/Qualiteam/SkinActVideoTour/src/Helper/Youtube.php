<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Helper;

/**
 * Helper class YouTube
 */
class Youtube
{
    /**
     * Get youtube video id
     *
     * @param string $video_url
     *
     * @return string
     */
    public static function getYoutubeVideoId(string $video_url): string
    {
        preg_match('/src="([^"]*)"/i', $video_url, $link);

        $link = !empty($link) && !empty($link[1]) ? $link[1] : $video_url;

        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/", $link, $id);

        if (!empty($id) && !empty($id[1])) {
            return $id[1];
        }

        return '';
    }

    /**
     * Get youtube embed video url
     *
     * @param string $youtubeId
     *
     * @return string
     */
    public static function getYoutubeEmbedVideoUrl(string $youtubeId): string
    {
        return sprintf("%s/%s", self::getYoutubeEmbedLink(), $youtubeId);
    }

    /**
     * Get embed youtube link
     *
     * @return string
     */
    protected static function getYoutubeEmbedLink(): string
    {
        return 'https://www.youtube.com/embed';
    }
}