<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Controller\Admin;

/**
 * Canada Post shipmet tracking controller
 */
class CapostTracking extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'shipment_id'];

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params (OPTIONAL)
     *
     * @return void
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        // Remove all expired tracking info
        \XLite\Core\Database::getRepo('XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking')->removeExpired();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Tracking details');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return (
            (
                parent::checkACL()
                || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders')
            )
            && $this->getShipment()
        );
    }

    /**
     * Get shipment data
     *
     * @return \XC\CanadaPost\Model\Order\Parcel\Shipment|null
     */
    public function getShipment()
    {
        $shipmentId = intval(\XLite\Core\Request::getInstance()->shipment_id);

        return \XLite\Core\Database::getRepo('XC\CanadaPost\Model\Order\Parcel\Shipment')->find($shipmentId);
    }

    /**
     * Get tracking details
     *
     * @return \XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking|null
     */
    public function getTrackingDetails()
    {
        $trackingDetails = null;

        if ($this->getShipment()) {
            $trackingDetails = $this->getShipment()->getTrackingDetails();
        }

        return $trackingDetails;
    }
}
