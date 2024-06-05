<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Model\Repo;


class MessageImage extends \XLite\Model\Repo\Base\Image
{
    public function getStorageName()
    {
        return 'message_image';
    }

//    public function getFileSystemRoot()
//    {
//        return parent::getFileSystemRoot();
//    }
}