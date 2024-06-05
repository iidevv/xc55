<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use CDev\Wholesale\API\Endpoint\ProductWholesalePrice\DTO\ProductWholesalePriceInput as Input;
use CDev\Wholesale\API\Endpoint\ProductWholesalePrice\DTO\ProductWholesalePriceOutput as Output;

/**
 * Wholesale price model (product)
 *
 * @ORM\Entity
 * @ORM\Table  (name="wholesale_prices",
 *      indexes={
 *          @ORM\Index (name="range", columns={"product_id", "membership_id", "quantityRangeBegin", "quantityRangeEnd"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     shortName="Product Wholesale Price",
 *     input=Input::class,
 *     output=Output::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/wholesale_prices/{id}.{_format}",
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
 *              "path"="/products/{product_id}/wholesale_prices/{id}.{_format}",
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
 *              "path"="/products/{product_id}/wholesale_prices/{id}.{_format}",
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
 *              "path"="/products/{product_id}/wholesale_prices.{_format}",
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
 *              "path"="/products/{product_id}/wholesale_prices.{_format}",
 *              "controller"="xcart.api.cdev.wholesale.product_wholesale_price.controller",
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
class WholesalePrice extends \CDev\Wholesale\Model\Base\AWholesalePrice
{
    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="wholesalePrices")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Return owner
     *
     * @return \XLite\Model\Product
     */
    public function getOwner()
    {
        return $this->getProduct();
    }

    /**
     * Set owner
     *
     * @param \XLite\Model\Product $owner Owner
     *
     * @return static
     */
    public function setOwner($owner)
    {
        return $this->setProduct($owner);
    }

    /**
     * @inheritdoc
     */
    public function getOwnerPrice()
    {
        return $this->getOwner()
            ? $this->getOwner()->getPrice()
            : null;
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
     * @inheritdoc
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return static
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
