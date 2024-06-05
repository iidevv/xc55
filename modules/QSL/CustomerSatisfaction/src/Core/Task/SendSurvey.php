<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Core\Task;

use XLite\Core\Database;

/**
 * Scheduled task that sends survey.
 */
class SendSurvey extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Customer satisfaction delivery');
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        $qb = Database::getRepo('XLite\Model\Order')->createQueryBuilder();
        $qb->andWhere('o.surveyFutureSendDate < :now')
            ->andWhere('o.surveyFutureSendDate > :zero')
            ->setParameter('now', \XLite\Core\Converter::time())
            ->setParameter('zero', 0);
        $ordersIterator = $qb->iterate();

        $batchSize = 10;
        $counter = 0;
        foreach ($ordersIterator as $orderData) {
            $counter++;
            $order = $orderData[0];

            Database::getRepo('QSL\CustomerSatisfaction\Model\Survey')->createSurvey($order);
            $order->setSurveyFutureSendDate(0);
            Database::getEM()->flush();

            if ($counter % $batchSize === 0) {
                Database::getEM()->clear();
            }
        }
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        // 24h
        return 86400;
    }
}
