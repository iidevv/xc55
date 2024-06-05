<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\Migration;

interface MigrationInterface
{
    public function getCreateMigration(): array;

    public function getUpdateMigration(array $enabledModuleTables, array $disabledModuleTables, array $disabledModuleColumns): array;
}
