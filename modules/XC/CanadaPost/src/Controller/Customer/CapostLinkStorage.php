<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Controller\Customer;

/**
 * Storage
 */
class CapostLinkStorage extends \XLite\Controller\Customer\Storage
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest()
    {
        $requestData = \XLite\Core\Request::getInstance()->getData();
        if (
            !$this->getStorage()
            && isset($requestData['returnUrl'])
            && \XLite\Core\Auth::getInstance()->isAdmin()
        ) {
            $this->redirect($requestData['returnUrl']);
        }

        parent::handleRequest();
    }

    /**
     * Get storage
     *
     * @return \XLite\Model\Base\Storage|null
     */
    protected function getStorage()
    {
        if ($this->getCapostLinkId()) {
            // Get storage by a link
            $this->storage = $this->getCapostStorageByLink();
        } else {
            $this->storage = parent::getStorage();
        }

        if (
            isset($this->storage)
            && !$this->checkCapostStorageAccess($this->storage)
        ) {
            // Unauthorized request
            $this->storage = null;
        }

        return $this->storage;
    }

    /**
     * Get storage by a link
     *
     * @return \XLite\Model\Base\Storage|\XC\CanadaPost\Model\Base\Link|null
     */
    protected function getCapostStorageByLink()
    {
        if (
            !isset($this->storage)
            || !is_object($this->storage)
            || !($this->storage instanceof \XLite\Model\Base\Storage)
        ) {
            $class = \XLite\Core\Request::getInstance()->storage;

            if (class_exists($class)) {
                $link = \XLite\Core\Database::getRepo($class)->find($this->getCapostLinkId());

                if (isset($link)) {
                    $this->storage = $link->getStorage();

                    if (
                        !isset($this->storage)
                        && $link->callApiGetArtifact(true)
                    ) {
                        // Download artifact
                        $this->storage = $link->getStorage();

                        if (!$this->storage->isFileExists()) {
                            $this->storage = null;
                        }
                    }
                }
            }
        }

        return $this->storage;
    }

    /**
     * Get Canada Post link ID
     *
     * @return mixed
     */
    protected function getCapostLinkId()
    {
        return \XLite\Core\Request::getInstance()->linkId;
    }

    /**
     * Check - is user allowed to get file from storage or not
     *
     * @param \XLite\Model\Base\Storage $storage File model
     *
     * @return boolean
     */
    protected function checkCapostStorageAccess(\XLite\Model\Base\Storage $storage)
    {
        $result = true;

        if (
            $storage instanceof \XC\CanadaPost\Model\Order\Parcel\Shipment\Link\Storage
            || $storage instanceof \XC\CanadaPost\Model\Order\Parcel\Manifest\Link\Storage
        ) {
            // Protect shipment documents from unauthorized requests
            if (!\XLite\Core\Auth::getInstance()->isAdmin()) {
                $result = false;
            }
        }

        if (
            $storage instanceof \XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File
            && !$this->checkCapostTrackingFileAccess($storage)
        ) {
            // Protect tracking details documents from unauthorized requests
            $result = false;
        }

        return $result;
    }

    /**
     * Check - is tracking file allowed for a user
     *
     * @param \XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File $storage File object
     *
     * @return boolean
     */
    protected function checkCapostTrackingFileAccess(\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File $storage)
    {
        $result = false;

        if (
            \XLite\Core\Auth::getInstance()->isLogged()
            && (
                \XLite\Core\Auth::getInstance()->isAdmin()
                || (
                    \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()
                        == $storage->getTrackingDetails()->getShipment()->getParcel()->getOrder()->getOrigProfile()->getProfileId()
                )
            )
        ) {
            $result = true;
        }

        return $result;
    }
}
