<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrdersImport\Logic\Import\Processor;

use XLite\Core\Database;
use XLite\Logic\Order\Modifier\AModifier;
use XLite\Model\Order;
use XLite\Model\OrderItem;

class Orders extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Prefixes names
     */
    public const CUSTOMER_PREFIX            = 'customer';
    public const ITEM_PREFIX                = 'item';
    public const SURCHARGE_PREFIX           = 'surcharge';
    public const PAYMENT_TRANSACTION_PREFIX = 'paymentTransaction';
    public const DETAIL_PREFIX              = 'detail';
    public const ADDRESS_FIELD_SUFFIX       = 'AddressField';
    public const TRACKING_NUMBER_SUFFIX     = 'TrackingNumber';
    public const COLUMN_SAVE_SPECIALCHARS   = 'saveSpecialChars';

    private $orderModifiers = null;
    private $addressFields = [];

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Orders imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('XLite\Model\Order');
    }

    /**
     * Return true if import run in create-only mode
     *
     * @return boolean
     */
    protected function isCreateMode()
    {
        return $this->importer->getOptions()->importMode === \XLite\View\Import\Begin::MODE_CREATE_ONLY;
    }

    /**
     * Returns csv format manual URL
     *
     * @return string
     */
    public static function getCSVFormatManualURL()
    {
        return static::t('https://support.x-cart.com/en/articles/5387452-csv-import-orders');
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'orderNumber' => [
                static::COLUMN_IS_KEY => true,
            ],
        ];

        $columns['customer'] = [
            static::COLUMN_IS_MULTICOLUMN  => true,
            static::COLUMN_IS_MULTIROW     => true,
            static::COLUMN_HEADER_DETECTOR => true,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        $columns['items'] = [
            static::COLUMN_IS_MULTICOLUMN  => true,
            static::COLUMN_IS_MULTIROW     => true,
            static::COLUMN_HEADER_DETECTOR => true,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        $columns += [
            'subtotal' => [],
        ];

        $columns['surcharges'] = [
            static::COLUMN_IS_MULTICOLUMN  => true,
            static::COLUMN_IS_MULTIROW     => true,
            static::COLUMN_HEADER_DETECTOR => true,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        $columns['transactions'] = [
            static::COLUMN_IS_MULTICOLUMN  => true,
            static::COLUMN_IS_MULTIROW     => true,
            static::COLUMN_HEADER_DETECTOR => true,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        $columns += [
            'total'          => [],
            'currency'       => [],
            'shippingMethod' => [
                static::COLUMN_IS_TAGS_ALLOWED => true,
                static::COLUMN_SAVE_SPECIALCHARS => true,
            ],
            'trackingNumber' => [],
            'date'           => [],
            'recent'         => [],
            'paymentStatus'  => [],
            'shippingStatus' => [],
            'stockStatus'    => [],
            'notes'          => [],
            'adminNotes'     => [],
        ];

        $columns['details'] = [
            static::COLUMN_IS_MULTICOLUMN  => true,
            static::COLUMN_IS_MULTIROW     => true,
            static::COLUMN_HEADER_DETECTOR => true,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        return $columns;
    }

    /**
     * Clear empty lines in complex column(items, transactions, etc)
     *
     * @param $complexColumn
     *
     * @return array
     */
    protected function clearEmptyLines($complexColumn)
    {
        $emptyValues = [];

        foreach ($complexColumn as $name => $column) {
            foreach ($column as $key => $val) {
                if (empty($val)) {
                    $emptyValues[$name][$key] = true;
                } else {
                    $emptyValues[$name][$key] = false;
                }
            }
        }

        $resultEmptiness = reset($emptyValues);
        foreach ($resultEmptiness as &$val) {
            $val = true;
        }

        while (!(empty($emptyValues))) {
            $currentRow = array_pop($emptyValues);

            foreach ($currentRow as $key => $val) {
                $resultEmptiness[$key] = $resultEmptiness[$key] && $val;
            }
        }

        foreach ($resultEmptiness as $key => $empty) {
            if ($empty) {
                foreach ($complexColumn as $name => $column) {
                    unset($complexColumn[$name][$key]);
                }
            }
        }

        return $complexColumn;
    }

    // }}}

    // {{{ Header detectors

    /**
     * Detect customer header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectCustomerHeader(array $column, array $row)
    {
        $patternCustomer = static::CUSTOMER_PREFIX . '(Email|Anonymous|AddressSame)';
        $patternAddress = static::CUSTOMER_PREFIX . '.+' . '(Shipping|Billing)' . static::ADDRESS_FIELD_SUFFIX;
        $pattern = "({$patternCustomer}|{$patternAddress})";

        return $this->detectHeaderByPattern($pattern, $row);
    }

    /**
     * Detect items header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectItemsHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('(item.+|.+ \(item surcharge\)(\[.*\])?)', $row);
    }

    /**
     * Detect surcharges header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectSurchargesHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('.+ \(surcharge\)(\[.*\])?', $row);
    }

    /**
     * Detect transactions header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectTransactionsHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern(static::PAYMENT_TRANSACTION_PREFIX . '.+', $row);
    }

    /**
     * Detect details header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectDetailsHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern(static::DETAIL_PREFIX . '.+', $row);
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'REQUIRED-COLUMN'                => 'Column X is required',
                'ORDER-NUMBER-FMT'               => 'Wrong order number format',
                'ORDER-LOGIN-FMT'                => 'Wrong login format',
                'ORDER-ANONYMOUS-FMT'            => 'Wrong anonymous format',
                'ORDER-SAME-FMT'                 => 'Wrong address same format',
                'ORDER-SUBTOTAL-FMT'             => 'Wrong subtotal format',
                'ORDER-TOTAL-FMT'                => 'Wrong total format',
                'ORDER-CURRENCY-FMT'             => 'Wrong currency format',
                'ORDER-DATE-FMT'                 => 'Wrong date format',
                'ORDER-SHIPPING-STATUS-NF'       => 'Shipping status not found, "New" will be used for order',
                'ORDER-PAYMENT-STATUS-NF'        => 'Payment status not found, "Awaiting payment" will be used for order',
                'ORDER-STOCK-STATUS-NF'          => 'Stock status not found',
                'ORDER-MODIFIER-NF'              => 'Order modifier not found',
                'ORDER-MODIFIER-FMT'             => 'Wrong order modifier format',
                'ORDER-ITEM-FMT'                 => 'Wrong order item format',
                'ORDER-ITEM-SKU-FMT'             => 'Wrong order item sku format',
                'ORDER-ITEM-NAME-FMT'            => 'Wrong order item name format',
                'ORDER-ITEM-PRICE-FMT'           => 'Wrong order item price format',
                'ORDER-ITEM-SUBTOTAL-FMT'        => 'Wrong order item subtotal format',
                'ORDER-ITEM-TOTAL-FMT'           => 'Wrong order item total format',
                'ORDER-ITEM-QUANTITY-FMT'        => 'Wrong order item quantity format',
                'ORDER-ITEM-BO-QUANTITY-FMT'     => 'Wrong order item backordered quantity format',
                'ORDER-ITEM-MODIFIER-NF'         => 'Order item modifier not found',
                'ORDER-ITEM-MODIFIER-FMT'        => 'Wrong item order modifier format',
                'ORDER-ADDRESS-CCODE-FMT'        => 'Wrong country code format',
                'ORDER-ADDRESS-SID-FMT'          => 'Wrong state id format',
                'ORDER-TRANSACTION-FMT'          => 'Wrong order transaction format',
                'ORDER-TRANSACTION-METHOD-FMT'   => 'Wrong order transaction method format',
                'ORDER-TRANSACTION-STATUS-NF'    => 'Order transaction status not found',
                'ORDER-TRANSACTION-VALUE-FMT'    => 'Wrong order transaction value format',
                'ORDER-TRANSACTION-TYPE-NF'      => 'Order transaction type not found',
                'ORDER-TRANSACTION-SYSTEMID-FMT' => 'Wrong order transaction system id format',
                'ORDER-TRANSACTION-ID-FMT'       => 'Wrong order transaction id format',
                'ORDER-TRANSACTION-CURRENCY-FMT' => 'Wrong order transaction currency format',
            ];
    }

    /**
     * Verify 'orderNumber' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyOrderNumber($value, array $column)
    {
        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('ORDER-NUMBER-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'customer' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyCustomer($value, array $column)
    {
        $value = array_map(static fn ($item) => reset($item), $value);

        if (isset($value[static::CUSTOMER_PREFIX . 'Email'])) {
            $this->verifyCustomerEmail($value[static::CUSTOMER_PREFIX . 'Email'], $column);
        } else {
            $this->addError('REQUIRED-COLUMN', ['column' => $column, 'value' => static::CUSTOMER_PREFIX . 'Email']);
        }

        if (isset($value[static::CUSTOMER_PREFIX . 'Anonymous'])) {
            $this->verifyCustomerAnonymous($value[static::CUSTOMER_PREFIX . 'Anonymous'], $column);
        }

        if (isset($value[static::CUSTOMER_PREFIX . 'AddressSame'])) {
            $this->verifyCustomerAddressSame($value[static::CUSTOMER_PREFIX . 'AddressSame'], $column);
        }

        foreach ($value as $name => $field) {
            if (preg_match_all('/' . static::CUSTOMER_PREFIX . '(.+)' . '(Shipping|Billing)' . static::ADDRESS_FIELD_SUFFIX . '/', $name, $matches)) {
                $this->verifyAddress($field, $matches[1][0], $column);
            }
        }
    }

    /**
     * Verify 'customerEmail' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyCustomerEmail($value, array $column)
    {
        if (!$this->verifyValueAsEmail($value)) {
            $this->addWarning('ORDER-LOGIN-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'customerAnonymous' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyCustomerAnonymous($value, array $column)
    {
        if (!$this->verifyValueAsBoolean($value)) {
            $this->addWarning('ORDER-ANONYMOUS-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'customerAddressSame' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyCustomerAddressSame($value, array $column)
    {
        if (!$this->verifyValueAsBoolean($value)) {
            $this->addWarning('ORDER-SAME-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify Address value
     *
     * @param mixed $value  Value
     * @param mixed $name   Field name
     * @param array $column Column info
     */
    protected function verifyAddress($value, $name, $column)
    {
        $method = 'verifyAddressField' . \Includes\Utils\Converter::convertToUpperCamelCase($name);
        if (method_exists($this, $method)) {
            $this->$method($value, $column, 0);
        }
    }

    /**
     * Verify 'address field country code' value
     *
     * @param mixed   $value  Value
     * @param array   $column Column info
     * @param integer $index  Row offset
     *
     * @return void
     */
    protected function verifyAddressFieldCountryCode($value, array $column, $index)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsCountryCode($value)) {
            $this->addWarning('ORDER-ADDRESS-CCODE-FMT', ['column' => $column, 'value' => $value], $index);
        }
    }

    /**
     * Verify 'address field state Id' value
     *
     * @param mixed   $value  Value
     * @param array   $column Column info
     * @param integer $index  Row offset
     *
     * @return void
     */
    protected function verifyAddressFieldStateId($value, array $column, $index)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->normalizeValueAsState($value)) {
            $this->addWarning('ORDER-ADDRESS-SID-FMT', ['column' => $column, 'value' => $value], $index);
        }
    }

    /**
     * Verify 'items' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyItems($value, array $column)
    {
        $value = $this->clearEmptyLines($value);

        $requiredColumns = [
            'itemSKU',
            'itemName',
            'itemPrice',
            'itemSubtotal',
            'itemTotal',
        ];
        $missedColumns = [];

        foreach ($requiredColumns as $requiredColumn) {
            if (!isset($value[$requiredColumn])) {
                $missedColumns[] = $requiredColumn;
            }
        }

        if (!empty($missedColumns)) {
            $this->addError('ORDER-ITEM-FMT', ['column' => $column, 'value' => implode(', ', $missedColumns)]);
        } else {
            foreach ($value['itemSKU'] as $offset => $val) {
                if ($this->verifyValueAsEmpty($val)) {
                    $this->addWarning('ORDER-ITEM-SKU-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            foreach ($value['itemName'] as $offset => $val) {
                if ($this->verifyValueAsEmpty($val)) {
                    $this->addError('ORDER-ITEM-NAME-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            foreach ($value['itemPrice'] as $offset => $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('ORDER-ITEM-PRICE-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            foreach ($value['itemSubtotal'] as $offset => $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('ORDER-ITEM-SUBTOTAL-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            foreach ($value['itemTotal'] as $offset => $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('ORDER-ITEM-TOTAL-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            if (isset($value['itemQuantity'])) {
                foreach ($value['itemQuantity'] as $offset => $val) {
                    if (!$this->verifyValueAsUinteger($val)) {
                        $this->addWarning('ORDER-ITEM-QUANTITY-FMT', ['column' => $column, 'value' => $val], $offset);
                    }
                }
            }

            if (isset($value['itemBackorderedAmount'])) {
                foreach ($value['itemBackorderedAmount'] as $offset => $val) {
                    if (!$this->verifyValueAsUinteger($val)) {
                        $this->addWarning('ORDER-ITEM-BO-QUANTITY-FMT', ['column' => $column, 'value' => $val], $offset);
                    }
                }
            }

            foreach ($value as $name => $values) {
                if (preg_match_all('/^(.+) \(item surcharge\)(\[.*\])?$/', $name, $matches)) {
                    $this->verifyItemSurcharge($matches[1][0], $values, $column);
                }
            }
        }
    }

    /**
     * Verify 'itemSurcharges' value
     *
     * @param mixed $values Value
     * @param array $column Column info
     */
    protected function verifyItemSurcharge($code, $values, array $column)
    {
        $code = trim(preg_replace('/\(item surcharge\)(\[.*\])?$/', '', $code));
        if (!$this->getModifierByCode($code)) {
            $this->addWarning('ORDER-ITEM-MODIFIER-NF', ['column' => $column, 'value' => $code]);
        } else {
            foreach ($values as $offset => $value) {
                if (!empty($value)) {
                    if (!$this->verifyValueAsFloat($value)) {
                        $this->addWarning('ORDER-ITEM-MODIFIER-FMT', ['column' => $column, 'value' => $value], $offset);
                    }
                }
            }
        }
    }

    /**
     * Verify 'subtotal' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifySubtotal($value, array $column)
    {
        if (!$this->verifyValueAsFloat($value)) {
            $this->addWarning('ORDER-SUBTOTAL-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'surcharges' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifySurcharges($value, array $column)
    {
        $value = array_map(static fn ($item) => reset($item), $value);

        foreach ($value as $code => $val) {
            if (!empty($val)) {
                $code = trim(preg_replace('/\(surcharge\)(\[.*\])?$/', '', $code));
                if (!$this->getModifierByCode($code)) {
                    $this->addWarning('ORDER-MODIFIER-NF', ['column' => $column, 'value' => $code]);
                } elseif (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('ORDER-MODIFIER-FMT', ['column' => $column, 'value' => $val]);
                }
            }
        }
    }

    /**
     * Verify 'surcharges' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyTransactions($value, array $column)
    {
        $value = $this->clearEmptyLines($value);

        $requiredColumns = [
            'paymentTransactionMethod',
            'paymentTransactionStatus',
            'paymentTransactionValue',
            'paymentTransactionType',
            'paymentTransactionSystemId',
            'paymentTransactionId',
        ];
        $missedColumns = [];

        foreach ($requiredColumns as $requiredColumn) {
            if (!isset($value[$requiredColumn])) {
                $missedColumns[] = $requiredColumn;
            }
        }

        if (!empty($missedColumns)) {
            $this->addError('ORDER-TRANSACTION-FMT', ['column' => $column, 'value' => implode(', ', $missedColumns)]);
        } else {
            foreach ($value['paymentTransactionMethod'] as $offset => $val) {
                if ($this->verifyValueAsEmpty($val)) {
                    $this->addWarning('ORDER-TRANSACTION-METHOD-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            $statuses = \XLite\Model\Payment\Transaction::getStatuses();
            foreach ($value['paymentTransactionStatus'] as $offset => $val) {
                if (!isset($statuses[trim($val)])) {
                    $this->addError('ORDER-TRANSACTION-STATUS-NF', ['column' => $column, 'value' => $val], $offset);
                }
            }

            foreach ($value['paymentTransactionValue'] as $offset => $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('ORDER-TRANSACTION-VALUE-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            $types = \XLite\Model\Payment\BackendTransaction::getTypes();
            foreach ($value['paymentTransactionType'] as $offset => $val) {
                if (!isset($types[trim($val)])) {
                    $this->addWarning('ORDER-TRANSACTION-TYPE-NF', ['column' => $column, 'value' => $val], $offset);
                }
            }

            foreach ($value['paymentTransactionSystemId'] as $offset => $val) {
                if ($this->verifyValueAsEmpty($val)) {
                    $this->addWarning('ORDER-TRANSACTION-SYSTEMID-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            foreach ($value['paymentTransactionId'] as $offset => $val) {
                if ($this->verifyValueAsEmpty($val)) {
                    $this->addWarning('ORDER-TRANSACTION-ID-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            foreach ($value['paymentTransactionId'] as $offset => $val) {
                if ($this->verifyValueAsEmpty($val)) {
                    $this->addWarning('ORDER-TRANSACTION-ID-FMT', ['column' => $column, 'value' => $val], $offset);
                }
            }

            if (isset($value['paymentTransactionCurrency'])) {
                foreach ($value['paymentTransactionCurrency'] as $offset => $val) {
                    if (!$this->verifyValueAsCurrencyCode($val)) {
                        $this->addWarning('ORDER-TRANSACTION-CURRENCY-FMT', [
                            'column' => $column,
                            'value'  => $val,
                        ], $offset);
                    }
                }
            }
        }
    }

    /**
     * Verify 'total' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyTotal($value, array $column)
    {
        if (!$this->verifyValueAsFloat($value)) {
            $this->addWarning('ORDER-TOTAL-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'currency' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyCurrency($value, array $column)
    {
        if (!$this->verifyValueAsCurrencyCode($value)) {
            $this->addWarning('ORDER-CURRENCY-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'date' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyDate($value, array $column)
    {
        if (!$this->verifyValueAsDate($value)) {
            $this->addWarning('ORDER-DATE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'paymentStatus' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyPaymentStatus($value, array $column)
    {
        if (!Database::getRepo('XLite\Model\Order\Status\Payment')->findOneBy(['code' => $value])) {
            $this->addWarning('ORDER-PAYMENT-STATUS-NF', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'shippingStatus' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyShippingStatus($value, array $column)
    {
        if (!Database::getRepo('XLite\Model\Order\Status\Shipping')->findOneBy(['code' => $value])) {
            $this->addWarning('ORDER-SHIPPING-STATUS-NF', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'stockStatus' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyStockStatus($value, array $column)
    {
        if (!$this->verifyValueAsStockStatus($value)) {
            $this->addWarning('ORDER-STOCK-STATUS-NF', ['column' => $column, 'value' => $value]);
        }
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'customerAnonymous' value
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function normalizeCustomerAnonymousValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'customerAddressSame' value
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function normalizeCustomerAddressSameValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'currency' value
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function normalizeCurrencyValue($value)
    {
        return $this->normalizeValueAsCurrency($value);
    }

    /**
     * Normalize 'date' value
     *
     * @param mixed $value Value
     *
     * @return integer
     */
    protected function normalizeDateValue($value)
    {
        return $this->normalizeValueAsDate($value);
    }

    /**
     * Normalize 'recent' value
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function normalizeRecentValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'paymentStatus' value
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function normalizePaymentStatusValue($value)
    {
        $status = Database::getRepo('XLite\Model\Order\Status\Payment')->findOneBy(['code' => $value]);

        return $status ?: Database::getRepo('XLite\Model\Order\Status\Payment')->findOneBy([
            'code' => \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
        ]);
    }

    /**
     * Normalize 'shippingStatus' value
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function normalizeShippingStatusValue($value)
    {
        $status = Database::getRepo('XLite\Model\Order\Status\Shipping')->findOneBy(['code' => $value]);

        return $status ?: Database::getRepo('XLite\Model\Order\Status\Shipping')->findOneBy([
            'code' => \XLite\Model\Order\Status\Shipping::STATUS_NEW,
        ]);
    }

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        $result = parent::importData($data);
        if ($result) {
            $this->getRepository()->initializeNextOrderNumber((int) $data['orderNumber']);
        }

        return $result;
    }

    // }}}

    // {{{ Import

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        return Database::getRepo('XLite\Model\Order')->findOneBy(['orderNumber' => $data['orderNumber']]);
    }

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\Order
     */
    protected function createModel(array $data)
    {
        $order = parent::createModel($data);
        $order->setRecent(false);

        return $order;
    }

    /**
     * Import 'customer' value
     *
     * @param Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importCustomerColumn(Order $order, $value, array $column)
    {
        $shippingFields = [];
        $billingFields = [];

        foreach ($value as $key => &$field) {
            $field = array_shift($field);

            if (preg_match_all('/' . static::CUSTOMER_PREFIX . '(.+)Shipping' . static::ADDRESS_FIELD_SUFFIX . '/', $key, $matches)) {
                $shippingFields[$matches[1][0]] = $field;
            }

            if (preg_match_all('/' . static::CUSTOMER_PREFIX . '(.+)Billing' . static::ADDRESS_FIELD_SUFFIX . '/', $key, $matches)) {
                $billingFields[$matches[1][0]] = $field;
            }
        }

        $email = $value[static::CUSTOMER_PREFIX . 'Email'];
        $anonymous = isset($value[static::CUSTOMER_PREFIX . 'Anonymous'])
            ? $this->normalizeCustomerAnonymousValue($value[static::CUSTOMER_PREFIX . 'Anonymous'])
            : false;


        if ($orderProfile = $order->getProfile()) {
            $orderProfile->setLogin($email);
            $orderProfile->setAnonymous($anonymous);
        }

        $profile = $this->getCustomerByEmailAndAnonymous($email, $anonymous);

        if (isset($orderProfile)) {
            $order->setOrigProfile($profile);
        } else {
            $order->setProfileCopy($profile);
        }
        $profile = $order->getProfile();

        foreach ($profile->getAddresses() as $item) {
            Database::getEM()->remove($item);
        }
        $profile->getAddresses()->clear();

        $shippingAddress = $this->createAddress($shippingFields);
        $shippingAddress->setIsShipping(true);
        $profile->addAddresses($shippingAddress);
        $shippingAddress->setProfile($profile);

        if (
            isset($value[static::CUSTOMER_PREFIX . 'AddressSame'])
            && $this->normalizeValueAsBoolean($value[static::CUSTOMER_PREFIX . 'AddressSame'])
        ) {
            $shippingAddress->setIsBilling(true);
        } else {
            $billingAddress = $this->createAddress($billingFields);
            $billingAddress->setIsBilling(true);
            $profile->addAddresses($billingAddress);
            $billingAddress->setProfile($profile);
        }
    }

    /**
     * Create new address based on data
     *
     * @param $data
     *
     * @return \XLite\Model\Address
     */
    protected function createAddress($data)
    {
        $address = Database::getRepo('XLite\Model\Address')->insert(null, false);

        if (isset($data['StateId']) && $state = $this->normalizeValueAsState($data['StateId'])) {
            $data['state'] = $state;
            unset($data['StateId']);
        } else {
            $data['state'] = $data['CustomState'];
            unset($data['CustomState']);
        }

        foreach ($data as $key => $value) {
            $method = 'set' . \Includes\Utils\Converter::convertToUpperCamelCase($key);
            if (
                !method_exists($address, $method)
                && !property_exists($address, $key)
                && !$this->checkAddressField($key)
            ) {
                unset($data[$key]);
            }
        }

        Database::getRepo('XLite\Model\Address')->update($address, $data, false);

        return $address;
    }

    /**
     * Check address field existence
     *
     * @param $serviceName
     *
     * @return mixed
     */
    protected function checkAddressField($serviceName)
    {
        if (!isset($this->addressFields[$serviceName])) {
            $this->addressFields[$serviceName] = (bool)\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findOneBy(['serviceName' => $serviceName]);
        }

        return $this->addressFields[$serviceName];
    }

    /**
     * Return profile by login and anonymous
     *
     * @param      $email
     * @param bool $anonymous
     *
     * @return mixed|\XLite\Model\Profile
     */
    protected function getCustomerByEmailAndAnonymous($email, $anonymous = false)
    {
        $repo = Database::getRepo('XLite\Model\Profile');
        $cnd = new \XLite\Core\CommonCell();
        $cnd->login = $email;
        $cnd->anonymous = $anonymous;
        $cnd->order_id = null;

        $result = $repo->search($cnd);

        if ($result) {
            $profile = array_pop($result);
        } elseif (!($profile = $this->findScheduledForInsertionCustomer($email, $anonymous))) {
            $profile = $repo->insert(null, false);
            $profile->setLogin($email);
            $profile->setAnonymous($anonymous);
        }

        return $profile;
    }

    protected function findScheduledForInsertionCustomer($email, $anonymous)
    {
        $uow = Database::getEM()->getUnitOfWork();

        $insertions = $uow->getScheduledEntityInsertions();

        if (!empty($insertions['XLite\Model\Profile'])) {
            foreach ($insertions['XLite\Model\Profile'] as $profile) {
                /* @var Profile $profile */
                if (
                    $profile->getLogin() === $email
                    && $profile->getAnonymous() === $anonymous
                    && !$profile->getOrder()
                ) {
                    return $profile;
                }
            }
        }

        return null;
    }

    /**
     * Import 'items' value
     *
     * @param Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importItemsColumn(Order $order, $value, array $column)
    {
        $value = $this->clearEmptyLines($value);

        foreach ($order->getItems() as $item) {
            Database::getEM()->remove($item);
        }
        $order->getItems()->clear();

        $itemsData = [];

        foreach ($value as $row => $rowValues) {
            foreach ($rowValues as $key => $param) {
                $itemsData[$key][$row] = $param;
            }
        }

        foreach ($itemsData as $itemData) {
            $item = $this->getItemByData($itemData);
            $item->setOrder($order);
            $order->addItems($item);
        }
    }

    /**
     * Return order item by data
     *
     * @param array $data
     *
     * @return OrderItem
     */
    protected function getItemByData($data)
    {
        $item = new OrderItem();
        if ($product = $this->detectProductBySku($data['itemSKU'])) {
            $item->setProduct($product);
            if (isset($data['itemAttributes'])) {
                $attributeValues = $this->prepareAttributeValues($product, static::parseMultipleValue($data['itemAttributes']));
                foreach ($attributeValues as $attributeValue) {
                    $attributeValue->setOrderItem($item);
                    $item->addAttributeValues($attributeValue);
                }
            }
        } elseif (isset($data['itemAttributes'])) {
            foreach (static::parseMultipleValue($data['itemAttributes']) as $line) {
                [$name, $value] = $line ? ($attribute = explode('=', $line)) : ['', ''];

                if (!empty($name) && !empty($value)) {
                    if (count($attribute) > 2) {
                        $i = 1;
                        while (++$i < count($attribute)) {
                            $value .= '=' . $attribute[$i];
                        }
                    }

                    $newValue = new \XLite\Model\OrderItem\AttributeValue();
                    $newValue->setName($name);
                    $newValue->setValue($value);
                    $newValue->setOrderItem($item);
                    $item->addAttributeValues($newValue);
                }
            }
        }

        $item->setName($data['itemName']);
        $item->setPrice(doubleval($data['itemPrice']));
        $item->setItemNetPrice(doubleval($data['itemPrice']));
        $item->setAmount($data['itemQuantity'] ?? 1);
        $item->setBackorderedAmount($data['itemBackorderedAmount'] ?? 0);
        $item->setSku($data['itemSKU']);
        $item->setSubtotal(doubleval($data['itemSubtotal']));
        $item->setTotal(doubleval($data['itemTotal']));

        foreach ($data as $name => $value) {
            if (!empty($value) && preg_match_all('/^(.+) \(item surcharge\)(\[.*\])?$/', $name, $matches)) {
                $code = trim($matches[1][0]);
                if ($modifier = $this->getModifierByCode($code)) {
                    $surcharge = new \XLite\Model\OrderItem\Surcharge();
                    $surcharge->setType($modifier->getType());
                    $surcharge->setCode($code);
                    $surcharge->setValue(doubleval($value));
                    $surcharge->setAvailable(true);
                    $surcharge->setClass(get_class($modifier));
                    $surcharge->setInclude(preg_match('/\[include\]/', $name));

                    $info = $modifier->getSurchargeInfo($surcharge);
                    $surcharge->setName($info->name);

                    $surcharge->setWeight(count($item->getSurcharges()));
                    $item->addSurcharges($surcharge);
                    $surcharge->setOwner($item);
                }
            }
        }

        return $item;
    }

    /**
     * Return order item attribute values
     *
     * @param \XLite\Model\Product $product
     * @param                      $data
     *
     * @return array
     */
    protected function prepareAttributeValues(\XLite\Model\Product $product, $data)
    {
        $attributes = $product->getEditableAttributes();
        $itemAttrs = [];

        foreach ($data as $attr) {
            $attribute = explode('=', $attr);

            if (isset($attribute[0]) && isset($attribute[1])) {
                if (count($attribute) > 2) {
                    $i = 1;
                    while (++$i < count($attribute)) {
                        $attribute[1] .= '=' . $attribute[$i];
                    }
                }

                $itemAttrs[$attribute[0]] = $attribute[1];
            }
        }

        $attributeValuesIds = [];
        $attributesData = [];


        foreach ($attributes as $attribute) {
            foreach ($attribute->getTranslations() as $translation) {
                if (isset($itemAttrs[$translation->getName()])) {
                    $attributesData[$attribute->getId()] = [
                        'name'  => $translation->getName(),
                        'value' => $itemAttrs[$translation->getName()],
                    ];

                    switch ($attribute->getType()) {
                        case \XLite\Model\Attribute::TYPE_CHECKBOX:
                            foreach ($attribute->getAttributeValue($product) as $atv) {
                                if ($atv->getValue() == $this->normalizeValueAsBoolean($itemAttrs[$translation->getName()])) {
                                    $attributeValuesIds[$attribute->getId()] = $atv->getId();
                                }
                            }
                            break;
                        case \XLite\Model\Attribute::TYPE_SELECT:
                            foreach ($attribute->getAttributeValue($product) as $atv) {
                                foreach ($atv->getAttributeOption()->getTranslations() as $optionTranslation) {
                                    if ($optionTranslation->getName() == $itemAttrs[$translation->getName()]) {
                                        $attributeValuesIds[$attribute->getId()] = $atv->getId();
                                        $attributesData[$attribute->getId()] = [
                                            'name'  => $itemAttrs[$translation->getName()],
                                            'value' => $itemAttrs[$translation->getName()],
                                        ];
                                    }
                                }
                            }
                            break;
                        default:
                            $attributeValuesIds[$attribute->getId()] = $itemAttrs[$translation->getName()];
                    }
                    unset($itemAttrs[$translation->getName()]);
                }
            }
        }

        $attributeValues = $product->prepareAttributeValues($attributeValuesIds);
        $orderItemAttributeValues = [];

        foreach ($attributeValues as $attributeValue) {
            if (is_array($attributeValue)) {
                $attributeValue = $attributeValue['attributeValue'];
            }

            if (isset($attributesData[$attributeValue->getAttribute()->getId()])) {
                $value = $attributesData[$attributeValue->getAttribute()->getId()]['value'];
                $name = $attributesData[$attributeValue->getAttribute()->getId()]['name'];

                $newValue = new \XLite\Model\OrderItem\AttributeValue();
                $newValue->setName($name);
                $newValue->setValue($value);
                $newValue->setAttributeId($attributeValue->getAttribute()->getId());
                if (isset($attributeValuesIds[$attributeValue->getAttribute()->getId()])) {
                    $newValue->setAttributeValue($attributeValue);
                }
                $orderItemAttributeValues[] = $newValue;
            }
        }

        foreach ($itemAttrs as $name => $value) {
            $newValue = new \XLite\Model\OrderItem\AttributeValue();
            $newValue->setName($name);
            $newValue->setValue($value);
            $orderItemAttributeValues[] = $newValue;
        }


        return $orderItemAttributeValues;
    }

    /**
     * Return product by sku
     *
     * @param $sku
     *
     * @return null|\XLite\Model\Product
     */
    protected function detectProductBySku($sku)
    {
        return Database::getRepo('XLite\Model\Product')->findOneBy(['sku' => $sku]);
    }

    /**
     * Import 'surcharges' value
     *
     * @param Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importSurchargesColumn(Order $order, $value, array $column)
    {
        $value = array_map(static fn ($item) => reset($item), $value);

        foreach ($value as $name => $val) {
            if (!empty($val) && preg_match_all('/(.+) \(surcharge\)(\[.*\])?$/', $name, $matches)) {
                $code = trim($matches[1][0]);
                if ($modifier = $this->getModifierByCode($code)) {
                    $modifier->initialize($order, new \XLite\DataSet\Collection\OrderModifier($this->getOrderModifiers()));

                    $surcharge = $this->getOrderSurcharge($order, $modifier);

                    if ($surcharge === null) {
                        $surcharge = new \XLite\Model\Order\Surcharge();
                        $surcharge->setType($modifier->getType());
                        $surcharge->setCode($code);
                        $surcharge->setOwner($order);

                        $order->addSurcharges($surcharge);
                    }

                    $surcharge->setValue((float)$val);
                    $surcharge->setAvailable(true);
                    $surcharge->setClass(get_class($modifier));
                    $surcharge->setInclude(preg_match('/\[include\]/', $val));

                    $info = $modifier->getSurchargeInfo($surcharge);
                    $surcharge->setName($info->name);

                    $surcharge->setWeight(count($order->getSurcharges()));
                }
            }
        }
    }

    /**
     * @param $code
     *
     * @return null | AModifier
     */
    protected function getModifierByCode($code)
    {
        foreach ($this->getOrderModifiers() as $modifier) {
            if ($modifier->getCode() && $modifier->isCodeApplicable(trim($code))) {
                return $modifier;
            }
        }

        return null;
    }

    /**
     * Return order modifiers(\XLite\Model\Order\Modifier) list
     *
     * @return array
     */
    protected function getOrderModifiers()
    {
        if ($this->orderModifiers === null) {
            $this->orderModifiers = [];

            foreach (Database::getRepo('XLite\Model\Order\Modifier')->findAll() as $modifierWrapper) {
                if ($modifier = $modifierWrapper->getModifier()) {
                    $this->orderModifiers[] = $modifier;
                }
            }
        }

        return $this->orderModifiers;
    }

    /**
     * @param Order     $order
     * @param AModifier $modifier
     *
     * @return Order\Surcharge|null
     */
    protected function getOrderSurcharge(Order $order, AModifier $modifier)
    {
        $code = $modifier->getCode();
        $type = $modifier->getType();

        foreach ($order->getSurcharges() as $surcharge) {
            if ($surcharge->getType() === $type && $surcharge->getCode() === $code) {
                return $surcharge;
            }
        }

        return null;
    }

    /**
     * Import 'transactions' value
     *
     * @param Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importTransactionsColumn(Order $order, $value, array $column)
    {
        $value = $this->clearEmptyLines($value);

        $transactionsData = [];
        foreach ($value as $row => $rowValues) {
            $row = preg_replace('/^' . static::PAYMENT_TRANSACTION_PREFIX . '/', '', $row);
            foreach ($rowValues as $key => $param) {
                $transactionsData[$key][$row] = $param;
            }
        }

        $systemId = $transactionsData['SystemId'] ?? false;

        foreach ($order->getPaymentTransactions() as $transaction) {
            if ($systemId && $systemId !== $transaction->getPublicTxnId()) {
                Database::getEM()->remove($transaction);
            }
        }
        $order->getPaymentTransactions()->clear();

        $paymentMethodName = '';
        foreach ($transactionsData as $transactionData) {
            if (!isset($transactionData['Currency'])) {
                $transactionData['Currency'] = $order->getCurrency() ? $order->getCurrency()->getCode() : '';
            }
            $transaction = $this->getTransactionByData($transactionData);
            $order->addPaymentTransactions($transaction);
            $transaction->setOrder($order);
            $paymentMethodName = $transaction->getMethodLocalName();

            if (
                isset($transactionData['Data'])
                && !$transaction->getData()->count()
            ) {
                $dataArray = unserialize($transactionData['Data']);
                foreach ($dataArray as $name => $value) {
                    $customerData = new \XLite\Model\Payment\TransactionData();
                    $customerData->setName($name);
                    $customerData->setValue($value);
                    $customerData->setAccessLevel(\XLite\Model\Payment\TransactionData::ACCESS_CUSTOMER);
                    $customerData->setTransaction($transaction);
                    $transaction->addData($customerData);
                }
            }
        }

        $order->setPaymentMethodName($paymentMethodName);
    }

    /**
     * Return transaction by data
     *
     * @param $data
     *
     * @return \XLite\Model\Payment\Transaction
     */
    protected function getTransactionByData($data)
    {
        $repo = Database::getRepo('XLite\Model\Payment\Transaction');
        if (
            $data['SystemId']
            && ($exists = $repo->findOneByPublicTxnId($data['SystemId']))
        ) {
            $transaction = $exists;
        } else {
            $transaction = $repo->insert(null, false);
        }

        if ($method = Database::getRepo('\XLite\Model\Payment\Method')->findOneBy(['service_name' => $data['Method']])) {
            $transaction->setPaymentMethod($method);
        } else {
            $transaction->setMethodName($data['Method']);
            $transaction->setMethodLocalName($data['Method']);
        }

        $transaction->setStatus($data['Status']);
        $transaction->setValue($data['Value']);
        $transaction->setType($data['Type']);
        $transaction->setPublicTxnId($data['SystemId']);
        $transaction->setPublicId($data['Id']);
        $transaction->setCurrency($this->normalizeValueAsCurrency($data['Currency']));
        $transaction->setNote($data['Note'] ?? '');

        return $transaction;
    }

    /**
     * Import 'shippingMethod' value
     *
     * @param Order  $order  Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importShippingMethodColumn(Order $order, $value, array $column)
    {
        $methodTranslation = Database::getRepo('XLite\Model\Shipping\MethodTranslation')
            ->findOneBy(['name' => $value]);
        if ($methodTranslation) {
            $method = $methodTranslation->getOwner();
            $order->setShippingId($method->getMethodId());
        }
        $order->setShippingMethodName($value);
    }

    /**
     * Import 'trackingNumber' value
     *
     * @param Order  $order  Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importTrackingNumberColumn(Order $order, $value, array $column)
    {
        $trackingNumbers = array_filter(explode(',', $value), static function ($v, $k) {
            return strlen(trim($v)) > 0;
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($order->getTrackingNumbers() as $trackingNumber) {
            Database::getEM()->remove($trackingNumber);
        }
        $order->getTrackingNumbers()->clear();

        foreach ($trackingNumbers as $number) {
            $trackingNumber = new \XLite\Model\OrderTrackingNumber();
            $trackingNumber->setOrder($order);
            $trackingNumber->setValue(trim($number));
            $order->addTrackingNumbers($trackingNumber);
        }
    }

    /**
     * Import 'details' value
     *
     * @param Order  $order  Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importDetailsColumn(Order $order, $value, array $column)
    {
        $value = $this->clearEmptyLines($value);

        $repo = Database::getRepo('XLite\Model\OrderDetail');

        foreach ($order->getDetails() as $detail) {
            Database::getEM()->remove($detail);
        }
        $order->getDetails()->clear();

        $details = [];

        foreach ($value as $row => $rowValues) {
            foreach ($rowValues as $key => $param) {
                $details[$key][$row] = $param;
            }
        }

        foreach ($details as $detailData) {
            $detail = $repo->insert(null, false);
            $detail->setOrder($order);
            $detail->setName($detailData['detailCode'] ?? '');
            $detail->setLabel($detailData['detailLabel'] ?? '');
            $detail->setValue($detailData['detailValue'] ?? '');
        }
    }

    // }}}

    /**
     * Get purified value
     *
     * @param array $column Column info
     * @param mixed $value  Value
     *
     * @return mixed
     */
    protected function getPurifiedValue(array $column, $value)
    {
        if (!$this->isColumnSaveSpecialChars($column)) {
            return parent::getPurifiedValue($column, $value);
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->getPurifiedValue($column, $v);
            }

            return $value;
        }

        // save inline tags like &reg;
        $value = preg_replace('/&([a-z]+);/u', '#$1;', $value);
        $value = parent::getPurifiedValue($column, $value);
        $value = preg_replace('/#([a-z]+);/u', '&$1;', $value);

        return $value;
    }

    /**
     * Return true if tags are allowed in the column content
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isColumnSaveSpecialChars(array $column)
    {
        return $this->isColumnTagsAllowed($column)
            && (
            isset($column[static::COLUMN_SAVE_SPECIALCHARS])
                ? $this->resultColumnProperty($column[static::COLUMN_SAVE_SPECIALCHARS])
                : false
            );
    }
}
