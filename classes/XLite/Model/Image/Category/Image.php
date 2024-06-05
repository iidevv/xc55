<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Image\Category;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\CategoryIcon\DTO\IconInput;
use XLite\API\Endpoint\CategoryIcon\DTO\IconOutput;
use XLite\Controller\API\CategoryIcon\DeleteCategoryIcon;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table  (name="category_images")
 * @ApiPlatform\ApiResource(
 *     shortName="Category Icon",
 *     output=IconOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/categories/{category_id}/icon.{_format}",
 *             "identifiers"={"category_id"},
 *             "requirements"={"category_id"="\d+"},
 *             "openapi_context"={
 *                  "summary"="Retrieve an icon from a category",
 *                  "parameters"={
 *                      {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *             },
 *          },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/categories/{category_id}/icon.{_format}",
 *             "identifiers"={"category_id"},
 *             "requirements"={"category_id"="\d+"},
 *             "controller"=DeleteCategoryIcon::class,
 *             "read"=false,
 *             "openapi_context"={
 *                  "summary"="Delete an icon from a category",
 *                  "parameters"={
 *                      {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *             },
 *         },
 *     },
 *     collectionOperations={
 *         "post"={
 *             "method"="POST",
 *             "input"=IconInput::class,
 *             "output"=IconOutput::class,
 *             "path"="/categories/{category_id}/icon.{_format}",
 *             "identifiers"={"category_id"},
 *             "requirements"={"category_id"="\d+"},
 *             "openapi_context"={
 *                 "summary"="Add an icon to a category",
 *                 "parameters"={
 *                     {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                 },
 *                 "requestBody"={
 *                     "content"={
 *                         "application/json"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "alt"={
 *                                         "type"="string",
 *                                         "description"="Alt text"
 *                                     },
 *                                     "externalUrl"={
 *                                         "type"="string",
 *                                         "description"="URL to the image file which will be downloaded from there"
 *                                     },
 *                                     "attachment"={
 *                                         "type"="string",
 *                                         "description"="base64-encoded image"
 *                                     },
 *                                     "filename"={
 *                                         "type"="string",
 *                                         "description"="Image name with correct extension. Required for 'attachement' field."
 *                                     },
 *                                 },
 *                             },
 *                         },
 *                     },
 *                 },
 *             },
 *         },
 *     }
 * )
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Relation to a category entity
     *
     * @var \XLite\Model\Category
     *
     * @ORM\OneToOne   (targetEntity="XLite\Model\Category", inversedBy="image")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * Alternative image text
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $alt = '';

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
     * Set category
     *
     * @param \XLite\Model\Category $category
     * @return Image
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
