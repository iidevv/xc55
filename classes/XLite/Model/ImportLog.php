<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (name="import_logs")
 * @ORM\HasLifecycleCallbacks
 */
class ImportLog extends \XLite\Model\AEntity
{
    /**
     * Type codes
     */
    public const TYPE_WARNING = 'W';
    public const TYPE_ERROR   = 'E';
    public const TYPE_SKU     = 'S';

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
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=1)
     */
    protected $type = self::TYPE_WARNING;

    /**
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=32)
     */
    protected $code;

    /**
     * @var array
     *
     * @ORM\Column (type="array")
     */
    protected $arguments;

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $file;

    /**
     * @var int
     *
     * @ORM\Column (name="`row`", type="integer", options={"unsigned": true})
     */
    protected $row;

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $processor;

    /**
     * Update date
     *
     * @return void
     * @ORM\PrePersist
     */
    public function updateDate()
    {
        $this->setDate(\XLite\Core\Converter::time());
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
     * Set date
     *
     * @param integer $date
     * @return ImportLog
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
     * Set type
     *
     * @param string $type
     * @return ImportLog
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
     * Set code
     *
     * @param string $code
     * @return ImportLog
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
     * Set arguments
     *
     * @param array $arguments
     * @return ImportLog
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * Get arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return ImportLog
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set row
     *
     * @param integer $row
     * @return ImportLog
     */
    public function setRow($row)
    {
        $this->row = $row;
        return $this;
    }

    /**
     * Get row
     *
     * @return integer
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set processor
     *
     * @param string $processor
     * @return ImportLog
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
        return $this;
    }

    /**
     * Get processor
     *
     * @return string
     */
    public function getProcessor()
    {
        return $this->processor;
    }
}
