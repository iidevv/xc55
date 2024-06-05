<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract catalog model
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Catalog extends \XLite\Model\Base\I18n
{
    public const CLEAN_URL_HISTORY_LENGTH = 10;

    /**
     * WEB LC root postprocessing constant
     */
    public const WEB_LC_ROOT = '{{WEB_LC_ROOT}}';

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $cleanURLs;

    /**
     * The main procedure to generate clean URL
     *
     * @return string
     */
    public function generateCleanURL()
    {
        /** @var \XLite\Model\Repo\CleanURL $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');

        return $repo->generateCleanURL($this);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->cleanURLs = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Set clean urls
     *
     * @param \Doctrine\Common\Collections\Collection|string $cleanURLs
     *
     * @return void
     */
    public function setCleanURLs($cleanURLs)
    {
        if (is_string($cleanURLs)) {
            if ($cleanURLs) {
                $this->setCleanURL($cleanURLs);
            }
        } else {
            $this->cleanURLs = $cleanURLs;
        }
    }

    /**
     * Set clean URL
     *
     * @param string  $cleanURL Clean url
     * @param boolean $force    Allow non unique URL OPTIONAL
     *
     * @return void
     */
    public function setCleanURL($cleanURL, $force = false)
    {
        if ($this->getCleanURL()) {
            $cu = $this->getCleanUrls()->last();
            if ($cu && !$cu->isPersistent()) {
                $this->getCleanUrls()->removeElement($cu);
                \XLite\Core\Database::getEM()->remove($cu);
            }
        }

        if ($cleanURL && $this->getCleanURL() !== $cleanURL) {
            /** @var \XLite\Model\Repo\CleanURL $repo */
            $repo = \Xlite\Core\Database::getRepo('\XLite\Model\CleanURL');

            $cleanURLObject = new \XLite\Model\CleanURL();

            $cleanURLObject->setEntity($this);
            $cleanURLObject->setCleanURL($cleanURL);


            if ($force || $repo->isURLUnique($cleanURL, $this)) {
                \XLite\Core\Database::getEM()->persist($cleanURLObject);

                /** @var \Doctrine\Common\Collections\Collection $cleanURLs */
                $cleanURLs = $this->getCleanURLs();
                $cleanURLs->add($cleanURLObject);
            }

            $this->filterCleanURLDuplicates();
            $this->filterCleanURLHistoryLength();
        }
    }

    /**
     * Get clean URL
     *
     * @return string
     */
    public function getCleanURL()
    {
        /** @var \Doctrine\Common\Collections\Collection $cleanURLs */
        $cleanURLs = $this->getCleanURLs();

        return $cleanURLs && $cleanURLs->count()
            ? $cleanURLs->last()->getCleanURL()
            : '';
    }

    /**
     * Lifecycle callback
     *
     * @return void
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prepareBeforeSave()
    {
        //TODO PreUpdate doesn't help here http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preupdate
        if (\XLite\Core\Converter::isEmptyString($this->getCleanURL())) {
            $this->setCleanURL($this->generateCleanURL());
        }
    }

    /**
     * Remove duplicates from clean url history
     *
     * @return void
     */
    protected function filterCleanURLDuplicates()
    {
        $cleanURLs = [];
        foreach (array_reverse($this->getCleanURLs()->toArray()) as $cleanURLObject) {
            if (in_array($cleanURLObject->getCleanURL(), $cleanURLs)) {
                \XLite\Core\Database::getEM()->remove($cleanURLObject);
                $this->getCleanURLs()->removeElement($cleanURLObject);
            } else {
                $cleanURLs[] = $cleanURLObject->getCleanURL();
            }
        }
    }

    /**
     * Cut clean url history
     *
     * @return void
     */
    protected function filterCleanURLHistoryLength()
    {
        $count = 0;
        foreach (array_reverse($this->getCleanURLs()->toArray()) as $cleanURLObject) {
            if ($count++ >= static::CLEAN_URL_HISTORY_LENGTH) {
                $this->getCleanURLs()->removeElement($cleanURLObject);
                \XLite\Core\Database::getEM()->remove($cleanURLObject);
            }
        }
    }

    // {{{ Preprocessing text values methods

    /**
     * Get processed value
     *
     * @param string $value Value to process
     *
     * @return string
     */
    public static function getPreprocessedValue($value)
    {
        return str_replace(
            static::getWebPreprocessingTags(),
            static::getWebPreprocessingURL(),
            $value
        );
    }

    /**
     * Register tags to be replaced with some URLs
     *
     * @return array
     */
    protected static function getWebPreprocessingTags()
    {
        return [
            static::WEB_LC_ROOT,
        ];
    }

    /**
     * Register URLs that should be given instead of tags
     *
     * @return array
     */
    protected static function getWebPreprocessingURL()
    {
        // Get URL of shop. If the HTTPS is used then it should be cleaned from ?xid=<xid> construction
        $url = \XLite\Core\URLManager::getShopURL(
            null,
            \XLite\Core\Request::getInstance()->isHTTPS(),
            [],
            null,
            false
        );

        // We are cleaning URL from unnecessary here <xid> construction
        $url = preg_replace('/(\?.*)/', '', $url);

        return [
            $url,
        ];
    }

    /**
     * Get processed value for meta description tag
     *
     * @param string $description Description to process
     *
     * @return string
     */
    public static function postprocessMetaDescription($description)
    {
        $description = preg_replace(
            '/\s+/',
            ' ',
            \XLite\Core\Converter::filterCurlyBrackets(
                strip_tags(preg_replace('/(<(.+?)[\s]*\/?[\s]*>)/', '$1 ', $description))
            )
        );

        return mb_substr($description, 0, 512);
    }

    // }}}
}
