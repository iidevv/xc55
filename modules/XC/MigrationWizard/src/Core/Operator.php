<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Operator
 * @Extender\Mixin
 */
class Operator extends \XLite\Core\Operator
{
    /**
     * Try to make HEAD request and check if resource is available. Returns headers if request is successful.
     *
     * @param string $url URL
     *
     * @return mixed
     */
    public static function checkURLAvailability($url)
    {
        $result = null;

        $bouncer = new \XLite\Core\HTTP\Request($url);
        $bouncer->verb = 'HEAD';

        // Get Migration Wizard configuration options
        $options = \XLite::getInstance()->getOptions('migration_wizard', 'disable_follow_redirects');
        // Configure CURl to follow HTTP's redirects or not
        $bouncer->setAdditionalOption(CURLOPT_FOLLOWLOCATION, empty($options['disable_follow_redirects']));

        $response = $bouncer->sendRequest();

        if ($response && $response->code == 200) {
            $result = $response->headers;
        } elseif ($response) {
            unset($bouncer);
            $bouncer = new \XLite\Core\HTTP\Request($url);
            $bouncer->verb = 'GET';
            $response = $bouncer->sendRequest();

            if ($response && $response->code == 200) {
                $result = $response->headers;
            }
        }

        if ($result && !$result->ContentLength) {
            /**
             * define fake length if request was succesfull but have no length
             *
             * @see #XC4-148165
             */
            $result->ContentLength = 69;
        }

        return $result;
    }

    /**
     * Curls URL and writes it to file
     *
     * @param string $url URL
     *
     * @return string|void
     */
    public static function writeURLContentsToFile($url, $file)
    {
        $result = null;

        $bouncer = new \XLite\Core\HTTP\Request($url);

        // Get Migration Wizard configuration options
        $options = \XLite::getInstance()->getOptions('migration_wizard', 'disable_follow_redirects');
        // Configure CURl to follow HTTP's redirects or not
        $bouncer->setAdditionalOption(CURLOPT_FOLLOWLOCATION, empty($options['disable_follow_redirects']));

        $response = $bouncer->requestToFile($file);

        if ($response && $response->code == 200) {
            $result = true;
        }

        return $result;
    }
}
