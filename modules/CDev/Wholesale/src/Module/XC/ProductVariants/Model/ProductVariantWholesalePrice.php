<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use CDev\Wholesale\Module\XC\ProductVariants\API\Endpoint\ProductVariantWholesalePrice\DTO\ProductVariantWholesalePriceInput as Input;
use CDev\Wholesale\Module\XC\ProductVariants\API\Endpoint\ProductVariantWholesalePrice\DTO\ProductVariantWholesalePriceOutput as Output;
use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @ORM\Entity
 * @ORM\Table  (name="product_variant_wholesale_prices",
 *      indexes={
 *          @ORM\Index (name="range", columns={"product_variant_id", "membership_id", "quantityRangeBegin", "quantityRangeEnd"})
 *      }
 * )
 *
 * @ApiPlatform\ApiResource(
 *     shortName="Product Variant Wholesale Price",
 *     input=Input::class,
 *     output=Output::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/variants/{variant_id}/wholesale_prices/{id}.{_format}",
 *              "identifiers"={"product_id", "variant_id", "id"},
 *              "requirements"={"product_id"="\d+", "variant_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a wholesale price from a product variant",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="variant_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/variants/{variant_id}/wholesale_prices/{id}.{_format}",
 *              "identifiers"={"product_id", "variant_id", "id"},
 *              "requirements"={"product_id"="\d+", "variant_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Update a wholesale price of a product variant",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="variant_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/variants/{variant_id}/wholesale_prices/{id}.{_format}",
 *              "identifiers"={"product_id", "variant_id", "id"},
 *              "requirements"={"product_id"="\d+", "variant_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Delete a wholesale price from a product variant",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="variant_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/variants/{variant_id}/wholesale_prices.{_format}",
 *              "identifiers"={"product_id", "variant_id"},
 *              "requirements"={"product_id"="\d+", "variant_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of wholesale prices from a product variant",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="variant_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  },
 *              }
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/variants/{variant_id}/wholesale_prices.{_format}",
 *              "controller"="xcart.api.cdev.wholesale.product_variant_wholesale_price.controller",
 *              "identifiers"={"product_id", "variant_id"},
 *              "requirements"={"product_id"="\d+", "variant_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Add a wholesale price to a product variant",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="variant_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     }
 * )
 *
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariantWholesalePrice extends \CDev\Wholesale\Model\Base\AWholesalePrice
{
    /**
     * Relation to a product variant entity
     *
     * @var \XC\ProductVariants\Model\ProductVariant
     *
     * @ORM\ManyToOne  (targetEntity="XC\ProductVariants\Model\ProductVariant",cascade={"persist"})
     * @ORM\JoinColumn (name="product_variant_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productVariant;

    /**
     * Return owner
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    public function getOwner()
    {
        return $this->getProductVariant();
    }

    /**
     * Set owner
     *
     * @param \XC\ProductVariants\Model\ProductVariant $owner Owner
     *
     * @return static
     */
    public function setOwner($owner)
    {
        return $this->setProductVariant($owner);
    }

    /**
     * @inheritdoc
     */
    public function getOwnerPrice()
    {
        if ($this->getOwner()) {
            return $this->getOwner()->getDefaultPrice()
                ? $this->getOwner()->getProduct()->getPrice()
                : $this->getOwner()->getPrice();
        } else {
            return null;
        }
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->getOwner() ? $this->getOwner()->getProduct() : null;
    }

    /**
     * Set product: fake method for compatibility with \CDev\Wholesale\Model\WholesalePrice class
     *
     * @param \XLite\Model\Product $product
     *
     * @return static
     */
    public function setProduct($product)
    {
        return $this;
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
     * Set productVariant
     *
     * @param \XC\ProductVariants\Model\ProductVariant|null $productVariant
     * @return ProductVariantWholesalePrice
     */
    public function setProductVariant(\XC\ProductVariants\Model\ProductVariant $productVariant = null)
    {
        $this->productVariant = $productVariant;
        return $this;
    }

    /**
     * Get productVariant
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }
}
