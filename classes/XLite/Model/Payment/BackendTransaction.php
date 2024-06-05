<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment backend transaction
 *
 * @ORM\Entity
 * @ORM\Table  (name="payment_backend_transactions",
 *      indexes={
 *          @ORM\Index (name="td", columns={"transaction_id","date"})
 *      }
 * )
 */
class BackendTransaction extends \XLite\Model\AEntity
{
    /**
     * Transaction status codes
     */
    public const STATUS_INITIALIZED = 'I';
    public const STATUS_INPROGRESS  = 'P';
    public const STATUS_SUCCESS     = 'S';
    public const STATUS_PENDING     = 'W';
    public const STATUS_FAILED      = 'F';

    /**
     * Transaction types
     */
    public const TRAN_TYPE_AUTH          = 'auth';
    public const TRAN_TYPE_SALE          = 'sale';
    public const TRAN_TYPE_CAPTURE       = 'capture';
    public const TRAN_TYPE_CAPTURE_PART  = 'capturePart';
    public const TRAN_TYPE_CAPTURE_MULTI = 'captureMulti';
    public const TRAN_TYPE_VOID          = 'void';
    public const TRAN_TYPE_VOID_PART     = 'voidPart';
    public const TRAN_TYPE_VOID_MULTI    = 'voidMulti';
    public const TRAN_TYPE_REFUND        = 'refund';
    public const TRAN_TYPE_REFUND_PART   = 'refundPart';
    public const TRAN_TYPE_REFUND_MULTI  = 'refundMulti';
    public const TRAN_TYPE_GET_INFO      = 'getInfo';
    public const TRAN_TYPE_ACCEPT        = 'accept';
    public const TRAN_TYPE_DECLINE       = 'decline';
    public const TRAN_TYPE_TEST          = 'test';


    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Transaction creation timestamp
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date;

    /**
     * Status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $status = self::STATUS_INITIALIZED;

    /**
     * Transaction value
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

    /**
     * Transaction type
     *
     * @var string
     *
     * @ORM\Column (type="string", length=20)
     */
    protected $type;

    /**
     * Payment transactions
     *
     * @var \XLite\Model\Payment\Transaction
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Payment\Transaction", inversedBy="backend_transactions")
     * @ORM\JoinColumn (name="transaction_id", referencedColumnName="transaction_id", onDelete="CASCADE")
     */
    protected $payment_transaction;

    /**
     * Transaction data
     *
     * @var \XLite\Model\Payment\BackendTransactionData
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Payment\BackendTransactionData", mappedBy="transaction", cascade={"all"})
     */
    protected $data;

    /**
     * Get types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TRAN_TYPE_AUTH          => 'Authorize',
            static::TRAN_TYPE_SALE          => 'Sale',
            static::TRAN_TYPE_CAPTURE       => 'Capture',
            static::TRAN_TYPE_CAPTURE_PART  => 'Capture partially',
            static::TRAN_TYPE_CAPTURE_MULTI => 'Capture multiple',
            static::TRAN_TYPE_VOID          => 'Void',
            static::TRAN_TYPE_VOID_PART     => 'Void partially',
            static::TRAN_TYPE_VOID_MULTI    => 'Void multiple',
            static::TRAN_TYPE_REFUND        => 'Refund',
            static::TRAN_TYPE_REFUND_PART   => 'Refund partially',
            static::TRAN_TYPE_REFUND_MULTI  => 'Refund multiple',
            static::TRAN_TYPE_GET_INFO      => 'Get information',
            static::TRAN_TYPE_ACCEPT        => 'Accept',
            static::TRAN_TYPE_DECLINE       => 'Decline',
            static::TRAN_TYPE_TEST          => 'Test',
        ];
    }

    /**
     * Get charge value modifier
     *
     * @return float
     */
    public function getChargeValueModifier()
    {
        $value = 0;

        if (!$this->isFailed()) {
            $value += $this->getValue();
        }

        return $value;
    }

    /**
     * Get payment method object related to the parent payment transaction
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return $this->getPaymentTransaction()->getPaymentMethod();
    }

    /**
     * Check - transaction is succeed or not
     *
     * @return boolean
     */
    public function isSucceed()
    {
        return $this->getStatus() == static::STATUS_SUCCESS;
    }

    /**
     * Check - transaction is failed or not
     *
     * @return boolean
     */
    public function isFailed()
    {
        return $this->getStatus() == static::STATUS_FAILED;
    }

    /**
     * Check - order is completed or not
     *
     * @return boolean
     */
    public function isCompleted()
    {
        return $this->getStatus() == static::STATUS_SUCCESS;
    }

    /**
     * Check if the backend transaction is of refunded type
     *
     * @return boolean
     */
    public function isRefund()
    {
        return in_array($this->getType(), [
            static::TRAN_TYPE_REFUND,
            static::TRAN_TYPE_REFUND_MULTI,
            static::TRAN_TYPE_REFUND_PART,
        ]);
    }

    /**
     * Check if the backend transaction is full refund
     *
     * @return boolean
     */
    public function isFullRefund()
    {
        return $this->getValue() >= $this->getParentValue();
    }

    /**
     * Check if the backend transaction is of capture type
     *
     * @return boolean
     */
    public function isCapture()
    {
        return in_array($this->getType(), [
            static::TRAN_TYPE_CAPTURE,
            static::TRAN_TYPE_CAPTURE_MULTI,
            static::TRAN_TYPE_CAPTURE_PART,
        ]);
    }

    /**
     * Check if the backend transaction is of void type
     *
     * @return boolean
     */
    public function isVoid()
    {
        return in_array($this->getType(), [
            static::TRAN_TYPE_VOID,
            static::TRAN_TYPE_VOID_MULTI,
            static::TRAN_TYPE_VOID_PART,
        ]);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->data = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get human-readable status
     *
     * @return string
     */
    public function getReadableStatus()
    {
        return $this->getPaymentTransaction()->getReadableStatus($this->getStatus());
    }

    /**
     * Return true if operation is allowed for currect transaction
     *
     * @param string $operation Name of operation
     *
     * @return boolean
     */
    public function isOperationAllowed($operation)
    {
        return in_array($operation, $this->getPaymentMethod()->getProcessor()->getAllowedTransactions());
    }

    /**
     * Return true if transaction is an initial
     *
     * @return boolean
     */
    public function isInitial()
    {
        return in_array(
            $this->getType(),
            [
                static::TRAN_TYPE_AUTH,
                static::TRAN_TYPE_SALE,
            ]
        );
    }

    // {{{ Data operations

    /**
     * Set data cell
     *
     * @param string $name  Data cell name
     * @param string $value Value
     * @param string $label Public name OPTIONAL
     *
     * @return void
     */
    public function setDataCell($name, $value, $label = null)
    {
        $data = null;

        if (!isset($value)) {
            $value = '';
        }

        foreach ($this->getData() as $cell) {
            if ($cell->getName() == $name) {
                $data = $cell;
                break;
            }
        }

        if (!$data) {
            $data = new \XLite\Model\Payment\BackendTransactionData();
            $data->setName($name);
            $this->addData($data);
            $data->setTransaction($this);
        }

        if ($label && !$data->getLabel()) {
            $data->setLabel($label);
        }

        $data->setValue($value);
    }

    /**
     * Get data cell
     *
     * @param string $name Parameter name
     *
     * @return \XLite\Model\Payment\BackendTransactionData
     */
    public function getDataCell($name)
    {
        $value = null;

        foreach ($this->getData() as $cell) {
            if ($cell->getName() == $name) {
                $value = $cell;
                break;
            }
        }

        return $value;
    }

    /**
     * Register transaction in order history
     *
     * @param string $suffix Suffix text to add to the end of event description
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    public function registerTransactionInOrderHistory($suffix = null)
    {
        $descrSuffix = !empty($suffix) ? ' [' . static::t($suffix) . ']' : '';

        \XLite\Core\OrderHistory::getInstance()->registerTransaction(
            $this->getPaymentTransaction()->getOrder()->getOrderId(),
            static::t($this->getHistoryEventDescription(), $this->getHistoryEventDescriptionData()) . $descrSuffix,
            $this->getEventData()
        );

        return $this;
    }

    /**
     * Get description of order history event (language label is returned)
     *
     * @return string
     */
    public function getHistoryEventDescription()
    {
        return 'Backend payment transaction X issued';
    }

    /**
     * Get data for description of order history event (substitution data for language label is returned)
     *
     * @return array
     */
    public function getHistoryEventDescriptionData()
    {
        return [
            'trx_method' => static::t($this->getPaymentMethod()->getName()),
            'trx_type'   => static::t($this->getType()),
            'trx_value'  => $this->getPaymentTransaction()->getOrder()->getCurrency()->roundValue($this->getValue()),
            'trx_status' => static::t($this->getReadableStatus()),
        ];
    }

    /**
     * getEventData
     *
     * @return array
     */
    public function getEventData()
    {
        $result = [];

        foreach ($this->getData() as $cell) {
            $result[] = [
                'name'  => $cell->getLabel() ?: $cell->getName(),
                'value' => $cell->getValue()
            ];
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
     * Set date
     *
     * @param integer $date
     * @return BackendTransaction
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
     * Set status
     *
     * @param string $status
     * @return BackendTransaction
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
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
     * Set value
     *
     * @param float $value
     *
     * @return BackendTransaction
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
     * @return BackendTransaction
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
     * Set payment_transaction
     *
     * @param \XLite\Model\Payment\Transaction $paymentTransaction
     * @return BackendTransaction
     */
    public function setPaymentTransaction(\XLite\Model\Payment\Transaction $paymentTransaction = null)
    {
        $this->payment_transaction = $paymentTransaction;
        return $this;
    }

    /**
     * Get payment_transaction
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function getPaymentTransaction()
    {
        return $this->payment_transaction;
    }

    /**
     * Add data
     *
     * @param \XLite\Model\Payment\BackendTransactionData $data
     * @return BackendTransaction
     */
    public function addData(\XLite\Model\Payment\BackendTransactionData $data)
    {
        $this->data[] = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return boolean
     */
    public function hasCustomAmount()
    {
        return in_array($this->getType(), [
            static::TRAN_TYPE_REFUND_PART,
            static::TRAN_TYPE_REFUND_MULTI,
        ]);
    }

    /**
     * @param $amount
     *
     * @return $this
     * @throws \XLite\Core\Exception\IncorrectValueException
     */
    public function setCustomAmount($amount)
    {
        $method = $this->getType()
            ? __FUNCTION__ . \Includes\Utils\Converter::convertToUpperCamelCase($this->getType())
            : null;

        if (method_exists($this, $method)) {
            return $this->{$method}($amount);
        }

        return $this;
    }

    /**
     * @param $amount
     *
     * @return $this
     * @throws \XLite\Core\Exception\IncorrectValueException
     */
    public function setCustomAmountRefundPart($amount)
    {
        if ($amount > 0 && $amount <= $this->getMaxRefundAmount()) {
            $this->setValue($amount);
        } else {
            throw new \XLite\Core\Exception\IncorrectValueException('Incorrect amount');
        }

        return $this;
    }

    /**
     * @param $amount
     *
     * @return $this
     * @throws \XLite\Core\Exception\IncorrectValueException
     */
    public function setCustomAmountRefundMulti($amount)
    {
        return $this->setCustomAmountRefundPart($amount);
    }

    /**
     * Return max amount to refund
     *
     * @return float
     */
    public function getMaxRefundAmount()
    {
        $currency = $this->getPaymentTransaction()->getCurrency() ?: $this->getPaymentTransaction()->getOrder()->getCurrency();
        return $currency->roundValue($this->getPaymentTransaction()->getChargeValueModifier());
    }

    /**
     * Return max amount to refund
     *
     * @return float
     */
    public function getParentValue()
    {
        $currency = $this->getPaymentTransaction()->getCurrency() ?: $this->getPaymentTransaction()->getOrder()->getCurrency();
        return $currency->roundValue($this->getPaymentTransaction()->getValue());
    }
}
