<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use QSL\ProductStickers\API\Endpoint\ProductSticker\DTO\ProductStickerInput as Input;
use QSL\ProductStickers\API\Endpoint\ProductSticker\DTO\ProductStickerOutput as Output;

/**
 * @ORM\Entity
 * @ORM\Table (name="product_stickers")
 * @ApiPlatform\ApiResource(
 *     shortName="Product Sticker",
 *     input=Input::class,
 *     output=Output::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/product_stickers/{sticker_id}.{_format}",
 *              "identifiers"={"sticker_id"},
 *              "requirements"={"sticker_id"="\d+"},
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="sticker_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/product_stickers/{sticker_id}.{_format}",
 *              "identifiers"={"sticker_id"},
 *              "requirements"={"sticker_id"="\d+"},
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="sticker_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/product_stickers/{sticker_id}.{_format}",
 *              "identifiers"={"sticker_id"},
 *              "requirements"={"sticker_id"="\d+"},
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="sticker_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/product_stickers.{_format}",
 *              "identifiers"={},
 *              "requirements"={}
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/product_stickers.{_format}",
 *              "controller"="xcart.api.qsl.product_stickers.product_sticker.controller",
 *              "identifiers"={},
 *              "requirements"={}
 *          }
 *     }
 * )
 */
class ProductSticker extends \XLite\Model\Base\I18n
{
     /**
     * Unique ID
     *
     * @var   integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={"unsigned": true})
     */
    protected $sticker_id;

    /**
     * Position
     *
     * @var   integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
    * @ORM\Column (type="boolean")
    */
    protected $enabled = true;

    /**
     * Name color
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $text_color;

    /**
     * Name color
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $bg_color;

    /**
     * Products
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Product", mappedBy="product_stickers", fetch="LAZY")
     */
    protected $products;

    /**
     * Categories
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Category", mappedBy="category_stickers", fetch="LAZY")
     */
    protected $categories;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $isLabel = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\ProductStickers\Model\ProductStickerTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @return string
     */
    public function getTextColor()
    {
        return $this->text_color;
    }

    /**
     * @param string $text_color
     */
    public function setTextColor($text_color)
    {
        $this->text_color = $text_color;
    }

    /**
     * @return string
     */
    public function getBgColor()
    {
        return $this->bg_color;
    }

    /**
     * @param string $bg_color
     */
    public function setBgColor($bg_color)
    {
        $this->bg_color = $bg_color;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return ProductSticker
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @return integer
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Add products
     *
     * @param \XLite\Model\Product $products
     * @return ProductSticker
     */
    public function addProducts(\XLite\Model\Product $products)
    {
        $this->products[] = $products;
        return $this;
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Get sticker_id
     *
     * @return integer
     */
    public function getProductStickerId()
    {
        return $this->sticker_id;
    }

    /**
     * Set sticker_id
     *
     * @return $this
     */
    public function setProductStickerId($sticker_id)
    {
        $this->sticker_id = $sticker_id;
        return $this;
    }

    /**
     * @param boolean $isLabel
     * @return $this
     */
    public function setIsLabel($isLabel)
    {
        $this->isLabel = $isLabel;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsLabel()
    {
        return $this->isLabel;
    }

    /**
     * @return bool
     */
    public function isLabel()
    {
        return $this->isLabel;
    }
}
