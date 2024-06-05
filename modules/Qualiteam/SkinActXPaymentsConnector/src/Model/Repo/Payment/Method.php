<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Model\Repo\Payment;

use Includes\Utils\FileManager;
use Symfony\Component\Yaml\Yaml;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Payment method repository
 *
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Repo\Payment\Method
{
    /**
     * Names of fields that are used in search
     */
    const P_CLASS = 'class';
    const P_FROM_MARKETPLACE = 'fromMarketplace';

    /**
     * Prepare certain search condition for enabled flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere($this->getMainAlias($queryBuilder) . '.class = :class_value')
            ->setParameter('class_value', $value);
    }

    /**
     * Prepare certain search condition for enabled flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndFromMarketplace(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere($this->getMainAlias($queryBuilder) . '.fromMarketplace = :fromMarketplaceValue')
            ->setParameter('fromMarketplaceValue', $value);
    }

    /**
     * Prepare data before calling parent method - Update payment methods with data received from the marketplace
     *
     * @param array List of payment methods received from marketplace
     *
     * @return void
     */
    public function updatePaymentMethods($data, $countryCode = '')
    {
        if (!empty($data) && is_array($data)) {

            foreach ($data as $key => $item) {

                if (
                    'XPay_XPaymentsCloud' == $item['moduleName']
                    && 'XPaymentsCloud' != $item['service_name']
                ) {

                    $data[$key]['moduleName'] = 'Qualiteam_SkinActXPaymentsConnector';

                    if ('SavedCard' == $item['service_name']) {
                        $data[$key]['class'] = 'Qualiteam\\SkinActXPaymentsConnector\\Model\\Payment\\Processor\\SavedCard';
                        $data[$key]['added'] = true;
                        $data[$key]['enabled'] = true;
                    } else {
                        $data[$key]['class'] = 'Qualiteam\\SkinActXPaymentsConnector\\Model\\Payment\\Processor\\XPayments';
                    }
                }
            }
        }

        parent::updatePaymentMethods($data, $countryCode);

        if (!empty($data) && is_array($data)) {

            foreach ($data as $key => $item) {
                if (
                    'XPay_XPaymentsCloud' == $item['moduleName']
                    && 'XPaymentsCloud' != $item['service_name']
                ) {

                    $data[$key]['moduleName'] = 'Qualiteam_SkinActXPaymentsConnector';

                    if ('SavedCard' == $item['service_name']) {
                        $data[$key]['class'] = 'Qualiteam\\SkinActXPaymentsConnector\\Model\\Payment\\Processor\\SavedCard';
                        $data[$key]['added'] = true;
                        $data[$key]['enabled'] = true;
                    } else {
                        $data[$key]['class'] = 'Qualiteam\\SkinActXPaymentsConnector\\Model\\Payment\\Processor\\XPayments';
                    }
                } else {
                    unset($data[$key]);
                }
            }
        }

        $data = array_values($data);

        $yaml = Yaml::dump(['XLite\\Model\\Payment\\Method' => $data]);

        $yamlFile = LC_DIR_TMP . 'pm_xpc.yaml';

        FileManager::write(LC_DIR_TMP . 'pm_xpc.yaml', $yaml);

        Database::getInstance()->loadFixturesFromYaml($yamlFile);

    }
}
