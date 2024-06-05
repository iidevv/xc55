<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Base;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Image abstract store
 *
 * TODO: remove once fixed in core https://bt.x-cart.com/view.php?id=47001
 *                                 https://bt.x-cart.com/view.php?id=47064
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Extender\Mixin
 */
abstract class Image extends \XLite\Model\Base\Image
{
    /**
     * Renew properties by path
     *
     * @param string $path Path
     *
     * @return boolean
     */
    protected function renewByPath($path)
    {
        $result = parent::renewByPath($path);

        if ($result && $this->isURL($path)) {
            $hash = \Includes\Utils\FileManager::getHash($path);
            if ($hash) {
                $this->setHash($hash);
            }
        }

        return $result;
    }
}
