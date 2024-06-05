<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Import;

use XCart\Extender\Mapping\ListChild;

/**
 * Warnings section widget
 *
 * @ListChild (list="import.completed.content", weight="1000", zone="admin")
 */
class Warnings extends \XLite\View\Import\Failed
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/warnings.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getImporter()
            && (
                \XLite\Logic\Import\Importer::hasWarnings()
                || \XLite\Logic\Import\Importer::hasErrors()
            );
    }

    /**
     * Return true if 'Proceed' button should be displayed
     *
     * @return boolean
     */
    protected function isDisplayProceedButton()
    {
        return false;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getTitle()
    {
        return static::t(
            'The script found {{number}} errors during import',
            [
                'number' => \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->countLogs()
            ]
        );
    }
}
