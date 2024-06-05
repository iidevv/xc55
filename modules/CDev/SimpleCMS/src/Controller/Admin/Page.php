<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Controller\Admin;

use XLite\Core\Auth;
use XLite\Model\Role\Permission;

class Page extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @var array
     */
    protected $params = ['target', 'id'];

    /**
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage custom pages');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $id = (int) \XLite\Core\Request::getInstance()->id;
        $model = $id
            ? \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')->find($id)
            : null;

        return ($model && $model->getId())
            ? $model->getName() // static::t('Edit page')
            : static::t('New page');
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            'Content pages',
            $this->buildURL('pages', '', ['page' => 'content'])
        );
    }

    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');
        if (!\XLite\Core\Request::getInstance()->id) {
            $this->setReturnURL(
                $this->buildURL(
                    'page',
                    '',
                    ['id' => $this->getModelForm()->getModelObject()->getId()]
                )
            );
        }
    }

    protected function doActionUpdateAndClose()
    {
        $pageToRedirect = Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS) ? 'content' : 'primary';

        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(
                \XLite\Core\Converter::buildURL('pages', null, ['page' => $pageToRedirect])
            );
        }
    }

    /**
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'CDev\SimpleCMS\View\Model\Page';
    }
}
