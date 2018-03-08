<?php

namespace AppBundle\Adapter;

use AppBundle\Interfaces\BankAccountInterface;
use AppBundle\Entity\BankAccount;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class AbstractBankAccountManager
 * @package AppBundle\Adapter
 */
class AbstractBankAccountManager implements BankAccountInterface
{
    /**
     * @var null
     */
    private $balance = null;

    /**
     * @var
     */
    private $successor;

    /**
     * AbstractBankAccountManager constructor.
     * @param $openingBalance
     */
    public function __construct($openingBalance)
    {
        $this->balance = $openingBalance;
    }

    /**
     * @param BankAccount $account
     */
    public function setNext(BankAccount $account)
    {
        $this->successor = $account;
    }

    /**
     * TODO implement Chain of Responsibility pattern
     * to get urgent money and check if the sum required
     * is in bitcoin, bank or paypal account
     * and withdraw the money from the right account
     */
    public function pay(float $amountToPay)
    {
        if ($this->canPay($amountToPay)) {
            echo sprintf('Paid %s using %s' . PHP_EOL, $amountToPay, get_called_class());
        } elseif ($this->successor) {
            echo sprintf('Cannot pay using %s. Proceeding ..' . PHP_EOL, get_called_class());
            $this->successor->pay($amountToPay);
        } else {
            throw new Exception('None of the accounts have enough balance');
        }
    }

    /**
     * @param $amount
     * @return bool
     */
    public function canPay($amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * @param $amount
     * @param BankAccount $account
     */
    public function transfer($amount, BankAccount $account)
    {
        // implement this method
    }

    /**
     * Open a bank account
     * @return int
     */
    public function openAccount()
    {
        // implement this method
    }

    /**
     * If user requires more than his current balance he can get an overdraft
     * @param $amount
     * @return bool
     */
    public function overdraft($amount)
    {
        if($amount <= 1000) {
            $this->balance -= $amount;
        }
    }

    /**
     * Adds to the balance the value deposit at user request
     * @param $amount
     * @return bool
     */
    public function deposit($amount)
    {
        $this->balance += $amount;
    }

    /**
     * Withdraws money out from account
     * @param $amount
     * @return bool
     */
    public function withdraw($amount)
    {
        $this->balance -= $amount;
    }

    /**
     * Retrieve the bank account balance
     * @return int
     */
    public function getBalance()
    {
        return $this->balance();
    }

    /**
     * Delete account based on parameter
     * @return bool
     */
    public function closeAccount()
    {
        // implement this method
    }
}