<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Doctrine\Common\Cache\Psr6\CacheAdapter;
use Includes\Utils\ConfigParser;
use Includes\Utils\URLManager;
use XCart\Domain\ModuleManagerDomain;
use XCart\Domain\ServiceLicenseDomain;
use XCartMarketplace\Connector\Exceptions\ClientException;
use XCartMarketplace\Connector\Exceptions\RequestValidationException;
use XLite\InjectLoggerTrait;

class MarketplaceClient extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    /**
     * @var \XCartMarketplace\Connector\Client
     */
    protected $client;

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        $this->initClient();
    }

    /**
     * @param string $target         Request target
     * @param array  $params         Request parameters
     * @param bool   $ignoreCache    Flag: true - ignore cache and force sending request
     * @param array  $serviceOptions Additional (service) options, e.g. 'range' for get_addon_pack/get_core_pack
     *
     * @return array|null
     */
    public function retrieve(string $target, array $params = [], bool $ignoreCache = true, array $serviceOptions = []): ?array
    {
        $response = null;

        try {
            $params = array_merge($params, $this->resolveAutoParams($params['autoResolveParams'] ?? []));
            unset($params['autoResolveParams']);
            $this->client->addRequest($target, $params, $ignoreCache, $serviceOptions);

            $response = $this->client->getData()[$target] ?? null;
        } catch (RequestValidationException | ClientException $e) {
            $this->getLogger()->error($e->getMessage());
        }

        return $response;
    }

    protected function initClient(): void
    {
        if ($this->client === null) {
            $xlite = \XLite::getInstance();
            $licenseRepository = \XCart\Container::getContainer()->get(ServiceLicenseDomain::class);
            $config = new \XCartMarketplace\Connector\Config([
                'url'                => ConfigParser::getOptions(['marketplace', 'url']),
                'shopID'             => $this->getShopId(),
                'shopDomain'         => $this->getShopDomain(),
                'shopURL'            => $xlite->getShopURL(),
                'email'              => $this->getAdminEmail(),
                'currentCoreVersion' => [
                    'major' => $xlite->getMajorVersion(),
                    'minor' => $xlite->getMinorOnlyVersion(),
                    'build' => $xlite->getBuildVersion(),
                ],
                'installation_lng'   => \XLite::getInstallationLng(),
                'shopCountryCode'    => \XLite\Core\Config::getInstance()->Company->location_country,
                'affiliateId'        => \XLite::getAffiliateId(),
                'trial'              => ConfigParser::getOptions(['service', 'is_trial']),
                'cloud'              => ConfigParser::getOptions(['service', 'is_cloud']),
                'xcn_license_key'    => $licenseRepository->getLicenseKey(),
            ]);

            $this->client = new \XCartMarketplace\Connector\Client(
                $config,
                CacheAdapter::wrap(Cache::getInstance()->getDriver()),
                $this->getLogger()
            );
        }
    }

    protected function getAdminEmail(): string
    {
        return \XLite\Core\Mailer::getSiteAdministratorMail();
    }

    protected function getShopId(): string
    {
        $installerDetails = ConfigParser::getOptions(['installer_details']);
        $authCode         = $installerDetails['auth_code'];

        return $authCode
            ? md5("{$authCode}{$installerDetails['shared_secret_key']}")
            : '';
    }

    protected function getShopDomain(): string
    {
        $hostDetails = ConfigParser::getOptions(['host_details']);

        return URLManager::isHTTPS() || ($hostDetails['force_https'] ?? false)
            ? $hostDetails['https_host']
            : $hostDetails['http_host'];
    }

    private static function explodeVersion(?string $version = ''): array
    {
        [$major1, $major2, $minor, $build] = explode('.', $version);

        return [
            'major' => "{$major1}.{$major2}",
            'minor' => $minor,
            'build' => $build,
        ];
    }

    /**
     * Called from method_exists
     * @throws \Exception
     */
    private function autoResolvedmodules(): array
    {
        $moduleRepository = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);

        return array_values(array_map(function ($module) {
            $version = $this->explodeVersion($module['version']);

            return [
                'author'  => $module['author'],
                'name'    => $module['name'],
                'major'   => $version['major'],
                'minor'   => $version['minor'],
                'build'   => $version['build'],
                'enabled' => $module['isEnabled'],
            ];
        }, $moduleRepository->getAllModules()));
    }

    private function resolveAutoParams(array $params2resolve): array
    {
        $result = [];
        foreach ($params2resolve as $resolveParamName) {
            $methodDataProvider = 'autoResolved' . $resolveParamName;
            if (method_exists($this, $methodDataProvider)) {
                $result[$resolveParamName] = $this->$methodDataProvider();
            }
        }

        return $result;
    }
}
