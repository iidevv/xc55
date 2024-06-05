<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\UserPermissions\Controller\Admin;

/**
 * Role
 */
class Role extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $param = ['target', 'id'];

    /**
     * Role id
     *
     * @var integer
     */
    protected $id;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $model = $this->getModelForm()->getModelObject();

        return ($model && $model->getId())
            ? $model->getPublicName()
            : static::t('Role');
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        $this->addLocationNode(
            'Roles',
            \XLite\Core\Converter::buildUrl('roles')
        );
    }

    /**
     * Update coupon
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');

        if ($this->getModelForm()->getModelObject()->getId()) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('roles'));
        }
    }

    /**
    * Get model form class
    *
    * @return string
    */
    protected function getModelFormClass()
    {
        return 'CDev\UserPermissions\View\Model\Role';
    }
}
