<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Converter;

/**
 * News message controller
 */
class NewsMessage extends \XLite\Controller\Admin\AAdmin
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
        return parent::checkACL() || Auth::getInstance()->isPermissionAllowed('manage news');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $id = intval(\XLite\Core\Request::getInstance()->id);
        $model = $id
            ? \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')->find($id)
            : null;

        return ($model && $model->getId())
            ? $model->getName()
            : static::t('Create news message');
    }

    protected function getUpdateActionReturnPage(): string
    {
        return Converter::buildURL('news_messages');
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl($this->getUpdateActionReturnPage());
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XC\News\View\Model\NewsMessage';
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            'News messages',
            $this->buildURL(
                'pages',
                '',
                [
                    'page' => 'news_messages'
                ]
            )
        );
    }
}
