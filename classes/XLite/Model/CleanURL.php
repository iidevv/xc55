<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * CleanURL
 *
 * @ORM\Entity
 * @ORM\Table (name="clean_urls",
 *      indexes={
 *          @ORM\Index (name="cleanURL", columns={"cleanURL"}),
 *      }
 * )
 */
class CleanURL extends \XLite\Model\AEntity
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={"unsigned": true })
     */
    protected $id;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="cleanURLs")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Relation to a category entity
     *
     * @var \XLite\Model\Category
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Category", inversedBy="cleanURLs")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * Clean URL
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $cleanURL;

    /**
     * Set entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    public function setEntity($entity)
    {
        $entityType = \XLite\Model\Repo\CleanURL::getEntityType($entity);

        $method = 'set' . \Includes\Utils\Converter::convertToUpperCamelCase($entityType);
        if (method_exists($this, $method)) {
            $this->{$method}($entity);
        }
    }

    /**
     * Get entity
     *
     * @return \XLite\Model\AEntity
     */
    public function getEntity()
    {
        $entity = null;

        foreach (\XLite\Model\Repo\CleanURL::getEntityTypes() as $type) {
            $method = 'get' . \Includes\Utils\Converter::convertToUpperCamelCase($type);
            if (method_exists($this, $method)) {
                $entity = $this->{$method}();

                if ($entity) {
                    break;
                }
            }
        }

        return $entity;
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
     * Set cleanURL
     *
     * @param string $cleanURL
     * @return CleanURL
     */
    public function setCleanURL($cleanURL)
    {
        $this->cleanURL = $cleanURL;
        return $this;
    }

    /**
     * Get cleanURL
     *
     * @return string
     */
    public function getCleanURL()
    {
        return $this->cleanURL;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return CleanURL
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
     * @return CleanURL
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
