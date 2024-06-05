<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Core\EventListener;

use XCart\Extender\Mapping\Extender;

/**
 * Export
 * @Extender\Mixin
 */
class Export extends \XLite\Core\EventListener\Export
{
    public const CHUNK_LENGTH = 100;

    /**
     * @inheritdoc
     */
    protected function initializeStep()
    {
        set_time_limit(0);

        parent::initializeStep();
    }

    /**
     * @inheritdoc
     */
    protected function finishStep()
    {
        parent::finishStep();

        $this->getItems()->getStep()->closeXLSWriter();
    }
}
