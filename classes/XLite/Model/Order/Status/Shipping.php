<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\OrderShippingStatus\DTO\OrderShippingStatusInput as Input;
use XLite\API\Endpoint\OrderShippingStatus\DTO\OrderShippingStatusOutput as Output;

/**
 * Shipping status
 *
 * @ORM\Entity
 * @ORM\Table  (name="order_shipping_statuses",
 *      indexes={
 *          @ORM\Index (name="code", columns={"code"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     shortName="OrderShippingStatus",
 *     input=Input::class,
 *     output=Output::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/orders/{id}/shipping_status.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/orders/{id}/shipping_status.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          }
 *     },
 *     collectionOperations={}
 * )
 */
class Shipping extends \XLite\Model\Order\Status\AStatus
{
    /**
     * Statuses
     */
    public const STATUS_NEW                 = 'N';
    public const STATUS_PROCESSING          = 'P';
    public const STATUS_SHIPPED             = 'S';
    public const STATUS_DELIVERED           = 'D';
    public const STATUS_WILL_NOT_DELIVER    = 'WND';
    public const STATUS_RETURNED            = 'R';
    public const STATUS_WAITING_FOR_APPROVE = 'WFA';
    public const STATUS_NEW_BACKORDERED     = 'NBA';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Order\Status\ShippingTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Disallowed to set manually statuses
     *
     * @return array
     */
    public static function getDisallowedToSetManuallyStatuses()
    {
        return [
            static::STATUS_WAITING_FOR_APPROVE,
            static::STATUS_NEW_BACKORDERED,
        ];
    }

    /**
     * Status is allowed to set manually
     *
     * @return boolean
     */
    public function isAllowedToSetManually()
    {
        return !in_array(
            $this->getCode(),
            static::getDisallowedToSetManuallyStatuses()
        );
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
     * Set code
     *
     * @param string $code
     *
     * @return Shipping
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Shipping
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
     * List of change status handlers;
     * top index - old status, second index - new one
     * (<old_status> ----> <new_status>: $statusHandlers[$old][$new])
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        return [
            static::STATUS_NEW => [
                static::STATUS_SHIPPED => ['ship'],
                static::STATUS_WAITING_FOR_APPROVE => ['waitingForApprove']
            ],

            static::STATUS_PROCESSING => [
                static::STATUS_SHIPPED => ['ship'],
                static::STATUS_WAITING_FOR_APPROVE => ['waitingForApprove']
            ],

            static::STATUS_DELIVERED => [
                static::STATUS_SHIPPED => ['ship'],
            ],

            static::STATUS_WILL_NOT_DELIVER => [
                static::STATUS_SHIPPED => ['ship'],
            ],

            static::STATUS_RETURNED => [
                static::STATUS_SHIPPED => ['ship'],
            ],

            static::STATUS_WAITING_FOR_APPROVE => [
                static::STATUS_SHIPPED => ['ship'],
            ],

            static::STATUS_NEW_BACKORDERED => [
                static::STATUS_NEW              => ['releaseBackorder'],
                static::STATUS_PROCESSING       => ['releaseBackorder'],
                static::STATUS_SHIPPED          => ['ship', 'releaseBackorder'],
                static::STATUS_DELIVERED        => ['releaseBackorder'],
                static::STATUS_WILL_NOT_DELIVER => ['releaseBackorder'],
                static::STATUS_RETURNED         => ['releaseBackorder'],
            ],
        ];
    }
}
