<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;


/**
 * @Extender\Mixin
 */
class Files extends \Qualiteam\SkinActMain\Controller\Customer\Files
{

    protected function doActionUploadFromFile()
    {
        if (Request::getInstance()->review_file > 0) {

            $request = \XLite\Core\Request::getInstance();

            $request->is_image = null;

            if ($request->type === 'video') {
                $file = $request->register
                    ? new \XLite\Model\Video\Content()
                    : new \XLite\Model\Video\Temporary();
            } else {
                $file = $request->register
                    ? new \XLite\Model\Image\Content()
                    : new \XLite\Model\TemporaryFile();

                if ($request->extended) {
                    $file->allowExtendedTypes();
                }
            }

            if ($request->alt) {
                $file->setAlt($request->alt);
            }

            $file->renewMimesForReview();

            $message = '';

            if ($file->loadFromRequest('file')) {
                $this->checkFile($file);
                $this->postProcessImageUpload($file);
                \XLite\Core\Database::getEM()->persist($file);
                \XLite\Core\Database::getEM()->flush();
            } elseif ($file->getLoadErrorMessage()) {
                $message = call_user_func_array(['\XLite\Controller\Admin\Files',
                    't'], $file->getLoadErrorMessage());
            } else {
                $message = static::t('File is not uploaded');
            }

            $this->sendResponse($file, $message);

        } else {
            parent::doActionUploadFromFile();
        }

    }

}