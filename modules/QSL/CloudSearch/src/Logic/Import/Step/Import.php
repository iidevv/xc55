<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Logic\Import\Step;

use QSL\CloudSearch\Core\IndexingEvent\IndexingEventListener;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Import step
 *
 * @Extender\Mixin
 */
class Import extends \XLite\Logic\Import\Step\Import
{
    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $tmpVar = Database::getRepo('XLite\Model\TmpVar');

        $tmpVar->setVar('csImportStarted', LC_START_TIME);
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        IndexingEventListener::triggerLatestChangesReindex();
    }
}
