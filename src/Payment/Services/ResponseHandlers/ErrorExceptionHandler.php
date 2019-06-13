<?php

namespace Mundipagg\Core\Payment\Services\ResponseHandlers;

use Mundipagg\Core\Kernel\Services\LocalizationService;
use Mundipagg\Core\Payment\Aggregates\Order as PaymentOrder;

final class ErrorExceptionHandler extends AbstractResponseHandler
{
    /**
     * @param $error
     * @param PaymentOrder|null $paymentOrder
     * @return mixed
     */
    public function handle($error, PaymentOrder $paymentOrder = null)
    {
        $orderCode = null;
        $exceptionLogMethod = 'exception';
        if ($paymentOrder !== null) {
            $orderCode = $paymentOrder->getCode();
            $this->logService->orderInfo(
                $orderCode,
                "Failed to create order at Mundipagg!"
            );
            $exceptionLogMethod = 'orderException';
        }

        $this->logService->$exceptionLogMethod($error, $orderCode);

        $message =
            'An error occurred when trying to create the order. ' .
            'Please try again. Error Reference: %s';

        if (
            method_exists($error, "getMessage") &&
            !empty($error->getMessage())
        ) {
            $message = $error->getMessage();
        }

        $i18n = new LocalizationService();

        $frontErrorMessage = $i18n->getDashboard(
            $message,
            $paymentOrder->getCode()
        );

        return $frontErrorMessage;
    }
}