<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Monolog\Handler;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Monolog\Logger;

class CloudWatch extends \Maxbanton\Cwh\Handler\CloudWatch
{
    /**
     * @var bool
     */
    private $isConfigured;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        CloudWatchLogsClient $client,
        $group,
        $stream,
        $retention = 14,
        $batchSize = 10000,
        array $tags = [],
        $level = Logger::DEBUG,
        $bubble = true,
        $createGroup = true
    ) {
        parent::__construct(
            $client,
            $group,
            $stream,
            $retention,
            $batchSize,
            $tags,
            $level,
            $bubble,
            $createGroup
        );

        $client->getCredentials()->then(function ($credentials) {
            $this->isConfigured = empty($credentials->getAccessKeyId()) || empty($credentials->getSecretKey()) ? false : true;
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        if (!$this->isConfigured) {
            return;
        }

        parent::write($record);
    }
}
