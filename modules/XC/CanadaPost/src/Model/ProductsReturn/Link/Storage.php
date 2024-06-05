<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\ProductsReturn\Link;

use Doctrine\ORM\Mapping as ORM;

/**
 * Link's storage
 *
 * @ORM\Entity
 * @ORM\Table  (name="capost_return_link_storage")
 */
class Storage extends \XLite\Model\Base\Storage
{
    // {{{ Associations

    /**
     * Relation to a link
     *
     * @var \XC\CanadaPost\Model\ProductsReturn\Link
     *
     * @ORM\OneToOne   (targetEntity="XC\CanadaPost\Model\ProductsReturn\Link", inversedBy="storage")
     * @ORM\JoinColumn (name="linkId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $link;

    // }}}

    // {{{ Service operations

    /**
     * Set link
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Link $link Link object (OPTIONAL)
     *
     * @return void
     */
    public function setLink(\XC\CanadaPost\Model\ProductsReturn\Link $link = null)
    {
        $this->link = $link;
    }

    /**
     * Assemble path for save into DB
     *
     * @param string $path Path
     *
     * @return string
     */
//    protected function assembleSavePath($path)
//    {
//        return $this->getLink()->getReturn()->getOrder()->getOrderId() . LC_DS . parent::assembleSavePath($path);
//    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
/*
    protected function getStoreFileSystemRoot()
    {
        $path = parent::getStoreFileSystemRoot() . $this->getLink()->getReturn()->getOrder()->getOrderId() . LC_DS;

        \Includes\Utils\FileManager::mkdirRecursive($path);

        return $path;
    }
*/

    // }}}

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Storage
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return Storage
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set mime
     *
     * @param string $mime
     * @return Storage
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
        return $this;
    }

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set storageType
     *
     * @param string $storageType
     * @return Storage
     */
    public function setStorageType($storageType)
    {
        $this->storageType = $storageType;
        return $this;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Storage
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Storage
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get link
     *
     * @return \XC\CanadaPost\Model\ProductsReturn\Link
     */
    public function getLink()
    {
        return $this->link;
    }
}
