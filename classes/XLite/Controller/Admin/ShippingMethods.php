<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use Includes\Utils\Module\Manager;
use XLite;
use XLite\Core\Converter;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Model\Shipping\Method;

/**
 * Shipping methods management page controller
 */
class ShippingMethods extends AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['add', 'switch', 'hard_remove']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getMethod()
            ? static::t($this->getMethod()->getProcessorObject()->getProcessorName())
            : static::t('shipping');
    }

    /**
     * Returns shipping method
     *
     * @return null|Method
     */
    public function getMethod()
    {
        return $this->getShippingMethodsRepo()
            ->findOnlineCarrier($this->getProcessorId());
    }

    /**
     * Returns current processor id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return Request::getInstance()->processor;
    }

    /**
     * Returns current carrier code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        $processorId = $this->getProcessorId();

        return $processorId && $processorId !== 'offline'
            ? $processorId
            : '';
    }

    /**
     * @return \XLite\Model\Repo\Shipping\Method
     */
    protected function getShippingMethodsRepo()
    {
        return \XLite\Core\Database::getRepo(
            \XLite\Model\Shipping\Method::class
        );
    }

    public function doActionAdd()
    {
        $request   = Request::getInstance();
        $id        = $request->id;
        $rebuildId = $request->rebuildId;

        if ($rebuildId) {
            TopMessage::addInfo('If anything crops up, just rollback or contact our support team - they know how to fix it right away.', [
                'rollback_url' => XLite::getInstance()->getShopURL('service.php?/rollback', null, [
                    'id' => $rebuildId,
                ]),
            ]);
        }

        $url = null;

        /** @var \XLite\Model\Shipping\Method $method */
        $method = $this->getShippingMethodsRepo()->find($id);

        if ($method) {
            if ($method->getProcessor() === 'offline') {
                $method->setAdded(true);
                $method->update();
            } else {
                $module = $method->getProcessorModule();

                if (Manager::getRegistry()->isModuleEnabled($module)) {
                    $processor = $method->getProcessorObject();
                    $this->getLogger()->debug('processor', ['processor' => $processor]);

                    if ($processor) {
                        $settingsUrl = $processor->getSettingsURL();

                        if ($settingsUrl) {
                            $url = $settingsUrl;
                        } else {
                            $method->setAdded(true);
                            $method->update();
                        }
                    }
                }
            }
        }

        $this->redirect($url ?: Converter::buildURL('shipping_methods'));
    }

    public function doActionHardRemove()
    {
        $id = \XLite\Core\Request::getInstance()->id;

        if ($id) {
            /** @var \XLite\Model\Shipping\Method $method */
            $method = $this->getShippingMethodsRepo()->find($id);

            if ($method) {
                \XLite\Core\Database::getEM()->remove($method);
                \XLite\Core\Database::getEM()->flush();

                \XLite\Core\TopMessage::addInfo('Shipping method has been removed');

                $this->setHardRedirect(true);
                $this->setReturnURL(
                    $this->buildURL('shipping_methods')
                );
            }
        }
    }

    /**
     * Run controller
     *
     * @return void
     */
    protected function run()
    {
        \XLite\Core\Marketplace::getInstance()->updateShippingMethods();

        parent::run();
    }
}
