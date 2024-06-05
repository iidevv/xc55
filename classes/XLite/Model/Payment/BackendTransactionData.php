<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

use Doctrine\ORM\Mapping as ORM;

/**
 * Backend transaction data storage
 *
 * @ORM\Entity
 * @ORM\Table (name="payment_backend_transaction_data",
 *      indexes={
 *          @ORM\Index (name="tn", columns={"backend_transaction_id","name"})
 *      }
 * )
 */
class BackendTransactionData extends \XLite\Model\AEntity
{
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
    protected $access_level = \XLite\Model\Payment\TransactionData::ACCESS_ADMIN;

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
     * @var \XLite\Model\Payment\BackendTransaction
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Payment\BackendTransaction", inversedBy="data")
     * @ORM\JoinColumn (name="backend_transaction_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $transaction;

    /**
     * Check record availability
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return (\XLite::isAdminZone() && $this->getAccessLevel() == \XLite\Model\Payment\TransactionData::ACCESS_ADMIN)
            || $this->getAccessLevel() == \XLite\Model\Payment\TransactionData::ACCESS_CUSTOMER;
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
     * @return BackendTransactionData
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
     * @return BackendTransactionData
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
     * @return BackendTransactionData
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
     * @return BackendTransactionData
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
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     * @return BackendTransactionData
     */
    public function setTransaction(\XLite\Model\Payment\BackendTransaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Get transaction
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
