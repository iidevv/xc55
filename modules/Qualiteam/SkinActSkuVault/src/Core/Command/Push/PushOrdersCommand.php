<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push;

use Psr\Http\Message\ResponseInterface;
use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\ICommand;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;
use Qualiteam\SkinActSkuVault\Model\Log;
use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\OperationTypes;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use XLite\Core\Database;
use XLite\Model\Order;
use XLite\Model\Product;
use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\HydratorException;

class PushOrdersCommand implements ICommand
{
    private APIService      $api;
    private BaseConverter   $pushOrderConverter;
    private HydratorFactory $hydratorFactory;

    private GetTokens $getTokens;

    /**
     * @var $orderIds['order_id'] integer
     * @var $orderIds['order_number'] string
     */
    private           $orderIds;

    public function __construct(
        APIService      $api,
        BaseConverter   $pushOrderConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens,
        array           $orderIds
    ) {
        $this->api                = $api;
        $this->pushOrderConverter = $pushOrderConverter;
        $this->hydratorFactory    = $hydratorFactory;
        $this->getTokens          = $getTokens;
        $this->orderIds           = $orderIds;
    }

    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        try {
            $dto = [];

            foreach ($this->orderIds as $id) {
                $hydrator = $this->hydratorFactory->hydrator($id['order_id'], Order::class, $this->pushOrderConverter);
                $dto[]    = $hydrator->getDTO();
            }

            if (!empty($dto)) {
                $result = $this->api->sendRequest(
                    'POST',
                    'sales/syncOnlineSales',
                    [
                        'body' => json_encode([
                            'Sales'       => $dto,
                            'TenantToken' => $tokens['TenantToken'],
                            'UserToken'   => $tokens['UserToken']
                        ])
                    ]
                );

                $this->processResult($result);
            }

        } catch (APIException $e) {
            $this->processApiError($e->getContents(), $e->getResponse());
            throw new CommandException('Command failed', 0, $e);
        } catch (HydratorException $e) {
            throw new CommandException('Command failed', 0, $e);
        }
    }

    protected function processApiError(string $contents, ResponseInterface $response): void
    {
        $error = json_decode($contents, true);

        $status = $error['Status'];
        $errors = $error['Errors'];

        if (is_array($errors)) {
            $this->createErrorLogEntries($errors);
        } else {
            $message = '';
            if (!empty($status)) {
                $message .= ' ' . var_export($status, true);
            }
            if (!empty($errors)) {
                $message .= ' ' . var_export($errors, true);
            }
            if ($response->getReasonPhrase()) {
                $message .= ' ' . $response->getReasonPhrase();
            }

            $logEntry = new Log();
            $logEntry->setDate(time())
                ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
                ->setStatus(SyncStatuses::STATUS_ERROR)
                ->setMessage($message)
                ->setOperation(OperationTypes::TYPE_ADD_SALE);
            Database::getEM()->persist($logEntry);

            Database::getEM()->flush();
        }
    }

    protected function processResult(array $result): void
    {
        if (!empty($result['Errors'])) {
            $this->createErrorLogEntries($result['Errors']);

            $successOrderNumbers = array_diff(
                array_column($this->orderIds, 'order_number'),
                array_column($result['Errors'], 'OrderId')
            );
        } else {
            $successOrderNumbers = array_column($this->orderIds, 'order_number');
        }

        $this->createSuccessLogEntries($successOrderNumbers);
        $this->setSkuvaultNotSync($successOrderNumbers);
    }

    protected function setSkuvaultNotSync($successOrderNumbers)
    {
        foreach ($successOrderNumbers as $successOrderNumber) {
            $order = Database::getRepo(Order::class)->findOneBy(['orderNumber' => $successOrderNumber]);
            if ($order) {
                $order->setSkuvaultNotSync(Order::NOT_SYNC_YES);
            }
        }

        Database::getEM()->flush();
    }

    protected function createErrorLogEntries(array $errors)
    {
        foreach ($errors as $error) {
            $logEntry = new Log();
            $logEntry->setDate(time())
                ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
                ->setStatus(SyncStatuses::STATUS_ERROR)
                ->setMessage(var_export($error, true))
                ->setOperation(OperationTypes::TYPE_ADD_SALE);

            Database::getEM()->persist($logEntry);
        }
        Database::getEM()->flush();
    }

    protected function createSuccessLogEntries(array $orderNumbers)
    {
        foreach ($orderNumbers as $orderNumber) {
            $logEntry = new Log();
            $logEntry->setDate(time())
                ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
                ->setStatus(SyncStatuses::STATUS_SUCCESS)
                ->setMessage('Order with number #' . $orderNumber . ' is pushed to SkuVault.')
                ->setOperation(OperationTypes::TYPE_ADD_SALE);

            Database::getEM()->persist($logEntry);
        }
        Database::getEM()->flush();
    }
}
