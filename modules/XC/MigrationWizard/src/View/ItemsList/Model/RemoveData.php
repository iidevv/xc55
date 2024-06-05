<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Remove data items list
 * @Extender\Mixin
 */
class RemoveData extends \XLite\View\ItemsList\Model\RemoveData
{
    /**
     * Types
     */
    public const TYPE_MEMBERSHIPS  = 'memberships';
    public const TYPE_PAYMENTS     = 'payments';
    public const TYPE_SHIPPINGS    = 'shippings';
    public const TYPE_ZONES        = 'zones';

    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        return parent::getPlainData() + [
            static::TYPE_MEMBERSHIPS => [
                'name' => static::t('Memberships'),
            ],
            static::TYPE_PAYMENTS => [
                'name' => static::t('Payments'),
            ],
            static::TYPE_SHIPPINGS => [
                'name' => static::t('Shippings'),
            ],
            static::TYPE_ZONES => [
                'name' => static::t('Zones'),
            ],
        ];
    }

    /**
     * Check - allow remove memberships or not
     *
     * @return boolean
     */
    protected function isAllowRemoveMemberships()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Membership')->count();
    }

    /**
     * Check - allow remove payments or not
     *
     * @return boolean
     */
    protected function isAllowRemovePayments()
    {
        $count = 0;

        $payments = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findBy(['added' => true]);

        foreach ($payments as $payment) {
            if (
                $payment->added
                && $payment->class === 'XLite\Model\Payment\Processor\Offline'
            ) {
                $count++;
                continue;
            }
            if (
                $payment->enabled
                &&  (
                    in_array($payment->class, [
                        'XLite\Model\Payment\Processor\COD',
                        'XLite\Model\Payment\Processor\Check',
                        'XLite\Model\Payment\Processor\PhoneOrdering',
                        'XLite\Model\Payment\Processor\PurchaseOrder'])
                    || !empty($payment->moduleName)
                )
            ) {
                $count++;
                continue;
            }
        }

        return 0 < $count;
    }

    /**
     * Check - allow remove shippings or not
     *
     * @return boolean
     */
    protected function isAllowRemoveShippings()
    {
        $count = 0;

        $shippings = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')
            ->findBy(['added' => true]);

        foreach ($shippings as $shipping) {
            if (
                $shipping->added
                && $shipping->processor === 'offline'
            ) {
                $count++;
                continue;
            }
            if (
                $shipping->enabled
                && !empty($shipping->moduleName)
            ) {
                $count++;
                continue;
            }
        }

        return 0 < $count;
    }

    /**
     * Check - allow remove zones or not
     *
     * @return boolean
     */
    protected function isAllowRemoveZones()
    {
        return 1 < \XLite\Core\Database::getRepo('XLite\Model\Zone')->count();
    }

    /**
     * Build metod name
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param string               $pattern Pattern
     *
     * @return string
     */
    protected function buildMethodName(\XLite\Model\AEntity $entity, $pattern)
    {
        $method = parent::buildMethodName($entity, $pattern);

        if (empty($method)) {
            switch ($entity->getId()) {
                case static::TYPE_MEMBERSHIPS:
                    $name = 'Memberships';
                    break;

                case static::TYPE_PAYMENTS:
                    $name = 'Payments';
                    break;

                case static::TYPE_SHIPPINGS:
                    $name = 'Shippings';
                    break;

                case static::TYPE_ZONES:
                    $name = 'Zones';
                    break;

                default:
            }

            $method = $name ? sprintf($pattern, $name) : null;
        }

        return $method;
    }
}
