<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\Order\Parcel\Manifest;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class represents a Canada Post parcel shipment links
 *
 * @ORM\Entity
 * @ORM\Table  (name="order_capost_parcel_manifest_links")
 */
class Link extends \XC\CanadaPost\Model\Base\Link
{
    /**
     * Link's manifest (reference to the Canada Post parcel manifest model)
     *
     * @var \XC\CanadaPost\Model\Order\Parcel\Manifest
     *
     * @ORM\ManyToOne  (targetEntity="XC\CanadaPost\Model\Order\Parcel\Manifest", inversedBy="links")
     * @ORM\JoinColumn (name="manifestId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $manifest;

    /**
     * Relation to a storage entity
     *
     * @var \XC\CanadaPost\Model\Order\Parcel\Manifest\Link\Storage
     *
     * @ORM\OneToOne (targetEntity="XC\CanadaPost\Model\Order\Parcel\Manifest\Link\Storage", mappedBy="link", cascade={"all"}, fetch="EAGER")
     */
    protected $storage;

    // {{{ Service methods

    /**
     * Set manifest
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Manifest $manifest Manifest object (OPTIONAL)
     *
     * @return void
     */
    public function setManifest(\XC\CanadaPost\Model\Order\Parcel\Manifest $manifest = null)
    {
        $this->manifest = $manifest;
    }

    /**
     * Set storage
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Manifest\Link\Storage $storage Storage object
     *
     * @return void
     */
    public function setStorage(\XC\CanadaPost\Model\Order\Parcel\Manifest\Link\Storage $storage)
    {
        $storage->setLink($this);

        $this->storage = $storage;
    }

    // }}}

    /**
     * Get store class
     *
     * @return string
     */
    protected function getStorageClass()
    {
        return '\XC\CanadaPost\Model\Order\Parcel\Manifest\Link\Storage';
    }

    /**
     * Get filename for PDF documents
     *
     * @return string
     */
    public function getFileName()
    {
        return 'm_' . $this->getManifest()->getManifestId() . '_' . parent::getFileName();
    }

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
     * Set rel
     *
     * @param string $rel
     * @return Link
     */
    public function setRel($rel)
    {
        $this->rel = $rel;
        return $this;
    }

    /**
     * Get rel
     *
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Set href
     *
     * @param string $href
     * @return Link
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set idx
     *
     * @param integer $idx
     * @return Link
     */
    public function setIdx($idx)
    {
        $this->idx = $idx;
        return $this;
    }

    /**
     * Get idx
     *
     * @return integer
     */
    public function getIdx()
    {
        return $this->idx;
    }

    /**
     * Set mediaType
     *
     * @param string $mediaType
     * @return Link
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;
        return $this;
    }

    /**
     * Get mediaType
     *
     * @return string
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Get manifest
     *
     * @return \XC\CanadaPost\Model\Order\Parcel\Manifest
     */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * Get storage
     *
     * @return \XC\CanadaPost\Model\Order\Parcel\Manifest\Link\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
