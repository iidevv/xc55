<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;

/**
 * Payment status multilingual data
 *
 * @ORM\Entity
 * @ORM\Table (name="skuvault_logs")
 */
class Log extends AEntity
{
    /**
     * Unique id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * Direction (SkuVault to X-Cart or X-Cart to SkuVault)
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=16, nullable=true)
     */
    protected $direction;

    /**
     * Status (Success / Error etc.)
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=16, nullable=true)
     */
    protected $status;

    /**
     * Operation (Sync Inventory / Sync Orders etc.)
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=32, nullable=true)
     */
    protected $operation;

    /**
     * Message
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $message;

    /**
     * Date
     *
     * @var int
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Log
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     * @return Log
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Log
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param string $operation
     * @return Log
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param int $date
     * @return Log
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }
}
