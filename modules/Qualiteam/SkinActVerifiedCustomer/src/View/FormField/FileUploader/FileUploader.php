<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\View\FormField\FileUploader;


class FileUploader extends \XLite\View\FormField\FileUploader\Simple
{

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/FileUploaderChanges.js';
        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/FileUploaderChanges.css';
        return $list;
    }

    protected function getFileUploaderWidget()
    {
        return '\Qualiteam\SkinActVerifiedCustomer\View\FileUploader';
    }

    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        $list['data-verification_file'] = 1;
        return $list;
    }

    protected function getDir()
    {
        return '';
    }

    protected function isImage()
    {
        return false;
    }

    protected function getFieldTemplate()
    {
        return 'modules/Qualiteam/SkinActVerifiedCustomer/uploader/file_uploader/multiple.twig';
    }

    protected function getFiles()
    {
        return $this->getValue();
    }
}