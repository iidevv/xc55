<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Controller\Admin;

use XLite\Core\Request;
use XLite\Model\TemporaryFile;
use XLite\View\FileUploader;

/**
 * File upload controller
 */
class Magic360Files extends \XLite\Controller\Admin\Files
{
    /**
     * Return content
     *
     * @param mixed  $file    File
     * @param string $message Message OPTIONAL
     *
     * @return void
     */
    protected function getContent($file, $message = '')
    {
        $headers = $this->getAdditionalHeaders();
        if ($message) {
            $headers['X-Upload-Error'] = $message;
        }
        static::sendHeaders($headers);

        $viewer = new \Qualiteam\SkinActMagicImages\View\FileUploader(
            [
                FileUploader::PARAM_NAME         => Request::getInstance()->name,
                FileUploader::PARAM_MULTIPLE     => Request::getInstance()->multiple,
                FileUploader::PARAM_OBJECT       => $file,
                FileUploader::PARAM_OBJECT_ID    => Request::getInstance()->object_id,
                FileUploader::PARAM_MESSAGE      => $message,
                FileUploader::PARAM_IS_TEMPORARY => true,
                FileUploader::PARAM_MAX_WIDTH    => Request::getInstance()->max_width,
                FileUploader::PARAM_MAX_HEIGHT   => Request::getInstance()->max_height,
                FileUploader::PARAM_IS_IMAGE     => $file instanceof TemporaryFile
                    ? Request::getInstance()->is_image
                    : null,
            ]
        );

        $this->printAJAXOutput($viewer);
        exit(0);
    }
}
