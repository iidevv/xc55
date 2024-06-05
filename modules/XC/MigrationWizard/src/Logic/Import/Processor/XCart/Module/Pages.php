<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * SimpleCMS module
 */
class Pages extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    protected static $correspondFields = [
        'p.`title`'    => 'name',
        'p.`filename`' => 'body',

        'IF(p.`meta_description` IS NULL OR p.`meta_description` = \'\', p.`title`, p.`meta_description`)' => 'teaser',

        'p.`title_tag`'     => 'metaTitle',
        'p.`meta_keywords`' => 'metaKeywords',
    ];

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Constructor
     *
     * @param \XLite\Logic\Import\Importer $importer Importer
     */
    public function __construct(\XLite\Logic\Import\Importer $importer)
    {
        parent::__construct($importer);

        // Update metadata to use custom ID value
        static::updateMetadata();
    }

    /**
     * Update entities metadata
     */
    public static function updateMetadata()
    {
        if (class_exists('\CDev\SimpleCMS\Model\Page')) {
            $metadata = \XLite\Core\Database::getEM()->getClassMetadata('CDev\SimpleCMS\Model\Page');
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        }
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'id'           => [
                static::COLUMN_IS_KEY => true,
            ],
            'pageFile'     => [],
            'name'         => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
            'teaser'       => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'body'         => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
                static::COLUMN_PARSE_IMAGES    => 'parse_after_normalization',
            ],
            'metaTitle'    => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
            'metaKeywords' => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'metaDescType' => [],
            'cleanURLs'    => [],
            'enabled'      => [],
            'cleanURLType'  => [],
            'cleanURLId'  => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $languageFields  = static::getPageLanguageFieldsSQL();
        $cleanURLsFields = static::getCleanURLsFieldsSQLfor('cleanURLs');

        $metaDescrType = "IF(p.meta_description <> '', 'C', 'A') AS `metaDescType`,";
        if (version_compare(static::getPlatformVersion(), '4.2.0') < 0) {
            $metaDescrType = "'A' AS `metaDescType`,";
        }

        return 'p.pageid AS `id`,'
            . 'p.filename AS `pageFile`,'
            . $languageFields
            . $metaDescrType
            . $cleanURLsFields
            . "IF(p.active = 'Y', true, false) AS `enabled`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = self::getTablePrefix();

        $cleanURLsTables = self::getCleanURLsJoinSQLfor(Configuration::CLEAN_URL_TYPE_S, 'p.pageid');

        return " {$prefix}pages AS p"
            . " {$cleanURLsTables}";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        return "p.`level` = 'E'";
    }

    /**
     * Define registry entry
     *
     * @return string
     */
    public static function defineRegistryEntry()
    {
        return [
            static::REGISTRY_SOURCE => 'id',
            static::REGISTRY_RESULT => 'id',
        ];
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['CDev\SimpleCMS'];
    }

    /**
     * Define Fields That Will Be Used For Count
     *
     * @return string
     */
    public static function defineCountFields()
    {
        return 'COUNT(DISTINCT p.filename)';
    }

    /**
     * Define Fields Used For Sql Group By
     *
     * @return array
     */
    public static function defineDatagrouper()
    {
        return ['p.filename'];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get page language fields SQL
     *
     * @return string
     */
    public static function getPageLanguageFieldsSQL()
    {
        $fields = static::$correspondFields;

        if (version_compare(static::getPlatformVersion(), '4.3.0') < 0) {
            unset($fields['p.`title_tag`']);
        }

        if (version_compare(static::getPlatformVersion(), '4.2.0') < 0) {
            unset($fields['IF(p.`meta_description` IS NULL OR p.`meta_description` = \'\', p.`title`, p.`meta_description`)']);
            unset($fields['p.`meta_keywords`']);
            $fields['IF(p.`title` <> \'\', p.`title`, \'\')'] = 'teaser';
        }

        return static::getLanguageFieldsSQLfor(
            $fields,
            Configuration::getAvailableLanguages()
        );
    }

    /**
     * @param $node
     *
     * @return string
     */
    protected function getNodeInnerHTML($node)
    {
        $dom = new \DOMDocument();
        foreach ($node->childNodes as $child) {
            $dom->appendChild($dom->importNode($child, true));
        }

        return trim($dom->saveHTML());
    }

    /**
     * @param integer $id
     * @param string  $lng
     * @param string  $fileName
     * @param string  $level
     *
     * @return string
     */
    protected function getPageContent($id, $lng, $fileName, $level)
    {
        $localPath = $this->getPageFilePath($fileName, $lng, $level);
        $content = null;
        if ($localPath) {
            $content = $this->getPageContentByPath($localPath);
        }

        if ($content === null) {
            foreach ($this->getPageFileURLs($fileName, $lng, $level) as $url) {
                $content = $this->getPageContentByURL($url, true) ?: null;

                if ($content) {
                    break;
                }
            }
        }

        if ($content === null) {
            $content = $this->getPageContentByURL($this->getPageURL($id, $lng));
        }

        $content = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $content);

        return $content ?: '';
    }

    /**
     * @param string $url
     * @param bool $wholePage
     *
     * @return string
     */
    protected function getPageContentByURL($url, $wholePage = false)
    {
        $pageBody = '';

        $request = new \XLite\Core\HTTP\Request($url);

        // Get Migration Wizard configuration options
        $options = \XLite::getInstance()->getOptions('migration_wizard', 'disable_follow_redirects');

        // Configure CURl to follow HTTP's redirects or not
        $request->setAdditionalOption(CURLOPT_FOLLOWLOCATION, empty($options['disable_follow_redirects']));
        $request->setAdditionalOption(CURLOPT_COOKIESESSION, true);
        $request->setAdditionalOption(CURLOPT_COOKIEFILE, LC_DIR_TMP . '/migration-pages.cookies');

        if (
            ($options = \XLite::getInstance()->getOptions('migration_wizard', 'disable_ssl_check'))
            && !empty($options['disable_ssl_check'])
        ) {
            // Disable SSL verification
            $request->setAdditionalOption(CURLOPT_SSL_VERIFYHOST, false);
            $request->setAdditionalOption(CURLOPT_SSL_VERIFYPEER, false);
        }

        $response = $request->sendRequest();
        $valid    = $response && ((int) $response->code === 200);
        $matches  = [];

        if ($valid && $wholePage) {
            $pageBody = $response->body;
        } elseif (
            $valid
            && preg_match('/<!-- central space -->\n*((?:.*\n)+.*)<!-- \/central space -->/', $response->body, $matches)
            && $matches
        ) {
            $doc = new \DOMDocument();
            $doc->loadHTML(mb_convert_encoding($matches[1], 'HTML-ENTITIES', 'UTF-8'));

            $finder = new \DomXPath($doc);
            $node   = $finder->query("//div[@class='content']");

            if ($node && $node->item(0)) {
                $pageBody = $this->getNodeInnerHTML($node->item(0));
            }
        }

        return $pageBody;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getPageContentByPath($path)
    {
        return \Includes\Utils\FileManager::read($path);
    }

    /**
     * @param string $fileName
     * @param string $lng
     * @param string $level
     *
     * @return string
     */
    protected function getPageFilePath($fileName, $lng, $level)
    {
        $_site_path = static::getSitePath();
        if ($level === 'R') {
            return $_site_path . '/' . $fileName;
        }

        $_path = false;

        if (empty($_site_path)) {
            return $_path;
        }

        $paths = [
            $_site_path . '/skin/common_files/pages/' . $lng . '/' . $fileName,
            $_site_path . '/skin1/common_files/pages/' . $lng . '/' . $fileName,
            $_site_path . '/skin/pages/' . $lng . '/' . $fileName,
            $_site_path . '/skin1/pages/' . $lng . '/' . $fileName,
        ];

        foreach ($paths as $path) {
            if (\Includes\Utils\FileManager::isExists($path)) {
                $_path = $path;
                break;
            }
        }

        return $_path;
    }

    /**
     * @param string $fileName
     * @param string $lng
     * @param string $level
     *
     * @return array
     */
    protected function getPageFileURLs($fileName, $lng, $level)
    {
        if ($level === 'R') {
            return static::getSiteUrl() . '/' . $fileName;
        }

        $urls = [
            static::getSiteUrl() . '/skin/common_files/pages/' . $lng . '/' . $fileName,
            static::getSiteUrl() . '/skin1/common_files/pages/' . $lng . '/' . $fileName,
            static::getSiteUrl() . '/skin/pages/' . $lng . '/' . $fileName,
            static::getSiteUrl() . '/skin1/pages/' . $lng . '/' . $fileName,
        ];

        return $urls;
    }

    /**
     * @param integer $id
     * @param string  $lng
     *
     * @return string
     */
    protected function getPageURL($id, $lng)
    {
        return Configuration::getStaticPageURL($id, $lng);
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating static pages');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    protected function normalizePageFieldValue($name, $value)
    {
        $result = $value;

        $sourceFields = array_flip(static::$correspondFields);

        $pageDataPDOStatement = $this->getPageDataPDOStatement($sourceFields[$name], $name);

        if (
            $pageDataPDOStatement
            && $pageDataPDOStatement->execute([$this->currentRowData['pageFile']])
            && ($found = $pageDataPDOStatement->fetchAll(\PDO::FETCH_KEY_PAIR))
        ) {
            $default = $this->normalizeValueAsString($this->getDefLangValue($value));

            foreach (array_keys($value) as $k) {
                $result[$k] = !empty($found[$k]) && ($found[$k] !== $default) ? $found[$k] : $default;
            }
        }

        return $result;
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getPageDataPDOStatement($field, $name)
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            "SELECT p.language `code`, {$field} `{$name}`"
            . " FROM {$prefix}pages p"
            . ' WHERE p.filename = ?'
            . ' AND p.level="E"'
            . ' GROUP BY language'
        );
    }

    /**
     * Normalize 'name' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeNameValue($value)
    {
        return $this->normalizePageFieldValue('name', $value);
    }

    /**
     * Normalize 'body' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeBodyValue($value)
    {
        return $this->executeCachedRuntime(function () use ($value) {
            $result = [];

            $bodyLanguagePDOStatement = $this->getBodyLanguagePDOStatement();

            if (
                $bodyLanguagePDOStatement
                && $bodyLanguagePDOStatement->execute([$this->currentRowData['pageFile']])
                && ($languages = $bodyLanguagePDOStatement->fetchAll(\PDO::FETCH_COLUMN))
            ) {
                foreach ($languages as $lng) {
                    $pageBody = $this->getPageContent($this->currentRowData['id'], $lng, $this->currentRowData['pageFile'], 'E');
                    $result[$lng] = $pageBody;
                }
            }

            $availableLanguages = Configuration::getAvailableLanguages();

            $_result = [];
            foreach ($result as $code => $body) {
                if (strtolower($code) == 'us') {
                    $_result['en'] = $body;
                }

                if (in_array(strtolower($code), $availableLanguages)) {
                    $_result[strtolower($code)] = $body;
                }
            }

            if (empty($_result)) {
                $_result['en'] = !empty($result) ? reset($result) : '';
            }

            $defaultBody = $_result['en'] ?? reset($_result);
            foreach (array_keys($value) as $code) {
                if (!isset($_result[$code])) {
                    $_result[$code] = $defaultBody;
                }
            }

            return $_result;
        }, ['normalizeBodyValue', $this->currentRowData['pageFile']]);
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getBodyLanguagePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT p.language `code`'
            . " FROM {$prefix}pages p"
            . ' WHERE p.filename = ?'
            . ' GROUP BY language'
        );
    }

    /**
     * Normalize 'teaser' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeTeaserValue($value)
    {
        return $this->normalizePageFieldValue('teaser', $value);
    }

    /**
     * Normalize 'meta title' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeMetaTitleValue($value)
    {
        return $this->normalizePageFieldValue('metaTitle', $value);
    }

    /**
     * Normalize 'meta keywords' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeMetaKeywordsValue($value)
    {
        return $this->normalizePageFieldValue('metaKeywords', $value);
    }

    /**
     * Normalize 'cleanURLs' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeCleanURLsValue($value)
    {
        return $this->getCleanUrl(Configuration::CLEAN_URL_TYPE_S, $value);
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Logic" defaultstate="collapsed">

    /**
     * @param \CDev\SimpleCMS\Model\Page $model  Page
     * @param string                $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importPageFileColumn(\CDev\SimpleCMS\Model\Page $model, $value, array $column)
    {
    }

    protected static function checkTransferableDataPresent()
    {
        $prefix = self::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}pages"
            . ' WHERE active = "Y" LIMIT 1'
        );
    }

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        //collect page content before model creation, flush session issue
        $this->normalizeBodyValue($data['body']);

        $res = parent::importData($data);

        if ($this->currentlyProcessingModel && isset($data['cleanURLs'])) {
            $this->importOriginCleanURL_n_History($this->currentlyProcessingModel, $this->normalizeCleanURLsValue($data['cleanURLs']), $data);
        }

        return $res;
    }

    // }}} </editor-fold>
}
