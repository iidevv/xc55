<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Task;

use Qualiteam\SkinActYotpoReviews\Core\Dispatcher\UpdateYotpoReviewsDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Container;
use XLite\Core\Task\Base\Periodic;

class UpdateYotpoReviews extends Periodic
{
    protected ?MessageBusInterface $bus;

    public function getTitle()
    {
        return static::t('SkinActYotpoReviews update yotpo reviews');
    }

    protected function runStep()
    {
        $dispatcher = new UpdateYotpoReviewsDispatcher();
        $message    = $dispatcher->getMessage();

        $this->bus = Container::getContainer() ? Container::getContainer()?->get('messenger.default_bus') : null;
        $this->bus->dispatch($message);
    }

    protected function getPeriod()
    {
        return static::INT_1_DAY;
    }
}