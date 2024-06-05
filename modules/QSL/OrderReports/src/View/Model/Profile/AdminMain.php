<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\View\Model\Profile;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Administrator profile model widget. This widget is used in the admin interface
 * @Extender\Mixin
 */
abstract class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    use ExecuteCachedTrait;

    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        switch ($name) {
            case 'orders_count':
                if ($value) {
                    $value = $this->executeCachedRuntime(function () use ($value) {
                        $totalSpend = \XLite\Core\Database::getRepo('\XLite\Model\Order')->createQueryBuilder('o')
                            ->select('sum(o.total) as total')
                            ->linkInner('o.orig_profile', 'profile')
                            ->andWhere('profile.login = :email')
                            ->setParameter('email', $this->getModelObject()->getLogin())
                            ->linkInner('o.paymentStatus', 'ps')
                            ->andWhere('ps.code IN (:order_statuses)')
                            ->setParameter(
                                'order_statuses',
                                \QSL\OrderReports\Controller\Admin\OrderReports::getAllowedOrderStatuses()
                            )
                            ->groupBy('profile.login')
                            ->getArrayResult();

                        $currency = \XLite::getInstance()->getCurrency();

                        $totalSpend = $totalSpend ? $totalSpend[0]['total'] : 0;

                        $value = $value . ' (' . \XLite\View\AView::formatPrice($totalSpend, $currency) . ')';

                        return $value;
                    }, [$value, $this->getModelObject()->getLogin()]);
                }

                break;

            default:
        }

        return $value;
    }
}
