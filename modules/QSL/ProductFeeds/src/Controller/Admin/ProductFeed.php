<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Controller\Admin;

/**
 * Controller for the Feed Settings page.
 */
class ProductFeed extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @var array
     */
    protected $params = ['target', 'feed_id'];

    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        $model = $this->getFeedModel();

        return ($model && $model->getId())
            ? static::t('X feed settings', ['name' => $model->getName()])
            : \XLite\Core\Translation::getInstance()->lbl('Feed settings');
    }

    /**
     * Return current feed options.
     *
     * @return array
     */
    public function getOptions()
    {
        $class = $this->getFeedModel()->getGeneratorClass();
        $category = 'QSL\\ProductFeeds' . substr($class, strrpos($class, '\\'));

        return \XLite\Core\Database::getRepo('XLite\Model\Config')
            ->getByCategory($category, true, true);
    }

    /**
     * Get CSS classes for the form containter div.
     *
     * @return string
     */
    public function getPage()
    {
        return 'feed settings-feed-' . $this->getFeedMachineName();
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            'Product feeds',
            $this->buildURL('product_feeds')
        );
    }

    /**
     * Get feed indentifier from the request.
     *
     * @return integer
     */
    protected function getFeedId()
    {
        return (int) \XLite\Core\Request::getInstance()->feed_id;
    }

    /**
     * Get the feed model being configured on the page.
     *
     * @return \QSL\ProductFeeds\Model\ProductFeed|null
     */
    protected function getFeedModel()
    {
        $id = $this->getFeedId();

        return $id
            ? \XLite\Core\Database::getRepo('QSL\ProductFeeds\Model\ProductFeed')->find($id)
            : null;
    }

    /**
     * Update model
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('update')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('product_feeds'));
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'QSL\ProductFeeds\View\Model\Settings\FeedSettings';
    }

    /**
     * Get the feed name without all special characters.
     *
     * @return string
     */
    protected function getFeedMachineName()
    {
        return preg_replace('/\W+/', '-', strtolower($this->getFeedModel()->getName()));
    }
}
