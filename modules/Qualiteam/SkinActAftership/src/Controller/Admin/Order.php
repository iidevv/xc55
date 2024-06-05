<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Controller\Admin;

use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\OrderHistory;

/**
 * Class order
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * @return void
     */
    protected function updateTracking(): void
    {
        $values = $this->prepareAftershipCouriersData($this->getOrder()->getTrackingNumbers()->toArray());

        parent::updateTracking();

        $this->processAftershipCouriersInfoChanges(
            $values,
            $this->prepareAftershipCouriersData($this->getOrder()->getTrackingNumbers()->toArray())
        );
    }

    /**
     * Prepare data
     *
     * @param array $values
     *
     * @return array
     */
    protected function prepareAftershipCouriersData(array $values): array
    {
        return array_combine(
            array_map(static function (\XLite\Model\OrderTrackingNumber $number) {
                return $number->getValue();
            }, $values),
            array_map(static function (\XLite\Model\OrderTrackingNumber $number) {
                return $number->getAftershipCourierName();
            }, $values)
        );
    }

    /**
     * @param array $old
     * @param array $new
     *
     * @return void
     */
    protected function processAftershipCouriersInfoChanges(array $old, array $new): void
    {
        $added = array_diff_key(array_diff_assoc($new, $old), $old);
        $changed = array_map(static function ($key) use ($old, $new) {
            return [
                'old' => $old[$key],
                'new' => $new[$key],
                'name' => $key,
            ];
        }, array_keys(array_intersect_key(array_diff_assoc($new, $old), $old)));

        if ($added || $changed) {
            $info = OrderHistory::getInstance()->getAftershipCouriersInfoLines(
                $added,
                $changed,
            );

            $i = 0;
            foreach ($info as $line) {
                $i++;
                static::setOrderChanges(
                    static::t('Aftership couriers information') . ":$i",
                    $line,
                    ''
                );
            }
        }
    }
}
