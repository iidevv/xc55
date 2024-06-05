<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\AttributeValue\Text\DTO\AttributeValueTextInput as Input;
use XLite\API\Endpoint\AttributeValue\Text\DTO\AttributeValueTextOutput as Output;
use XLite\Controller\API\AttributeValue\Text\Post as PostController;

/**
 * Attribute value (text)
 *
 * @ORM\Entity
 * @ORM\Table  (name="attribute_values_text",
 *      indexes={
 *          @ORM\Index (name="product_id", columns={"product_id"}),
 *          @ORM\Index (name="attribute_id", columns={"attribute_id"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     attributes={"pagination_enabled": false},
 *     compositeIdentifier=false,
 *     shortName="Textarea Attribute Value",
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_text/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a value from a product textarea attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attributes_text/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Update a value of a product textarea attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/attributes_text/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Delete a value from a product textarea attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "post"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/attributes_text/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "controller"=PostController::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Add a value to a product textarea attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class AttributeValueText extends \XLite\Model\AttributeValue\AAttributeValue
{
    /**
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product", inversedBy="attributeValueT")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Editable flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $editable = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueTextTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Return diff
     * todo: add test
     *
     * @param array $oldValues Old values
     * @param array $newValues New values
     *
     * @return array
     */
    public static function getDiff(array $oldValues, array $newValues)
    {
        $diff = [];
        if ($newValues) {
            foreach ($newValues as $attributeId => $value) {
                if (
                    !isset($oldValues[$attributeId])
                    || $value != $oldValues[$attributeId]
                ) {
                    $diff[$attributeId] = $value;
                }
            }
        }

        return $diff;
    }

    /**
     * Return attribute value as string
     *
     * @return string
     */
    public function asString()
    {
        /** @see \XLite\Model\AttributeValue\AttributeValueTextTranslation */
        return $this->getValue();
    }

    /**
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \XLite\Model\Product $product
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
    }

    /**
     * @return boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * @param boolean $editable
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $value
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setValue($value)
    {
        return $this->setTranslationField(__FUNCTION__, $value);
    }

    // }}}
}
