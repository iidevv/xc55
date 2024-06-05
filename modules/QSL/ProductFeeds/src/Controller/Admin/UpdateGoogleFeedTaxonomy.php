<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Controller\Admin;

/**
 * Controller for the Update Google Taxonomy feature.
 */
class UpdateGoogleFeedTaxonomy extends \XLite\Controller\Admin\AAdmin
{
    public const UPDATE_STATUS_NOT_CHANGED = 0;
    public const UPDATE_STATUS_UPDATED     = 1;
    public const UPDATE_STATUS_ERROR       = -1;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Update feed categories';
    }

    /**
     * Do the Update action.
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $result = $this->updateGoogleTaxonomy();

        if ($result === static::UPDATE_STATUS_NOT_CHANGED) {
            \XLite\Core\TopMessage::addInfo(
                static::t('The Google taxonomy is up to date already')
            );
        } elseif ($result === static::UPDATE_STATUS_ERROR) {
            \XLite\Core\TopMessage::addError(
                static::t('Failed to retrieve the new taxonomy from Gooogle servers')
            );
        } else {
            \XLite\Core\TopMessage::addInfo(
                static::t('The Google taxonomy has been updated')
            );
        }

        // $this->setReturnURL($this->buildURL('product_feeds', '', array()));

        \XLite\Core\Database::getEM()->flush();

        $engine = \XLite\Core\Database::getRepo('QSL\ProductFeeds\Model\ProductFeed')
            ->findOneByGeneratorClass('QSL\ProductFeeds\Logic\FeedGenerator\GoogleShopping');
        if ($engine) {
            $this->redirect(
                $this->buildUrl(
                    'product_feed',
                    '',
                    ['feed_id' => $engine->getFeedId()]
                )
            );
        }
    }

    protected function updateGoogleTaxonomy()
    {
        $updated = static::UPDATE_STATUS_NOT_CHANGED;

        [$taxonomyDate, $taxonomy] = $this->retrieveGoogleTaxonomy();

        if (!$taxonomyDate || !is_array($taxonomy) || empty($taxonomy)) {
            $updated = static::UPDATE_STATUS_ERROR;
        } elseif ($taxonomyDate > $this->getCurrentTaxonomyDate()) {
            $repo = \XLite\Core\Database::getRepo('QSL\ProductFeeds\Model\GoogleShoppingCategory');

            $maxId = 0;
            foreach ($repo->findAll() as $category) {
                $name = $category->getName();
                $key = array_search($name, $taxonomy);
                if ($key === false) {
                    // The category is deprecated now
                    $category->setName('[DEPRECATED!] ' . str_replace('[DEPRECATED!] ', '', $name));
                    $category->setDeprecated(true);
                } else {
                    // The category is still in the taxonomy
                    unset($taxonomy[$key]);
                }
                if ($category->getGoogleId() > $maxId) {
                    // Update the max ID number for using it for new categories later
                    $maxId = $category->getGoogleId();
                }
            }

            // Since we have removed all existing categories from the array, $taxonomy includes only new categories now
            foreach ($taxonomy as $name) {
                if (trim($name)) {
                    $category = new \QSL\ProductFeeds\Model\GoogleShoppingCategory();
                    $category->setName($name);
                    $category->setGoogleId(++$maxId);
                    \XLite\Core\Database::getEM()->persist($category);
                }
            }

            // Now we update the taxonomy date to the current date
            $this->updateTaxonomyDate(time());

            $updated = static::UPDATE_STATUS_UPDATED;
        } else {
            // No changes are needed, we just update the taxonomy date
            $this->updateTaxonomyDate(time());
        }

        return $updated;
    }

    protected function getCurrentTaxonomyDate()
    {
        return (int) \XLite\Core\Config::getInstance()->QSL->ProductFeeds->googleshop_taxonomy_version;
    }

    protected function updateTaxonomyDate($taxonomyDate)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            [
                'category' => 'QSL\ProductFeeds\GoogleShopping',
                'name'     => 'googleshop_taxonomy_version',
                'value'    => (int) $taxonomyDate,
            ]
        );
    }

    protected function getGoogleTaxonomyUrl()
    {
        return 'http://www.google.com/basepages/producttype/taxonomy.en-US.txt';
    }

    protected function retrieveGoogleTaxonomyRaw()
    {
        try {
            return file_get_contents($this->getGoogleTaxonomyUrl());
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function retrieveGoogleTaxonomy()
    {
        $date = false;
        $categories = [];

        $raw = $this->retrieveGoogleTaxonomyRaw();
        if ($raw) {
            $match = [];
            foreach (preg_split("/\r\n|\n|\r/", $raw) as $line) {
                if (!$date && preg_match('/^# Google_Product_Taxonomy_Version: (\d{4})-(\d{2})-(\d{2})$/', $line, $match)) {
                    $date = mktime(0, 0, 0, $match[2], $match[3], $match[1]);
                } else {
                    $categories[] = $line;
                }
            }
        }

        return [$date, $categories];
    }
}
