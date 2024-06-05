<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Dispatcher;

use Qualiteam\SkinActYotpoReviews\Core\Factory\Commands\UpdateAverageRatingAndVotesCountFactory;
use Qualiteam\SkinActYotpoReviews\Messenger\Message\ExportMessage;
use XCart\Container;

class UpdateAverageRatingAndVotesCountDispatcher
{
    protected ExportMessage $message;

    public function __construct()
    {
        /** @var UpdateAverageRatingAndVotesCountFactory $commandFactory */
        $commandFactory = Container::getContainer()
            ? Container::getContainer()?->get('Qualiteam\SkinActYotpoReviews\Core\Factory\Commands\UpdateAverageRatingAndVotesCountFactory')
            : null;

        $command = $commandFactory->createCommand();
        $this->message = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}