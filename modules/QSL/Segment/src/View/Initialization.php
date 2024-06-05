<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\View;

use XCart\Extender\Mapping\ListChild;
use XLite\InjectLoggerTrait;

/**
 * Initialization
 *
 * @ListChild (list="head", zone="customer", weight="10")
 */
class Initialization extends \XLite\View\AView
{
    use InjectLoggerTrait;

    /**
     * Messages-as-JSON
     *
     * @var string
     */
    protected $messages;

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/Segment/head.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getWriteKey();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * Get write key
     *
     * @return string
     */
    protected function getWriteKey()
    {
        return \XLite\Core\Config::getInstance()->QSL->Segment->write_key;
    }

    /**
     * Get mediator
     *
     * @return \QSL\Segment\Core\Mediator
     */
    protected function getMediator()
    {
        return \QSL\Segment\Core\Mediator::getInstance();
    }

    /**
     * Get 'page' arguments
     *
     * @return array
     */
    protected function getPageArguments()
    {
        return [
            $this->getPageTitle(),
            [
                'Page Name' => $this->getPageTitle(),
            ],
        ];
    }

    /**
     * Get messages
     *
     * @return array
     */
    protected function getMessages()
    {
        if (!isset($this->messages)) {
            $this->messages = $this->getMediator()->getMessages();

            if ($this->messages) {
                $this->getLogger('QSL-Segment')->debug('Translate messages to JSON', [
                    'messages' => $this->messages
                ]);
            }

            $this->messages = array_merge(
                [['type' => 'page', 'arguments' => $this->getPageArguments()]],
                $this->messages
            );

            $this->messages = array_values($this->messages);
        }

        return $this->messages;
    }

    /**
     * Get page category
     *
     * @return null|string
     */
    protected function getPageCategory()
    {
        $category = null;
        $controller = \XLite::getController();

        if ($controller instanceof \XLite\Controller\Customer\Product) {
            $category = static::t('Product');
        } elseif (
            $controller instanceof \XLite\Controller\Customer\Category
            && !($controller instanceof \XLite\Controller\Customer\Main)
        ) {
            $category = static::t('Category');
        } elseif (
            $controller instanceof \XLite\Controller\Customer\AccessDenied
            || $controller instanceof \XLite\Controller\Customer\PageNotFound
        ) {
            $category = static::t('Error');
        }

        return $category;
    }

    /**
     * Get page title
     *
     * @return string
     */
    protected function getPageTitle()
    {
        $controller = \XLite::getController();
        if ($controller instanceof \XLite\Controller\Customer\Checkout) {
            $title = static::t('Checkout');
        } elseif ($controller instanceof \XLite\Controller\Customer\Cart) {
            $title = static::t('Cart');
        } elseif ($controller instanceof \XLite\Controller\Customer\CheckoutSuccess) {
            $title = static::t('Order completed');
        } elseif ($controller instanceof \XLite\Controller\Customer\Search) {
            $title = static::t('Products search');
        } else {
            $title = \XLite::getController()->getTitle();
        }

        return $title;
    }

    /**
     * Check - debug mode or not
     *
     * @return boolean
     */
    protected function isDebug()
    {
        return LC_DEVELOPER_MODE;
    }

    /**
     * Get settings
     *
     * @return array
     */
    protected function getSettings()
    {
        $allowed = [];
        foreach (\XLite\Core\Config::getInstance()->QSL->Segment as $name => $value) {
            if (strpos($name, 'event_') === 0 && $value) {
                $allowed[] = substr($name, 6);
            }
        }

        return [
            'writeKey'      => $this->getWriteKey(),
            'messages'      => $this->getMessages(),
            'debug'         => (bool)\XLite\Core\Config::getInstance()->QSL->Segment->debug,
            'debugLog'      => $this->isDebug(),
            'ready'         => true,
            'allowed'       => $allowed,
            'context'       => $this->getMediator()->getOptionsBlock(),
        ];
    }

    /**
     * Get settings as JSON
     *
     * @return string
     */
    protected function getSettingsAsJSON()
    {
        return json_encode($this->getSettings());
    }
}
