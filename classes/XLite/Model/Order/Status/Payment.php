<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\OrderPaymentStatus\DTO\OrderPaymentStatusInput as Input;
use XLite\API\Endpoint\OrderPaymentStatus\DTO\OrderPaymentStatusOutput as Output;

/**
 * Payment status
 *
 * @ORM\Entity
 * @ORM\Table  (name="order_payment_statuses",
 *      indexes={
 *          @ORM\Index (name="code", columns={"code"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     shortName="OrderPaymentStatus",
 *     input=Input::class,
 *     output=Output::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/orders/{id}/payment_status.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/orders/{id}/payment_status.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          }
 *     },
 *     collectionOperations={}
 * )
 */
class Payment extends \XLite\Model\Order\Status\AStatus
{
    /**
     * Statuses
     */
    public const STATUS_AUTHORIZED     = 'A';
    public const STATUS_PART_PAID      = 'PP';
    public const STATUS_PAID           = 'P';
    public const STATUS_DECLINED       = 'D';
    public const STATUS_CANCELED       = 'C';
    public const STATUS_QUEUED         = 'Q';
    public const STATUS_REFUNDED       = 'R';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Order\Status\PaymentTranslation", mappedBy="owner", cascade={"all"})
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
            static::STATUS_AUTHORIZED,
            static::STATUS_DECLINED
        ];
    }

    /**
     * Not compatible with Shipping statuses
     *
     * @return array
     */
    public static function getNotCompatibleWithShippingStatuses()
    {
        return [
            static::STATUS_DECLINED,
            static::STATUS_CANCELED,
        ];
    }

    /**
     * Get open order statuses
     *
     * @return array
     */
    public static function getOpenStatuses()
    {
        return [
            static::STATUS_AUTHORIZED,
            static::STATUS_PART_PAID,
            static::STATUS_PAID,
            static::STATUS_QUEUED,
        ];
    }

    /**
     * Get paid statuses
     *
     * @return array
     */
    public static function getPaidStatuses()
    {
        return [
            static::STATUS_AUTHORIZED,
            static::STATUS_PART_PAID,
            static::STATUS_PAID,
        ];
    }

    /**
     * Payment status is compatible with shipping status
     *
     * @return boolean
     */
    public function isCompatibleWithShippingStatus()
    {
        return !in_array(
            $this->getCode(),
            static::getNotCompatibleWithShippingStatuses()
        );
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
     * @return Payment
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
     * @return Payment
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
            static::STATUS_QUEUED => [
                static::STATUS_AUTHORIZED => ['authorize'],
                static::STATUS_PAID       => ['process'],
                static::STATUS_DECLINED   => ['decline', 'uncheckout', 'fail'],
                static::STATUS_CANCELED   => ['decline', 'uncheckout', 'cancel'],
            ],

            static::STATUS_AUTHORIZED => [
                static::STATUS_PAID       => ['process'],
                static::STATUS_DECLINED   => ['decline', 'uncheckout', 'fail'],
                static::STATUS_CANCELED   => ['decline', 'uncheckout', 'cancel'],
            ],

            static::STATUS_PART_PAID => [
                static::STATUS_PAID       => ['process'],
                static::STATUS_DECLINED   => ['decline', 'uncheckout', 'fail'],
                static::STATUS_CANCELED   => ['decline', 'uncheckout', 'fail'],
            ],

            static::STATUS_PAID => [
                static::STATUS_DECLINED   => ['decline', 'uncheckout', 'fail'],
                static::STATUS_CANCELED   => ['decline', 'uncheckout', 'cancel'],
            ],

            static::STATUS_DECLINED => [
                static::STATUS_AUTHORIZED => ['checkout', 'queue', 'authorize'],
                static::STATUS_PART_PAID  => ['checkout', 'queue'],
                static::STATUS_PAID       => ['checkout', 'queue', 'process'],
                static::STATUS_QUEUED     => ['checkout', 'queue'],
                static::STATUS_CANCELED   => ['cancel'],
            ],

            static::STATUS_CANCELED => [
                static::STATUS_AUTHORIZED => ['checkout', 'queue', 'authorize'],
                static::STATUS_PART_PAID  => ['checkout', 'queue'],
                static::STATUS_PAID       => ['checkout', 'queue', 'process'],
                static::STATUS_QUEUED     => ['checkout', 'queue'],
                static::STATUS_DECLINED   => ['fail'],
            ],

            static::STATUS_REFUNDED => [
                static::STATUS_PAID       => ['process'],
                static::STATUS_DECLINED   => ['fail'],
                static::STATUS_CANCELED   => ['cancel'],
            ],
        ];
    }
}
