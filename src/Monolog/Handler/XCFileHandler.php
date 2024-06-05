<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Monolog\Handler;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use function date;

class XCFileHandler extends StreamHandler
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $file;

    /**
     * @var array
     */
    private $streams = [];

    public function __construct(string $path = '', string $file = '', int $level = Logger::DEBUG)
    {
        $this->path = $path;
        $this->file = $file;
        parent::__construct($this->path, $level, true, 0644, false);
    }

    protected function write(array $record): void
    {
        if (empty($this->file)) {
            return;
        }

        $streamName = !empty($record['extra']['stream']) ? $record['extra']['stream'] : $this->file;

        $this->setStreamName($streamName);

        parent::write($record);

        $this->streams[$streamName]['url']    = $this->url;
        $this->streams[$streamName]['stream'] = $this->stream;
    }

    private function createFilePath(string $streamName): string
    {
        return $this->path . '/' . date('Y/m') . '/' . $streamName . '.' . date('Y-m-d') . '.log';
    }

    private function setStreamName($streamName): void
    {
        $url = $this->createFilePath($streamName);

        if (isset($this->streams[$streamName]['url']) && $this->streams[$streamName]['url'] === $url) {
            $this->url    = $this->streams[$streamName]['url'];
            $this->stream = $this->streams[$streamName]['stream'] ?? null;
        } else {
            $this->url    = $url;
            $this->stream = null;
        }
    }
}
