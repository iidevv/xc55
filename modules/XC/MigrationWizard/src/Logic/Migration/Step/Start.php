<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

/**
 * Migration Logic - Start
 */
class Start extends \XC\MigrationWizard\Logic\Migration\Step\AStep
{
    /**
     * Return step line title
     *
     * @return string
     */
    public static function getLineTitle()
    {
        return 'Step-Start';
    }

    /**
     * Get Settings To Be Changed Before Migration
     */
    public function getIncompatibleSettings(): array
    {
        return [];// TODO checkaim

        if (
            ($options = \XLite::getInstance()->getOptions('log_details', 'suppress_errors'))
            && isset($options['suppress_errors'])
            && empty($options['suppress_errors'])
        ) {
            $result[] = \XLite\Core\Translation::lbl('Please make sure you have "suppress_errors = On" in the [log_details] section in your config.php file.');
        }

        return $result;
    }
}
