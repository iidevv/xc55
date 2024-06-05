<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image;

use Doctrine\ORM\Mapping as ORM;

/**
 * Content images file storage
 *
 * @ORM\Entity
 * @ORM\Table  (name="content_images")
 */
class Content extends \XLite\Model\Base\Image
{
    /**
     * Check - file is image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return 0 < $this->getWidth();
    }
}
