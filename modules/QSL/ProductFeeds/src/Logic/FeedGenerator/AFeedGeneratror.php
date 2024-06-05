<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic\FeedGenerator;

use Includes\Utils\FileManager;
use QSL\ProductFeeds\Core\FeedItem;
use QSL\ProductFeeds\Model\ProductFeed;

/**
 * Base for classes generating feed files. A generator must not alter the feed.
 */
abstract class AFeedGeneratror extends \XLite\Base
{
    /**
     * Name of the directory where feed files reside.
     */
    public const FEED_DIR = 'feeds';

    /**
     * Cached information on which modules are enabled, or not.
     *
     * @var array
     */
    protected static $enabledModules = [];

    /**
     * Errors happened during the feed generation process.
     *
     * @var array
     */
    protected $errors;

    /**
     * Whether there were fatal errors when generating the feed.
     *
     * @var boolean
     */
    protected $hasFatalErrors = false;

    /**
     * The feed model that the file is generated for.
     *
     * @var \QSL\ProductFeeds\Model\ProductFeed
     */
    protected $feed;

    /**
     * Number of entries processed from the last chunk of data.
     *
     * @var integer
     */
    protected $processed = 0;

    /**
     * Handle for the feed file.
     *
     * @var resource
     */
    protected $fileHandle;

    /**
     * Information on feed columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * Get the feed filename.
     *
     * @return string
     */
    abstract public function getFeedFilename();


    /**
     * Get the minimum number of hours that should pass between automatic feed updates.
     *
     * @return integer
     */
    abstract public function getAutoRefreshDelay();

    /**
     * Define information on CSV columns (key - machine name, value - header)
     *
     * @return array
     */
    abstract protected function defineColumns();

    /**
     * Constructor.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $feed Feed instance.
     */
    public function __construct(ProductFeed $feed)
    {
        $this->feed = $feed;
        $this->resetErrors();
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->hasFileHandle()) {
            $this->closeFileHandle();
        }
    }

    /**
     * Perform actions before processing the first chunk of data.
     *
     * @return void
     */
    public function initFeed()
    {
        if ($this->isHeaderRowEnabled()) {
            $this->getFeed()->setFilename($this->getFeedFilename());
            $this->initFeedFile();
            $this->writeHeaderRow();
        }
    }

    /**
     * Process a chunk of data and export entries from the chunk.
     */
    public function processChunk()
    {
        $this->processProducts($this->getChunkOfProducts());
    }

    /**
     * Get the number of entries exported when processed the last chunk of data.
     *
     * @return integer
     */
    public function countProcessedItems()
    {
        return $this->processed;
    }

    /**
     * Check whether there is more data to export.
     *
     * @return boolean
     */
    public function hasMoreChunks()
    {
        return $this->getFeed()->getPosition() < $this->countFeedItems();
    }

    /**
     * Perform actions after processing the last chunk of data.
     *
     * @return void
     */
    public function completeFeed()
    {
    }

    /**
     * Get path to the resulting feed file.
     *
     * @return string
     */
    public function getFeedFilePath()
    {
        return $this->getFeedDirectory() . LC_DS . $this->getFeedFilename();
    }

    /**
     * Get path to the temporary feed file being generated.
     *
     * @return string
     */
    public function getTempFilePath()
    {
        return $this->getFeedDirectory() . LC_DS . $this->getFeedFilename() . '.tmp';
    }

    /**
     * Check whether there were errors.
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Check whether there were fatal errors.
     *
     * @return boolean
     */
    public function hasFatalErrors()
    {
        return $this->hasFatalErrors;
    }

    /**
     * Get a list of error messages.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the total number of products to be exported into the feed file.
     *
     * @return integer
     */
    public function countFeedItems()
    {
        return $this->getChunkQueryBuilder()
            ->select('COUNT(DISTINCT ' . $this->getChunkQueryBuilder()->getMainAlias() . '.product_id)')
            ->getSingleScalarResult();
    }

    /**
     * Replaces the feed file with the new one that has been just generated.
     */
    public function replaceFeedFileWithTemp()
    {
        $this->closeFileHandle();

        if ($this->removeOldFeedFile()) {
            $this->renameTempFeedFile();
        }
    }

    protected function removeOldFeedFile()
    {
        $success = true;
        $oldPath = $this->getFeed()->getPath();
        if ($oldPath) {
            $success = unlink($oldPath);
            if (!$success) {
                $this->addError(static::t('Can\'t remove the old feed file: X.', ['file' => $oldPath]));
            }
        }

        return $success;
    }

    protected function renameTempFeedFile()
    {
        $oldPath = $this->getTempFilePath();
        $newPath = $this->getFeedFilePath();
        $success = rename($oldPath, $newPath);
        if ($success) {
            $this->getFeed()->setPath($newPath);
        } else {
            $this->addError(
                static::t(
                    "Can't replace the earlier generated feed file ({{new}}) with the new one ({{file}}).",
                    [
                        'file' => $oldPath,
                        'new'  => $newPath,
                    ]
                )
            );
        }
    }

    /**
     * Close the file handle opened for writing to the feed file.
     *
     * @return void
     */
    protected function closeFileHandle()
    {
        $success = fclose($this->getFileHandle());
        $this->fileHandle = false;

        if (!$success) {
            $this->addError(static::t('Can\'t close X.', ['file' => $this->getTempFilePath()]));
        }
    }

    /**
     * Get the feed the file that the file is generated for.
     *
     * @return \QSL\ProductFeeds\Model\ProductFeed
     */
    protected function getFeed()
    {
        return $this->feed;
    }

    /**
     * Get path to the directory where feed files reside.
     *
     * @return string
     */
    protected function getFeedDirectory()
    {
        return LC_DIR_FILES . static::FEED_DIR;
    }

    /**
     * Get handle for the feed file.
     *
     * @param boolean $new Whether it should be a new file, or an existing one OPTIONAL
     *
     * @return resource
     */
    protected function getFileHandle($new = false)
    {
        if ($new && $this->hasFileHandle()) {
            $this->closeFileHandle();
        }

        if (!isset($this->fileHandle)) {
            $this->fileHandle = $this->openFileHandle($new);
        }

        return $this->fileHandle;
    }

    /**
     * Check whether a file handle was opened for the feed file.
     *
     * @return boolean
     */
    protected function hasFileHandle()
    {
        return is_resource($this->fileHandle);
    }

    /**
     * Create and prepare the directory where feed files will be stored.
     *
     * @param string $path Path to the directory where generated feed files will be stored.
     */
    protected function createFeedDirectory($path)
    {
        \Includes\Utils\FileManager::mkdir($path);
    }

    /**
     * Creates the .htaccess file in the directory for feed files.
     *
     * @param string $directoryPath Path to the directory
     *
     * @return void
     */
    protected function createHtaccess($directoryPath)
    {
        $file = $directoryPath . '/.htaccess';
        if (!FileManager::isExists($file) || (FileManager::getFileSize($file) < 266)) {
            file_put_contents(
                $file,
                <<<EOT
Options -Indexes
Allow from all
FileETag None
<ifModule mod_headers.c>
   Header unset ETag
   Header set Cache-Control "max-age=0, no-cache, no-store, must-revalidate"
   Header set Pragma "no-cache"
   Header set Expires "Wed, 11 Jan 1984 05:00:00 GMT"
</ifModule>
EOT
            );
        }
    }

    /**
     * Open a new handle for the feed file.
     *
     * @param boolean $new Whether it should be a new feed file, or an existing one OPTIONAL
     *
     * @return resource|bool
     */
    protected function openFileHandle($new = false)
    {
        $directory = $this->getFeedDirectory();

        if (!\Includes\Utils\FileManager::isExists($directory)) {
            $this->createFeedDirectory($directory);
        }

        $error = !is_writable($directory);
        if ($error) {
            $this->addError(
                static::t('Directory X does not have permissions to write. Please set necessary permissions to directory X', ['path' => $directory]),
                true
            );

            return false;
        }

        $this->createHtaccess($directory);
        $path = $this->getTempFilePath();
        $handle = @fopen($path, $new ? 'wb' : 'ab');
        if ($handle === false) {
            $this->addError(static::t('Can\'t open X file for writing data.', ['file' => $path]), true);
        }

        return $handle;
    }

    /**
     * Init the feed file before writing the first row.
     *
     * @return void
     */
    protected function initFeedFile()
    {
        $this->getFileHandle(true);
    }

    /**
     * Check whether the header row should be written to the feed file.
     *
     * @return boolean
     */
    protected function isHeaderRowEnabled()
    {
        return true;
    }

    /**
     * Write the header row to the feed file.
     *
     * @return void
     */
    protected function writeHeaderRow()
    {
        $headers = [];
        foreach ($this->getColumns() as $name => $column) {
            $headers[] = $column['header'] ?? $name;
        }

        $this->writeRow($headers);
    }

    /**
     * Get information on CSV columns (key - machine name, value - header)
     *
     * @return array
     */
    protected function getColumns()
    {
        if (!isset($this->columns)) {
            $this->columns = [];
            foreach ($this->defineColumns() as $key => $info) {
                $this->columns[$key] = $this->completeColumnInfo($key, $info);
            }
        }

        return $this->columns;
    }

    /**
     * Fill missing fields in the column information.
     *
     * @param string $name Column name.
     * @param array  $info Column info.
     *
     * @return array
     */
    protected function completeColumnInfo($name, array $info)
    {
        if (!isset($info['required'])) {
            $info['required'] = false;
        }

        if (!isset($info['name'])) {
            $info['name'] = $name;
        }

        return $info;
    }

    /**
     * Writes a line into a CSV file.
     *
     * @param resource $handle    File handle
     * @param array    $fields    Columns to write
     * @param string   $delimiter Column delimiter OPTIONAL
     * @param string   $enclosure String enclosure OPTIONAL
     *
     * @return integer
     */
    protected function fputcsv($handle, array $fields, $delimiter = ",", $enclosure = '"')
    {
        // fputcsv can't handle empty enclosures, so we need a workaround for feeds which require this format
        return $enclosure ? fputcsv($handle, $fields, $delimiter, $enclosure)
            : fwrite($handle, implode($delimiter, $fields) . "\n");
    }

    /**
     * Write a row to the feed file.
     *
     * @param array $row Row
     *
     * @return integer
     */
    protected function writeRow(array $row)
    {
        $h = $this->getFileHandle();
        $written = !is_resource($h) ? false : $this->fputcsv(
            $h,
            $row,
            $this->getColumnDelimiter(),
            $this->getStringEnclosure()
        );

        if ($written === false) {
            $this->addError(
                static::t('Failed write to file X. There may not be enough disc-space. Please check if there is enough disc-space.', ['path' => $this->getTempFilePath()])
            );
        }

        return $written;
    }

    /**
     * Get the char to glue values before writing a row to the feed file.
     *
     * @return string
     */
    protected function getColumnDelimiter()
    {
        return ',';
    }

    /**
     * Get the char to enclosure complex string values;
     *
     * @return string
     */
    protected function getStringEnclosure()
    {
        return '"';
    }

    /**
     * Reset all errors.
     *
     * @return void
     */
    protected function resetErrors()
    {
        $this->errors = [];
        $this->hasFatalErrors = false;
    }

    /**
     * Register an error message.
     *
     * @param string  $error Error message.
     * @param boolean $fatal Whether it is a fatal error OPTIONAL
     *
     * @return void
     */
    protected function addError($error, $fatal = false)
    {
        $this->errors[] = $error;
        $this->hasFatalErrors = $this->hasFatalErrors || $fatal;
    }

    /**
     * Export products to the feed file.
     *
     * @param array $products Products to export.
     *
     * @return void
     */
    protected function processProducts($products)
    {
        $counter = 0;
        foreach ($products as $product) {
            if ($this->processProduct($product)) {
                $counter++;
            } elseif ($this->hasFatalErrors()) {
                break;
            }
        }
        $this->processed = $counter;
    }

    /**
     * Export product to the feed file.
     *
     * @param \XLite\Model\Product $product Product to export.
     *
     * @return boolean Whether the product has been processed successfully
     */
    protected function processProduct(\XLite\Model\Product $product)
    {
        $success = true;

        $items = $this->getFeedItems($product);
        foreach ($items as $item) {
            $success = $success && $this->processItem($item);
        }

        return $success;
    }

    /**
     * Get feed items for a product.
     *
     * @param \XLite\Model\Product $product Product to export.
     *
     * @return array
     */
    protected function getFeedItems(\XLite\Model\Product $product)
    {
        return [
            new FeedItem($product)
        ];
    }

    /**
     * Export item to the feed file.
     *
     * @param \QSL\ProductFeeds\Core\FeedItem $item Feed item.
     *
     * @return boolean Whether the item has been processed successfully
     */
    protected function processItem(FeedItem $item)
    {
        $row = [];
        foreach ($this->getColumns() as $name => $column) {
            $row[$name] = $this->getColumnValue($column, $item);
        }
        $written = $this->writeRow($row);

        return is_int($written);
    }

    /**
     * Get column value for an item.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getColumnValue(array $column, FeedItem $item)
    {
        $getterName = 'get' . \Includes\Utils\Converter::convertToUpperCamelCase($column['name']) . 'ColumnValue';
        if (method_exists($this, $getterName)) {
            $value = call_user_func_array([$this, $getterName], [$column, $item]);
        } elseif (isset($column['mapped'])) {
            $value = $this->getMappedField($column, $item);
        } elseif (isset($column['value'])) {
            $value = $column['value'];
        } else {
            $value = $item->getFieldValue($column['name']);
        }

        return $value;
    }
    /**
     * Get value for a field mapped through module settings.
     *
     * @param array                                        $column Information on the column
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed
     *
     * @return mixed
     */
    protected function getMappedField(array $column, FeedItem $item)
    {
        return $this->mapProductField(
            \XLite\Core\Config::getInstance()->QSL->ProductFeeds->{$column['mapped']},
            $item
        );
    }

    /**
     * Get value of the mapped field by its name.
     *
     * @param string                                       $mapped Serialized array of mapped field names (sku, productId, ..., attr:[integer]).
     * @param \QSL\ProductFeeds\Core\FeedItem $item   Item being processed.
     *
     * @return mixed
     */
    protected function mapProductField($mapped, FeedItem $item)
    {
        $value = '';
        $valuePriority = 4;

        $fields = unserialize($mapped);
        if (is_array($fields)) {
            foreach (unserialize($mapped) as $field) {
                if (preg_match('/^attr:([\d]+)$/', $field, $matches)) {
                    $attribute = \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->find((int) $matches[1]);
                    // Hash key is the priority in which product fields will be mapped to the column
                    $priority = $attribute->getProductClass() ? 1 : 2;
                    $v = $attribute ? $item->getAttributeValue($attribute) : null;
                } else {
                    // Regular fields have the least priority
                    $priority = 3;
                    $v = $item->getFieldValue($field);
                }

                if ($v && ($priority < $valuePriority)) {
                    $value = $v;
                    $valuePriority = $priority;
                }
            }
        }

        return $value;
    }

    /**
     * Get iterator over items in the chunk of data from the current feed position.
     *
     * @return array
     */
    protected function getChunkOfProducts()
    {
        $feed = $this->getFeed();

        return $this->getChunkQueryBuilder()
            ->setFirstResult($feed->getPosition())
            ->setMaxResults($this->getMaxProductsPerStep())
            ->groupBy('p.product_id') // We need this to make LIMIT and OFFSET work with multilingual product results
            ->getResult();
    }

    /**
     * Get the number of products to process per step.
     *
     * @return integer
     */
    protected function getMaxProductsPerStep()
    {
        return \XLite\Core\Config::getInstance()->QSL->ProductFeeds->feed_generator_products_per_step;
    }

    /**
     * Get the query builder for retrieving all items matching to the feed criteria.
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function getChunkQueryBuilder()
    {
        return $this->applyFeedSettings($this->factoryChunkQueryBuilder());
    }

    /**
     * Get the query builder for retrieving all items matching to the feed criteria.
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function factoryChunkQueryBuilder()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Product')
            ->createQueryBuilder('p')
            ->orderby('p.product_id', 'ASC');
    }

    /**
     * Get the query builder for retrieving all items matching to the feed criteria.
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb Query builder instance.
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function applyFeedSettings(\XLite\Model\QueryBuilder\AQueryBuilder $qb)
    {
        return $qb;
    }

    /**
     * Get the category path.
     *
     * @param \XLite\Model\Category $category Category the path is generating for
     * @param string                $glue     String to glue categories in the path OPTIONAL
     *
     * @return string
     */
    protected function getCategoryPath(\XLite\Model\Category $category, $glue = ' > ')
    {
        $path = [];

        foreach ($category->getRepository()->getCategoryPath($category->getCategoryId()) as $c) {
            $path[] = $c->getName();
        }

        return implode(' > ', $path);
    }

    /**
     * Build an URL for including into the feed.
     *
     * @param string $target Target OPTIONAL
     * @param string $action Action OPTIONAL
     * @param array  $params Parameters OPTIONAL
     *
     * @return string
     */
    protected function buildFeedUrl($target = '', $action = '', array $params = [])
    {
        $suffix = $this->getUrlSuffix();

        if ($suffix) {
            // Get the hash-part from the URL suffix
            $hpos = strpos($suffix, '#');
            $h = is_int($hpos) ? substr($suffix, $hpos) : '';

            // Split the URL suffix into array of parameters
            parse_str(
                is_int($hpos) ? substr($suffix, 0, $hpos) : $suffix,
                $p
            );
            // ... and merge them with the parameters included into the URL being created
            if (!empty($p)) {
                $params = $p + $params;
            }
        }

        // Build the URL
        $url = \Includes\Utils\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL(
                $target,
                $action,
                $params,
                \XLite::CART_SELF,
                true
            )
        );

        // Add the hash-part from the URL suffix to the created URL
        if ($suffix && $h) {
            $uhpos = strpos('#', $url);
            $url = (is_int($uhpos) ? substr($url, 0, $uhpos) : $url) . $h;
        }

        return $url;
    }

    /**
     * Get suffix for URLs included into the feed.
     *
     * @return string
     */
    protected function getUrlSuffix()
    {
        return '';
    }
}
