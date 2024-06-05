<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Command\Update;

use Qualiteam\SkinActYotpoReviews\Core\Command\ACreateUpdateCommand;
use Qualiteam\SkinActYotpoReviews\Core\Command\ICommand;

class Product extends ACreateUpdateCommand implements ICommand
{
    /**
     * @throws \Exception
     */
    protected function executeCommand(): void
    {
        $this->getResultYotpoRequest();

        if ($this->isErrorResult()) {
            $this->showError();
        }
    }
}
