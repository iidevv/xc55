<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

/**
 * AllInOne solutions
 */
class AllInOneSolutionService extends \XLite\Base\Singleton
{
    /**
     * Solutions list
     *
     * @var array
     */
    protected $solutions = [];

    /**
     * Add solution
     *
     * @param \XLite\View\AView     $solution   Solution
     * @param string                $key        Solution key OPTIONAL
     *
     * @return void
     */
    public function addSolution($solution, $key = null)
    {
        if ($key) {
            $this->solutions[$key] = $solution;
        } else {
            $this->solutions[] = $solution;
        }
    }

    /**
     * Get solutions
     *
     * @return array
     */
    public function getSolutions()
    {
        return $this->solutions;
    }
}
