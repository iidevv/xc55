<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;

use XLite\InjectLoggerTrait;

/**
 * Class TestJob
 * TODO remove me
 */
class TestJob extends JobAbstract
{
    use InjectLoggerTrait;

    /**
     * @var
     */
    private $msg;

    public function __construct($msg)
    {
        parent::__construct();

        $this->msg = $msg;
    }

    public function handle()
    {
        $this->markAsStarted();
        var_dump('test from testJob| ' . $this->msg);

        $this->getLogger('test_job')->debug($this->msg);
        usleep(100 * 1000);

        $this->markAsFinished();
    }
}
