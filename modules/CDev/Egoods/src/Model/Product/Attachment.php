<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Extender\Mixin
 */
abstract class Attachment extends \CDev\FileAttachments\Model\Product\Attachment
{
    /**
     * Private attachment
     *
     * @var bool
     *
     * @ORM\Column (type="boolean")
     */
    protected $private = false;

    /**
     * @var bool
     */
    protected $oldScope;

    /**
     * Attachment history
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="CDev\Egoods\Model\Product\Attachment\AttachmentHistoryPoint", mappedBy="attachment", cascade={"all"})
     * @ORM\OrderBy ({"date" = "ASC"})
     */
    protected $history;

    /**
     * Return Private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private && $this->canBePrivate();
    }

    /**
     * Set private scope flag
     *
     * @param boolean $private Scope flag
     *
     * @return void
     */
    public function setPrivate($private)
    {
        if (!isset($this->oldScope)) {
            $this->oldScope = $this->private;
        }

        $this->private = intval($private);

        $this->prepareChangeScope();
    }

    /**
     * Checks if this attachment can be private in current store conditions
     */
    public function canBePrivate()
    {
        return $this->getStorage() && (!$this->getStorage()->isURL() || $this->getStorage()->canBeSigned());
    }

    /**
     * Set private flag for duplicate attachment
     *
     * @param boolean                                                             $private Private flag
     * @param \CDev\FileAttachments\Model\Product\Attachment\Storage $storage Original storage
     *
     * @return void
     */
    public function setDuplicatePrivate($private, \CDev\FileAttachments\Model\Product\Attachment\Storage $storage)
    {
        $this->getStorage()->setPath($storage->getPath());
        $this->getStorage()->setStorageType($storage->getStorageType());
        $this->private = $private;
        $this->oldScope = $private;
    }

    /**
     * Prepare change scope
     *
     * @return void
     */
    public function prepareChangeScope()
    {
        $storage = $this->getStorage();

        if (!$storage->isURL() && isset($this->oldScope) && $this->oldScope != $this->getPrivate()) {
            $duplicates = $this->getStorage()->getDuplicates();

            if ($this->getPrivate()) {
                $storage->maskStorage();
            } else {
                if ($storage->isPrivatePath()) {
                    $storage->unmaskStorage();
                }
            }

            foreach ($duplicates as $duplicate) {
                if ($duplicate instanceof \CDev\FileAttachments\Model\Product\Attachment\Storage) {
                    $duplicate->getAttachment()->setDuplicatePrivate($this->getPrivate(), $this->getStorage());
                }
            }

            $this->oldScope = $this->getPrivate();
        }
    }

    /**
     * Synchronize private state
     *
     * @return void
     *
     * @ORM\PrePersist
     */
    public function synchronizePrivateState()
    {
        if ($this->getStorage()->isPrivatePath()) {
            $this->oldScope = true;
            $this->setPrivate(true);
            $this->getStorage()->setFileName(
                substr(
                    $this->getStorage()->getFileName(),
                    0,
                    \CDev\Egoods\Model\Product\Attachment\Storage::PRIVATE_SUFFIX_LENGTH * -1
                )
            );
        }
    }

    /**
     * Return History
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set History
     *
     * @param \CDev\Egoods\Model\Product\Attachment\AttachmentHistoryPoint $attachmentHistoryPoint
     *
     * @return $this
     */
    public function addHistoryPoint($attachmentHistoryPoint)
    {
        $this->history[] = $attachmentHistoryPoint;
        return $this;
    }

    /**
     * Get attachment icon type
     *
     * @return string
     */
    public function getIconType()
    {
        /** @var \CDev\Egoods\Model\Product\Attachment\Storage $storage */
        $storage = $this->getStorage();

        if ($storage && $storage->canBeSigned()) {
            return 's3';
        }

        return parent::getIconType();
    }
}
