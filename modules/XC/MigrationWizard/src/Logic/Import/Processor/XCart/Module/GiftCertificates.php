<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * GiftCerts module
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 */
class GiftCertificates extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    public const XC4_CONDITION = "gc.status IN ('A','P','D','E') AND (gc.amount > 0 OR gc.debit > 0 OR gc.send_via !='')";
    public const ORDER_ITEM_ID_GCID_NUMBER = 3;// Sync with \XC\MigrationWizard\Logic\Import\Processor\XCart\Orders

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'gcid' => [
                static::COLUMN_IS_KEY => true,
            ],
            'card_type' => [],
            'order' => [],// HAS TO BE SELECTED FIRST, BEFORE status
            'recipient_email' => [],
            'recipient_address' => [],
            'recipient_name' => [],
            'message' => [],
            'sender_signature' => [],
            'amount' => [],
            'balance' => [],
            'enabled' => [],
            'status' => [],//HAS TO BE SELECTED AFTER order FIELD
            'add_date' => [],
            'delivery_date' => [],
            'used' => [],

            'xc4EntityId' => [],
        ];
        //order_item_id delivery_date?
    }



    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('\RedSqui\GiftCertificates\Model\GiftCerts');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        if (version_compare(static::getPlatformVersion(), '4.4.0') < 0) {
            $_zip4 = "''";
        } else {
            $_zip4 = "NULLIF(gc.recipient_zip4, '')";
        }

        return "gc.gcid AS `xc4EntityId`"
            . ", gc.gcid AS `gcid`"
            . ", gc.send_via AS `card_type`"
            . ", gc.orderid AS `order`" // HAS TO BE SELECTED FIRST, BEFORE status
            . ", gc.purchaser AS `sender_signature`"
            . ", gc.recipient_email AS `recipient_email`"
            . ", CONCAT_WS(' ',NULLIF(gc.recipient, ''), NULLIF(gc.recipient_firstname, ''), NULLIF(gc.recipient_lastname, '')) AS `recipient_name`"
            . ", CONCAT_WS(',', NULLIF(gc.recipient_address, ''), NULLIF(gc.recipient_city, ''), NULLIF(gc.recipient_state, ''), CONCAT_WS('-',NULLIF(gc.recipient_zipcode, ''), $_zip4), NULLIF(gc.recipient_country, ''), NULLIF(gc.recipient_county, ''), NULLIF(gc.recipient_phone, '')) AS `recipient_address`"
            . ", gc.message AS `message`"
            . ", gc.amount AS `amount`"
            . ", gc.debit AS `balance`"
            . ", IF(gc.status IN ('P','A'),1,0) AS `enabled`"// HAS TO BE SELECTED AFTER ORDER FIELD
            . ", gc.status AS `status`"// HAS TO BE SELECTED AFTER ORDER FIELD
            . ", gc.add_date AS `add_date`"
            . ", gc.add_date AS `delivery_date`"
            . ", 1 AS `used`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();
        return "{$tp}giftcerts AS gc ";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = static::XC4_CONDITION;

        if (static::isDemoModeMigration()) {
            $orderIds = static::getDemoOrderIds();
            if (!empty($orderIds)) {
                $orderIds = implode(',', $orderIds);
                $result .= " AND gc.orderid IN ({$orderIds})";
            }
        }

        return $result;
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['RedSqui\GiftCertificates'];
    }

    /**
     * Return TRUE if processor has a heading row
     *
     * @return boolean
     */
    public static function hasHeadingRow()
    {
        return count(static::defineSubProcessors()) === 0;
    }


    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'Status' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeStatusValue($value, $data)
    {
        switch ($value) {
            case 'P':
                if ($this->assignedOrder) {
                    // The Status Can Be Changed The Order'S Status
                    // Only GC With Order Can Have Pending Status As The Status Cannot Be Changed By Admin
                    return \RedSqui\GiftCertificates\Model\GiftCerts::STATUS_NOT_PAYED_YET;//Pending
                } else {
                    // Force Activate When The Asotiated Order Is Empty. The Same Code For Enabled Field
                    return \RedSqui\GiftCertificates\Model\GiftCerts::STATUS_ACTIVE;
                }
                break;
            case 'A':
                return \RedSqui\GiftCertificates\Model\GiftCerts::STATUS_ACTIVE;//Active
                break;
            default:
                return \RedSqui\GiftCertificates\Model\GiftCerts::STATUS_NOT_PAYED_YET;//Disabled/Expired Migrated As Pending
        }
    }

    /**
     * Normalize 'Enabled' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normaLizeEnabledValue($value, $data)
    {
        if (
            $value == 1
            && !$this->assignedOrder
            && $data['status'] != 'A'
        ) {
            // Auto Disable Pending(Etc) Gifcerts Without Assigned Order
            return 0;
        }
        return $value;
    }

    /**
     * Normalize 'Card_type' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeCardTypeValue($value)
    {
        switch ($value) {
            case 'E':
                return \RedSqui\GiftCertificates\Model\GiftCerts::TYPE_VIRTUAL;
            case 'P':
                return \RedSqui\GiftCertificates\Model\GiftCerts::TYPE_TANGIBLE;
        }
    }

    /**
     * Normalize 'Order' value
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function normalizeOrderValue($value)
    {
        if (
            !empty($value)
            && $this->assignedOrder
        ) {
            return $this->assignedOrder;
        }

        return null;
    }

    /**
     * Normalize 'Message' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeMessageValue($value)
    {
        return substr($value, 0, \RedSqui\GiftCertificates\Model\GiftCerts::LENGTH_LIMIT_MESSAGE);
    }

    /**
     * Normalize 'Recipient_address' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeRecipientAddressValue($value)
    {
        return substr($value, 0, \RedSqui\GiftCertificates\Model\GiftCerts::LENGTH_LIMIT_ADDRESS);
    }

    /**
     * Normalize 'Recipient_name' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeRecipientNameValue($value)
    {
        return substr($value, 0, \RedSqui\GiftCertificates\Model\GiftCerts::LENGTH_LIMIT_NAME);
    }

    /**
     * Normalize 'Sender_signature' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeSenderSignatureValue($value)
    {
        return substr($value, 0, \RedSqui\GiftCertificates\Model\GiftCerts::LENGTH_LIMIT_SIGNATURE);
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}giftcerts AS gc LIMIT 1"
        );
    }

    // }}} </editor-fold>

    /**
     * Get Title To Clarify Which Entity Is Migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migration gift certificates');
    }



    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        return $this->getRepository()->getCardByCode($data['gcid']);
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
        if (
            !empty($data['order'])
            && ($order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findOneByOrderNumber($data['order']))
        ) {
            $this->assignedOrder = $order;
        } else {
            $this->assignedOrder = false;
        }

        $res = parent::importData($data);

        if (
            $this->assignedOrder
            && $this->currentlyProcessingModel
        ) {
            $order_item = \XLite\Core\Database::getRepo('\XLite\Model\OrderItem')->findOneBy([
                'sku' => \RedSqui\GiftCertificates\Model\GiftCerts::GC_SKU_PREFIX . substr($data['gcid'], 0, static::ORDER_ITEM_ID_GCID_NUMBER),
                'price' => $data['amount'],
            ]);

            if ($order_item) {
                $order_item->setGiftCard($this->currentlyProcessingModel);

                $this->currentlyProcessingModel->setOrderItem($order_item);
            }
        }

        return $res;
    }
}
