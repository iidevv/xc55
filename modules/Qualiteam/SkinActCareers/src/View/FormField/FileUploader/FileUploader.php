<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\FormField\FileUploader;


class FileUploader extends \XLite\View\FormField\FileUploader\Simple
{

    protected function getFileUploaderWidget()
    {
        return '\Qualiteam\SkinActCareers\View\FileUploader';
    }

    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        $list['data-interview'] = 1;
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
        return 'modules/Qualiteam/SkinActCareers/uploader/multiple.twig';
    }

}