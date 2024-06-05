<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\UserPermissions\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Role extends \XLite\Model\Role
{
    /**
     * Enabled
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Check - specified permission is allowed or not
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public function isPermissionAllowed($code)
    {
        return $this->getEnabled() && parent::isPermissionAllowed($code);
    }

    /**
     * Check - role is permanent (unremovable and foreave enable) or not
     *
     * @return boolean
     */
    public function isPermanentRole()
    {
        return $this->getId() == $this->getRepository()->getPermanentRole()->getId();
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Role
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get public name
     *
     * @return string
     */
    public function getPublicName()
    {
        return parent::getPublicName() . (!$this->getEnabled() ? ' ' . \XLite\Core\Translation::getInstance()->translate('(disabled role)') : '');
    }
}
