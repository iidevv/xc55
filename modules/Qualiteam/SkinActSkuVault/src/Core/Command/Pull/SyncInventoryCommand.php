<?php

namespace Qualiteam\SkinActSkuVault\Core\Command\Pull;

use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\ICommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Model\Log;
use Qualiteam\SkinActSkuVault\Model\SkuvaultItem;
use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\OperationTypes;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use XC\ProductVariants\Model\ProductVariant;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Model\Product;

class SyncInventoryCommand implements ICommand
{
    const ITEMS = 'Items';
    const SKU   = 'Sku';
    const QTY   = 'AvailableQuantity';
    const TIME  = 'LastModifiedDateTimeUtc';

    private APIService $api;
    private GetTokens  $getTokens;
    private string     $modifiedAfter;
    private string     $modifiedBefore;

    public function __construct(
        APIService $api,
        GetTokens  $getTokens,
        string     $modifiedAfter,
        string     $modifiedBefore
    ) {
        $this->api            = $api;
        $this->getTokens      = $getTokens;
        $this->modifiedAfter  = $modifiedAfter;
        $this->modifiedBefore = $modifiedBefore;
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        try {
            $result = $this->api->sendRequest('POST', 'inventory/getAvailableQuantities', [
                'body' => json_encode([
                    'ModifiedAfterDateTimeUtc'  => $this->modifiedAfter,
                    'ModifiedBeforeDateTimeUtc' => $this->modifiedBefore,
                    'PageNumber'                => 0,
                    'PageSize'                  => 10000,
                    'TenantToken'               => $tokens['TenantToken'],
                    'UserToken'                 => $tokens['UserToken']
                ])
            ]);

            $this->processItems($result);

            Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Qualiteam\\SkinActSkuVault',
                'name'     => 'skuvault_items_last_sync_time',
                'value'    => Converter::time(),
            ]);

        } catch (APIException $e) {
            throw new CommandException('Command failed', 0, $e);
        }

    }

    /**
     * @param array $result
     * @return void
     * @throws \Exception
     */
    protected function processItems(array $result): void
    {
        if (isset($result[self::ITEMS]) && !empty($result[self::ITEMS])) {
            foreach ($result[self::ITEMS] as $item) {
                $this->processEntry($item);
            }
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws \Exception
     */
    protected function processEntry(array $data): void
    {
        if (
            !empty($data[self::SKU])
            && !empty($data[self::QTY])
        ) {
            $this->updateItemQty($data[self::SKU], $data[self::QTY], $data[self::TIME]);
        }
    }

    /**
     * @param string $sku
     * @param int $qty
     * @param string $lastModifiedTime
     * @return void
     * @throws \Exception
     */
    protected function updateItemQty(string $sku, int $qty, string $lastModifiedTime): void
    {
        if ($qty < 0) {
            $qty = 0;
        }

        /** @var Product $product */
        $product = Database::getRepo(Product::class)->findOneBySku($sku);
        /** @var ProductVariant $variant */
        $variant = Database::getRepo(ProductVariant::class)->findOneBySku($sku);

        if (!$product && !$variant) {
            $this->createProductOrVariantNotFoundLogEntry($sku);
        } elseif (
            ($product && $product->isSkippedFromSync())
            || ($variant && $variant->getProduct()->isSkippedFromSync())
        ) {
            $this->createProductOrVariantSkippedLogEntry($sku);
        } else {
            /** @var SkuvaultItem $syncData */
            $syncData = Database::getRepo(SkuvaultItem::class)->findOneBySku($sku);

            if ($syncData) {
                $syncData->setAvailable($qty);
                $syncData->setSyncDate($lastModifiedTime ? strtotime($lastModifiedTime) : Converter::time());
            } else {
                if ($product) {
                    $productId = $product->getProductId();
                    $variantId = 0;
                } elseif ($variant) {
                    $productId = $variant->getProduct()->getProductId();
                    $variantId = $variant->getId();
                }
                $syncData = new SkuvaultItem();
                $syncData->setSku($sku)
                    ->setProductId($productId)
                    ->setVariantId($variantId)
                    ->setAvailable($qty)
                    ->setProductCreated('Y')
                    ->setInventoryCreated('Y')
                    ->setSyncDate($lastModifiedTime ? strtotime($lastModifiedTime) : Converter::time());
                Database::getEM()->persist($syncData);
            }

            if ($product) {
                $product->setAmount($qty);
            } elseif ($variant) {
                $variant->setDefaultAmount(false);
                $variant->setAmount($qty);
            }

            $this->createSyncInventorySuccessLogEntry($sku);
        }
        Database::getEM()->flush();
    }

    /**
     * @param string $sku
     * @return void
     * @throws \Exception
     */
    protected function createProductOrVariantNotFoundLogEntry(string $sku): void
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_SKUVAULT_TO_XC)
            ->setStatus(SyncStatuses::STATUS_ERROR)
            ->setMessage('Product or variant with SKU ' . $sku . ' is not found in X-Cart.')
            ->setOperation(OperationTypes::TYPE_SYNC_INVENTORY);
        Database::getEM()->persist($logEntry);
    }

    /**
     * @param string $sku
     * @return void
     * @throws \Exception
     */
    protected function createProductOrVariantSkippedLogEntry(string $sku): void
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_SKUVAULT_TO_XC)
            ->setStatus(SyncStatuses::STATUS_ERROR)
            ->setMessage('Product or variant with SKU ' . $sku . ' is skipped in X-Cart.')
            ->setOperation(OperationTypes::TYPE_SYNC_INVENTORY);
        Database::getEM()->persist($logEntry);
    }

    protected function createSyncInventorySuccessLogEntry(string $sku)
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_SKUVAULT_TO_XC)
            ->setStatus(SyncStatuses::STATUS_SUCCESS)
            ->setMessage('Product or variant inventory with SKU ' . $sku . ' is updated in X-Cart.')
            ->setOperation(OperationTypes::TYPE_SYNC_INVENTORY);
        Database::getEM()->persist($logEntry);
    }
}
