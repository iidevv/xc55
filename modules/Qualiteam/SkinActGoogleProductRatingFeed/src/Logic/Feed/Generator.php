<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed;

use Includes\Utils\FileManager;
use Qualiteam\SkinActGoogleProductRatingFeed\Core\EventListener\FeedGeneration;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\EventListener;
use XLite\Core\EventTask as EventTaskCore;
use XLite\InjectLoggerTrait;
use XLite\Logic\AGenerator;
use XLite\Model\TmpVar;
use XLite\Model\EventTask;
use Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Step\Reviews;
use XLite\Model\Language;

/**
 * Generator
 */
class Generator extends AGenerator
{
    use InjectLoggerTrait;

    public const TTL = 604800;

    /**
     * Record
     *
     * @var string
     */
    protected $record;

    /**
     * Is page has alternative language url
     *
     * @var boolean
     */
    protected $hasAlternateLangUrls;

    /**
     * @var \Qualiteam\SkinActGoogleProductRatingFeed\Core\Task\FeedUpdater
     */
    protected $feedUpdater;

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        Database::getRepo(TmpVar::class)->setVar(
            static::getTickDurationVarName(),
            $this->count() ? round($this->getOptions()->time / $this->count(), 3) : 0
        );

        foreach ($this->getSteps() as $step) {
            $step->finalize();
        }

        $this->setRecord($this->getRecord() . $this->getFooter());
        $this->flushRecord();
        $this->removeFeedFiles();
        $this->moveFeeds();
    }

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getTickDurationVarName()
    {
        return 'feedRatingGenerationTickDuration';
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            if (!isset($this->options['count'])) {
                $this->options['count'] = 0;
                foreach ($this->getSteps() as $step) {
                    $this->options['count']                    += $step->count();
                    $this->options['count' . get_class($step)] = $step->count();
                }
            }
            $this->countCache = $this->options['count'];
        }

        return $this->countCache;
    }

    /**
     * Return Record
     *
     * @return string
     */
    public function getRecord()
    {
        return $this->record;
    }

    // {{{ Steps

    /**
     * Set Record
     *
     * @param string $record
     *
     * @return $this
     */
    public function setRecord($record)
    {
        $this->record = $record;

        return $this;
    }

    /**
     * Return footer
     *
     * @return string
     */
    protected function getFooter()
    {
        return '</reviews></feed>';
    }

    /**
     * Write record
     *
     * @return void
     */
    public function flushRecord()
    {
        if ($this->getRecord()) {
            FileManager::write($this->getCurrentTemporaryFeedPath(), $this->getRecord(), FILE_APPEND);
            $this->setRecord('');
        }
    }

    /**
     * Get feed path
     *
     * @return string
     */
    protected function getCurrentTemporaryFeedPath()
    {
        return LC_DIR_DATA . static::getPrefix() . static::getFilenamePart() . '.' . $this->getFileIndex() . '.xml';
    }

    // }}}

    /**
     * Get file prefix for generated sitemaps
     *
     * @return string
     */
    protected static function getPrefix()
    {
        return 'tmp_';
    }

    /**
     * Get file prefix for generated sitemaps
     *
     * @return string
     */
    protected static function getFilenamePart()
    {
        return 'googleproductratingfeed';
    }

    // }}}

    // {{{ Error / warning routines

    /**
     * Return current file index
     *
     * @return int
     */
    protected function getFileIndex()
    {
        return $this->getOptions()->fileIndex;
    }

    // }}}

    // {{{ Service variable names

    /**
     * Remove temporary files
     */
    protected function removeFeedFiles()
    {
        foreach ($this->getFeedFiles() as $path) {
            FileManager::deleteFile($path);
        }
    }

    /**
     * Return array of previously generated sitemap files
     *
     * @return array
     */
    protected function getFeedFiles()
    {
        return glob(LC_DIR_DATA . static::getFilenamePart() . '.*.xml') ?: [];
    }

    /**
     * Move feed files
     *
     * @return void
     */
    public function moveFeeds()
    {
        $sep    = preg_quote(LC_DS, '/');
        $prefix = preg_quote($this->getPrefix(), '/');
        foreach ($this->getTemporaryFeedFiles() as $path) {
            $to = preg_replace('/^(.+' . $sep . ')' . $prefix . '(' . static::getFilenamePart() . '\..*\.xml)$/', '\\1\\2', $path);
            FileManager::move($path, $to);
        }
    }

    // }}}


    // {{{ File operations

    /**
     * Return array of temporary files
     *
     * @return array
     */
    protected function getTemporaryFeedFiles()
    {
        return glob(LC_DIR_DATA . static::getPrefix() . static::getFilenamePart() . '.*.xml') ?: [];
    }

    /**
     * Get process tick duration
     *
     * @return float
     */
    public function getTickDuration()
    {
        $result = null;
        if ($this->getOptions()->time && 1 < $this->getOptions()->position) {
            $result = $this->getOptions()->time / $this->getOptions()->position;
        } else {
            $tick = Database::getRepo(TmpVar::class)
                ->getVar(static::getTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result ?: static::DEFAULT_TICK_DURATION;
    }

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
    }

    /**
     * Get sitemap by index
     *
     * @param integer $index Index
     *
     * @return string
     */
    public function getFeed($index = 1)
    {
        $path = LC_DIR_DATA . static::getFilenamePart() . '.' . $index . '.xml';

        return FileManager::isExists($path) ? file_get_contents($path) : null;
    }

    /**
     * Add sitemap item to record
     *
     * @param array $item
     *
     * @return $this
     */
    public function addToRecord(array $item)
    {
        $string = '<review>';

        foreach ($item as $tag => $value) {
            if (empty($value)) {
                continue;
            }

            if (is_array($value)) {
                $string = $this->getSubTags($value, $tag, $string);
            } else {
                $string = $this->prepareTag($value, $tag, $string);
            }
        }

        $string .= '</review>';

        $this->setRecord($this->getRecord() . $string);

        return $this;
    }

    protected function getSubTags(array $value, string $tag, string $string): string
    {
        $string .= '<' . $tag . '>';

        foreach ($value as $subtag => $entry) {
            if (is_array($entry)) {
                $string = $this->getSubTags($entry, $subtag, $string);
            } else {
                $string = $this->prepareTag($entry, $subtag, $string);
            }
        }

        $string .= '</' . $tag . '>';

        return $string;
    }

    protected function prepareTag(string $value, string $tag, string $string): string
    {
        if (in_array($tag, $this->getCustomDisplayTags(), true)) {
            $string .= $value;
        } elseif ($tag === 'reviewer_images') {
            $string .= '<' . $tag . '>' . $value . '</' . $tag . '>';
        } else {
            $string .= '<' . $tag . '>' . htmlspecialchars($value) . '</' . $tag . '>';
        }

        return $string;
    }

    protected function getCustomDisplayTags(): array
    {
        return [
            'name',
            'review_url',
            'overall',
        ];
    }

    /**
     * Check - feed files generated or not
     *
     * @return boolean
     */
    public function isGenerated()
    {
        $list = glob(LC_DIR_DATA . static::getFilenamePart() . '.*.xml');

        return $list && 0 < count($list);
    }

    /**
     * Check - sitemap file is obsolete or not
     *
     * @param integer $ttl TTL OPTIONAL
     *
     * @return boolean
     */
    public function isObsolete($ttl = self::TTL)
    {
        $time = null;

        $list = glob(LC_DIR_DATA . static::getFilenamePart() . '.*.xml');

        if ($list) {
            foreach ($list as $path) {
                $time = $time ? min($time, filemtime($path)) : filemtime($path);
            }
        }

        return $time && $time + $ttl < \XLite\Core\Converter::time();
    }

    /**
     * Generate feed in headless mode
     *
     * @return bool
     */
    public function generate()
    {
        static::run([]);
        static::lock();
        $event = static::getEventName();

        do {
            FeedGeneration::getInstance()->unsetGenerator();
            $em = Database::getEM();
            $em->clear();

            if (isset($this->feedUpdater)) {
                $this->feedUpdater->mergeModel();
            }

            $result = EventListener::getInstance()->handle($event, []);

            $state = Database::getRepo(TmpVar::class)->getEventState($event);

            if (
                $state['state'] === EventTaskCore::STATE_FINISHED
                || $state['state'] === EventTaskCore::STATE_ABORTED
            ) {
                $result = false;
            }
        } while ($result);

        $errors = EventListener::getInstance()->getErrors();

        if ($errors) {
            $result = false;
        }

        Database::getEM()->flush();
        Database::getRepo(EventTask::class)->cleanTasks($event, 0);
        static::unlock();

        return $result;
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'feedRatingGeneration';
    }

    /**
     * @param $feedUpdater
     */
    public function setFeedUpdater($feedUpdater)
    {
        $this->feedUpdater = $feedUpdater;
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
        if (!FileManager::isExists(LC_DIR_DATA)) {
            FileManager::mkdir(LC_DIR_DATA);
            if (!FileManager::isExists(LC_DIR_DATA)) {
                $message = 'The directory ' . LC_DIR_DATA . ' can not be created. Check the permissions to create directories.';

                $this->getLogger('Qualiteam-SkinActGoogleProductRatingFeed')->error($message);

                $this->addError('Directory permissions', $message);

                static::cancel();
            }
        }

        $this->removeTemporaryFeedFiles();
        $this->setFileIndex(1);
        $this->setRecord($this->getHead());
    }

    /**
     * Add error
     *
     * @param string $title Title
     * @param string $body  Body
     *
     * @return void
     */
    public function addError($title, $body)
    {
        $this->getOptions()->errors[] = [
            'title' => $title,
            'body'  => $body,
        ];
    }

    /**
     * Remove temporary files
     */
    protected function removeTemporaryFeedFiles()
    {
        foreach ($this->getTemporaryFeedFiles() as $path) {
            FileManager::deleteFile($path);
        }
    }

    /**
     * Set current file index
     *
     * @param integer $index
     */
    protected function setFileIndex($index)
    {
        $this->getOptions()->fileIndex = $index;
    }

    protected function getVersion(): string
    {
        return '2.3';
    }

    /**
     * Return head
     *
     * @return string
     */
    protected function getHead()
    {
        $updated = date('Y-m-d', LC_START_TIME) . 'T' . date('H:i:s', LC_START_TIME) . 'Z';
        $companyName = Config::getInstance()->Company->company_name;
        $version = $this->getVersion();

        return <<<HEAD
<?xml version="1.0" encoding="UTF-8" ?>
<feed xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.google.com/shopping/reviews/schema/product/2.3/product_reviews.xsd">
<publisher>
  <name>{$companyName}</name>
</publisher>
<version>{$version}</version>
<updated>{$updated}</updated>
<lastBuildDate>{$updated}</lastBuildDate>
<reviews>
HEAD;
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return $this->getStepsList();
    }

    /**
     * Return steps list
     *
     * @return array
     */
    protected function getStepsList()
    {
        return [
            Reviews::class,
        ];
    }

    /**
     * Process steps
     *
     * @return void
     */
    protected function processSteps()
    {
        if ($this->getOptions()->include) {
            foreach ($this->steps as $i => $step) {
                if (!in_array($step, $this->getOptions()->include)) {
                    unset($this->steps[$i]);
                }
            }
        }

        $steps       = $this->steps;
        $this->steps = [];
        foreach ($steps as $step) {
            if (class_exists($step)) {
                if ($this->hasAlternateLangUrls()) {
                    foreach (Database::getRepo(Language::class)->findActiveLanguages() as $language) {
                        $this->steps[] = new $step($this, $language->getCode());
                    }
                } else {
                    $this->steps[] = new $step($this);
                }
            }
        }

        $this->steps = array_values($this->steps);
    }

    // }}}

    // {{{

    /**
     * Check if store has alternative language url
     *
     * @return bool
     */
    public function hasAlternateLangUrls()
    {
        if ($this->hasAlternateLangUrls === null) {
            $router                     = \XLite\Core\Router::getInstance();
            $this->hasAlternateLangUrls = LC_USE_CLEAN_URLS
                && $router->isUseLanguageUrls()
                && count($router->getActiveLanguagesCodes()) > 1;
        }

        return $this->hasAlternateLangUrls;
    }

    /**
     * Define current step
     *
     * @return integer
     */
    protected function defineStep()
    {
        $currentStep = null;

        if (!Database::getRepo(TmpVar::class)->getVar(static::getCancelFlagVarName())) {
            $i = $this->getOptions()->position;
            foreach ($this->getSteps() as $n => $step) {
                if ($i < $step->count()) {
                    $currentStep = $n;
                    $step->seek($i);
                    break;
                } else {
                    $i -= $step->count();
                }
            }
        }

        return $currentStep;
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getCancelFlagVarName()
    {
        return 'feedRatingGenerationCancelFlag';
    }

    /**
     * Build location URL
     *
     * @param array $loc Locationb as array
     *
     * @return string
     */
    protected function buildLoc(array $loc)
    {
        $target = $loc['target'];
        unset($loc['target']);

        return Converter::buildFullURL($target, '', $loc, \XLite::getCustomerScript(), true);
    }

    // }}}
}
