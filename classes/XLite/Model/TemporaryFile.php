<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;
use Includes\Utils\URLManager;

/**
 * Temporary file store
 *
 * @ORM\Entity
 * @ORM\Table  (name="temporary_files")
 */
class TemporaryFile extends \XLite\Model\Base\Image
{
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
     * @return TemporaryFile
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
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
     * Check - file is image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return 0 < $this->getWidth();
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
        $this->includeFilenameInHash = false;
        parent::renewByPath($path);

        return true;
    }
}
