<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Model\Base;

use XCart\Extender\Mapping\Extender;
use XC\GoogleFeed\Main;

/**
 * Storage abstract store
 * @Extender\Mixin
 */
abstract class Storage extends \XLite\Model\Base\Storage
{
    /**
     * Get URL
     *
     * @return string
     */
    public function getGoogleFeedURL()
    {
        $url = null;

        if ($this->isURL()) {
            $url = $this->getPath();
        } elseif ($this->getStorageType() == static::STORAGE_RELATIVE) {
            $url = Main::getShopURL(
                $this->getWebRoot() . $this->convertPathToURL($this->getPath())
            );
        } else {
            $root = $this->getFileSystemRoot();
            if (strncmp($root, $this->getPath(), strlen($root)) === 0) {
                $path = substr($this->getPath(), strlen($root));
                $url = Main::getShopURL(
                    $this->getWebRoot() . $this->convertPathToURL($path)
                );
            } else {
                $url = $this->getGetterURL();
            }
        }

        return \XLite\Core\Converter::makeURLValid($url);
    }
}
