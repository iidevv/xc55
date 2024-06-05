<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\View\FormField\FileUploader;


class PhotoFileUploader extends \XLite\View\FormField\FileUploader\Simple
{

    protected function getFileUploaderWidget()
    {
        return '\Qualiteam\SkinActCustomerReviews\View\FileUploader';
    }

    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        $list['data-review_file'] = 1;
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
        return 'modules/Qualiteam/SkinActCustomerReviews/uploader/file_uploader/multiple.twig';
    }

    protected function getFiles()
    {
        return $this->getValue();
    }
}