<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\Order;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class represents a Canada Post parcel
 *
 * @ORM\Entity
 * @ORM\Table (name="order_capost_parcels",
 *      indexes={
 *          @ORM\Index (name="status", columns={"status"}),
 *          @ORM\Index (name="number", columns={"number"})
 *      }
 * )
 */
class Parcel extends \XLite\Model\AEntity
{
    /**
     * Parcel statuses
     */
    public const STATUS_PROPOSED   = 'P';
    public const STATUS_CREATED    = 'C';
    public const STATUS_TRANSMITED = 'T';

    /**
     * Quote types
     */
    public const QUOTE_TYPE_CONTRACTED     = 'C';
    public const QUOTE_TYPE_NON_CONTRACTED = 'N';

    /**
     * Parcel (shipment) options
     */
    public const OPT_DEFAULT              = '';
    // Delivery way types
    public const OPT_WTD_HOLD_FOR_PICK_UP = 'HFP';
    public const OPT_WTD_LEAVE_AT_DOOR    = 'LAD';
    public const OPT_WTD_DO_NOT_SAFE_DROP = 'DNS';
    // Age proof values
    public const OPT_AGE_PROOF_18         = 'PA18';
    public const OPT_AGE_PROOF_19         = 'PA19';
    // Non-delivery handling codes (required for some U.S.A. and international shipments)
    public const OPT_RET_AT_SENDER_EXP    = 'RASE';
    public const OPT_RET_TO_SENDER        = 'RTS';
    public const OPT_ABANDON              = 'ABAN';
    // Other parcel (shipment) options codes
    public const OPT_SIGNATURE            = 'SO';
    public const OPT_COVERAGE             = 'COV';
    public const OPT_COD                  = 'COD';
    public const OPT_DELIVER_TO_PO        = 'D2PO';

    /**
     * Options classes
     */
    public const OPT_CLASS_WAY_TO_DELIVER = 'way_to_deliver';
    public const OPT_CLASS_AGE_PROOF      = 'age_proof';
    public const OPT_CLASS_SIGNATURE      = 'signature';
    public const OPT_CLASS_COVERAGE       = 'coverage';
    public const OPT_CLASS_NON_DELIVERY   = 'non_delivery';

    /**
     * Options schema fields
     */
    public const OPT_SCHEMA_CLASS           = 'class';
    public const OPT_SCHEMA_TITLE           = 'title';
    public const OPT_SCHEMA_TEMPLATE        = 'template';
    public const OPT_SCHEMA_OPTIONS         = 'options';
    public const OPT_SCHEMA_ALLOWED_OPTIONS = 'allowedOptions';
    public const OPT_SCHEMA_MANDATORY       = 'mandatory';
    public const OPT_SCHEMA_MULTIPLE        = 'multiple';

    /**
     * Parcel unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Parcel number
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $number;

    /**
     * Status code
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $status = self::STATUS_PROPOSED;

    /**
     * Previous status code
     *
     * @var string
     */
    protected $oldStatus = self::STATUS_PROPOSED;

    /**
     * Quote type
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $quoteType = self::QUOTE_TYPE_NON_CONTRACTED;

    /**
     * Parcel's order (referece to the orders model)
     *
     * @var \XLite\Model\Order
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="capostParcels")
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Parcel items (referece to the parcel's items model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\CanadaPost\Model\Order\Parcel\Item", mappedBy="parcel", cascade={"all"})
     */
    protected $items;

    /**
     * Parcel shipment info (return from "Create Shipment" and "Create Non-Contract Shipment" requests)
     *
     * @var \XC\CanadaPost\Model\Order\Parcel\Shipment
     *
     * @ORM\OneToOne (targetEntity="XC\CanadaPost\Model\Order\Parcel\Shipment", mappedBy="parcel", cascade={"all"})
     */
    protected $shipment;

    // {{{ Parcel dimensions and weight

    /**
     * Parcel box weight (max weight)
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $boxWeight = 0.0000;

    /**
     * Parcel box width
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $boxWidth = 0.0000;

    /**
     * Parcel box length
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $boxLength = 0.0000;

    /**
     * Parcel box height
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $boxHeight = 0.0000;

    // }}}

    // {{{ Parcel types

    /**
     * Is parcel a document
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $isDocument = false;

    /**
     * Is parcel unpackaged
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $isUnpackaged = false;

    /**
     * Is parcel a mailing tube
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $isMailingTube = false;

    /**
     * Is parcel oversized
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $isOversized = false;

    // }}}

    // {{{ Notifications

    /**
     * Send notification on shipment
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $notifyOnShipment = false;

    /**
     * Send notification on exception
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $notifyOnException = false;

    /**
     * Send notification on delivery
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $notifyOnDelivery = false;

    // }}}

    // {{{ Parcel options

    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $optSignature = false;

    /**
     * Option "Coverage amount"
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $optCoverage = 0.0000;

    /**
     * Options "Proof of age required - 18" and "Proof of age required - 19"
     *
     * @var string
     *
     * @ORM\Column (type="string", length=4)
     */
    protected $optAgeProof = self::OPT_DEFAULT;

    /**
     * Delivery way type (the "Card for pickup", "Do not safe drop", "Leave at door" options)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=3)
     */
    protected $optWayToDeliver = self::OPT_DEFAULT;

    /**
     * Non-delivery handling type (the "Return at Sender’s Expense", "Return to Sender", "Abandon" options)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=4)
     */
    protected $optNonDelivery = self::OPT_DEFAULT;

    // }}}

    /**
     * Non-delivery handling type (the "Return at Sender’s Expense", "Return to Sender", "Abandon" options)
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $hasRemovedItems = false;

    /**
     * Canada Post API calls errors
     *
     * @var null|array
     */
    protected $apiCallErrors = null;

    /**
     * Parcel status handlers list
     *
     * @var array
     */
    protected static $statusHandlers = [

        self::STATUS_PROPOSED => [
            self::STATUS_CREATED     => 'create',
        ],

        self::STATUS_CREATED => [
            self::STATUS_PROPOSED    => 'propose',
            self::STATUS_TRANSMITED  => 'transmit',
        ],

        self::STATUS_TRANSMITED      => [],
    ];

    // {{{ Service methods

    /**
     * Constructor
     *
     * @param array $data Entity properties (OPTIONAL)
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Set old status (not stored in the DB)
     *
     * @param string $value Status code
     *
     * @return void
     */
    public function setOldStatus($value)
    {
        $this->oldStatus = $value;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order Order object (OPTIONAL)
     *
     * @return void
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
    }

    /**
     * Add an item to parcel
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $newItem Item object
     *
     * @return void
     */
    public function addItem(\XC\CanadaPost\Model\Order\Parcel\Item $newItem)
    {
        $newItem->setParcel($this);

        $this->addItems($newItem);
    }

    /**
     * Set shipment
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Shipment $shipment Shipment object (OPTIONAL)
     *
     * @return void
     */
    public function setShipment(\XC\CanadaPost\Model\Order\Parcel\Shipment $shipment = null)
    {
        if ($shipment !== null) {
            $shipment->setParcel($this);
        }

        $this->shipment = $shipment;
    }

    /**
     * Clone object
     *
     * @param boolean $cloneItems Clone parcel items
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity($cloneItems = true)
    {
        $newParcel = parent::cloneEntity();

        if ($this->getOrder()) {
            $newParcel->setOrder($this->getOrder());
        }

        if (
            $cloneItems
            && $this->hasItems()
        ) {
            foreach ($this->getItems() as $item) {
                $newParcel->addItem($item);
            }
        }

        return $newParcel;
    }

    // }}}

    // {{{ Parcel (shipment) options methods

    /**
     * Get list of all valid parcel (shipment) options codes and their names
     *
     * @param string|null $option Option code
     *
     * @return array|null
     */
    public static function getValidOptions($option = null)
    {
        $list = [

            // "Way to deliver" class options
            static::OPT_WTD_HOLD_FOR_PICK_UP => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_WAY_TO_DELIVER,
                static::OPT_SCHEMA_TITLE     => 'Card (hold) for pickup',
            ],
            static::OPT_WTD_LEAVE_AT_DOOR    => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_WAY_TO_DELIVER,
                static::OPT_SCHEMA_TITLE     => 'Leave at door - do not card',
            ],
            static::OPT_WTD_DO_NOT_SAFE_DROP => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_WAY_TO_DELIVER,
                static::OPT_SCHEMA_TITLE     => 'Do not safe drop',
            ],

            // "Proof of age" class options
            static::OPT_AGE_PROOF_18         => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_AGE_PROOF,
                static::OPT_SCHEMA_TITLE     => 'Proof of Age Required - 18',
            ],
            static::OPT_AGE_PROOF_19         => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_AGE_PROOF,
                static::OPT_SCHEMA_TITLE     => 'Proof of Age Required - 19',
            ],

            // "Signature" class options
            static::OPT_SIGNATURE            => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_SIGNATURE,
                static::OPT_SCHEMA_TITLE     => 'Signature',
            ],

            // "Coverage" class options
            static::OPT_COVERAGE             => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_COVERAGE,
                static::OPT_SCHEMA_TITLE     => 'Coverage',
            ],

            // "Collect on delivery" option (not implemented yet)
            static::OPT_COD                  => [
                static::OPT_SCHEMA_TITLE     => 'Collect on delivery',
            ],

            // "Deliver to Post Office" option (not implemented yet)
            static::OPT_DELIVER_TO_PO        => [
                static::OPT_SCHEMA_TITLE     => 'Deliver to Post Office',
            ],

            // "Non-delivery handling" class options
            static::OPT_RET_AT_SENDER_EXP    => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_NON_DELIVERY,
                static::OPT_SCHEMA_TITLE     => 'Return at Sender\'s Expense',
            ],
            static::OPT_RET_TO_SENDER        => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_NON_DELIVERY,
                static::OPT_SCHEMA_TITLE     => 'Return to Sender',
            ],
            static::OPT_ABANDON              => [
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_NON_DELIVERY,
                static::OPT_SCHEMA_TITLE     => 'Abandon',
            ],
        ];

        return $option !== null
            ? ($list[$option] ?? null)
            : $list;
    }

    /**
     * Get list of specified class valid parcel (shipment) options codes and their names
     *
     * @param string $optionsClass Options class
     *
     * @return array|null
     */
    public static function getValidOptionsByClass($optionsClass)
    {
        $classOptions = [];

        if (!empty($optionsClass)) {
            $options = static::getValidOptions();

            foreach ($options as $k => $v) {
                if (isset($v[static::OPT_SCHEMA_CLASS]) && $optionsClass == $v[static::OPT_SCHEMA_CLASS]) {
                    $classOptions[$k] = $v;
                }
            }
        }

        return (empty($classOptions)) ? null : $classOptions;
    }

    /**
     * Get all visible valid parcel options classes
     *
     * @return array
     */
    public static function getValidOptionClasses()
    {
        $list = [
            static::OPT_CLASS_WAY_TO_DELIVER => [
                static::OPT_SCHEMA_TITLE     => 'Way to deliver',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/way_to_deliver.twig',
                static::OPT_SCHEMA_MULTIPLE  => true,
            ],
            static::OPT_CLASS_SIGNATURE      => [
                static::OPT_SCHEMA_TITLE     => 'Signature',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/signature.twig',
                static::OPT_SCHEMA_MULTIPLE  => false,
            ],
            static::OPT_CLASS_AGE_PROOF      => [
                static::OPT_SCHEMA_TITLE     => 'Proof of age',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/age_proof.twig',
                static::OPT_SCHEMA_MULTIPLE  => true,
            ],
            static::OPT_CLASS_COVERAGE       => [
                static::OPT_SCHEMA_TITLE     => 'Coverage amount',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/coverage.twig',
                static::OPT_SCHEMA_MULTIPLE  => false,
            ],
            static::OPT_CLASS_NON_DELIVERY   => [
                static::OPT_SCHEMA_TITLE     => 'Non-delivery instructions',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/non_delivery.twig',
                static::OPT_SCHEMA_MULTIPLE  => true,
            ],
        ];

        return $list;
    }

    /**
     * Get all allowed options classes for the parcel
     *
     * @return array
     */
    public function getAllowedOptionClasses()
    {
        $service = $this->getOrder()->getCapostDeliveryService();

        $allClasses = static::getValidOptionClasses();

        $allowedClasses = [];

        if ($service !== null) {
            $allOptions = static::getValidOptions();

            foreach ($service->getOptions() as $k => $v) {
                $code = $v->getCode();

                if (
                    isset($allOptions[$code])
                    && isset($allOptions[$code][static::OPT_SCHEMA_CLASS])
                ) {
                    $class = $allOptions[$code][static::OPT_SCHEMA_CLASS];

                    if (!isset($allowedClasses[$class])) {
                        $allowedClasses[$class] = $allClasses[$class];
                        $allowedClasses[$class][static::OPT_SCHEMA_MANDATORY] = $v->getMandatory();
                        $allowedClasses[$class][static::OPT_SCHEMA_ALLOWED_OPTIONS] = [];
                    }

                    if (
                        $v->getMandatory()
                        && !$allowedClasses[$class][static::OPT_SCHEMA_MANDATORY]
                    ) {
                        // Mark class as mandatory if one of the options is mandatory
                        $allowedClasses[$class][static::OPT_SCHEMA_MANDATORY] = $v->getMandatory();
                    }

                    $allowedClasses[$class][static::OPT_SCHEMA_ALLOWED_OPTIONS][$code] = $allOptions[$code];
                }
            }
        } else {
            $allowedClasses = $allClasses;
        }

        if (
            $this->isDeliveryToPostOffice()
            && isset($allowedClasses[static::OPT_CLASS_WAY_TO_DELIVER])
        ) {
            // Remove "Way to deliver" class (it's not supported with D2PO)
            unset($allowedClasses[static::OPT_CLASS_WAY_TO_DELIVER]);
        }

        return $allowedClasses;
    }

    /**
     * Get Canada Post delivery service details
     *
     * @return \XC\CanadaPost\Model\DeliveryService|null
     */
    public function getDeliveryService()
    {
        return $this->getOrder()->getCapostDeliveryService();
    }

    // }}}

    // {{{ Change parcel status routine

    /**
     * Set status
     *
     * @param string $value Status code
     *
     * @return boolean
     */
    public function setStatus($value)
    {
        $oldStatus = ($this->status != $value) ? $this->status : null;

        $result = false;

        $statusHandler = $this->getStatusHandler($oldStatus, $value);

        if (
            $oldStatus
            && $this->isPersistent()
            && !empty($statusHandler)
        ) {
            $result = $this->{'changeStatus' . ucfirst($statusHandler)}();

            if ($result) {
                $this->oldStatus = $oldStatus;
                $this->status = $value;
            }

            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }

    /**
     * Return base part of the certain "change status" handler name
     *
     * @param string $old Old status code
     * @param string $new New status code
     *
     * @return string
     */
    protected function getStatusHandler($old, $new)
    {
        return (isset(static::$statusHandlers[$old][$new])) ? static::$statusHandlers[$old][$new] : '';
    }

    /**
     * Status handler: "Created" to "Proposed" (void shipment)
     *
     * @return boolean
     */
    protected function changeStatusPropose()
    {
        $result = !$this->hasShipment();

        if ($this->canBeProposed()) {
            $isAllowedToRemove = true;

            if ($this->getQuoteType() == static::QUOTE_TYPE_CONTRACTED) {
                // Call API request if contracted shipment
                $isAllowedToRemove = $this->callApiVoidShipment();
            }

            if ($isAllowedToRemove) {
                $shipment = $this->getShipment();
                $this->setShipment(null);

                \XLite\Core\Database::getEM()->remove($shipment);
                \XLite\Core\Database::getEM()->flush();

                $result = true;
            }
        }

        return $result;
    }

    /**
     * Status handler: "Proposed" to "Created"
     *
     * @return boolean
     */
    protected function changeStatusCreate()
    {
        $capostConfig = \XLite\Core\Config::getInstance()->XC->CanadaPost;

        if ($capostConfig->quote_type == static::QUOTE_TYPE_NON_CONTRACTED) {
            // Call "Create Non-Contract Shipment" request
            $result = $this->callApiCreateNCShipment();
        } else {
            // Call "Create Shipment" request
            $result = $this->callApiCreateShipment();
        }

        // Update current qoute type
        $this->setQuoteType($capostConfig->quote_type);

        return $result;
    }

    /**
     * Status handler: "Created" to "Transmited"
     *
     * @return boolean
     */
    protected function changeStatusTransmit()
    {
        $result = false;

        if ($this->canBeTransmited()) {
            // Call "Transmit Shipments" request
            $result = $this->callApiTransmitShipment();
        }

        return $result;
    }

    /**
     * Check - is parcel/shipment can be created
     *
     * @return boolean
     */
    public function canBeCreated()
    {
        return ($this->getStatus() == static::STATUS_PROPOSED);
    }

    /**
     * Check - is parcel/shipment can be proposed
     *
     * @return boolean
     */
    public function canBeProposed()
    {
        return (
            $this->hasShipment()
            && $this->getStatus() == static::STATUS_CREATED
            && (
                $this->getQuoteType() == static::QUOTE_TYPE_NON_CONTRACTED
                || (
                    $this->getQuoteType() == static::QUOTE_TYPE_CONTRACTED
                    && \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type == static::QUOTE_TYPE_CONTRACTED
                )
            )
        );
    }

    /**
     * Check - is parcel/shipment can be transmitted
     *
     * @return boolean
     */
    public function canBeTransmited()
    {
        return $this->getStatus() === static::STATUS_CREATED
            && $this->hasShipment()
            && $this->getQuoteType() === static::QUOTE_TYPE_CONTRACTED
            && \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type === static::QUOTE_TYPE_CONTRACTED;
    }

    // }}}

    /**
     * Check - parcel has shipment assigned or not
     *
     * @return boolean
     */
    public function hasShipment()
    {
        return $this->getShipment() !== null;
    }

    /**
     * Check - parcel has item or not
     *
     * @return boolean
     */
    public function hasItems()
    {
        return 0 < $this->getItems()->count();
    }

    /**
     * Get subtotal of the parcel's items (in store currency)
     *
     * @return float
     */
    public function getAmount()
    {
        $amount = 0.00;

        if ($this->hasItems()) {
            foreach ($this->getItems() as $item) {
                $amount += $item->getAmount();
            }
        }

        return $amount;
    }

    /**
     * Get subtotal of the parcel's items (in store currency)
     *
     * @return float
     */
    public function getSubtotal()
    {
        $subtotal = 0.00;

        if ($this->hasItems()) {
            foreach ($this->getItems() as $item) {
                $subtotal += $item->getSubtotal();
            }
        }

        return $subtotal;
    }

    /**
     * Get total weight of the parcel's items (in store weight units)
     *
     * @return float
     */
    public function getWeight()
    {
        $weight = 0.00;

        if ($this->hasItems()) {
            foreach ($this->getItems() as $item) {
                $weight += $item->getTotalWeight();
            }
        }

        return $weight;
    }

    /**
     * Get total weight of the parcel's items in KG
     *
     * @param boolean $adjustFloatValue Flag - adjust float value or not (OPTIONAL)
     *
     * @return float
     */
    public function getWeightInKg($adjustFloatValue = false)
    {
        $weight = 0;

        if ($this->hasItems()) {
            foreach ($this->getItems() as $item) {
                $weight += $item->getTotalWeightInKg($adjustFloatValue);
            }
        }

        return $weight;
    }

    /**
     * Get maximum allowed weight of the parcel's box in KG
     *
     * @param boolean $adjustFloatValue Flag - adjust float value or not (OPTIONAL)
     *
     * @return float
     */
    public function getBoxWeightInKg($adjustFloatValue = false)
    {
        // Convert weight from store units to KG (weight must be in KG)
        $weight = \XLite\Core\Converter::convertWeightUnits(
            $this->getBoxWeight(),
            \XLite\Core\Config::getInstance()->Units->weight_unit,
            'kg'
        );

        if ($adjustFloatValue) {
            // Adjust according to the XML element schema
            $weight = \XC\CanadaPost\Core\Service\AService::adjustFloatValue($weight, 3, 0, 999.999);
        }

        return $weight;
    }

    /**
     * Check - is parcel overweight or not
     *
     * @return boolean
     */
    public function isOverWeight()
    {
        return ($this->getBoxWeight() < $this->getWeight());
    }

    /**
     * Check - is parcel editable or not
     *
     * @return boolean
     */
    public function isEditable()
    {
        return (
            $this->getStatus() == static::STATUS_PROPOSED
            && !$this->hasShipment()
        );
    }

    /**
     * Check - is parcel should be delivered to the Canada Post post office ot not
     *
     * @return boolean
     */
    public function isDeliveryToPostOffice()
    {
        return ($this->getOrder()->getCapostOffice());
    }

    /**
     * Check - is parcel locked ot not (API calls allowed or not)
     *
     * @return boolean
     */
    public function areAPICallsAllowed()
    {
        return (
            $this->hasItems()
            && !(
                $this->getStatus() != static::STATUS_PROPOSED
                && $this->getQuoteType() == static::QUOTE_TYPE_CONTRACTED
                && \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type == static::QUOTE_TYPE_NON_CONTRACTED
            )
        );
    }

    // {{{ Canada Post API calls

    /**
     * Get Canada Post API call errors
     *
     * @return null|array
     */
    public function getApiCallErrors()
    {
        return $this->apiCallErrors;
    }

    /**
     * Call Create Shipment request
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiCreateShipment()
    {
        $result = false;

        $shipment = $this->getShipment();

        if ($shipment !== null) {
            $this->apiCallErrors = [
                'CALL_ERROR' => 'Parcel already has shipment data'
            ];
        } else {
            $data = \XC\CanadaPost\Core\API::getInstance()->callCreateShipmentRequest($this);

            $result = $this->handleCreateShipmentApiCallResult(static::QUOTE_TYPE_CONTRACTED, $data);
        }

        return $result;
    }

    /**
     * Call Create Non-Contract Shipment request
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiCreateNCShipment()
    {
        $result = false;

        $shipment = $this->getShipment();

        if ($shipment !== null) {
            $this->apiCallErrors = [
                'CALL_ERROR' => 'Parcel already has shipment data'
            ];
        } else {
            $data = \XC\CanadaPost\Core\API::getInstance()->callCreateNCShipmentRequest($this);

            $result = $this->handleCreateShipmentApiCallResult(static::QUOTE_TYPE_NON_CONTRACTED, $data);
        }

        return $result;
    }

    /**
     * Handle return from "callApiCreateShipment" and "callApiCreateNCShipment" methods
     *
     * @param string                 $callType Call type
     * @param \XLite\Core\CommonCell $data     Returned value
     *
     * @return boolean
     */
    protected function handleCreateShipmentApiCallResult($callType, \XLite\Core\CommonCell $data)
    {
        $result = false;

        if (isset($data->errors)) {
            // Parse errors
            $this->apiCallErrors = $data->errors;
        } elseif (isset($data->shipmentId)) {
            // Parse valid response
            $shipment = new \XC\CanadaPost\Model\Order\Parcel\Shipment();
            $shipment->setParcel($this);

            $this->setShipment($shipment);

            \XLite\Core\Database::getEM()->persist($shipment);

            foreach (['shipmentId', 'shipmentStatus', 'trackingPin', 'returnTrackingPin', 'poNumber'] as $_field) {
                $shipment->{'set' . \Includes\Utils\Converter::convertToUpperCamelCase($_field)}($data->{$_field});
            }

            if (isset($data->links)) {
                foreach ($data->links as $_link) {
                    $link = new \XC\CanadaPost\Model\Order\Parcel\Shipment\Link();
                    $link->setShipment($shipment);

                    $shipment->addLink($link);

                    foreach (['rel', 'href', 'mediaType', 'idx'] as $_field) {
                        $link->{'set' . \Includes\Utils\Converter::convertToUpperCamelCase($_field)}($_link->{$_field});
                    }
                }
            }

            \XLite\Core\Database::getEM()->flush();

            $result = true;
        }

        return $result;
    }

    /**
     * Call "Void Shipment" request (for Contracted shipments only)
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiVoidShipment()
    {
        $result = false;

        $data = \XC\CanadaPost\Core\API::getInstance()->callVoidShipmentRequest($this);

        if (isset($data->errors)) {
            // Save errors
            $this->apiCallErrors = $data->errors;
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * Call "Transmit Shipments" request (for Contracted shipments only)
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiTransmitShipment()
    {
        $result = false;

        $data = \XC\CanadaPost\Core\API::getInstance()->callTransmitShipmentsRequest($this);

        if (isset($data->errors)) {
            // Save errors
            $this->apiCallErrors = $data->errors;
        } else {
            // Valid result

            sleep(2); // time to generate manifests

            $shipment = $this->getShipment();

            foreach ($data->links as $link) {
                $manifest = new \XC\CanadaPost\Model\Order\Parcel\Manifest(
                    [
                        'rel'        => $link->rel,
                        'href'       => $link->href,
                        'media_type' => $link->mediaType,
                    ]
                );

                if (isset($link->idx)) {
                    $manifest->setIdx($link->idx);
                }

                \XLite\Core\Database::getEM()->persist($manifest);

                $shipment->addManifest($manifest);

                if (
                    !$manifest->callApiGetManifest()
                    && $manifest->getApiCallErrors()
                ) {
                    // Error is occurred
                    if ($this->apiCallErrors === null) {
                        $this->apiCallErrors = [];
                    }

                    $this->apiCallErrors += $manifest->getApiCallErrors();
                }
            }

            \XLite\Core\Database::getEM()->flush();

            $result = true;
        }

        return $result;
    }

    // }}}

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
     * Set number
     *
     * @param integer $number
     * @return Parcel
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set quoteType
     *
     * @param string $quoteType
     * @return Parcel
     */
    public function setQuoteType($quoteType)
    {
        $this->quoteType = $quoteType;
        return $this;
    }

    /**
     * Get quoteType
     *
     * @return string
     */
    public function getQuoteType()
    {
        return $this->quoteType;
    }

    /**
     * Set boxWeight
     *
     * @param float $boxWeight
     * @return Parcel
     */
    public function setBoxWeight($boxWeight)
    {
        $this->boxWeight = $boxWeight;
        return $this;
    }

    /**
     * Get boxWeight
     *
     * @return float
     */
    public function getBoxWeight()
    {
        return $this->boxWeight;
    }

    /**
     * Set boxWidth
     *
     * @param float $boxWidth
     * @return Parcel
     */
    public function setBoxWidth($boxWidth)
    {
        $this->boxWidth = $boxWidth;
        return $this;
    }

    /**
     * Get boxWidth
     *
     * @return float
     */
    public function getBoxWidth()
    {
        return $this->boxWidth;
    }

    /**
     * Set boxLength
     *
     * @param float $boxLength
     * @return Parcel
     */
    public function setBoxLength($boxLength)
    {
        $this->boxLength = $boxLength;
        return $this;
    }

    /**
     * Get boxLength
     *
     * @return float
     */
    public function getBoxLength()
    {
        return $this->boxLength;
    }

    /**
     * Get box length in cm
     *
     * @return float
     */
    public function getBoxLengthInCm()
    {
        return $this->getValueInCm($this->getBoxLength());
    }

    /**
     * Get box width in cm
     *
     * @return float
     */
    public function getBoxWidthInCm()
    {
        return $this->getValueInCm($this->getBoxWidth());
    }

    /**
     * Get box height in cm
     *
     * @return float
     */
    public function getBoxHeightInCm()
    {
        return $this->getValueInCm($this->getBoxHeight());
    }

    /**
     * Set boxHeight in cm
     *
     * @param float $boxHeight
     */
    public function setBoxHeightInCm($boxHeight)
    {
        $this->setBoxHeight($this->convertValueFromCm($boxHeight));
    }

    /**
     * Set boxWidth in cm
     *
     * @param float $boxWidth
     */
    public function setBoxWidthInCm($boxWidth)
    {
        $this->setBoxWidth($this->convertValueFromCm($boxWidth));
    }

    /**
     * Set boxLength in cm
     *
     * @param float $boxLength
     */
    public function setBoxLengthInCm($boxLength)
    {
        $this->setBoxLength($this->convertValueFromCm($boxLength));
    }

    /**
     * Get some value in cm
     *
     * @param float $value
     *
     * @return float
     */
    protected function getValueInCm($value)
    {
        return \XLite\Core\Converter::convertDimensionUnits(
            $value,
            \XLite\Core\Config::getInstance()->Units->dim_unit,
            'cm'
        );
    }

    /**
     * Convert cm value to system units
     *
     * @param float $value
     *
     * @return float
     */
    protected function convertValueFromCm($value)
    {
        return \XLite\Core\Converter::convertDimensionUnits(
            $value,
            'cm',
            \XLite\Core\Config::getInstance()->Units->dim_unit
        );
    }

    /**
     * Set boxHeight
     *
     * @param float $boxHeight
     * @return Parcel
     */
    public function setBoxHeight($boxHeight)
    {
        $this->boxHeight = $boxHeight;
        return $this;
    }

    /**
     * Get boxHeight
     *
     * @return float
     */
    public function getBoxHeight()
    {
        return $this->boxHeight;
    }

    /**
     * Set isDocument
     *
     * @param boolean $isDocument
     * @return Parcel
     */
    public function setIsDocument($isDocument)
    {
        $this->isDocument = (bool)$isDocument;
        return $this;
    }

    /**
     * Get isDocument
     *
     * @return boolean
     */
    public function getIsDocument()
    {
        return $this->isDocument;
    }

    /**
     * Set isUnpackaged
     *
     * @param boolean $isUnpackaged
     * @return Parcel
     */
    public function setIsUnpackaged($isUnpackaged)
    {
        $this->isUnpackaged = (bool)$isUnpackaged;
        return $this;
    }

    /**
     * Get isUnpackaged
     *
     * @return boolean
     */
    public function getIsUnpackaged()
    {
        return $this->isUnpackaged;
    }

    /**
     * Set isMailingTube
     *
     * @param boolean $isMailingTube
     * @return Parcel
     */
    public function setIsMailingTube($isMailingTube)
    {
        $this->isMailingTube = (bool)$isMailingTube;
        return $this;
    }

    /**
     * Get isMailingTube
     *
     * @return boolean
     */
    public function getIsMailingTube()
    {
        return $this->isMailingTube;
    }

    /**
     * Set isOversized
     *
     * @param boolean $isOversized
     * @return Parcel
     */
    public function setIsOversized($isOversized)
    {
        $this->isOversized = (bool)$isOversized;
        return $this;
    }

    /**
     * Get isOversized
     *
     * @return boolean
     */
    public function getIsOversized()
    {
        return $this->isOversized;
    }

    /**
     * Set notifyOnShipment
     *
     * @param boolean $notifyOnShipment
     * @return Parcel
     */
    public function setNotifyOnShipment($notifyOnShipment)
    {
        $this->notifyOnShipment = (bool)$notifyOnShipment;
        return $this;
    }

    /**
     * Get notifyOnShipment
     *
     * @return boolean
     */
    public function getNotifyOnShipment()
    {
        return $this->notifyOnShipment;
    }

    /**
     * Set notifyOnException
     *
     * @param boolean $notifyOnException
     * @return Parcel
     */
    public function setNotifyOnException($notifyOnException)
    {
        $this->notifyOnException = (bool)$notifyOnException;
        return $this;
    }

    /**
     * Get notifyOnException
     *
     * @return boolean
     */
    public function getNotifyOnException()
    {
        return $this->notifyOnException;
    }

    /**
     * Set notifyOnDelivery
     *
     * @param boolean $notifyOnDelivery
     * @return Parcel
     */
    public function setNotifyOnDelivery($notifyOnDelivery)
    {
        $this->notifyOnDelivery = (bool)$notifyOnDelivery;
        return $this;
    }

    /**
     * Get notifyOnDelivery
     *
     * @return boolean
     */
    public function getNotifyOnDelivery()
    {
        return $this->notifyOnDelivery;
    }

    /**
     * Set optSignature
     *
     * @param boolean $optSignature
     * @return Parcel
     */
    public function setOptSignature($optSignature)
    {
        $this->optSignature = (bool)$optSignature;
        return $this;
    }

    /**
     * Get optSignature
     *
     * @return boolean
     */
    public function getOptSignature()
    {
        return $this->optSignature;
    }

    /**
     * Set optCoverage
     *
     * @param float $optCoverage
     * @return Parcel
     */
    public function setOptCoverage($optCoverage)
    {
        $this->optCoverage = $optCoverage;
        return $this;
    }

    /**
     * Get optCoverage
     *
     * @return float
     */
    public function getOptCoverage()
    {
        return $this->optCoverage;
    }

    /**
     * Set optAgeProof
     *
     * @param string $optAgeProof
     * @return Parcel
     */
    public function setOptAgeProof($optAgeProof)
    {
        $this->optAgeProof = $optAgeProof;
        return $this;
    }

    /**
     * Get optAgeProof
     *
     * @return string
     */
    public function getOptAgeProof()
    {
        return $this->optAgeProof;
    }

    /**
     * Set optWayToDeliver
     *
     * @param string $optWayToDeliver
     * @return Parcel
     */
    public function setOptWayToDeliver($optWayToDeliver)
    {
        $this->optWayToDeliver = $optWayToDeliver;
        return $this;
    }

    /**
     * Get optWayToDeliver
     *
     * @return string
     */
    public function getOptWayToDeliver()
    {
        return $this->optWayToDeliver;
    }

    /**
     * Set optNonDelivery
     *
     * @param string $optNonDelivery
     * @return Parcel
     */
    public function setOptNonDelivery($optNonDelivery)
    {
        $this->optNonDelivery = $optNonDelivery;
        return $this;
    }

    /**
     * Get optNonDelivery
     *
     * @return string
     */
    public function getOptNonDelivery()
    {
        return $this->optNonDelivery;
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

    /**
     * Get order
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        return $this->order ? $this->order->getCurrency() : null;
    }

    /**
     * Add items
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Item $items
     * @return Parcel
     */
    public function addItems(\XC\CanadaPost\Model\Order\Parcel\Item $items)
    {
        $this->items[] = $items;
        return $this;
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get shipment
     *
     * @return \XC\CanadaPost\Model\Order\Parcel\Shipment
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * Remove parcel items whose orderItem has been removed
     *
     * @return void
     */
    public function removeIrrelevantParcelItems()
    {
        foreach ($this->getItems() as $item) {
            if (!$item->getOrderItem()) {
                $this->hasRemovedItems = true;
                \XLite\Core\Database::getRepo('XC\CanadaPost\Model\Order\Parcel\Item')->deleteById($item->getId());
            }
        }
    }

    /**
     * Return true if parcel has removed items
     *
     * @return boolean
     */
    public function hasRemovedItems()
    {
        return $this->hasRemovedItems;
    }
}
