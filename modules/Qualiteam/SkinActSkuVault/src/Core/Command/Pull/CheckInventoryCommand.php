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
use XLite\Core\Database;

class CheckInventoryCommand implements ICommand
{
    private APIService $api;
    private GetTokens  $getTokens;
    private            $productSkus;

    public function __construct(
        APIService $api,
        GetTokens  $getTokens,
        array      $productSkus
    ) {
        $this->api         = $api;
        $this->getTokens   = $getTokens;
        $this->productSkus = $productSkus;
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        if (!empty($this->productSkus)) {
            try {
                $result = $this->api->sendRequest('POST', 'products/getProducts', [
                    'body' => json_encode([
                        'PageNumber'  => 0,
                        'PageSize'    => 10000,
                        'ProductSKUs' => $this->productSkus,
                        'TenantToken' => $tokens['TenantToken'],
                        'UserToken'   => $tokens['UserToken']
                    ])
                ]);

                $this->processNotFoundItems($result);
            } catch (APIException $e) {
                throw new CommandException('Command failed', 0, $e);
            }
        }
    }

    public function processNotFoundItems(array $result)
    {
        if (!empty($result['Errors'])) {
            foreach ($result['Errors'] as $error) {
                preg_match("/SKU '(.+)' is not found/Uis", $error, $errSku);
                if (
                    !empty($errSku[1])
                    && strpos($error, "SKU '" . $errSku[1] . "' is not found") !== false
                ) {
                    $skuvaultItem = Database::getRepo(SkuvaultItem::class)->findOneBySku($errSku[1]);
                    $skuvaultItem->delete();
                    $this->createNotFoundLogEntry($errSku[1]);
                }
            }
        }
    }

    protected function createNotFoundLogEntry(string $sku)
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_SKUVAULT_TO_XC)
            ->setStatus(SyncStatuses::STATUS_SUCCESS)
            ->setMessage('Product with SKU ' . $sku . ' is not found in SkuVault. Delete sync')
            ->setOperation(OperationTypes::TYPE_DELETE_PRODUCT_SYNC);
        Database::getEM()->persist($logEntry);
        Database::getEM()->flush();
    }
}
