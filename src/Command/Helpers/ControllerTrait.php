<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Helpers;

trait ControllerTrait
{
    private $helper;

    /**
     * @param $target
     *
     * @return array
     */
    public function isControllerExistsInZones($target)
    {
        if (!$this->helper) {
            $this->helper = new Controller();
        }

        return $this->helper->isControllerExistsInZones($target);
    }
}
