<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Messenger\EventListener;

/**
 * == Leaking service-tool details? ==
 */
final class RebuildFlag
{
    public const REBUILD_FLAG_FILE_NAME = '.rebuildInProgress';

    private string $rebuildFlagPath;

    public function __construct()
    {
        $this->rebuildFlagPath = LC_DIR_VAR . self::REBUILD_FLAG_FILE_NAME;
    }

    /**
     * @throws RebuildInProgressException
     */
    public function check(): void
    {
        if (file_exists($this->rebuildFlagPath)) {
            throw new RebuildInProgressException();
        }
    }
}
