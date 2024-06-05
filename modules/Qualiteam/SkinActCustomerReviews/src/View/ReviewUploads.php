<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\View;


use XLite\Core\Config;

class ReviewUploads extends \XLite\View\AView
{

    protected function isVisible()
    {
        return parent::isVisible()
            && (Config::getInstance()->XC->Reviews->allow_upload_photos || Config::getInstance()->XC->Reviews->allow_upload_videos) ;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCustomerReviews/ReviewUploads.css';
        return $list;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCustomerReviews/ReviewUploads.twig';
    }

    protected function getFiles()
    {
        return $this->files;
    }

    /**
     * @param \Qualiteam\SkinActCustomerReviews\Model\ReviewFile $file
     */
    protected function getFileView($file)
    {
        $ext = pathinfo($file->getFileName(), PATHINFO_EXTENSION);
        $mime = \GuzzleHttp\Psr7\MimeType::fromExtension($ext);

        $isImage = $mime && strpos($mime, 'image/') === 0;
        $isVideo = $mime && strpos($mime, 'video/') === 0;

        if ($isImage) {

            $viewer = new \XLite\View\Image([
                'image' => $file,
                'maxWidth' => 100,
                'maxHeight' => 100,
                'alt' => '',
                'centerImage' => true,
                'useBlurBg' => false,
                'useTimestamp' => false,
                'useCache' => false
            ]);

            $widget = new \Qualiteam\SkinActCustomerReviews\View\Button\ReviewFilePopupButton([
                'content' => $viewer->getContent(),
                'file' => $file
            ]);

            return $widget->getContent();
        }

        if ($isVideo) {

            $content = (new \Qualiteam\SkinActCustomerReviews\View\ReviewVideoFileStaticPreview())->getContent();

            $widget = new \Qualiteam\SkinActCustomerReviews\View\Button\ReviewFilePopupButton([
                'content' => $content,
                'file' => $file
            ]);

            return $widget->getContent();
        }

        return '';
    }

}