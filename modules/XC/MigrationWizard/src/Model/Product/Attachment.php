<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Egoods")
 */
abstract class Attachment extends \CDev\FileAttachments\Model\Product\Attachment
{
    /**
     * Set private scope flag
     *
     * @param boolean $private Scope flag
     */
    public function setPrivate($private)
    {
        if (!isset($this->oldScope)) {
            $this->oldScope = $this->private;
        }

        $this->private = (int) $private;

        /**
         * Disable change scope behavior as MigrationWizard uses the original path
         *
         * @see $this->prepareChangeScope();
         */
    }
}
