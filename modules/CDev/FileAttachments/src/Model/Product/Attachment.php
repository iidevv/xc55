<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\Model\Product;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use CDev\FileAttachments\API\Endpoint\ProductAttachment\DTO\ProductAttachmentInput as Input;
use CDev\FileAttachments\API\Endpoint\ProductAttachment\DTO\ProductAttachmentUpdateInput as Update;
use CDev\FileAttachments\API\Endpoint\ProductAttachment\DTO\ProductAttachmentOutput as Output;

/**
 * Product attachment
 *
 * @ORM\Entity
 * @ORM\Table (
 *     name="product_attachments",
 *     indexes={
 *         @ORM\Index (name="o", columns={"orderby"})
 *     }
 * )
 * @ApiPlatform\ApiResource(
 *     shortName="Product Attachment",
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attachments/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attachments/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
 *              "input"=Update::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/attachments/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attachments.{_format}",
 *              "identifiers"={"product_id"},
 *              "requirements"={"product_id"="\d+"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  },
 *              }
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/attachments.{_format}",
 *              "controller"="xcart.api.cdev.file_attachments.product_attachment.controller",
 *              "identifiers"={"product_id"},
 *              "requirements"={"product_id"="\d+"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class Attachment extends \XLite\Model\Base\I18n
{
    public const ACCESS_ANY = 'A';
    public const ACCESS_REGISTERED = 'R';

    // {{{ Collumns

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $orderby = 0;

    // }}}

    // {{{ Associations

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product", inversedBy="attachments")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @var \CDev\FileAttachments\Model\Product\Attachment\Storage
     *
     * @ORM\OneToOne (targetEntity="CDev\FileAttachments\Model\Product\Attachment\Storage", mappedBy="attachment", cascade={"all"}, fetch="EAGER")
     */
    protected $storage;

    /**
     * Access - membership id or [self::ACCESS_ANY, self::ACCESS_REGISTERED]
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $access = self::ACCESS_ANY;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="CDev\FileAttachments\Model\Product\AttachmentTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    // }}}

    // {{{ Getters / setters

    /**
     * Get storage
     *
     * @return \CDev\FileAttachments\Model\Product\Attachment\Storage
     */
    public function getStorage($method = null)
    {
        if (!$this->storage) {
            $this->setStorage(new \CDev\FileAttachments\Model\Product\Attachment\Storage());
            if (isset($method)) {
                $this->storage->setStorageType($this->storage::STORAGE_URL);
            }
            $this->storage->setAttachment($this);
        }

        return $this->storage;
    }

    /**
     * Get public title
     *
     * @return string
     */
    public function getPublicTitle()
    {
        return $this->getTitle() ?: $this->getStorage()->getFileName();
    }

    /**
     * Get public url
     *
     * @return string
     */
    public function getURL()
    {
        return $this->getStorage()->getURL();
    }

    // }}}

    /**
     * Clone for product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntityForProduct(\XLite\Model\Product $product)
    {
        $newAttachment = parent::cloneEntity();

        $newAttachment->setProduct($product);
        $product->addAttachments($newAttachment);

        $this->getStorage()->cloneEntityForAttachment($newAttachment);

        return $newAttachment;
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
     * Set orderby
     *
     * @param integer $orderby
     * @return Attachment
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Attachment
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set storage
     *
     * @param \CDev\FileAttachments\Model\Product\Attachment\Storage $storage
     * @return Attachment
     */
    public function setStorage(\CDev\FileAttachments\Model\Product\Attachment\Storage $storage = null)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Return Access
     *
     * @return string
     */
    public function getAccess()
    {
        return !empty($this->access) ? $this->access : static::ACCESS_ANY;
    }

    /**
     * Set Access
     *
     * @param string $access
     *
     * @return $this
     */
    public function setAccess($access)
    {
        if ($access instanceof \XLite\Model\Membership) {
            $access = $access->getMembershipId();
        }

        $this->access = $access;
        return $this;
    }

    /**
     * Get attachment icon type
     *
     * @return string
     */
    public function getIconType()
    {
        $ext = strtolower($this->getStorage()->getExtension());

        if (in_array($ext, \XLite\Core\Converter::getArchiveExtensions())) {
            $icon = 'zip';
        } elseif (in_array($ext, \XLite\Core\Converter::getImageExtensions())) {
            $icon = 'image';
        } elseif (in_array($ext, \XLite\Core\Converter::getPhotoshopExtensions())) {
            $icon = 'ps';
        } elseif (in_array($ext, \XLite\Core\Converter::getPresentationExtensions())) {
            $icon = 'powerpoint';
        } elseif (in_array($ext, \XLite\Core\Converter::getAudioExtensions())) {
            $icon = 'music';
        } elseif (in_array($ext, \XLite\Core\Converter::getVideoExtensions())) {
            $icon = 'video';
        } elseif (in_array($ext, ['pdf', 'csv', 'ai', 'exe'])) {
            $icon = $ext;
        } elseif (in_array($ext, \XLite\Core\Converter::getDocumentExtensions())) {
            $icon = 'doc';
        } elseif (in_array($ext, \XLite\Core\Converter::getMSWordExtensions())) {
            $icon = 'word';
        } else {
            $icon = $this->getStorage()->isURL() ? 'url' : 'default';
        }

        return $icon;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $title
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setTitle($title)
    {
        return $this->setTranslationField(__FUNCTION__, $title);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $description
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
    }

    // }}}
}
