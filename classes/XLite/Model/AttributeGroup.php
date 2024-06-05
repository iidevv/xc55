<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\AttributeGroup\DTO\AttributeGroupInput as Input;
use XLite\API\Endpoint\AttributeGroup\DTO\AttributeGroupOutput as Output;
use XLite\Controller\API\AttributeGroup\Post;

/**
 * @ORM\Entity
 * @ORM\Table (name="attribute_groups")
 * @ApiPlatform\ApiResource(
 *     shortName="Attribute Group",
 *     input=Input::class,
 *     output=Output::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/attribute_groups/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "openapi_context"={
 *                  "summary"="Retrieve an attribute group",
 *             },
 *           },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/attribute_groups/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "openapi_context"={
 *                  "summary"="Update an attribute group",
 *             },
 *           },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/attribute_groups/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "openapi_context"={
 *                  "summary"="Delete an attribute group",
 *             },
 *           },
 *          "product_class_based_get_subresource"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attribute_groups/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "openapi_context"={
 *                  "summary"="Retrieve an attribute group from a product class",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_put_subresource"={
 *              "method"="PUT",
 *              "path"="/product_classes/{class_id}/attribute_groups/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "openapi_context"={
 *                  "summary"="Update an attribute group of a product class",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_delete_subresource"={
 *              "method"="DELETE",
 *              "path"="/product_classes/{class_id}/attribute_groups/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "openapi_context"={
 *                  "summary"="Delete an attribute group from a product class",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/attribute_groups.{_format}",
 *              "identifiers"={"id"},
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/attribute_groups.{_format}",
 *              "identifiers"={"id"},
 *              "openapi_context"={
 *                  "summary"="Create an attribute group",
 *             },
 *          },
 *          "product_class_based_get_subresources"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attribute_groups.{_format}",
 *              "identifiers"={"id"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of attribute groups from a product class",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "product_class_based_post_subresources"={
 *              "method"="POST",
 *              "path"="/product_classes/{class_id}/attribute_groups.{_format}",
 *              "identifiers"={"id"},
 *              "controller"=Post::class,
 *              "openapi_context"={
 *                  "summary"="Add an attribute group to a product class",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class AttributeGroup extends \XLite\Model\Base\I18n
{
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
    protected $position = 0;

    /**
     * @var \XLite\Model\ProductClass
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\ProductClass", inversedBy="attribute_groups")
     * @ORM\JoinColumn (name="product_class_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productClass;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Attribute", mappedBy="attributeGroup")
     */
    protected $attributes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeGroupTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return number of attributes associated with this class
     *
     * @return integer
     */
    public function getAttributesCount()
    {
        return count($this->getAttributes());
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
     * Set position
     *
     * @param integer $position
     * @return AttributeGroup
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set productClass
     *
     * @param \XLite\Model\ProductClass $productClass
     * @return AttributeGroup
     */
    public function setProductClass(\XLite\Model\ProductClass $productClass = null)
    {
        $this->productClass = $productClass;
        return $this;
    }

    /**
     * Get productClass
     *
     * @return \XLite\Model\ProductClass
     */
    public function getProductClass()
    {
        return $this->productClass;
    }

    /**
     * Add attributes
     *
     * @param \XLite\Model\Attribute $attributes
     * @return AttributeGroup
     */
    public function addAttributes(\XLite\Model\Attribute $attributes)
    {
        $this->attributes[] = $attributes;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
