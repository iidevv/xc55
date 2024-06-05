<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image abstract store
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Video extends \XLite\Model\Base\Storage
{
    /**
     * MIME type to extension translation table
     *
     * @var array
     */
    protected static $types = [
        'video/mpeg'      => 'mpeg',
        'video/mp4'       => 'mp4',
        'video/ogg'       => 'ogv',
        'video/quicktime' => 'mov',
        'video/webm'      => 'webm',
        'video/x-ms-wmv'  => 'wmv',
        'video/x-msvideo' => 'avi',
        'video/x-flv'     => 'flv',
        'video/3gpp'      => '3gp',
        'video/3gpp2'     => '3g2',
    ];

    /**
     * Check file is image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return false;
    }
}
