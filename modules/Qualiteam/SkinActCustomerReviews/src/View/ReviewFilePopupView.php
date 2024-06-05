<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\View;


use XLite\Core\Database;
use XLite\Core\Request;

class ReviewFilePopupView extends \XLite\View\AView
{

    protected function getFile()
    {
        static $file = null;

        if ($file === null) {
            $file = Database::getRepo('\Qualiteam\SkinActCustomerReviews\Model\ReviewFile')
                ->find((int)Request::getInstance()->fileId);
        }

        return $file;
    }

    protected function isVideo()
    {
        $file = $this->getFile();

        if ($file) {
            $ext = pathinfo($file->getFileName(), PATHINFO_EXTENSION);
            $mime = \GuzzleHttp\Psr7\MimeType::fromExtension($ext);
            return $mime && strpos($mime, 'video/') === 0;
        }

        return false;
    }

    protected function getFileUrl()
    {
        $file = $this->getFile();

        if ($file) {
            return $file->getFrontURL();
        }

        return '';
    }

    protected function isImage()
    {
        $file = $this->getFile();

        if ($file) {
            $ext = pathinfo($file->getFileName(), PATHINFO_EXTENSION);
            $mime = \GuzzleHttp\Psr7\MimeType::fromExtension($ext);
            return $mime && strpos($mime, 'image/') === 0;
        }

        return false;
    }


    public static function getAllowedTargets()
    {
        return ['review_file'];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCustomerReviews/ReviewFilePopupView.twig';
    }
}