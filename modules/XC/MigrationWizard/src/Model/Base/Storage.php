<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Base;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Storage abstract store
 *
 * TODO: remove once fixed in core https://bt.x-cart.com/view.php?id=47001
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Extender\Mixin
 */
abstract class Storage extends \XLite\Model\Base\Storage
{
    /**
     * Return TRUE if copy to filesystem is required
     *
     * @param string $sourceUrl
     *
     * @return boolean
     */
    protected function isCopyToFsRequired($sourceUrl, $result)
    {
        if (
            $result//TODO the bug is still might be there if migration_wizard section is used in config.php
            && ($options = \XLite::getInstance()->getOptions('migration_wizard', 'enable_copy_ext_images'))
            && empty($options['enable_copy_ext_images'])
        ) {
            $siteUrl = \XC\MigrationWizard\Logic\Migration\Wizard::getInstance()
                ->getStep('Connect')->getSiteUrl();

            $siteHost = parse_url($siteUrl, PHP_URL_HOST);
            $sourceHost = parse_url($sourceUrl, PHP_URL_HOST);

            if (!empty($siteUrl) && $siteHost !== $sourceHost) {
                $result = false;
            }
        }

        return $result;
    }

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

        if ($this->isURL($path)) {
            $headers = \XLite\Core\Operator::checkURLAvailability($path);
            $this->setSize(intval($headers->ContentLength));
        }

        return $result;
    }

    /**
     * Load from URL
     *
     * @param string  $url     URL
     * @param boolean $copy2fs Copy file to file system or not OPTIONAL
     *
     * @return boolean
     */
    public function loadFromURL($url, $copy2fs = false)
    {
        $remoteResource = $this->prepareURL($url);

        if ($remoteResource) {
            // The Only 1 Line Below Was Originaly The Difference Between The Method And The Parent
            $copy2fs = $this->isCopyToFsRequired($url, $copy2fs);
            // It Is Recommended To Call Return Parent::LOaDFromURL Here In The Future Refactoring

            if ($copy2fs) {
                $result = $this->copyFromURL($remoteResource);
            } else {
                $name = $remoteResource->getName();
                $savedPath = $this->getPath();
                $this->setPath($remoteResource->getURL());
                $this->setFileName($name);

                $result = $this->renew();

                if ($result) {
                    $this->removeFile($savedPath);
                    $this->setStorageType(static::STORAGE_URL);
                }
            }

            return $result;
        }

        return false;
    }

    /**
     * Check if file exists
     *
     * @param string  $path      Path to check OPTIONAL
     * @param boolean $forceFile Flag OPTIONAL
     *
     * @return boolean
     */
    public function isFileExists($path = null, $forceFile = false)
    {
        if ($this->isURL($path) && !$forceFile) {
            $exists = (bool) \XLite\Core\Operator::checkURLAvailability($path ?: $this->getPath());
        } else {
            $exists = \Includes\Utils\FileManager::isFileReadable($path ?: $this->getStoragePath());
        }

        return $exists;
    }
}
