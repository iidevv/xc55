<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Command\Create;

use Qualiteam\SkinActYotpoReviews\Core\Command\ACreateUpdateCommand;
use Qualiteam\SkinActYotpoReviews\Core\Command\ICommand;

class Order extends ACreateUpdateCommand implements ICommand
{
    /**
     * @throws \Exception
     */
    protected function executeCommand(): void
    {
        $this->getResultYotpoRequest();

        if (!$this->isErrorResult()) {
            $this->setYotpoId('order');
            $this->persistEntity();
            $this->updateEntity();
        }
    }
}
