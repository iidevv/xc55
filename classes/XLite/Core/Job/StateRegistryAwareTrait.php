<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;

use XLite\Core\Job\State\Registry\StateRegistryInterface;

trait StateRegistryAwareTrait
{
    protected $registry = null;

    /**
     * @param StateRegistryInterface $jobState
     *
     * @return mixed
     */
    public function setStateRegistry(StateRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return StateRegistryInterface
     */
    public function getStateRegistry()
    {
        return $this->registry;
    }
}
