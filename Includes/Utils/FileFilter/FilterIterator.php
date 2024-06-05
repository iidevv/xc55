<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\FileFilter;

use Exception;

class FilterIterator extends \FilterIterator
{
    /**
     * Pattern to filter paths
     *
     * @var string
     */
    protected $pattern;

    /**
     * List of filtering callbacks
     *
     * @var array
     */
    protected $callbacks = [];


    /**
     * Constructor
     *
     * @param \Iterator $iterator iterator to use
     * @param string    $pattern  pattern to filter paths
     *
     * @return void
     */
    public function __construct(\Iterator $iterator, $pattern = null)
    {
        parent::__construct($iterator);

        $this->pattern = $pattern;
    }

    /**
     * Add callback to filter files
     *
     * @param array $callback Callback to register
     *
     * @return void
     */
    public function registerCallback(array $callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('Filtering callback is not valid');
        }

        $this->callbacks[] = $callback;
    }

    /**
     * Check if current element of the iterator is acceptable through this filter
     *
     * @return bool
     */
    public function accept()
    {
        if (!($result = !isset($this->pattern))) {
            $result = preg_match($this->pattern, $this->getPathname());
        }

        if (!empty($this->callbacks)) {
            while ($result && ([, $callback] = each($this->callbacks))) {
                $result = call_user_func_array($callback, [$this]);
            }

            reset($this->callbacks);
        }

        return $result;
    }
}
