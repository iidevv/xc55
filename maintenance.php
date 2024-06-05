<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function getMaintenancePageContent(string $message, int $http_code = 500): string
{
    $maintenancePage = file_get_contents(__DIR__ . '/public/maintenance.html');
    $maintenancePage = str_replace('@MSG@', $message, $maintenancePage);

    return $maintenancePage;
}
