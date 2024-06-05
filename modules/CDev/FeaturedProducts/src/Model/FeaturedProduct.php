<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\DTO\CategoryFeaturedInput;
use CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\DTO\CategoryFeaturedOutput;
use CDev\FeaturedProducts\Controller\API\FeaturedProduct\DeleteCategoryFeatured;
use CDev\FeaturedProducts\Controller\API\FeaturedProduct\DeleteFrontPageFeatured;
use Doctrine\ORM\Mapping as ORM;

/**
 * Featured Product
 *
 * @ORM\Entity
 * @ORM\Table (name="featured_products",
 *      uniqueConstraints={
 *             @ORM\UniqueConstraint (name="pair", columns={"category_id","product_id"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     attributes={"pagination_enabled"=false},
 *     shortName="Featured Product",
 *     input=CategoryFeaturedInput::class,
 *     output=CategoryFeaturedOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/categories/{category_id}/featured/{product_id}.{_format}",
 *             "identifiers"={"category_id", "product_id"},
 *             "requirements"={"category_id"="\d+", "product_id"="\d+"},
 *             "openapi_context"={
 *                  "summary"="Retrieve a featured product from a category",
 *                  "parameters"={
 *                      {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *             },
 *          },
 *         "delete_category_featured"={
 *             "method"="DELETE",
 *             "path"="/categories/{category_id}/featured/{product_id}.{_format}",
 *             "identifiers"={"category_id", "product_id"},
 *             "requirements"={"category_id"="\d+", "product_id"="\d+"},
 *             "controller"=DeleteCategoryFeatured::class,
 *             "read"=false,
 *             "openapi_context"={
 *                  "summary"="Delete a featured product from a category",
 *                  "parameters"={
 *                      {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *             },
 *         },
 *         "delete_front_page_featured"={
 *             "method"="DELETE",
 *             "path"="/front_page/featured/{product_id}.{_format}",
 *             "identifiers"={"category_id", "product_id"},
 *             "requirements"={"product_id"="\d+"},
 *             "controller"=DeleteFrontPageFeatured::class,
 *             "read"=false,
 *             "openapi_context"={
 *                  "summary"="Delete a featured product from the front page",
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *             },
 *         },
 *     },
 *     collectionOperations={
 *         "add_category_featured"={
 *             "method"="POST",
 *             "path"="/categories/{category_id}/featured.{_format}",
 *             "identifiers"={"category_id", "product_id"},
 *             "requirements"={"category_id"="\d+"},
 *             "openapi_context"={
 *                 "summary"="Add a featured product to a category",
 *                 "parameters"={
 *                     {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                 }
 *             },
 *         },
 *         "get_category_featured"={
 *             "method"="GET",
 *             "path"="/categories/{category_id}/featured.{_format}",
 *             "identifiers"={"category_id", "product_id"},
 *             "requirements"={"category_id"="\d+"},
 *             "openapi_context"={
 *                  "summary"="Retrieve a list of featured products from a category",
 *                  "parameters"={
 *                     {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                 }
 *             },
 *         },
 *         "add_front_page_featured"={
 *             "method"="POST",
 *             "path"="/front_page/featured.{_format}",
 *             "identifiers"={"category_id", "product_id"},
 *             "openapi_context"={
 *                 "summary"="Add a featured product to the front page",
 *             },
 *         },
 *         "get_front_page_featured"={
 *             "method"="GET",
 *             "path"="/front_page/featured.{_format}",
 *             "identifiers"={"category_id", "product_id"},
 *             "openapi_context"={
 *                  "summary"="Retrieve a list of featured products from the front page",
 *             },
 *         },
 *     }
 * )
 */

class FeaturedProduct extends \XLite\Model\AEntity
{
    /**
     * Session cell name
     */
    public const SESSION_CELL_NAME = 'featuredProductsSearch';

    /**
     * Product + category link unique id
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
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="featuredProducts")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Category (relation)
     *
     * @var \XLite\Model\Category
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Category", inversedBy="featuredProducts")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
     */
    protected $category;


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
     * @return double
     */
    public function getPrice()
    {
        return $this->getProduct()->getPrice();
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
     * @param integer $position Category position
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
     * @return FeaturedProduct
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
     * @return FeaturedProduct
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
     * Set category
     *
     * @param \XLite\Model\Category $category
     * @return FeaturedProduct
     */
    public function setCategory(\XLite\Model\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \XLite\Model\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
