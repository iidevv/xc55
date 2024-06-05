<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Controller\Customer;

use Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Generator;
use XLite\Core\Config;

/**
 * Google feed controller
 */
class GoogleProductRatingFeed extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'key'];

    /**
     * @return bool
     */
    protected function checkAccess()
    {
        $key = Config::getInstance()->Qualiteam->SkinActGoogleProductRatingFeed->google_rating_feed_key;
        return parent::checkAccess()
            && $key
            && \XLite\Core\Request::getInstance()->key === $key;
    }

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        $generator = Generator::getInstance();
        $content = $generator->getFeed();
        if ($generator->isGenerated() && !Generator::isLocked()) {
            $this->displayContent($content);
        }

        $this->forceSendResponse();

        if (
            (!$generator->isGenerated() || $generator->isObsolete())
            && !Generator::isLocked()
        ) {
            $generator->generate();
        }
        die(0);
    }

    /**
     * Display content
     *
     * @param string $content Content
     *
     * @return void
     */
    protected function displayContent($content)
    {
        $filename = "googleproductratingfeed_" . \XLite\Core\Converter::formatDate(null, '%Y-%m-%d') . '.xml';
        ob_start();
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Type: application/xml; charset=UTF-8');
        header('Content-Length: ' . strlen($content));
        header('ETag: ' . md5($content));

        print ($content);

        $this->silent = true;
    }

    /**
     * Force browser to display response to user and continue executing
     *
     * @return void;
     */
    protected function forceSendResponse()
    {
        ignore_user_abort(true);

        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', 1);
        }
        if (session_id()) {
            session_write_close();
        }
        header('Connection: close');
        header('Content-Length: ' . ob_get_length());
        ob_end_flush();
        flush();
    }
}
