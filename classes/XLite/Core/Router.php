<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Clean URLs Router
 * TODO: Refactor Controllers, CleanURL repo, etc. move routing logic to router
 */
class Router extends \XLite\Base\Singleton
{
    /**
     * @var array
     */
    protected $activeLanguagesCodes = null;


    /**
     * @var bool
     */
    protected $isUseLangUrlsTmp = true;

    /**
     * Temporarily disable language urls
     */
    public function disableLanguageUrlsTmp()
    {
        $this->isUseLangUrlsTmp = false;
    }

    /**
     * Release disabling flag
     */
    public function releaseLanguageUrlsTmp()
    {
        $this->isUseLangUrlsTmp = true;
    }

    /**
     * Process \XLite\Core\Request data
     */
    public function processCleanUrls()
    {
        $request = $this->getRequest();

        if (!empty($request->url)) {
            $this->processCleanUrlLanguage();

            // Remove unnecessary running script name
            $request->url = str_replace(\XLite::getInstance()->getScript(), '', $request->url);

            preg_match(
                '#^((([./\w-]+)/)?([.\w-]+?)/)?([.\w-]+?)(/?)(\.([\w-]+))?$#ui',
                $request->url,
                $matches
            );

            $_GET['rest'] = $matches[3] ?? null;
            $_GET['last'] = $matches[4] ?? null;
            $_GET['url'] = $matches[5] ?? null;
            $_GET['ext'] = $matches[7] ?? null;
            \XLite\Core\Request::getInstance()->mapRequest();
        }
    }

    /**
     * Process \XLite\Core\Request, detect and set language
     */
    public function processCleanUrlLanguage()
    {
        if ($this->isUseLanguageUrls()) {
            $request = $this->getRequest();

            if (
                !empty($request->url)
                && preg_match('#^([a-z]{2})(/|$)#i', $request->url, $matches)
                && in_array($matches[1], $this->getActiveLanguagesCodes(), true)
            ) {
                $request->setLanguageCode($matches[1]);
                $request->url = substr($request->url, 3);
            }
        }
    }

    /**
     * Is use language urls
     *
     * @return bool
     */
    public function isUseLanguageUrls()
    {
        return $this->isUseLangUrlsTmp
               && \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'use_language_url']) == 'Y';
    }

    /**
     * Return array of codes for currently active languages
     *
     * @return array
     */
    public function getActiveLanguagesCodes()
    {
        if ($this->activeLanguagesCodes === null) {
            $result = [];

            foreach (\XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
                $result[] = $language->getCode();
            }

            $this->activeLanguagesCodes = $result;
        }

        return $this->activeLanguagesCodes;
    }

    /**
     * Return request object
     *
     * @return \XLite\Core\Request
     */
    public function getRequest()
    {
        return \XLite\Core\Request::getInstance();
    }
}
