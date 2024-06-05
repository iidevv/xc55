<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\State\Registry;

use XLite\Core\Job\State\JobStateInterface;

class EventDrivenRegistry implements StateRegistryInterface
{
    /**
     * @return mixed
     */
    public function get($id)
    {
        throw new \Exception('Not implemented yet');
    }

    /**
     * @param int     $jobId
     * @param boolean $cancelled
     */
    public function set($id, JobStateInterface $state)
    {
        throw new \Exception('Not implemented yet');
    }

    /**
     * @param int   $id
     * @param       $callback
     *
     * @return mixed
     */
    public function process($id, $callback)
    {
        throw new \Exception('Not implemented yet');
    }
}
