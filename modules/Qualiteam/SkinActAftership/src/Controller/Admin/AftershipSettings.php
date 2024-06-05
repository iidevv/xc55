<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Controller\Admin;

use Qualiteam\SkinActAftership\Couriers\Couriers;
use Qualiteam\SkinActAftership\Model\AftershipCouriers;
use XCart\Container;
use XLite\Core\Database;
use XLite\Core\TopMessage;
use XLite\Model\Config;
use XLite\View\Model\Settings;

/**
 * Class aftership settings
 */
class AftershipSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions(): array
    {
        return array_merge(parent::defineFreeFormIdActions(), ['collectCouriers']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle(): string
    {
        return '"Aftership" addon settings';
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->executeCachedRuntime(function () {
            return Database::getRepo(Config::class)
                ->findByCategoryAndVisible($this->getOptionsCategory());
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory(): string
    {
        return 'Qualiteam\SkinActAftership';
    }

    /**
     * Update settings
     *
     * @return void
     */
    protected function doActionUpdate(): void
    {
        $this->getModelForm()->performAction('update');
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string
     */
    protected function getModelFormClass(): string
    {
        return Settings::class;
    }

    /**
     * Collect all couriers
     *
     * @return bool
     * @throws \Exception
     */
    protected function doActionCollectCouriers(): bool
    {
        $dbCouriersCount = Database::getRepo(AftershipCouriers::class)
            ->search(null, true);

        $couriers = Container::getContainer()->get('aftershipGetAllCouriers')->getData();

        if (empty($couriers)) {
            TopMessage::addError('SkinActAftership received list of couriers is empty');
            return false;
        }

        $total = $couriers['data']['total'];
        $data = $couriers['data']['couriers'];

        $couriers = new Couriers($data, $total);

        if ($dbCouriersCount === 0) {
            $couriers->create();
            TopMessage::addInfo('SkinActAftership list of couriers successfully created');
        } else {
            $couriers->update();
            TopMessage::addInfo('SkinActAftership updated successful verify couriers slug code on the shipping methods page', [
                'page' => $this->buildURL('shipping_methods')
            ]);
        }

        return true;
    }
}
