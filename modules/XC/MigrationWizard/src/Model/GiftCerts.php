<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use XCart\Extender\Mapping\Extender;

/**
 * GiftCerts Module
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 * @Extender\Mixin
 * @Extender\Depend ("RedSqui\GiftCertificates")
 */
class GiftCerts extends \RedSqui\GiftCertificates\Model\GiftCerts
{
    /**
     * Don'T Do Default Assigments From Parent Module
     *
     * @param string $type Type of current operation
     *
     * @return void
     */
    public function prepareEntityBeforeCommit($type)
    {
        static $is_migration = null;
        if (
            is_null($is_migration)
            && class_exists('\XC\MigrationWizard\Logic\Migration\Wizard')
            && ($step = \XC\MigrationWizard\Logic\Migration\Wizard::getInstance()->getStep('TransferData'))
            && $step instanceof \XC\MigrationWizard\Logic\Migration\Step
        ) {
            $is_migration = $step->isImportRunning();
        }

        $res = null;

        if ($is_migration) {
            // Do Nothing
            // Don'T Do Default Assigments From Parent Module
        } else {
            $res = parent::prepareEntityBeforeCommit($type);
        }

        return $res;
    }
}
