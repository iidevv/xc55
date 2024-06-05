<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Autocomplete controller
 */
class Autocomplete extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        $content = json_encode($this->data);

        $xLite = \XLite::getInstance();
        $xLite->addHeader('Content-Type', 'application/json; charset=UTF-8');
        $xLite->addHeader('Content-Length', strlen($content));
        $xLite->addHeader('ETag', md5($content));

        $xLite->addContent($content);
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $dictionary = \XLite\Core\Request::getInstance()->dictionary;

        if ($dictionary) {
            $method = 'assembleDictionary' . \Includes\Utils\Converter::convertToUpperCamelCase($dictionary);
            if (method_exists($this, $method)) {
                // Method name assembled from 'assembleDictionary' + dictionary request argument
                $data = $this->$method((string)\XLite\Core\Request::getInstance()->term);
                $this->data = $this->processData($data);
            }
        }

        $this->silent = true;
    }

    /**
     * Process data
     *
     * @param array $data Key-value data
     *
     * @return array
     */
    protected function processData(array $data)
    {
        $list = [];

        foreach ($data as $k => $v) {
            $list[] = [
                'label' => $v,
                'value' => $k,
            ];
        }

        return $list;
    }
}
