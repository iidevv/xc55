<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\MessageHandler;

use Qualiteam\SkinActAftership\Core\Helper\IOrderTrackingNumberWorkerHelper;
use Qualiteam\SkinActAftership\Core\Message\AftershipErrorWorkToDo;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use XLite\Core\Database;
use XLite\InjectLoggerTrait;
use XLite\Model\OrderTrackingNumber;

class AftershipErrorWorker implements MessageHandlerInterface
{
    use InjectLoggerTrait;

    public function __construct(private IOrderTrackingNumberWorkerHelper $helper)
    {
    }

    public function __invoke(AftershipErrorWorkToDo $message): void
    {
        $id = $message->getData()->getOrderTrackingId();

        $this->getLogger('AftershipOrderTrackingError')->info('start to work with', [$id]);

        $orderTracking = Database::getRepo(OrderTrackingNumber::class)->findOneBy(['tracking_id' => $id]);

        if ($this->helper->shouldBeUpdated($orderTracking)) {
            $this->helper->updateOrderTrackingNumber($orderTracking);
        }

        $this->helper->finishJob();

        $this->getLogger('AftershipOrderTrackingError')->info('finish to work with', [$id]);
    }
}
