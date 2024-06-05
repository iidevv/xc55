<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Logic\Feed\Step;

use XLite\Logic\ARepoStep;

/**
 * Abstract step
 */
abstract class AFeedStep extends ARepoStep
{
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;

    /**
     * Constructor
     *
     * @param \XLite\Logic\AGenerator $generator Generator     OPTIONAL
     * @param string                  $languageCode            OPTIONAL
     */
    public function __construct(\XLite\Logic\AGenerator $generator = null, $languageCode = null)
    {
        $this->generator = $generator;
        $this->languageCode = $languageCode;
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
    }

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $options = $this->generator->getOptions();
            if (!isset($options['count' . get_class($this)])) {
                $options['count' . get_class($this)] = $this->getRepository()->countForFeedGeneration();
                $this->generator->setOptions($options);
            }
            $this->countCache = $options['count' . get_class($this)];
        }

        return $this->countCache;
    }

    /**
     * @return string
     */
    protected function getCurrentLanguage()
    {
        return \XLite::getController()->getCurrentLanguage();
    }

    // }}}

    // {{{ Data

    /**
     * Get items iterator
     *
     * @param boolean $reset Reset iterator OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected function getItems($reset = false)
    {
        if (!isset($this->items) || $reset) {
            $this->items = $this->getRepository()->getFeedGenerationIterator($this->position);
            $this->items->rewind();
        }

        return $this->items;
    }

    // }}}
}
