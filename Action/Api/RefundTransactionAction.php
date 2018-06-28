<?php
namespace Payum\Paypal\ExpressCheckout\Nvp\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\LogicException;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\RefundTransaction;

class RefundTransactionAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request RefundTransaction */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (null === $model['TRANSACTIONID']) {
            throw new LogicException('TRANSACTIONID must be set.');
        }
        if (null === $model['REFUNDTYPE']) {
            throw new LogicException('REFUNDTYPE must be set.');
        }
        if ('Partial' === $model['REFUNDTYPE']) {
            if (null === $model['AMT']) {
                throw new LogicException('AMT must be set for partial refund.');
            }
            if (null === $model['CURRENCYCODE']) {
                throw new LogicException('CURRENCYCODE must be set for partial refund.');
            }
            if (null === $model['NOTE']) {
                throw new LogicException('NOTE must be set for partial refund.');
            }
        }

        $model->replace(
            $this->api->refundTransaction((array) $model)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof RefundTransaction &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
