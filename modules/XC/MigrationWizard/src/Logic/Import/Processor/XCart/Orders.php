<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

use XCart\Extender\Mapping\Extender;

/**
 * Orders processor
 *
 * @Extender\Depend ("XC\OrdersImport")
 */
class Orders extends \XC\OrdersImport\Logic\Import\Processor\Orders
{
    public const ORDER_ITEM_ID_GCID_NUMBER = 3;//Sync with \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\GiftCertificates

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePreProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Languages',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\VolumeDiscounts',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Coupons',
        ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['items'] = [
            static::COLUMN_IS_MULTICOLUMN  => false,
            static::COLUMN_IS_MULTIROW     => false,
            static::COLUMN_HEADER_DETECTOR => false,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        $columns['coupon'] = [];

        $columns['xc4EntityId'] = [];

        return $columns;
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        if (version_compare(static::getPlatformVersion(), '4.4.0') < 0) {
            $userIdRelatedFieldset = 'IF(o.login, 0, 1) customerAnonymous,'
                . 'o.login customerId,';

            $addressDiffFieldset = 'o.b_zipcode     customerZipcodeBillingAddressField,'
                . 'o.phone                          customerPhoneBillingAddressField,'
                . 'o.s_zipcode                      customerZipcodeShippingAddressField,'
                . 'o.phone                        customerPhoneShippingAddressField,';

            $addressSame = 'IF(o.b_firstname = o.s_firstname
                AND o.b_lastname = o.s_lastname
                AND o.b_address = o.s_address
                AND o.b_city = o.s_city
                AND o.b_country = o.s_country
                AND o.b_state = o.s_state
                AND o.b_zipcode = o.s_zipcode, 1, 0)   customerAddressSame,';
        } else {
            $userIdRelatedFieldset = 'IF(o.userid, 0, 1) customerAnonymous,'
                . 'o.userid customerId,';

            $addressDiffFieldset = 'CONCAT(o.b_zipcode, " ", o.b_zip4) customerZipcodeBillingAddressField,'
                . 'o.b_phone                          customerPhoneBillingAddressField,'
                . 'CONCAT(o.s_zipcode, " ", o.s_zip4) customerZipcodeShippingAddressField,'
                . 'o.s_phone                          customerPhoneShippingAddressField,';

            $addressSame = 'IF(o.b_firstname = o.s_firstname
                AND o.b_lastname = o.s_lastname
                AND o.b_address = o.s_address
                AND o.b_city = o.s_city
                AND o.b_country = o.s_country
                AND o.b_state = o.s_state
                AND CONCAT(o.b_zipcode, " ", o.b_zip4) = CONCAT(o.s_zipcode, " ", o.s_zip4)
                AND o.b_phone = o.s_phone, 1, 0)      customerAddressSame,';
        }

        $shippingMethod = "IF(o.shipping = '', concat('shippingid=', o.shippingid), o.shipping) shippingMethod,";
        if (version_compare(static::getPlatformVersion(), '4.1.11') < 0) {
            $shippingMethod = "concat('shippingid=', o.shippingid) shippingMethod,";
        }

        return 'o.orderid xc4EntityId,'

            . 'o.orderid orderNumber,'
            . $userIdRelatedFieldset
            . 'o.email          customerEmail,'
            . $addressSame

            . 'o.b_firstname                      customerFirstnameBillingAddressField,'
            . 'o.b_lastname                       customerLastnameBillingAddressField,'
            . 'o.b_address                        customerStreetBillingAddressField,'
            . 'o.b_city                           customerCityBillingAddressField,'
            . 'o.b_country                        customerCountryCodeBillingAddressField,'
            . 'o.b_state                          customerStateIdBillingAddressField,'
            . 'o.b_state                          customerCustomStateBillingAddressField,'
            . $addressDiffFieldset

            . 'o.s_firstname                      customerFirstnameShippingAddressField,'
            . 'o.s_lastname                       customerLastnameShippingAddressField,'
            . 'o.s_address                        customerStreetShippingAddressField,'
            . 'o.s_city                           customerCityShippingAddressField,'
            . 'o.s_country                        customerCountryCodeShippingAddressField,'
            . 'o.s_state                          customerStateIdShippingAddressField,'
            . 'o.s_state                          customerCustomStateShippingAddressField,'

            . 'o.orderid items,'

            . 'o.orderid surcharges,'

            . 'o.discount        `DISCOUNT (surcharge)`,'
            . 'o.coupon_discount `DCOUPON (surcharge)`,'
            . 'o.shipping_cost   `SHIPPING (surcharge)`,'

            . "IF(o.coupon <> '', CONCAT(o.coupon, '``', o.coupon_discount), '')   coupon,"

            . '""         currency,'
            . 'o.subtotal subtotal,'
            . 'o.total    total,'

            . 'o.payment_method paymentTransactionMethod,'
            . 'o.status         paymentTransactionStatus,'
            . 'o.total          paymentTransactionValue,'
            . 'o.status         paymentTransactionType,'
            . 'o.orderid        paymentTransactionId,'
            //. '"USD"            paymentTransactionCurrency,'
            . '""               paymentTransactionNote,'

            // TODO PublicTxnId should be extracted from fields like $order["order"]["extra"]["xpc_txnid"] or $sql_tbl[order_extras].khash IN ('paypal_txnid', 'pnref').
            // The value will be used $transaction->setPublicTxnId($data['SystemId']);
            . '""               paymentTransactionSystemId,'

            . $shippingMethod
            . 'o.orderid  trackingNumber,'

            . 'o.date date,'

            . 'o.status paymentStatus,'
            . 'o.status shippingStatus,'

            . 'o.customer_notes notes,'
            . 'o.notes adminNotes,'
            . 'o.details details,'

            . 'FALSE recent';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = static::getTablePrefix();

        return "{$prefix}orders o";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = '1';

        if (static::isDemoModeMigration()) {
            $orderIds = static::getDemoOrderIds();
            if (!empty($orderIds)) {
                $orderIds = implode(',', $orderIds);
                $result = "o.orderid IN ({$orderIds})";
            }
        }

        $startDate = static::getOrdersStartDate();
        if ($startDate && static::isImportRunning()) {
            $result .= " AND o.date >= {$startDate}";
        }

        return $result;
    }

    /**
     * Define columns which fetched as id of main entity
     * This ones should be normalized before calculate checksum
     *
     * @return array
     */
    protected static function defineColumnsNeedNormalizeForHash()
    {
        return [
            'items',
            'trackingNumber',
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Orders migrated');
    }

    // }}} </editor-fold>

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
        $result = [];

        foreach ($row as $i => $head) {
            if ($head === 'items') {
                $result[] = $i;
            }
        }

        return $result;
    }

    /**
     * @param mixed $value Value
     *
     * @return integer
     */
    protected function normalizeDateValue($value)
    {
        return (int) $value;
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
        $paymentStatusHash = [
            'I' => 'Q',
            'Q' => 'Q',
            'A' => 'A',
            'P' => 'P',
            'D' => 'C',
            'F' => 'D',
            'C' => 'P',
            'B' => 'Q',
            'R' => 'R',
        ];

        return $paymentStatusHash[$value] ?? 'Q';
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
        $paymentStatusHash = [
            'I' => 'N',
            'Q' => 'N',
            'A' => 'N',
            'P' => 'N',
            'D' => 'N',
            'F' => 'N',
            'C' => 'S',
            'B' => 'N',
            'R' => 'R',
        ];

        return $paymentStatusHash[$value] ?? 'N';
    }

    /**
     * Normalize value as state
     *
     * @param mixed $value       Value
     * @param mixed @countryCode Country code
     *
     * @return string|\XLite\Model\State
     */
    protected function normalizeValueAsStateWithCountry($value, $countryCode)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndState($countryCode, $value)
            ?: $this->normalizeValueAsState($value);
    }

    /**
     * Normalize order items
     *
     * @param mixed $value       Value
     *
     * @return string|\XLite\Model\State
     */
    protected function normalizeItemsValue($value)
    {
        return $this->executeCachedRuntime(function () use ($value) {
            $records = [];

            $PDOStatement = $this->getItemsValuePDOStatement();
            if (
                $value
                && $PDOStatement
                && $PDOStatement->execute([$value, $value])
            ) {
                $records = $PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
            }

            return $records;
        }, ['normalizeItemsValue', $value]);
    }

    /**
     * Normalize value as currency
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Currency
     */
    protected function normalizeValueAsCurrency($value)
    {
        $result = \XLite::getInstance()->getCurrency();

        return $result;
    }

    protected function clearEmptyLines($complexColumn)
    {
        if (empty($complexColumn)) {
            return $complexColumn; // WA for MW-82
        } else {
            return parent::clearEmptyLines($complexColumn);
        }
    }

    /**
     * Import 'items' value
     *
     * @param \XLite\Model\Order $order  Order
     * @param array              $value  Value
     * @param array              $column Column info
     */
    protected function importItemsColumn(\XLite\Model\Order $order, $value, array $column)
    {
        $result = [];

        $records = $this->normalizeItemsValue($value);

        foreach ($records as $k => $item) {
            $extra_data = static::unserializeLatin1($item['extra_data']);
            unset($item['extra_data']);

            $item['itemSubtotal'] = $extra_data['display']['subtotal'] ?? ($item['itemPrice'] * $item['itemQuantity']);

            $item['itemTotal'] = $extra_data['display']['discounted_subtotal'] ?? ($item['itemPrice'] * $item['itemQuantity']);

            $item['itemAttributes'] = str_replace("\n", '&&', $item['itemAttributes']);
            $item['itemAttributes'] = str_replace(': ', '=', $item['itemAttributes']);

            if (empty($item['itemName'])) {
                $item['itemName'] = 'Deleted';
            }

            foreach ($item as $name => $itemValue) {
                $result[$name][$k] = $itemValue;
            }
        }

        parent::importItemsColumn($order, $result, $column);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getItemsValuePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT '
            . 'product         itemName,'
            . 'productcode     itemSKU,'
            . 'product_options itemAttributes,'
            . 'price           itemPrice,'
            . 'amount          itemQuantity,'
            . 'extra_data      extra_data'
            . " FROM {$prefix}order_details"
            . ' WHERE orderid = ?'
            . ' UNION ALL SELECT '
            . 'CONCAT("gift card (", amount, ")")          itemName,'//sync with RedSqui\GiftCertificates\Model\GiftCerts::getName()
            . 'CONCAT("gc-", LEFT(gcid,' . static::ORDER_ITEM_ID_GCID_NUMBER . '))     itemSKU,' //Sync with \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\GiftCertificates
            . '"" itemAttributes,'
            . 'amount           itemPrice,'
            . '"1"          itemQuantity,'
            . '""      extra_data'
            . " FROM {$prefix}giftcerts"
            . ' WHERE orderid = ?'
        );
    }

    /**
     * Import 'transactions' value
     *
     * @param \XLite\Model\Order $order  Order
     * @param array              $value  Value
     * @param array              $column Column info
     */
    protected function importTransactionsColumn(\XLite\Model\Order $order, $value, array $column)
    {
        $transactionStatusHash = [
            'I' => 'I',
            'Q' => 'P',
            'A' => 'S',
            'P' => 'S',
            'D' => 'C',
            'F' => 'F',
            'C' => 'S',
            'B' => 'S',
        ];

        $transactionTypeHash = [
            'I' => 'sale',
            'Q' => 'sale',
            'A' => 'auth',
            'P' => 'sale',
            'D' => 'sale',
            'F' => 'sale',
            'C' => 'sale',
            'B' => 'sale',
        ];

        $statusField    = $value['paymentTransactionStatus'];
        $statusRowIndex = key($statusField);
        $statusValue    = $statusField[$statusRowIndex];

        $value['paymentTransactionStatus'][$statusRowIndex] = $transactionStatusHash[$statusValue] ?? 'I';

        $typeField    = $value['paymentTransactionType'];
        $typeRowIndex = key($typeField);
        $typeValue    = $typeField[$typeRowIndex];

        $value['paymentTransactionType'][$typeRowIndex] = $transactionTypeHash[$typeValue] ?? 'I';

        parent::importTransactionsColumn($order, $value, $column);
    }

    /**
     * Import 'surcharges' value
     *
     * @param \XLite\Model\Order $order  Order
     * @param array              $value  Value
     * @param array              $column Column info
     */
    protected function importSurchargesColumn(\XLite\Model\Order $order, $value, array $column)
    {
        foreach ($order->getSurcharges() as $surcharge) {
            \XLite\Core\Database::getEM()->remove($surcharge);
        }
        $order->getSurcharges()->clear();

        if (isset($value['DCOUPON (surcharge)'])) {
            foreach ($value['DCOUPON (surcharge)'] as $k => $v) {
                $value['DCOUPON (surcharge)'][$k] = -(float) $v;
            }
        }

        if (isset($value['DISCOUNT (surcharge)'])) {
            foreach ($value['DISCOUNT (surcharge)'] as $k => $v) {
                $value['DISCOUNT (surcharge)'][$k] = -(float) $v;
            }
        }

        parent::importSurchargesColumn($order, $value, $column);
    }

    /**
     * Normalize tracking number
     *
     * @param mixed $value       Value
     *
     * @return string|\XLite\Model\State
     */
    protected function normalizeTrackingNumberValue($value)
    {
        return $this->executeCachedRuntime(function () use ($value) {
            $trackingNumber = '';

            if (version_compare(static::getPlatformVersion(), '4.7.6') < 0) {
                $PDOStatement = $this->getOldVersionTrackingNumberValuePDOStatement();
            } else {
                $PDOStatement = $this->getTrackingNumberValuePDOStatement();
            }

            if ($value && $PDOStatement && $PDOStatement->execute([$value])) {
                $trackingNumber = implode(',', $PDOStatement->fetchAll(\PDO::FETCH_COLUMN));
            }

            return $trackingNumber;
        }, ['normalizeTrackingNumberValue', $value]);
    }

    /**
     * Import 'coupon' value
     *
     * @param \XLite\Model\Order $order  Order
     * @param string             $value  Value
     * @param array              $column Column info
     */
    protected function importCouponColumn(\XLite\Model\Order $order, $value, array $column)
    {
        if (class_exists('CDev\Coupons\Main')) {
            foreach ($order->getUsedCoupons() as $usedCoupon) {
                \XLite\Core\Database::getEM()->remove($usedCoupon);
            }
            $order->getUsedCoupons()->clear();

            if (!empty($value)) {
                [$type, $code, $amount] = explode('``', $value);

                switch ($type) {
                    case 'percent':
                        $type = '%';
                        break;
                    case 'absolute':
                        $type = '$';
                        break;
                    case 'free_ship':
                        $type = 'S';
                        break;
                    default:
                        $type = '$';
                        break;
                }

                $usedCoupon = new \CDev\Coupons\Model\UsedCoupon();

                $usedCoupon->setOrder($order);
                $order->addUsedCoupons($usedCoupon);
                $usedCoupon->setValue(floatval($amount));

                $coupon = \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon')->findOneBy(['code' => $code]);
                if ($coupon) {
                    $usedCoupon->setCoupon($coupon);
                    $coupon->addUsedCoupons($usedCoupon);
                } else {
                    $usedCoupon->setCode($code);
                }

                $usedCoupon->setType($type);

                \XLite\Core\Database::getEM()->persist($usedCoupon);
            }
        }
    }

    /**
     * Import 'trackingNumber' value
     *
     * @param \XLite\Model\Order $order  Order
     * @param string             $value  Value
     * @param array              $column Column info
     */
    protected function importTrackingNumberColumn(\XLite\Model\Order $order, $value, array $column)
    {
        $value = $this->normalizeTrackingNumberValue($value);

        $trackingNumbers = array_filter(explode(',', $value), static function ($v) {
            return strlen(trim($v)) > 0;
        });

        foreach ($order->getTrackingNumbers() as $trackingNumber) {
            \XLite\Core\Database::getEM()->remove($trackingNumber);
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
     * Create new address based on data
     *
     * @param $data
     *
     * @return \XLite\Model\Address
     */
    protected function createAddress($data)
    {
        if (
            isset($data['StateId'])
            && isset($data['CountryCode'])
            && $state = $this->normalizeValueAsStateWithCountry($data['StateId'], $data['CountryCode'])
        ) {
            $data['state'] = $state;
            $data['StateId'] = $state;
        }

        if (isset($data['Street'])) {
            $streetLines = preg_split('/[\n\r]/', $data['Street']);
            $streetLines = array_filter($streetLines, static function ($value) {
                return $value !== '';
            });
            $data['Street'] = implode(', ', $streetLines);
        }

        return parent::createAddress($data);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getOldVersionTrackingNumberValuePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT '
            . 'tracking'
            . " FROM {$prefix}orders"
            . ' WHERE orderid = ?'
        );
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getTrackingNumberValuePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT '
            . 'tracking'
            . " FROM {$prefix}order_tracking_numbers"
            . ' WHERE orderid = ?'
        );
    }

    /**
     * Import 'paymentStatus' value
     *
     * @param \XLite\Model\Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importPaymentStatusColumn(\XLite\Model\Order $order, $value, array $column)
    {
        if (is_scalar($value)) {
            $value = ['paymentStatus' => [$value]];
        }

        $value = array_map(static fn ($item) => reset($item), $value);

        $code = $this->normalizeModelPlainProperty($value['paymentStatus'], $column);

        $order->setPaymentStatus(\XLite\Core\Database::getRepo('XLite\Model\Order\Status\Payment')
            ->findOneBy([
                'code' => $code
            ]));
    }

    /**
     * Import 'shippingStatus' value
     *
     * @param \XLite\Model\Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importShippingStatusColumn(\XLite\Model\Order $order, $value, array $column)
    {
        if (is_scalar($value)) {
            $value = ['shippingStatus' => [$value]];
        }

        $value = array_map(static fn ($item) => reset($item), $value);

        $code = $this->normalizeModelPlainProperty($value['shippingStatus'], $column);

        $order->setShippingStatus(\XLite\Core\Database::getRepo('XLite\Model\Order\Status\Shipping')
            ->findOneBy([
                'code' => $code
            ]));
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating orders');
    }

    /**
     * Import 'customer' value
     *
     * @param Order $order  Order
     * @param array $value  Value
     * @param array $column Column info
     */
    protected function importCustomerColumn(\XLite\Model\Order $order, $value, array $column)
    {
        foreach ($value[static::CUSTOMER_PREFIX . 'Email'] as &$email) {
            if (empty($email)) {
                $email = 'no-email-customer@example.com';
            }
        }
        unset($email);

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

        $userId = $value[static::CUSTOMER_PREFIX . 'Id'] ?? false;
        $email = $value[static::CUSTOMER_PREFIX . 'Email'];
        $anonymous = isset($value[static::CUSTOMER_PREFIX . 'Anonymous'])
            ? $this->normalizeCustomerAnonymousValue($value[static::CUSTOMER_PREFIX . 'Anonymous'])
            : false;

        if ($userId && $email) {
            $anonymous = $this->checkIfSameEmailAdminExists($userId, $email);
        }

        if ($orderProfile = $order->getProfile()) {
            $orderProfile->setLogin($email);
            $orderProfile->setAnonymous($anonymous);
        }

        $profile = null;
        if ($userId && !$anonymous) {
            $entry = static::getEntryFromRegistryByClassAndSourceId('XLite\Model\Profile', $userId);
            $profile = $entry ? \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($entry->getResultId()) : null;
        }

        if (!$profile) {
            $profile = $this->getCustomerByEmailAndAnonymous($email, $anonymous);
        }

        if (isset($orderProfile)) {
            $order->setOrigProfile($profile);
        } else {
            $order->setProfileCopy($profile);
        }
        $profile = $order->getProfile();

        foreach ($profile->getAddresses() as $item) {
            \XLite\Core\Database::getEM()->remove($item);
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
     * Detect coupon header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectCouponHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('coupon', $row);
    }

    /**
     * Import 'shippingMethod' value
     *
     * @param \XLite\Model\Order  $order  Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importShippingMethodColumn(\XLite\Model\Order $order, $value, array $column)
    {
        if (preg_match('/^shippingid=(.+)/', $value, $m)) {
            $shippingId = intval($m[1]);
            if ($shippingId != 0) {
                $prefix = static::getTablePrefix();

                $PDOStatement = static::getPreparedPDOStatement(
                    'SELECT '
                    . 'shipping'
                    . " FROM {$prefix}shipping"
                    . ' WHERE shippingid = ?'
                );

                if ($value && $PDOStatement && $PDOStatement->execute([$shippingId])) {
                    $value = $PDOStatement->fetch(\PDO::FETCH_COLUMN);
                } else {
                    $value = '';
                }
            } else {
                $value = '';
            }
        }

        parent::importShippingMethodColumn($order, $value, $column);
    }

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
        $patternCustomer = static::CUSTOMER_PREFIX . '(Email|Anonymous|AddressSame|Id)';
        $patternAddress = static::CUSTOMER_PREFIX . '.+' . '(Shipping|Billing)' . static::ADDRESS_FIELD_SUFFIX;
        $pattern = "({$patternCustomer}|{$patternAddress})";

        return $this->detectHeaderByPattern($pattern, $row);
    }

    protected function checkIfSameEmailAdminExists($userId, $email)
    {
        $records = $this->executeCachedRuntime(function () use ($email) {
            $records = [];

            $PDOStatement = $this->getUsersByEmailPDOStatement();

            if (
                $email
                && $PDOStatement
                && $PDOStatement->execute([$email])
            ) {
                $records = $PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
            }

            return $records;
        }, ['getUsersByEmail', $email]);

        $result = false;
        if (!empty($records)) {
            $record = reset($records);
            $result = $record['userId'] != $userId;
        }

        return $result;
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getUsersByEmailPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        if (version_compare(static::getPlatformVersion(), '4.4.0') < 0) {
            $idField = 'login AS `userId`,';
        } else {
            $idField = 'id AS `userId`,';
        }

        return static::getPreparedPDOStatement(
            'SELECT '
            . $idField
            . ' usertype AS `usertype`'
            . " FROM {$prefix}customers"
            . ' WHERE email = ?'
            . ' ORDER BY FIELD(usertype, "A", "P", "C")'
        );
    }
}
