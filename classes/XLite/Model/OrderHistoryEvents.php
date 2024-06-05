<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\OrderHistory\DTO\OrderHistoryOutput as Output;

/**
 * Order history events
 * todo: rename to OrderHistoryEvent
 *
 * @ORM\Entity
 * @ORM\Table (name="order_history_events")
 * @ORM\HasLifecycleCallbacks
 * @ApiPlatform\ApiResource(
 *     shortName="History Event",
 *     output=Output::class,
 *     itemOperations={},
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/orders/{id}/history.{_format}",
 *              "requirements"={"id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of order history events",
 *                  "responses"={
 *                      "404"={
 *                          "description"="Resource not found"
 *                      }
 *                  },
 *                  "parameters"={
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "get_cart_history_events"={
 *              "method"="GET",
 *              "path"="/carts/{id}/history.{_format}",
 *              "requirements"={"id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of cart history events",
 *                  "responses"={
 *                      "404"={
 *                          "description"="Resource not found"
 *                      }
 *                  },
 *                  "parameters"={
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class OrderHistoryEvents extends \XLite\Model\AEntity
{
    /**
     * Order history event unique id
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $event_id;

    /**
     * Event creation timestamp
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date;

    /**
     * Code of event
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $code;

    /**
     * Human-readable description of event
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1024, nullable=true)
     */
    protected $description;

    /**
     * Data for human-readable description
     *
     * @var string
     *
     * @ORM\Column (type="array")
     */
    protected $data;

    /**
     * Event comment
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $comment = '';

    /**
     * Event details
     *
     * @var \XLite\Model\OrderHistoryEventsData[]
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\OrderHistoryEventsData", mappedBy="event", cascade={"all"})
     */
    protected $details;

    /**
     * Relation to a order entity
     *
     * @var \XLite\Model\Order
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="events", fetch="LAZY")
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Author profile of the event
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne   (targetEntity="XLite\Model\Profile", inversedBy="event", cascade={"merge","detach","persist"})
     * @ORM\JoinColumn (name="author_id", referencedColumnName="profile_id", onDelete="SET NULL")
     */
    protected $author;

    /**
     * Author name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $authorName;

    /**
     * Author IP
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $authorIp = '';

    /**
     * Prepare order event before save data operation
     *
     * @return void
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prepareBeforeSave()
    {
        if (!is_numeric($this->date)) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->details = new ArrayCollection();
    }

    /**
     * Description getter
     *
     * @return string
     */
    public function getDescription()
    {
        return static::t($this->description, (array)$this->getData());
    }

    /**
     * Details setter
     *
     * @param array $details Array of event details array($name => $value)
     *
     * @return void
     */
    public function setDetails(array $details)
    {
        foreach ($details as $detail) {
            $data = new \XLite\Model\OrderHistoryEventsData();
            $data->setName($detail['name']);
            $data->setValue($detail['value']);

            $this->addDetails($data);
            $data->setEvent($this);
        }
    }

    /**
     * Clone order and all related data
     *
     * @return \XLite\Model\OrderHistoryEvents
     */
    public function cloneEntity()
    {
        $entity = parent::cloneEntity();

        // Clone order details
        if ($this->getDetails()) {
            foreach ($this->getDetails() as $detail) {
                $cloned = $detail->cloneEntity();
                $entity->addDetails($cloned);
                $cloned->setEvent($entity);
            }
        }

        return $entity;
    }

    /**
     * Get event_id
     *
     * @return integer
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return OrderHistoryEvents
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
     * Set code
     *
     * @param string $code
     * @return OrderHistoryEvents
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
     * Set description
     *
     * @param string $description
     * @return OrderHistoryEvents
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return OrderHistoryEvents
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return OrderHistoryEvents
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Add details
     *
     * @param \XLite\Model\OrderHistoryEventsData $details
     * @return OrderHistoryEvents
     */
    public function addDetails(\XLite\Model\OrderHistoryEventsData $details)
    {
        $this->details[] = $details;
        return $this;
    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     * @return OrderHistoryEvents
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

    /**
     * Set author
     *
     * @param \XLite\Model\Profile $author
     * @return OrderHistoryEvents
     */
    public function setAuthor(\XLite\Model\Profile $author = null)
    {
        $this->author = $author;

        if ($author && $author->isAdmin()) {
            $this->setAuthorName($author->getLogin());
        }

        return $this;
    }

    /**
     * Get author
     *
     * @return \XLite\Model\Profile
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getAuthorIp()
    {
        return $this->authorIp;
    }

    /**
     * @param string $authorIp
     *
     * @return $this
     */
    public function setAuthorIp($authorIp)
    {
        $this->authorIp = $authorIp;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     *
     * @return $this
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }

    public function showAuthor()
    {
        return ($this->getAuthor() && $this->getAuthor()->isAdmin())
            || $this->getAuthorName()
            || $this->getAuthorIp();
    }
}
