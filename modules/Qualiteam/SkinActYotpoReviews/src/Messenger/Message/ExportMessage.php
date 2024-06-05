<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Messenger\Message;

use Qualiteam\SkinActYotpoReviews\Core\Command\ICommand;

class ExportMessage
{
    protected ICommand $command;

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Command\ICommand $command
     */
    public function __construct(ICommand $command)
    {
        $this->command = $command;
    }

    /**
     * @return ICommand
     */
    public function getCommand(): ICommand
    {
        return $this->command;
    }
}