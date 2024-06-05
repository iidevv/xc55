<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

/**
 * Custom
 */
class Custom extends \XLite\Logic\Import\Processor\AProcessor
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const TABLE_TYPE_WSI = 'WSI';

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    public static function defineProcessor()
    {
        return 'XLite\Model\Shipping\Processor\Offline';
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'markup_id'         => [
                static::COLUMN_IS_KEY => true,
            ],
            'shipping_method'   => [],
            'zone'              => [],
            'min_weight'        => [],
            'max_weight'        => [],
            'min_total'         => [],
            'max_total'         => [],
            'min_items'         => [],
            'max_items'         => [],
            'markup_flat'       => [],
            'markup_percent'    => [],
            'markup_per_item'   => [],
            'markup_per_weight' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return 'SR.rateid AS `markup_id`,'
            . 'SR.shippingid AS `shipping_method`,'
            . 'SR.zoneid AS `zone`,'
            . 'SR.minweight AS `min_weight`,'
            . 'SR.maxweight AS `max_weight`,'
            . 'SR.mintotal AS `min_total`,'
            . 'SR.maxtotal AS `max_total`,'
            //            . ", SR.minitems AS `min_items`" // not supported in 4.x
            //            . ", SR.maxitems AS `max_items`" // not supported in 4.x
            . 'SR.rate AS `markup_flat`,'
            . 'SR.rate_p AS `markup_percent`,'
            . 'SR.item_rate AS `markup_per_item`,'
            . 'SR.weight_rate AS `markup_per_weight`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = self::getTablePrefix();

        return "{$prefix}shipping AS S"
            . " INNER JOIN {$prefix}shipping_rates AS SR"
            . " ON SR.shippingid = S.shippingid"
            . " AND S.code = ''"
            . " LEFT JOIN {$prefix}zones AS Z"
            . " ON Z.zoneid = SR.zoneid";
    }

    /**
     * Define registry entry
     *
     * @return array
     */
    public static function defineRegistryEntry()
    {
        return [
            static::REGISTRY_SOURCE => 'markup_id',
            static::REGISTRY_RESULT => 'markup_id',
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    public static function getProcessor()
    {
        $processor = null;

        if ($processor === null) {
            $class     = static::defineProcessor();
            $processor = new $class();
        }

        return $processor->getProcessorId();
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'zone' value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Zone
     */
    protected function normalizeZoneValue($value)
    {
        $zone = [];
        $className = 'XLite\Model\Zone';

        if (!isset($zone[$value])) {
            if ($value === '0') {
                $zone[$value] = \XLite\Core\Database::getRepo($className)->findOneBy(['is_default' => true]);

                return $zone[$value];
            }

            $entry = static::getEntryFromRegistryByClassAndSourceId($className, $value);

            if ($entry) {
                $zone[$value] = \XLite\Core\Database::getRepo($className)->find($entry->getResultId());

                return $zone[$value];
            }
        }

        return $zone[$value];
    }

    /**
     * Normalize 'shipping method' value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function normalizeShippingMethodValue($value)
    {
        $shippingMethod = [];
        $className = 'XLite\Model\Shipping\Method';

        if (!isset($shippingMethod[$value])) {
            $entry = static::getEntryFromRegistryByClassAndSourceId($className, $value);

            if ($entry) {
                $shippingMethod[$value] = \XLite\Core\Database::getRepo($className)->find($entry->getResultId());

                return $shippingMethod[$value];
            }

            $connection = $this->getConnection();
            $prefix     = $this->getTablePrefix();

            if (
                $connection
                && ($query = $connection->query(
                    'SELECT shipping, shipping_time'
                    . " FROM {$prefix}shipping"
                    . " WHERE shippingid='{$value}'"
                ))
                && !empty($query)
                && ($record = $query->fetch(\PDO::FETCH_ASSOC))
                && !empty($record)
            ) {
                $method = new \XLite\Model\Shipping\Method();

                $method->setProcessor(static::getProcessor());
                $method->setName($record['shipping']);
                $method->setDeliveryTime($record['shipping_time']);
                $method->setAdded(true);
                $method->setEnabled(true);
                $method->setTableType(static::TABLE_TYPE_WSI);

                \XLite\Core\Database::getEM()->persist($method);
                \XLite\Core\Database::getEM()->flush($method); // required for -> getMethodId()

                $sourceId = $value;
                $resultId = $method->getMethodId();

                $this->registerModelInRegistry($className, $sourceId, $resultId);

                $shippingMethod[$value] = $method;
            }
        }

        return $shippingMethod[$value];
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">
    /**
     * Ignore Some Fields That Have Worthless Values. Are Useful For Shippings
     *
     * @param string $field_name
     *
     * @return string
     */
    protected static function addWorthlessFieldCondition($field_name)
    {
        return "";
    }

    // }}} </editor-fold>
}
