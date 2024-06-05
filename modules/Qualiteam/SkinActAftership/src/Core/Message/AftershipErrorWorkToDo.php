<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Message;

use Qualiteam\SkinActAftership\Core\Message\AftershipErrorWorkToDoDataContainer;

class AftershipErrorWorkToDo
{
    public function __construct(private AftershipErrorWorkToDoDataContainer $data)
    {
    }

    public function getData()
    {
        return $this->data;
    }
}