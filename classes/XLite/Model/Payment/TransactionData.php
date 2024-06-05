<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction data storage
 *
 * @ORM\Entity
 * @ORM\Table (name="payment_transaction_data",
 *      indexes={
 *          @ORM\Index (name="tn", columns={"transaction_id","name"})
 *      }
 * )
 */
class TransactionData extends \XLite\Model\AEntity
{
    /**
     * Access level codes
     */
    public const ACCESS_ADMIN    = 'A';
    public const ACCESS_CUSTOMER = 'C';


    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $data_id;

    /**
     * Record name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $name;

    /**
     * Record public name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $label = '';

    /**
     * Access level
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $access_level = self::ACCESS_ADMIN;

    /**
     * Value
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $value;

    /**
     * Transaction
     *
     * @var \XLite\Model\Payment\Transaction
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Payment\Transaction", inversedBy="data")
     * @ORM\JoinColumn (name="transaction_id", referencedColumnName="transaction_id", onDelete="CASCADE")
     */
    protected $transaction;

    /**
     * Check record availability
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return (\XLite::isAdminZone() && $this->getAccessLevel() == static::ACCESS_ADMIN)
            || $this->getAccessLevel() == static::ACCESS_CUSTOMER;
    }

    /**
     * Get data_id
     *
     * @return integer
     */
    public function getDataId()
    {
        return $this->data_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TransactionData
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
     * @return TransactionData
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
     * Set access_level
     *
     * @param string $accessLevel
     * @return TransactionData
     */
    public function setAccessLevel($accessLevel)
    {
        $this->access_level = $accessLevel;
        return $this;
    }

    /**
     * Get access_level
     *
     * @return string
     */
    public function getAccessLevel()
    {
        return $this->access_level;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return TransactionData
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
     * Set transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction
     * @return TransactionData
     */
    public function setTransaction(\XLite\Model\Payment\Transaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Get transaction
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
