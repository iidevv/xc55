<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\View;

use XLite\Core\Request;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class FileUploader extends \XLite\View\FileUploader
{

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCustomerReviews/ReviewVideoFileStaticPreview.css';
        return $list;
    }

    protected function fileIsVideo()
    {
        if ($this->getObject()) {
            $ext = pathinfo($this->getObject()->getFileName(), PATHINFO_EXTENSION);
            $mime = \GuzzleHttp\Psr7\MimeType::fromExtension($ext);
            return $mime && strpos($mime, 'video/') === 0;
        }

        return false;
    }

    protected function fileIsImage()
    {
        if ($this->getObject()) {
            $ext = pathinfo($this->getObject()->getFileName(), PATHINFO_EXTENSION);
            $mime = \GuzzleHttp\Psr7\MimeType::fromExtension($ext);
            return $mime && strpos($mime, 'image/') === 0;
        }

        return false;
    }

    protected function fileIsDocument()
    {
        return $this->getObject() && (!$this->fileIsImage() && !$this->fileIsVideo());
    }

    protected function isImage()
    {
        return !$this->fileIsDocument() && !$this->fileIsVideo();
    }

    protected function hasView()
    {
        return parent::hasView() || $this->fileIsVideo();
    }

    protected function getPreview()
    {
        $preview = parent::getPreview();

        if ((\XLite::getController()->getTarget() === 'review'
                || Request::getInstance()->review_file > 0)
            && $preview === ''
        ) {

            if ($this->fileIsVideo()) {

                $content = (new \Qualiteam\SkinActCustomerReviews\View\ReviewVideoFileStaticPreview())->getContent();

                return $content;
            }

            if ($this->fileIsImage()) {

                $viewer = new \XLite\View\Image([
                    'image' => $this->getObject(),
                    'maxWidth' => $this->getParam(static::PARAM_MAX_WIDTH),
                    'maxHeight' => $this->getParam(static::PARAM_MAX_HEIGHT),
                    'alt' => '',
                    'centerImage' => true,
                    'useBlurBg' => false,
                    'useTimestamp' => $this->isFavicon(),
                    'useCache' => false
                ]);

                return $viewer->getContent();
            }

            return $this->getObject() ? $this->getObject()->getFileName() : '';
        }

        return $preview;
    }

}