<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFullWidthImages\View\FormField\FileUploader;

use Qualiteam\SkinActFullWidthImages\Module;

class Image extends \XLite\View\FormField\FileUploader\Image
{
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            Module::getModulePath() . 'uploader.js'
        ]);
    }

    protected function getDir()
    {
        return '';
    }

    protected function getFieldTemplate()
    {
        return Module::getModulePath() . 'multiple.twig';
    }
}