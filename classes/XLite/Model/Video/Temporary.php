<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Video;

use Doctrine\ORM\Mapping as ORM;
use XLite\Core\URLManager;

/**
 * Content images file storage
 *
 * @ORM\Entity
 * @ORM\Table  (name="temporary_videos")
 */
class Temporary extends \XLite\Model\Base\Video
{
    /**
     * Check - file is image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getURL()
    {
        return is_string(parent::getURL())
            ? URLManager::addTimestampToUrl(parent::getURL())
            : null;
    }

    /**
     * Renew properties by path
     *
     * @param string $path Path
     *
     * @return bool
     */
    protected function renewByPath($path)
    {
        parent::renewByPath($path);

        return true;
    }
}
