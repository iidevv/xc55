<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Controller\Customer;

/**
 * News message controller
 */
class NewsMessage extends \XLite\Controller\Customer\ACustomer
{
    /**
     * News message
     *
     * @var \XC\News\Model\NewsMessage
     */
    protected $newsMessage;

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->params[] = 'id';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $model = $this->getNewsMessage();

        $result = '';

        if ($model && $this->isVisible()) {
            $result = $model->getName();
        } elseif ($this->isVisible()) {
            $result = parent::getTitle();
        }

        return $result;
    }

    /**
     * Returns the page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        $model = $this->getNewsMessage();

        if ($model && $this->isVisible()) {
            $result = $model->getMetaTitle() ?: $model->getName();
        } else {
            $result = parent::getTitle();
        }

        return $result;
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $model = $this->getNewsMessage();

        return $model && $model->getMetaDesc() ? $model->getMetaDesc() : parent::getMetaDescription();
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        $model = $this->getNewsMessage();

        return $model ? $model->getMetaTags() : parent::getKeywords();
    }

    /**
     * Return news message ID
     *
     * @return integer
     */
    public function getNewsMessage()
    {
        if (!isset($this->newsMessage)) {
            $this->newsMessage = $this->defineNewsMessage();
        }

        return $this->newsMessage;
    }

    /**
     * Return URL previous news message
     *
     * @param \XC\News\Model\NewsMessage $news News message
     *
     * @return string
     */
    public function getPreviousURL(\XC\News\Model\NewsMessage $news)
    {
        [$previous,] = \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')
            ->findSiblingsByNews($news);

        return $previous ? static::buildURL('news_message', null, ['id' => $previous->getId()]) : null;
    }

    /**
    * Return URL next news message
    *
    * @param \XC\News\Model\NewsMessage $news News message
    *
    * @return string
    */
    public function getNextURL(\XC\News\Model\NewsMessage $news)
    {
        [, $next] = \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')
            ->findSiblingsByNews($news);

        return $next ? static::buildURL('news_message', null, ['id' => $next->getId()]) : null;
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            'News',
            static::buildURL('news_messages')
        );
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
     * Define news message
     *
     * @return \XC\News\Model\NewsMessage
     */
    protected function defineNewsMessage()
    {
        $model = \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')
            ->find(\XLite\Core\Request::getInstance()->id);

        return $model;
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $newsMessage = $this->getNewsMessage();

        return parent::isVisible()
            && $newsMessage
            && (empty($newsMessage) || $newsMessage->isEnabled());
    }
}
