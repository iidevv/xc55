<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Model\Repo;

/**
 * Record repository (quantity)
 */
class Record extends \QSL\BackInStock\Model\Repo\AbsRecord
{
    /**
     * Count waiting records by product (summary)
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return integer
     */
    public function countSumWaiting(\XLite\Model\Product $product)
    {
        $emails = $this->createQueryBuilder('r')
            ->select('r.email')
            ->andWhere('r.product = :product AND r.state != :sent')
            ->setParameter('sent', \QSL\BackInStock\Model\Record::STATE_SENT)
            ->setParameter('product', $product)
            ->getArrayResult();
        $emailsPrices = \XLite\Core\Database::getRepo('QSL\BackInStock\Model\RecordPrice')->createQueryBuilder('r')
            ->select('r.email')
            ->andWhere('r.product = :product AND r.state != :sent')
            ->setParameter('sent', \QSL\BackInStock\Model\Record::STATE_SENT)
            ->setParameter('product', $product)
            ->getArrayResult();

        $idx = [];

        foreach ($emails as $row) {
            $idx[$row['email']] = true;
        }

        foreach ($emailsPrices as $row) {
            $idx[$row['email']] = true;
        }

        return count($idx);
    }

    /**
     * @inheritdoc
     */
    public function sendNotifications()
    {
        $this->checkSendingRecords();

        $result = [0, 0];
        foreach ($this->findUnsentNotifications() as $record) {
            /** @var \QSL\BackInStock\Model\Record $record */
            $record->setState(\QSL\BackInStock\Model\Record::STATE_SENDING);
            $record->setStartSendingDate(\XLite\Core\Converter::time());
            \XLite\Core\Database::getEM()->flush($record);

            /** @var string|null $error */
            $error = \XLite\Core\Mailer::sendBackInStockNotification($record);
            if (!$error) {
                $record->setState(\QSL\BackInStock\Model\Record::STATE_SENT);
                $record->setSentDate(\XLite\Core\Converter::time());
                $result[0]++;
            } else {
                $record->setState(\QSL\BackInStock\Model\Record::STATE_BOUNCED);
                $result[1]++;
            }
        }

        return $result;
    }
}
