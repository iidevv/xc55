<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Controller\Admin;

/**
 * Products return controller
 */
class CapostReturn extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'id'];

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage returns');
    }

    /**
     * Return the current products_return title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Return') . ' ' . $this->getProductsReturn()->getNumber();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && $this->getProductsReturn();
    }

    /**
     * Get products return ID
     *
     * @return integer
     */
    protected function getProductsReturnId()
    {
        return intval(\XLite\Core\Request::getInstance()->id);
    }

    // {{{ Actions

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        \XLite\Core\Database::getRepo('XC\CanadaPost\Model\ProductsReturn')
            ->updateById($this->getProductsReturnId(), $this->getPostedData());
    }

    /**
     * Return requested changes for the order
     *
     * @param \XC\CanadaPost\Model\ProductsReturn $return Canada Post products return model
     * @param array                                            $data  Data to change
     *
     * @return array
     */
    protected function getProductsReturnChanges(\XC\CanadaPost\Model\ProductsReturn $return, array $data)
    {
        $changes = [];

        foreach ($data as $name => $value) {
            if ($name === 'status') {
                continue;
            }

            $returnValue = $return->{'get' . ucfirst($name)}();

            if ($returnrValue !== $value) {
                $changes[$name] = [
                    'old' => $returnValue,
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }

    // }}}

    // {{{ Common methods

    /**
     * Get products return model
     *
     * @return \XC\CanadaPost\Model\ProductsReturn|null
     */
    public function getProductsReturn()
    {
        return \XLite\Core\Database::getRepo('\XC\CanadaPost\Model\ProductsReturn')
            ->find($this->getProductsReturnId());
    }

    /**
     * Get related order model
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getProductsReturn()->getOrder();
    }

    // }}}
}
