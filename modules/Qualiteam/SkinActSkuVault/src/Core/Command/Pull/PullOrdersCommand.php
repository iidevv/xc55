<?php

namespace Qualiteam\SkinActSkuVault\Core\Command\Pull;

use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\ICommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Model\Log;
use Qualiteam\SkinActSkuVault\Model\StatusesMap;
use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\OperationTypes;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Model\Order;

class PullOrdersCommand implements ICommand
{
    private APIService $api;
    private GetTokens  $getTokens;

    private string $fromDate;

    private string $toDate;

    public function __construct(
        APIService $api,
        GetTokens  $getTokens,
        string     $fromDate,
        string     $toDate
    ) {
        $this->api       = $api;
        $this->getTokens = $getTokens;
        $this->fromDate  = $fromDate;
        $this->toDate    = $toDate;
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        try {
            $result = $this->api->sendRequest('POST', 'sales/getSalesByDate', [
                'body' => json_encode([
                    'DateField'   => 'Modified',
                    'FromDate'    => $this->fromDate,
                    'ToDate'      => $this->toDate,
                    'PageNumber'  => 0,
                    'PageSize'    => 1000,
                    'TenantToken' => $tokens['TenantToken'],
                    'UserToken'   => $tokens['UserToken']
                ])
            ]);

            $this->processResult($result);

            Database::getRepo('XLite\Model\Config')->createOption(
                [
                    'category' => 'Qualiteam\SkinActSkuVault',
                    'name'     => 'skuvault_sales_last_sync_time',
                    'value'    => Converter::time(),
                ]
            );

            Database::getEM()->flush();

        } catch (APIException $e) {
            throw new CommandException('Command failed', 0, $e);
        }
    }

    protected function processResult(array $result): void
    {
        if (!empty($result) && !isset($result['ResponseStatus']['ErrorCode'])) {
            foreach ($result as $sale) {
                $orderNumber = $sale['SellerSaleId'];

                /** @var Order $order */
                $order = Database::getRepo(Order::class)->findOneBy(['orderNumber' => $orderNumber]);
                if ($order) {
                    /** @var StatusesMap $statusesMap */
                    $statusesMap = $order->getStatusesMapSkuvaultToXc($sale['Status']);

                    if (!$statusesMap) {
                        $this->addErrorLogEntry('No status mappings are found for sale status ' . $sale['Status'] . ' (order number ' . $orderNumber . '), skipping');
                    } else {

                        /** @var Order\Status\Payment $paymentStatus */
                        $paymentStatus = Database::getRepo(Order\Status\Payment::class)->findOneBy(['id' => $statusesMap->getXcartPaymentStatus()]);
                        /** @var Order\Status\Shipping $shippingStatus */
                        $shippingStatus = Database::getRepo(Order\Status\Shipping::class)->findOneBy(['id' => $statusesMap->getXcartFullfilmentStatus()]);

                        $order->setPaymentStatus($paymentStatus);
                        $order->setShippingStatus($shippingStatus);

                        $this->addSuccessLogEntry('Set payment status ' . $paymentStatus->getName() . ' for order number ' . $orderNumber);
                        $this->addSuccessLogEntry('Set shipping status ' . $shippingStatus->getName() . ' for order number ' . $orderNumber);
                    }
                } else {
                    $this->addErrorLogEntry('No order found for order number ' . $orderNumber . ', skipping');
                }

            }
        }
    }

    protected function addErrorLogEntry(string $message): void
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_SKUVAULT_TO_XC)
            ->setStatus(SyncStatuses::STATUS_ERROR)
            ->setMessage($message)
            ->setOperation(OperationTypes::TYPE_SYNC_SALE);
        Database::getEM()->persist($logEntry);
    }

    protected function addSuccessLogEntry(string $message): void
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_SKUVAULT_TO_XC)
            ->setStatus(SyncStatuses::STATUS_SUCCESS)
            ->setMessage($message)
            ->setOperation(OperationTypes::TYPE_SYNC_SALE);
        Database::getEM()->persist($logEntry);
    }
}
