<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Converter extends \XLite\Core\Converter
{
    /**
     * Convert a timestamp to a timestamp for the 1st/last day of the same month/year.
     *
     * @param integer $date  The date (timestamp).
     * @param boolean $first Whether we need the first (true), or the last day OPTIONAL
     *
     * @return integer The first/last day of the same month and year (timestamp).
     */
    public static function convertDateToMonth($date, $first = true)
    {
        $month = date('n', $date);
        $year = date('Y', $date);

        return $first
            ? mktime(0, 0, 0, $month, 1, $year)
            : (mktime(0, 0, 0, $month + 1, 1, $year) - 1);
    }

    /**
     * Generate a random alpha-numeric string.
     *
     * @param integer $length     Number of random characters in the string OPTIONAL
     * @param string  $characters Allowed characters OPTIONAL
     *
     * @return string
     */
    public static function generateRandomString($length = 16, $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789')
    {
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $string;
    }
}
