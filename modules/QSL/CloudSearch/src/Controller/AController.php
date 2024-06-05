<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Controller;

use QSL\CloudSearch\Core\IndexingEvent\IndexingEventProfiler;
use QSL\CloudSearch\Core\RegistrationScheduler;
use QSL\CloudSearch\Core\ServiceApiClient;
use QSL\CloudSearch\Main;
use XCart\Extender\Mapping\Extender;
use XLite;

/**
 * Abstract controller
 *
 * @Extender\Mixin
 */
abstract class AController extends \XLite\Controller\AController
{
    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        if (
            $this->getTarget() !== 'cloud_search_api'
            && !XLite::isCacheBuilding()
        ) {
            $scheduler = RegistrationScheduler::getInstance();

            $apiClient = new ServiceApiClient();

            if ($scheduler->isScheduled()) {
                // Registration scheduled after running install.php and after cache rebuild
                if (XLite::isAdminZone() || !Main::isConfigured()) {
                    $apiClient->register();

                    $scheduler->unschedule();
                }
            }
        }

        parent::handleRequest();
    }

    public function __destruct()
    {
        IndexingEventProfiler::getInstance()->log();
    }
}
