<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Controller\Admin;

use Qualiteam\SkinActSkuVault\Core\Endpoint\EndpointException;
use XLite\Controller\Admin\AAdmin;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;

class SkuVault extends AAdmin
{
    const OPTIONS = [];

    public function getTitle()
    {
        return '"SkuVault Integration" Addon Settings';
    }

    public function getOptions()
    {
        $options = Database::getRepo('XLite\Model\Config')->findByCategoryAndVisible('Qualiteam\SkinActSkuVault');

        return array_filter($options, function ($option) {
            return in_array($option->getName(), static::OPTIONS);
        });
    }

    /**
     * Update model
     */
    public function doActionUpdate()
    {
        $data = array_filter(Request::getInstance()->getData(), function ($key) {
            return in_array($key, static::OPTIONS);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($data as $k => $v) {
            Database::getRepo('XLite\Model\Config')->createOption(
                [
                    'category' => 'Qualiteam\SkinActSkuVault',
                    'name'     => $k,
                    'value'    => $v,
                ]
            );
        }
    }

    public function processRequest()
    {
        try {
            parent::processRequest();
        } catch (\Twig\Error\RuntimeError $e) {
            if ($e->getPrevious() instanceof EndpointException) {
                TopMessage::addError('[SkuVault] Check credentials', [
                    'message' => $e->getPrevious()->getMessage(),
                ]);

                $this->setReturnURL($this->buildURL('skuvault_general'));
                $this->redirect();
            }
        }
    }
}
