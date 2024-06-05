<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\XMLSitemap\Controller\Admin;

use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\EventTask;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use CDev\XMLSitemap\Logic\Sitemap\Generator;

/**
 * Sitemap
 */
class Sitemap extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('SEO Settings');
    }

    /**
     * Get engines
     *
     * @return array
     */
    public function getEngines()
    {
        return [
            'Google'  => [
                'title' => 'Google',
                'url'   => 'https://google.com/webmasters/tools/ping?sitemap=%url%',
            ],
        ];
    }

    /**
     * Check - generation process is not-finished or not
     *
     * @return boolean
     */
    public function isSitemapGenerationNotFinished()
    {
        $eventName = Generator::getEventName();
        $state = Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
        && in_array(
            $state['state'],
            [EventTask::STATE_STANDBY, EventTask::STATE_IN_PROGRESS]
        )
        && !Database::getRepo('XLite\Model\TmpVar')->getVar($this->getSitemapGenerationCancelFlagVarName());
    }

    /**
     * Check - generation process is finished or not
     *
     * @return boolean
     */
    public function isSitemapGenerationFinished()
    {
        return !$this->isSitemapGenerationNotFinished();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getSitemapGenerationCancelFlagVarName()
    {
        return Generator::getSitemapGenerationCancelFlagVarName();
    }

    /**
     * Manually generate sitemap
     *
     * @return void
     */
    protected function doActionGenerate()
    {
        if ($this->isSitemapGenerationFinished()) {
            Generator::run([]);
        }

        $this->setReturnURL(
            $this->buildURL('sitemap')
        );
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionSitemapGenerationCancel()
    {
        Generator::cancel();
        TopMessage::addWarning('Sitemap generation has been stopped.');

        $this->setReturnURL(
            $this->buildURL('sitemap')
        );
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = Request::getInstance();

        if ($request->sitemap_generation_completed) {
            TopMessage::addInfo('Sitemap generation has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('sitemap')
            );
        } elseif ($request->sitemap_generation_failed) {
            TopMessage::addError('Sitemap generation has been stopped.');

            $this->setReturnURL(
                $this->buildURL('sitemap')
            );
        }
    }

    /**
     * Place URL into engine's endpoints
     *
     * @return void
     */
    protected function doActionLocate()
    {
        $engines = Request::getInstance()->engines;

        if ($engines) {
            foreach ($this->getEngines() as $key => $engine) {
                if (in_array($key, $engines)) {
                    $url = urlencode(
                        \XLite::getInstance()->getShopURL(
                            \XLite\Core\Converter::buildURL('sitemap', '', [], \XLite::getCustomerScript())
                        )
                    );
                    $url = str_replace('%url%', $url, $engine['url']);
                    if (\XLite\Core\Operator::checkURLAvailability($url)) {
                        TopMessage::addInfo(
                            'Site map successfully registred on X',
                            ['engine' => $key]
                        );
                    } else {
                        TopMessage::addWarning(
                            'Site map has not been registred in X',
                            ['engine' => $key]
                        );
                    }
                }
            }
        }

        $postedData = Request::getInstance()->getData();
        $options    = Database::getRepo('\XLite\Model\Config')
            ->findBy(['category' => $this->getOptionsCategory()]);
        $isUpdated  = false;

        foreach ($options as $key => $option) {
            $name = $option->getName();
            $type = $option->getType();

            if (isset($postedData[$name]) || $type == 'checkbox') {
                if ($type == 'checkbox') {
                    $option->setValue(isset($postedData[$name]) ? 'Y' : 'N');
                } else {
                    $option->setValue($postedData[$name]);
                }

                $isUpdated = true;
                Database::getEM()->persist($option);
            }
        }

        if ($isUpdated) {
            Database::getEM()->flush();
            Config::updateInstance();
        }
    }

    /**
     * Returns shipping options
     *
     * @return array
     */
    public function getOptions()
    {
        return Database::getRepo('\XLite\Model\Config')
            ->findByCategoryAndVisible($this->getOptionsCategory());
    }


    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'CDev\XMLSitemap';
    }
}
