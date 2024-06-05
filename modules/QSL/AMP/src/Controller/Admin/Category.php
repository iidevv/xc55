<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\Router;
use XLite\Core\Session;
use XLite\Core\TopMessage;
use QSL\AMP\Core\CacheUpdater;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\Controller\Admin\Category
{
    /**
     * doActionAdd
     *
     * @return void
     */
    protected function doActionUpdateGoogleAmpCache()
    {
        $request = Request::getInstance();

        $cacheUpdater = new CacheUpdater();

        if ($request->id) {
            $target = 'category';

            $params = ['category_id' => $request->id];

            $returnUrl = $this->buildURL('category', '', ['id' => $request->id]);
        } else {
            $target = '';

            $params = [];

            $returnUrl = $this->buildURL('front_page');
        }

        $pageUrl = $this->getAmpUrl($target, $params);

        $cacheUpdater->updateUrl($pageUrl);

        $languageSpecific = $this->getAllLanguageSpecificAmpUrls($target, $params);

        foreach ($languageSpecific as $code => $url) {
            $cacheUpdater->updateUrl($url);
        }

        if ($languageSpecific) {
            $message = static::t(
                'Google AMP cache update request sent (including localized)',
                [
                    'url'       => htmlspecialchars($pageUrl),
                    'localized' => implode(', ', array_keys($languageSpecific)),
                ]
            );
        } else {
            $message = static::t('Google AMP cache update request sent ', ['url' => htmlspecialchars($pageUrl)]);
        }

        TopMessage::addInfo($message);

        $this->setReturnURL($returnUrl);
    }

    /**
     * Get AMP version of the URL
     *
     * @param $target
     * @param $params
     *
     * @return string
     */
    protected function getAmpUrl($target, $params)
    {
        return Converter::buildFullURL(
            $target,
            '',
            $params + ['amp' => '1'],
            XLite::getCustomerScript(),
            null,
            true
        );
    }

    /**
     * Get AMP url for the specific language code
     *
     * @param $target
     * @param $params
     * @param $language
     *
     * @return string
     */
    protected function getLanguageSpecificAmpUrl($target, $params, $language)
    {
        $restoreLang = Session::getInstance()->getCurrentLanguage();

        Session::getInstance()->setLanguage($language);

        $url = $this->getAmpUrl($target, $params);

        Session::getInstance()->setLanguage($restoreLang);

        return $url;
    }

    /**
     * Get AMP url for the specific language code
     *
     * @param $target
     * @param $params
     *
     * @return array
     */
    protected function getAllLanguageSpecificAmpUrls($target, $params)
    {
        $urls = [];

        if (LC_USE_CLEAN_URLS && Router::getInstance()->isUseLanguageUrls()) {
            /** @var XLite\Model\Language $language */
            foreach (Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
                if (!$language->getDefaultAuth()) {
                    $urls[$language->getCode()] = $this->getLanguageSpecificAmpUrl($target, $params, $language->getCode());
                }
            }
        }

        return $urls;
    }
}
