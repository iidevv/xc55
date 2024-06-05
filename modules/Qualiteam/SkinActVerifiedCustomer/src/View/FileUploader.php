<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActVerifiedCustomer\View;

use XLite\Core\Request;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class FileUploader extends \XLite\View\FileUploader
{

    protected function hasView()
    {
        return parent::hasView() || $this->fileIsImage();
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

    protected function fileIsDocument()
    {
        return $this->getObject() && (!$this->fileIsImage() && !$this->fileIsVideo());
    }

    protected function isDownloadable()
    {
        return $this->getObject() && $this->fileIsDocument();
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

    protected function getPreview()
    {
        $preview = parent::getPreview();

        if ((\XLite::getController()->getTarget() === 'profile'
                || Request::getInstance()->verification_file > 0)
            && $preview === ''
        ) {

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

    protected function getLink()
    {
        $link = parent::getLink();

        if ((\XLite::getController()->getTarget() === 'profile'
                || Request::getInstance()->verification_file > 0)
            && $link === '#'
        ) {
            return $this->getObject() ? $this->getObject()->getFrontURL() : '#';
        }

        return $link;
    }

}