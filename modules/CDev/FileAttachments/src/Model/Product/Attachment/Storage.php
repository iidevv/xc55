<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\Model\Product\Attachment;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product attchament's storage
 *
 * @ORM\Entity
 * @ORM\Table  (name="product_attachment_storages")
 */
class Storage extends \XLite\Model\Base\Storage
{
    // {{{ Associations

    /**
     * Relation to a attachment
     *
     * @var \CDev\FileAttachments\Model\Product\Attachment
     *
     * @ORM\OneToOne  (targetEntity="CDev\FileAttachments\Model\Product\Attachment", inversedBy="storage")
     * @ORM\JoinColumn (name="attachment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attachment;

    // }}}

    // {{{ Service operations

    /**
     * Assemble path for save into DB
     *
     * @param string $path Path
     *
     * @return string
     */
    protected function assembleSavePath($path)
    {
        return $this->getAttachment()->getProduct()->getProductId() . LC_DS . parent::assembleSavePath($path);
    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getStoreFileSystemRoot()
    {
        $path = parent::getStoreFileSystemRoot() . $this->getAttachment()->getProduct()->getProductId() . LC_DS;
        \Includes\Utils\FileManager::mkdirRecursive($path);

        return $path;
    }

    /**
     * Clone for attachment
     *
     * @param \CDev\FileAttachments\Model\Product\Attachment $attachment Attachment
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntityForAttachment(\CDev\FileAttachments\Model\Product\Attachment $attachment)
    {
        $newStorage = parent::cloneEntity();

        $attachment->setStorage($newStorage);
        $newStorage->setAttachment($attachment);

        if ($this->getStorageType() !== static::STORAGE_URL) {
            // Clone local image (will be created new file with unique name)
            $newStorage->loadFromLocalFile($this->getStoragePath(), null, false);
        }

        return $newStorage;
    }

    /**
     * Get list of administrator permissions to download files of the storage
     *
     * @return array
     */
    public function getAdminPermissions()
    {
        return ['manage catalog'];
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
     * Set attachment
     *
     * @param \CDev\FileAttachments\Model\Product\Attachment $attachment
     * @return Storage
     */
    public function setAttachment(\CDev\FileAttachments\Model\Product\Attachment $attachment = null)
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * Get attachment
     *
     * @return \CDev\FileAttachments\Model\Product\Attachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
}
