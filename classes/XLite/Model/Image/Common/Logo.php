<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image\Common;

use Doctrine\ORM\Mapping as ORM;

/**
 * Content images file storage
 *
 * @ORM\Entity
 * @ORM\Table  (name="logo_images")
 */
class Logo extends \XLite\Model\Base\Image
{
    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        $webRootPrefix = strpos($this->path, 'public/') !== 0 ? 'public/' : '';

        return $webRootPrefix . $this->path;
    }

    /**
     * Alternative image text
     *
     * @var string
     */
    protected $alt = '';

    /**
     * Set alt
     *
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Check - image is exists in DB or not
     *
     * @return boolean
     */
    public function isExists()
    {
        return true;
    }

    /**
     * Resize on view
     *
     * @return boolean
     */
    protected function isUseDynamicImageResizing()
    {
        return $this->getMime() ? !array_key_exists($this->getMime(), static::$extendedTypes) : true;
    }
}
