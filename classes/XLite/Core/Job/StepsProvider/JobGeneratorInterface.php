<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\StepsProvider;

use XLite\Core\Job\Job;

/**
 * Class JobGeneratorInterface
 */
interface JobGeneratorInterface
{
    /**
     * @return Job
     */
    public function getNextJob();

    /**
     * @return boolean
     */
    public function isValid();
}
