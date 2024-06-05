<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Core;

use XLite\Core\Config;

/**
 * Order history main point of execution
 */
class AmazonS3 extends \XLite\Base\Singleton
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    public const DEFAULT_REGION = 'us-east-1';

    /**
     * AWS S3 client
     *
     * @var \S3
     */
    protected static $client;

    /**
     * AWS S3 client config
     *
     * @var \XLite\Core\CommonCell
     */
    protected static $config;

    /**
     * Valid status
     *
     * @var boolean
     */
    protected $valid = false;

    /**
     * URL prefix
     *
     * @var string
     */
    protected static $urlPrefix;

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        $config = static::getConfig();

        if ($config->access_key && $config->secret_key && $config->bucket && function_exists('curl_init')) {
            try {
                $this->valid = $this->checkSettings($config->bucket);
            } catch (\Exception $e) {
                \XLite\Logger::getInstance()->registerException($e);
            }
        }
    }

    /**
     * Check valid status
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Get module options
     *
     * @return \XLite\Core\CommonCell
     */
    protected static function getConfig()
    {
        if (!static::$config) {
            static::$config = new \XLite\Core\CommonCell([
                'storage_type' => Config::getInstance()->CDev->Egoods->storage_type,
                'access_key'   => Config::getInstance()->CDev->Egoods->amazon_access,
                'secret_key'   => Config::getInstance()->CDev->Egoods->amazon_secret,
                'bucket'       => Config::getInstance()->CDev->Egoods->bucket,
                'region'       => Config::getInstance()->CDev->Egoods->bucket_region,
                'link_ttl'     => Config::getInstance()->CDev->Egoods->ttl,
                'do_endpoint'  => Config::getInstance()->CDev->Egoods->do_endpoint
            ]);
        }

        return static::$config;
    }

    /**
     * Set module options
     */
    protected static function setConfig($key, $value)
    {
        if ($key === 'region') {
            static::$config->region = $value;
            static::$client = null;
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                [
                    'category' => 'CDev\\Egoods',
                    'name'     => 'bucket_region',
                    'value'    => $value,
                ]
            );
        }
    }

    /**
     * Read
     *
     * @param string $path Short path
     *
     * @return string
     */
    public function getPresignedUrl($path)
    {
        $result = null;

        try {
            $config = static::getConfig();
            $client = static::getClient();
            $command = $client->getCommand('GetObject', [
                'Bucket' => $config->bucket,
                'Key'    => $path,
            ]);

            if (is_int($config->link_ttl) && $config->link_ttl > 0 && $config->link_ttl < 7) {
                $request = $client->createPresignedRequest($command, "+{$config->link_ttl} days");
            } else {
                $request = $client->createPresignedRequest($command, '+7 days');
            }

            $result = (string) $request->getUri();
        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Check - file is exists or not
     *
     * @param string $path Short path
     *
     * @return boolean
     */
    public function isExists($path)
    {
        return static::getClient()->doesObjectExist(static::getConfig()->bucket, $path);
    }

    /**
     * Check settings
     *
     * @param string $bucket    S3 bucket
     * @param string $accessKey AWS access key OPTIONAL
     * @param string $secretKey AWS secret key OPTIONAL
     * @param string $endpoint  push endpoint if its DO Cloud
     *
     * @return boolean
     */
    public function checkSettings($bucket, $accessKey = null, $secretKey = null, $endpoint = null)
    {
        $valid = false;

        $client = (!empty($accessKey) || !empty($secretKey))
            ? static::getS3Client($accessKey, $secretKey, null, $endpoint)
            : static::getClient();

        if ($client) {
            $region = $this->detectBucketLocation($client, $bucket);

            if (isset($region)) {
                $valid = true;
                if ($this->getConfig()->region != $region) {
                    $this->setConfig('region', $region);
                }
            }
        }

        return $valid;
    }

    /**
     * Detect and return bucket location (region)
     *
     * @param \Aws\S3\S3Client $client S3 client
     * @param string           $bucket
     *
     * @return string
     */
    protected function detectBucketLocation($client, $bucket)
    {
        $location = null;

        try {
            $result = $client->determineBucketRegion($bucket);

            $location = $result;
        } catch (\Exception $e) {
        }

        return $location;
    }

    // {{{ Service methods

    /**
     * Get client
     *
     * @return \Aws\S3\S3Client
     */
    protected static function getClient()
    {
        if (!static::$client) {
            $config = static::getConfig();

            $region = $config->region ?: null;
            $endpoint = $config->storage_type === 'dos' ? $config->do_endpoint : null;

            static::$client = static::getS3Client($config->access_key, $config->secret_key, $region, $endpoint);
        }

        return static::$client;
    }

    /**
     * Create S3 client object
     *
     * @return \Aws\S3\S3Client
     */
    protected static function getS3Client($key, $secret, $region = null, $endpoint = null)
    {
        if (empty($region)) {
            $region = static::DEFAULT_REGION;
        }

        $params = [
            'signature'   => 'v4',
            'region'      => $endpoint ? explode('.', $endpoint)[0] : $region,
            'version'     => '2006-03-01',
            'credentials' => [
                'key'    => $key,
                'secret' => $secret
            ]
        ];

        if ($endpoint) {
            $params['endpoint'] = 'https://' . $endpoint;
        }

        return \Aws\S3\S3Client::factory($params);
    }

    // }}}
}
