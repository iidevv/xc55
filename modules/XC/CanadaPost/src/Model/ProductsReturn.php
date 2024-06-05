<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class represents a products return
 *
 * @ORM\Entity
 * @ORM\Table  (name="capost_returns",
 *      indexes={
 *          @ORM\Index (name="date", columns={"date"}),
 *          @ORM\Index (name="status", columns={"status"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class ProductsReturn extends \XLite\Model\AEntity
{
    /**
     * Return statuses
     */
    public const STATUS_INIT     = 'I';
    public const STATUS_REJECTED = 'R';
    public const STATUS_APPROVED = 'A';

    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Referece to the return items model
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\CanadaPost\Model\ProductsReturn\Item", mappedBy="return", cascade={"all"})
     */
    protected $items;

    /**
     * This structure represents a list of links
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\CanadaPost\Model\ProductsReturn\Link", mappedBy="return", cascade={"all"})
     */
    protected $links;

    /**
     * Referece to the orders model
     *
     * @var \XLite\Model\Order
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="capostReturns")
     * @ORM\JoinColumn (name="orderId", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Status code
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $status = self::STATUS_INIT;

    /**
     * Previous status code
     *
     * @var string
     */
    protected $oldStatus = self::STATUS_INIT;

    /**
     * Creation timestamp
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * Last renew timestamp
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $lastRenewDate = 0;

    /**
     * Customer notes
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $notes = '';

    /**
     * Admin notes
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $adminNotes = '';

    /**
     * Tracking PIN code
     *
     * @var string
     *
     * @ORM\Column (type="string", length=16, nullable=true)
     */
    protected $trackingPin;

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
        $this->links = new \Doctrine\Common\Collections\ArrayCollection();

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
     * Add an item
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Item $newItem Item object
     *
     * @return void
     */
    public function addItem(\XC\CanadaPost\Model\ProductsReturn\Item $newItem)
    {
        $newItem->setReturn($this);

        $this->addItems($newItem);
    }

    /**
     * Add a link
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Link $newLink Link model
     *
     * @return void
     */
    public function addLink(\XC\CanadaPost\Model\ProductsReturn\Link $newLink)
    {
        $newLink->setReturn($this);

        $this->addLinks($newLink);
    }

    // }}}

    /**
     * Return list of all allowed product return statuses
     *
     * @param string $status Status to get OPTIONAL
     *
     * @return array|string
     */
    public static function getAllowedStatuses($status = null)
    {
        $list = [
            static::STATUS_INIT     => 'Requires authorization',
            static::STATUS_APPROVED => 'Approved',
            static::STATUS_REJECTED => 'Rejected',
        ];

        return isset($status)
            ? ($list[$status] ?? null)
            : $list;
    }

    /**
     * Get formatted return ID
     *
     * @return string
     */
    public function getNumber()
    {
        return '#' . str_pad($this->getId(), 5, 0, STR_PAD_LEFT);
    }

    // {{{ Change status routine

    /**
     * Status handlers list
     *
     * @var array
     */
    protected static $statusHandlers = [
        self::STATUS_INIT         => [
            self::STATUS_REJECTED => 'reject',
            self::STATUS_APPROVED => 'approve',
        ],
        self::STATUS_APPROVED     => [],
        self::STATUS_REJECTED     => [],
    ];

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
            $result = $this->{'handleStatusChange' . ucfirst($statusHandler)}();

            if ($result) {
                $this->oldStatus = $oldStatus;
                $this->status = $value;
            }

            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }

    /**
     * Check if product return can be proposed
     *
     * @return boolean
     */
    public function canBeApproved()
    {
        return (
            $this->getStatus() == static::STATUS_INIT
        );
    }

    /**
     * Check if product return can be transmitted
     *
     * @return boolean
     */
    public function canBeRejected()
    {
        return (
            $this->getStatus() == static::STATUS_INIT
        );
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
     * Status change handler: "Requires Authorization" to "Approved"
     *
     * @return boolean
     */
    protected function handleStatusChangeApprove()
    {
        $result = false;

        if ($this->canBeApproved()) {
            $result = $this->callApiCreateAuthorizedReturn();

            if ($result) {
                // Send email notification
                \XLite\Core\Mailer::sendProductsReturnApproved($this);
            }

            $result = true;
        }

        return $result;
    }

    /**
     * Status change handler: "Requires Authorization" to "Rejected"
     *
     * @return boolean
     */
    protected function handleStatusChangeReject()
    {
        $result = false;

        if ($this->canBeRejected()) {
            // Send email notifications
            \XLite\Core\Mailer::sendProductsReturnRejected($this);

            $result = true;
        }

        return $result;
    }

    // }}}

    // {{{ Lifecycle callbacks

    /**
     * Prepare before saving
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prepareBeforeSave()
    {
        if (!is_numeric($this->date) || !is_int($this->date)) {
            $this->setDate(\XLite\Core\Converter::time());
        }

        $this->setLastRenewDate(\XLite\Core\Converter::time());
    }

    // }}}

    // {{{ Helper methods

    /**
     * Get total return items amount
     *
     * @return integer
     */
    public function getItemsTotalAmount()
    {
        $totalAmount = 0;

        foreach ($this->getItems() as $item) {
            $totalAmount += $item->getAmount();
        }

        return $totalAmount;
    }

    /**
     * Get total cost of all returned items
     *
     * @return float
     */
    public function getItemsTotalCost()
    {
        $totalCost = 0;

        foreach ($this->getItems() as $item) {
            $totalCost += $item->getOrderItem()->getPrice() * $item->getAmount();
        }

        return $totalCost;
    }

    /**
     * Get total weight of all returned items
     *
     * @return float
     */
    public function getItemsTotalWeight()
    {
        $totalWeight = 0;

        foreach ($this->getItems() as $item) {
            $totalWeight += $item->getOrderItem()->getObject()->getWeight() * $item->getAmount();
        }

        return $totalWeight;
    }

    /**
     * Check - return has links or not
     *
     * @return boolean
     */
    public function hasLinks()
    {
        return 0 < $this->getLinks()->count();
    }

    /**
     * Get "return label" link model
     *
     * @return \XC\CanadaPost\Model\Repo\ProductsReturn|null
     */
    public function getReturnLabelLink()
    {
        return $this->getLinkByRel('returnLabel');
    }

    /**
     * Get link by rel field
     *
     * @param string $rel Link's rel field value
     *
     * @return \XC\CanadaPost\Model\Repo\ProductsReturn|null
     */
    public function getLinkByRel($rel)
    {
        $link = null;

        foreach ($this->getLinks() as $_link) {
            if ($_link->getRel() == $rel) {
                $link = $_link;
                break;
            }
        }

        return $link;
    }

    // }}}

    // {{{ Canda Post API calls

    /**
     * Canada Post API calls errors
     *
     * @var null|array
     */
    protected $apiCallErrors = null;

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
     * Call "Create Authorized Return" request
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiCreateAuthorizedReturn()
    {
        $result = false;

        if ($this->hasLinks()) {
            // Return already has links (documents)
            // TODO: probably here should be the procedure that will check all links and download it's files

            $result = true;
        } else {
            $data = \XC\CanadaPost\Core\Service\Returns::getInstance()->callCreateAuthorizedReturnByProductsReturn($this);

            $result = $this->handleCreateAuthorizedReturnResult($data);

            if ($result) {
                // Dowload documents (aka artifacts)
                sleep(2); // lets give to Canada Post server 2 seconds to generate PDF documents

                $this->downloadArtifacts();
            }
        }

        return $result;
    }

    /**
     * Handle "Create Authorized Return" request return
     *
     * @param \XLite\Core\CommonCell $data Returned value
     *
     * @return boolean
     */
    protected function handleCreateAuthorizedReturnResult(\XLite\Core\CommonCell $data)
    {
        $result = false;

        if (isset($data->errors)) {
            // Parse errors
            $this->apiCallErrors = $data->errors;
        } elseif (isset($data->authorizedReturnInfo)) {
            $this->trackingPin = $data->authorizedReturnInfo->trackingPin;

            foreach ($data->authorizedReturnInfo->links as $_link) {
                $link = new \XC\CanadaPost\Model\ProductsReturn\Link();
                $link->setReturn($this);

                $this->addLink($link);

                \XLite\Core\Database::getEM()->persist($link);

                foreach (['rel', 'href', 'mediaType', 'idx'] as $_field) {
                    $link->{'set' . \Includes\Utils\Converter::convertToUpperCamelCase($_field)}($_link->{$_field});
                }
            }

            \XLite\Core\Database::getEM()->flush();

            $result = true;
        }

        return $result;
    }

    /**
     * Download related artifacts (documents)
     *
     * @return void
     */
    protected function downloadArtifacts()
    {
        $links = $this->getLinks();

        if (isset($links)) {
            foreach ($links as $k => $link) {
                $link->callApiGetArtifact();

                if ($link->getApiCallErrors()) {
                    // Save errors
                    // $this->apiCallErrors = array_merge((array) $this->apiCallErrors, $link->getApiCallErrors());
                    // TODO: change errors API
                }
            }
        }

        \XLite\Core\Database::getEM()->flush();
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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return ProductsReturn
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set lastRenewDate
     *
     * @param integer $lastRenewDate
     * @return ProductsReturn
     */
    public function setLastRenewDate($lastRenewDate)
    {
        $this->lastRenewDate = $lastRenewDate;
        return $this;
    }

    /**
     * Get lastRenewDate
     *
     * @return integer
     */
    public function getLastRenewDate()
    {
        return $this->lastRenewDate;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ProductsReturn
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set adminNotes
     *
     * @param string $adminNotes
     * @return ProductsReturn
     */
    public function setAdminNotes($adminNotes)
    {
        $this->adminNotes = $adminNotes;
        return $this;
    }

    /**
     * Get adminNotes
     *
     * @return string
     */
    public function getAdminNotes()
    {
        return $this->adminNotes;
    }

    /**
     * Set trackingPin
     *
     * @param string $trackingPin
     * @return ProductsReturn
     */
    public function setTrackingPin($trackingPin)
    {
        $this->trackingPin = $trackingPin;
        return $this;
    }

    /**
     * Get trackingPin
     *
     * @return string
     */
    public function getTrackingPin()
    {
        return $this->trackingPin;
    }

    /**
     * Add items
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Item $items
     * @return ProductsReturn
     */
    public function addItems(\XC\CanadaPost\Model\ProductsReturn\Item $items)
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
     * Add links
     *
     * @param \XC\CanadaPost\Model\ProductsReturn\Link $links
     * @return ProductsReturn
     */
    public function addLinks(\XC\CanadaPost\Model\ProductsReturn\Link $links)
    {
        $this->links[] = $links;
        return $this;
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLinks()
    {
        return $this->links;
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
