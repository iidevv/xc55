<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module\CDev\SimpleCMS\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Model\CleanURL;
use XLite\Model\Repo\CleanURL as CleanURLRepo;
use CDev\SimpleCMS\Model\Page;

/**
 * PrivacyPolicy
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SimpleCMS")
 */
class PrivacyPolicy extends \XC\GDPR\Core\PrivacyPolicy
{
    /**
     * @return bool
     */
    public function isStaticPageAvailable()
    {
        return (bool)$this->getStaticPage();
    }

    /**
     * @return bool
     */
    public function isNeedToCreateStaticPage()
    {
        return !$this->getStaticPage();
    }

    /**
     * @return mixed
     */
    public function getStaticPage()
    {
        return $this->getStaticPageId()
            ? $this->getRepo()->find(
                $this->getStaticPageId()
            )
            : false;
    }

    /**
     * @return Page
     */
    protected function getNewStaticPage()
    {
        $page = new Page();
        $page->setEnabled(true);

        /* @var LanguageLabel $label */
        $label = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->findOneBy(['name' => 'Privacy statement']);

        if ($label) {
            /* @var LanguageLabelTranslation $translation */
            foreach ($label->getTranslations() as $translation) {
                /* @var PageTranslation $pTrans */
                $pTrans = $page->getTranslation($translation->getCode());
                $pTrans->setName($translation->getLabel());
                $pTrans->setBody(static::t(
                    'Privacy policy page text',
                    [],
                    $translation->getCode()
                ));
                $pTrans->setTeaser('');
                $page->addTranslations($pTrans);
            }
        }

        if ($this->isAllowedToCreateCleanURL()) {
            $url = new CleanURL();
            $url->setEntity($page);
            $url->setCleanURL(
                static::DEFAULT_CLEAN_URL . '.' . CleanURLRepo::CLEAN_URL_DEFAULT_EXTENSION
            );

            $page->addCleanURLs($url);
        }

        return $page;
    }

    /**
     * @return Page|null
     * @throws \Exception
     */
    public function createPrivacyPolicyStaticPage()
    {
        if ($this->isNeedToCreateStaticPage()) {
            $page = $this->getNewStaticPage();
            \XLite\Core\Database::getEM()->persist($page);
            \XLite\Core\Database::getEM()->flush();

            if ($page && $page->getId()) {
                $this->updateStaticPageId((int)$page->getId());
            }
        }

        return $this->getStaticPage();
    }

    /**
     * @return null|\CDev\SimpleCMS\Model\Page
     */
    protected function findLastPage()
    {
        return $this->getRepo()
            ? $this->getRepo()->findOneBy([], ['id' => 'DESC'])
            : null;
    }

    /**
     * @return \CDev\SimpleCMS\Model\Repo\Page
     */
    private function getRepo()
    {
        return \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page');
    }

    /**
     * @return mixed
     */
    protected function getStaticPageId()
    {
        return \XLite\Core\Config::getInstance()->XC->GDPR->privacy_policy_static_page_id;
    }

    /**
     * @return bool
     */
    protected function isAllowedToCreateCleanURL()
    {
        return !\XLite\Core\Database::getRepo('XLite\Model\CleanURL')->findOneBy(
            [
                'cleanURL' => static::DEFAULT_CLEAN_URL . '.' . CleanURLRepo::CLEAN_URL_DEFAULT_EXTENSION
            ]
        );
    }

    /**
     * @param integer $id
     *
     * @throws \Exception
     */
    protected function updateStaticPageId($id)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            [
                'category' => 'XC\\GDPR',
                'name'     => 'privacy_policy_static_page_id',
                'value'    => $id,
            ]
        );
    }
}
