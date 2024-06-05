<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\DTO\VolumeDiscountInput;
use CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\DTO\VolumeDiscountOutput;
use CDev\VolumeDiscounts\Logic\Order\Modifier\Discount;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Database;
use XLite\Model\Membership;
use XLite\Model\Zone;
use XLite\View\FormField\Input\PriceOrPercent;
use XLite\View\FormField\Select\AbsoluteOrPercent;

/**
 * Volume discount model
 *
 * @ORM\Entity
 * @ORM\Table  (name="volume_discounts",
 *      indexes={
 *          @ORM\Index (name="date_range", columns={"dateRangeBegin","dateRangeEnd"}),
 *          @ORM\Index (name="subtotal", columns={"subtotalRangeBegin"}),
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     shortName="Volume Discount",
 *     input=VolumeDiscountInput::class,
 *     output=VolumeDiscountOutput::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/volume_discounts/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/volume_discounts/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/volume_discounts/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "identifiers"={},
 *              "path"="/volume_discounts.{_format}"
 *          },
 *          "post"={
 *              "method"="POST",
 *              "identifiers"={},
 *              "path"="/volume_discounts.{_format}"
 *          }
 *     }
 * )
 */
class VolumeDiscount extends \XLite\Model\AEntity
{
    public const TYPE_PERCENT  = '%';
    public const TYPE_ABSOLUTE = '$';


    /**
     * Discount unique ID
     *
     * @var   integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Value
     *
     * @var   float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

    /**
     * Type
     *
     * @var   string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_PERCENT;

    /**
     * Subtotal range (begin)
     *
     * @var   float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $subtotalRangeBegin = 0;

    /**
     * Membership
     *
     * @var   \XLite\Model\Membership
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Membership")
     * @ORM\JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $membership;

    /**
     * Zones
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Zone", inversedBy="volumeDiscounts")
     * @ORM\JoinTable (name="zones_volume_discounts",
     *      joinColumns={@ORM\JoinColumn (name="volume_discount_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="zone_id", referencedColumnName="zone_id", onDelete="CASCADE")}
     * )
     */
    protected $zones;

    /**
     * Date range (begin)
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $dateRangeBegin = 0;

    /**
     * Date range (end)
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $dateRangeEnd = 0;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->zones = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Returns handling fee
     *
     * @return array
     */
    public function getDiscount()
    {
        return [
            PriceOrPercent::PRICE_VALUE => $this->getValue(),
            PriceOrPercent::TYPE_VALUE  => $this->getType() === static::TYPE_PERCENT
                ? AbsoluteOrPercent::TYPE_PERCENT
                : AbsoluteOrPercent::TYPE_ABSOLUTE
        ];
    }

    /**
     * Set Discount
     *
     * @param array $Discount
     * @return VolumeDiscount
     */
    public function setDiscount($Discount): VolumeDiscount
    {
        $this->setValue(
            $Discount[PriceOrPercent::PRICE_VALUE] ?? 0
        );

        $this->setType(
            isset($Discount[PriceOrPercent::TYPE_VALUE])
            && $Discount[PriceOrPercent::TYPE_VALUE] === AbsoluteOrPercent::TYPE_PERCENT
                ? static::TYPE_PERCENT
                : static::TYPE_ABSOLUTE
        );

        return $this;
    }

    /**
     * Check - discount is absolute or not
     *
     * @return boolean
     */
    public function isAbsolute()
    {
        return $this->getType() === static::TYPE_ABSOLUTE;
    }

    /**
     * Get discount amount
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    public function getAmount(\XLite\Model\Order $order)
    {
        $subTotal = $order->getSubtotal();

        /** @var \XLite\Model\Order\Surcharge $surcharge */
        foreach ($order->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT) as $surcharge) {
            if (
                $surcharge->getAvailable()
                && !$surcharge->getInclude()
                && $surcharge->getCode() !== Discount::MODIFIER_CODE
            ) {
                $subTotal += $order->getCurrency()->roundValue($surcharge->getValue());
            }
        }

        $discount = $this->isAbsolute()
            ? $this->getValue()
            : ($subTotal * $this->getValue() / 100);

        return min($discount, $subTotal);
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
     * Set value
     *
     * @param float $value
     * @return VolumeDiscount
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return VolumeDiscount
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set subtotalRangeBegin
     *
     * @param float $subtotalRangeBegin
     * @return VolumeDiscount
     */
    public function setSubtotalRangeBegin($subtotalRangeBegin)
    {
        $this->subtotalRangeBegin = $subtotalRangeBegin;
        return $this;
    }

    /**
     * Get subtotalRangeBegin
     *
     * @return float
     */
    public function getSubtotalRangeBegin()
    {
        return $this->subtotalRangeBegin;
    }

    /**
     * Set dateRangeBegin
     *
     * @param integer $dateRangeBegin
     * @return VolumeDiscount
     */
    public function setDateRangeBegin($dateRangeBegin)
    {
        $this->dateRangeBegin = $dateRangeBegin;
        return $this;
    }

    /**
     * Get dateRangeBegin
     *
     * @return integer
     */
    public function getDateRangeBegin()
    {
        return $this->dateRangeBegin;
    }

    /**
     * Set dateRangeEnd
     *
     * @param integer $dateRangeEnd
     * @return VolumeDiscount
     */
    public function setDateRangeEnd($dateRangeEnd)
    {
        $this->dateRangeEnd = $dateRangeEnd;
        return $this;
    }

    /**
     * Get dateRangeEnd
     *
     * @return integer
     */
    public function getDateRangeEnd()
    {
        return $this->dateRangeEnd;
    }

    /**
     * Get real dateRangeEnd ()
     *
     * @return integer
     */
    public function getRealDateRangeEnd()
    {
        return $this->dateRangeEnd == 0
            ? PHP_INT_MAX
            : $this->dateRangeEnd;
    }

    /**
     * Add zone
     *
     * @param Zone $zone
     *
     * @return VolumeDiscount
     */
    public function addZone(Zone $zone)
    {
        $this->zones[] = $zone;
        return $this;
    }

    /**
     * Get zones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Clear zones
     */
    public function clearZones(): void
    {
        if ($this->getZones()) {
            $this->getZones()->clear();
        }
    }

    /**
     * Set membership
     *
     * @param string|null|Membership $membership
     * @return VolumeDiscount
     */
    public function setMembership($membership)
    {
        if (is_string($membership)) {
            $this->membership = Database::getRepo(Membership::class)->find($membership);
            return $this;
        }

        $this->membership = $membership;
        return $this;
    }

    /**
     * Get membership
     *
     * @return Membership|null
     */
    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    /**
     * Check - volume discount is expired or not
     *
     * @return boolean
     */
    public function isExpired(): bool
    {
        return 0 < $this->getDateRangeEnd() && $this->getDateRangeEnd() < \XLite\Core\Converter::time();
    }
}
