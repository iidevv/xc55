<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image\Product;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\ProductImage\DTO\ImageInput;
use XLite\API\Endpoint\ProductImage\DTO\ImageOutput;
use XLite\API\Endpoint\ProductImage\DTO\ImageUpdate;
use XLite\Controller\API\ProductImage\DeleteProductImage;

/**
 * Product image
 *
 * @ORM\Entity
 * @ORM\Table  (name="product_images")
 * @ApiPlatform\ApiResource(
 *     shortName="Product Image",
 *     output=ImageOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/products/{product_id}/images/{image_id}.{_format}",
 *             "identifiers"={"product_id", "image_id"},
 *             "requirements"={"product_id"="\d+", "image_id"="\d+"},
 *             "openapi_context"={
 *                  "summary"="Retrieve an image from a product",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="image_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  },
 *             },
 *          },
 *         "put"={
 *             "method"="PUT",
 *             "input"=ImageUpdate::class,
 *             "path"="/products/{product_id}/images/{image_id}.{_format}",
 *             "identifiers"={"product_id", "image_id"},
 *             "requirements"={"product_id"="\d+", "image_id"="\d+"},
 *             "read"=false,
 *             "openapi_context"={
 *                  "summary"="Update the properties of a product image",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="image_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  },
 *             },
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/products/{product_id}/images/{image_id}.{_format}",
 *             "identifiers"={"product_id", "image_id"},
 *             "requirements"={"product_id"="\d+", "image_id"="\d+"},
 *             "controller"=DeleteProductImage::class,
 *             "read"=false,
 *             "openapi_context"={
 *                  "summary"="Delete an image from a product",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="image_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  },
 *             },
 *         },
 *     },
 *     collectionOperations={
 *         "post"={
 *             "method"="POST",
 *             "input"=ImageInput::class,
 *             "path"="/products/{product_id}/images.{_format}",
 *             "identifiers"={"product_id", "image_id"},
 *             "requirements"={"product_id"="\d+"},
 *             "openapi_context"={
 *                  "summary"="Add an image to a product",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  },
 *                  "requestBody"={
 *                      "content"={
 *                          "application/json"={
 *                              "schema"={
 *                                  "type"="object",
 *                                  "properties"={
 *                                      "position"={
 *                                          "type"="integer"
 *                                      },
 *                                      "alt"={
 *                                          "type"="string",
 *                                          "description"="Alt text"
 *                                      },
 *                                      "externalUrl"={
 *                                          "type"="string",
 *                                          "description"="URL to the image file which will be downloaded from there"
 *                                      },
 *                                      "attachment"={
 *                                          "type"="string",
 *                                          "description"="base64-encoded image"
 *                                      },
 *                                      "filename"={
 *                                          "type"="string",
 *                                          "description"="Image name with correct extension. Required for 'attachement' field."
 *                                      },
 *                                  },
 *                              },
 *                          },
 *                      },
 *                  },
 *              },
 *          }
 *     }
 * )
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Image position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="images")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Alternative image text
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $alt = '';

    /**
     * Set orderby
     *
     * @param integer $orderby
     * @return Image
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
     * Set alt
     *
     * @param string $alt
     * @return Image
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
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Image
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
}
