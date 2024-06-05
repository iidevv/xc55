<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Model\Repo;

/**
 * Record repository
 */
class RecordPrice extends \QSL\BackInStock\Model\Repo\AbsRecord
{
    /**
     * @inheritdoc
     */
    public function sendNotifications()
    {
        $this->checkSendingRecords();

        $result = [0, 0];
        foreach ($this->findUnsentNotifications() as $record) {
            /** @var \QSL\BackInStock\Model\RecordPrice $record */
            $record->setState(\QSL\BackInStock\Model\ARecord::STATE_SENDING);
            $record->setStartSendingDate(\XLite\Core\Converter::time());
            \XLite\Core\Database::getEM()->flush($record);

            /** @var string|null $error */
            $error = \XLite\Core\Mailer::sendLowPriceNotification($record);
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
