<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XC\Upselling\API\Endpoint\ProductUpsellingProduct\DTO\ProductUpsellingProductInput as Input;
use XC\Upselling\API\Endpoint\ProductUpsellingProduct\DTO\ProductUpsellingProductOutput as Output;

/**
 * Upselling Product
 *
 * @ORM\Entity
 * @ORM\Table (name="upselling_products",
 *      indexes={
 *          @ORM\Index (name="parent_product_index", columns={"parent_product_id"}),
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 * @ApiPlatform\ApiResource(
 *     shortName="Related Product",
 *     input=Input::class,
 *     output=Output::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/related_products/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/related_products/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/related_products/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
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
 *              "path"="/products/{product_id}/related_products.{_format}",
 *              "identifiers"={"product_id"},
 *              "requirements"={"product_id"="\d+"},
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  },
 *              }
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/related_products.{_format}",
 *              "controller"="xcart.api.xc.upselling.product_upselling_product.controller",
 *              "identifiers"={"product_id"},
 *              "requirements"={"product_id"="\d+"},
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class UpsellingProduct extends \XLite\Model\AEntity
{
    /**
     * Session cell name
     */
    public const SESSION_CELL_NAME = 'upsellingProductsSearch';

    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Sort position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $orderBy = 0;

    /**
     * Product (relation)
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="upsellingProducts")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Parent product (relation)
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="upsellingParentProducts")
     * @ORM\JoinColumn (name="parent_product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $parentProduct;

    /**
     * SKU getter
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * Price getter
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * Check if the bi-directional link is needed
     *
     * @return boolean
     */
    public function getBidirectional()
    {
        $linkData = [
            'parentProduct' => $this->getProduct(),
            'product'       => $this->getParentProduct(),
        ];

        return (bool)$this->getRepository()->findOneBy($linkData);
    }

    /**
     * Check if the bi-directional link is needed
     *
     * @return boolean
     */
    public function setBidirectional($newValue)
    {
        $newValue
            ? $this->getRepository()->addBidirectionalLink($this)
            : $this->getRepository()->deleteBidirectionalLink($this);
    }

    /**
     * Amount getter
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->getProduct()->getPublicAmount();
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->getOrderBy();
    }

    /**
     * Set position
     *
     * @param integer $position Upselling link position
     *
     * @return void
     */
    public function setPosition($position)
    {
        return $this->setOrderBy($position);
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
     * Set orderBy
     *
     * @param integer $orderBy
     * @return UpsellingProduct
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * Get orderBy
     *
     * @return integer
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return UpsellingProduct
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
     * Set parentProduct
     *
     * @param \XLite\Model\Product $parentProduct
     * @return UpsellingProduct
     */
    public function setParentProduct(\XLite\Model\Product $parentProduct = null)
    {
        $this->parentProduct = $parentProduct;
        return $this;
    }

    /**
     * Get parentProduct
     *
     * @return \XLite\Model\Product
     */
    public function getParentProduct()
    {
        return $this->parentProduct;
    }
}
