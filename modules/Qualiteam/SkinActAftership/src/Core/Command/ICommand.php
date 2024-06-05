<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Command;

interface ICommand
{
    /**
     * Execute
     *
     * @return void
     * @throws CommandException
     */
    public function execute(): void;
}
