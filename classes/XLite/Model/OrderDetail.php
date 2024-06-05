<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\OrderDetail\DTO\OrderDetailOutput as Output;

/**
 * Order details
 *
 * @ORM\Entity
 * @ORM\Table (name="order_details",
 *      indexes={
 *          @ORM\Index (name="oname", columns={"order_id","name"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     shortName="Detail",
 *     output=Output::class,
 *     itemOperations={},
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/orders/{id}/details.{_format}",
 *              "requirements"={"id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of order details",
 *                  "responses"={
 *                      "404"={
 *                          "description"="Resource not found"
 *                      }
 *                  },
 *                  "parameters"={
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "get_cart_details"={
 *              "method"="GET",
 *              "path"="/carts/{id}/details.{_format}",
 *              "requirements"={"id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of cart details",
 *                  "responses"={
 *                      "404"={
 *                          "description"="Resource not found"
 *                      }
 *                  },
 *                  "parameters"={
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class OrderDetail extends \XLite\Model\AEntity
{
    /**
     * Order detail unique id
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $detail_id;

    /**
     * Record name (code)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Record label
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $label;

    /**
     * Value
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $value;

    /**
     * Relation to a order entity
     *
     * @var \XLite\Model\Order
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="details", fetch="LAZY")
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Get display record name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->getLabel() ?: $this->getName();
    }

    /**
     * Get detail_id
     *
     * @return integer
     */
    public function getDetailId()
    {
        return $this->detail_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return OrderDetail
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return OrderDetail
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return OrderDetail
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     * @return OrderDetail
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
