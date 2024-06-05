<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Marketplace
 */
class Marketplace extends \XLite\Controller\Admin\AAdmin
{
    protected $data = [];

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['update']);
    }

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
     * 'Update' action
     *
     * TODO Consider reimplementation of this without additional request to X-Cart backend
     */
    protected function doActionUpdate()
    {
        // Update info about payment methods
        \XLite\Core\Marketplace::getInstance()->updatePaymentMethods(\XLite\Core\Config::getInstance()->Company->location_country);

        // Update info about shipping methods
        \XLite\Core\Marketplace::getInstance()->updateShippingMethods();

        // Run get_dataset query for expired actions
        //$result = \XLite\Core\Marketplace::getInstance()->getDataset();

        if (empty($result)) {
            $result = [];
        }

        $data = [
            'actions' => array_keys($result),
        ];

        if (!empty($result['check_for_updates'])) {
            $data['check_for_updates_data'] = (0 < array_sum($result['check_for_updates']));
        }

        if (isset($result['get_addons'])) {
            $data['get_addons_data'] = !empty($result['get_addons']) && is_array($result['get_addons']);
        }

        $this->data = $data;
    }
}
