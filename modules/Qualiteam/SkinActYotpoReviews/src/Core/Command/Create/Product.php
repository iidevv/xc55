<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Command\Create;

use Qualiteam\SkinActYotpoReviews\Core\Command\ACreateUpdateCommand;
use Qualiteam\SkinActYotpoReviews\Core\Command\ICommand;

class Product extends ACreateUpdateCommand implements ICommand
{
    /**
     * @return void
     * @throws \Exception
     */
    protected function executeCommand(): void
    {
        $this->getResultYotpoRequest();

        if ($this->isErrorResult()) {
            $this->showError();
        } else {
            $this->setYotpoId('product');
            $this->persistEntity();
            $this->updateEntity();
        }
    }
}
