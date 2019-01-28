<?php declare(strict_types=1);

namespace Safebeat\Event;

use Safebeat\Entity\MoneyTransaction;
use Symfony\Component\EventDispatcher\GenericEvent;

class MoneyTransactionEvent extends GenericEvent
{
    public const TRANSACTION_CREATED = 'transaction.created';
    public const TRANSACTION_DELETED = 'transaction.deleted';
    public const TRANSACTION_UPDATED = 'transaction.updated';

    public function __construct(MoneyTransaction $subject, array $arguments = [])
    {
        parent::__construct($subject, $arguments);
    }

    public function getTransaction(): MoneyTransaction
    {
        return $this->subject;
    }
}
