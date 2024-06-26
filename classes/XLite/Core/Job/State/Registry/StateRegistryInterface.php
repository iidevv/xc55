<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\State\Registry;

use XLite\Core\Job\State\JobStateInterface;

interface StateRegistryInterface
{
    /**
     * @param int $id Job id
     *
     * @return JobStateInterface
     */
    public function get($id);

    /**
     * @param int               $id
     * @param JobStateInterface $state
     */
    public function set($id, JobStateInterface $state);

    /**
     * @param int   $id
     * @param       $callback
     *
     * @return mixed
     */
    public function process($id, $callback);
}
