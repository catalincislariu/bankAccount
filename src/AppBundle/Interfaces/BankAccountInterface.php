<?php

namespace AppBundle\Interfaces;

/**
 * Interface BankAccountInterface
 * Provides method declaration for bank accounts
 */
interface BankAccountInterface
{
    /**
     * Open a bank account
     * @return int
     */
    public function openAccount();

    /**
     * If user requires more than his current balance he can get an overdraft
     * @param $amount
     * @return bool
     */
    public function overdraft($amount);

    /**
     * Adds to the balance the value deposit at user request
     * @param $amount
     * @return bool
     */
    public function deposit($amount);

    /**
     * Withdraws money out from account
     * @param $amount
     * @return bool
     */
    public function withdraw($amount);

    /**
     * Retrieve the bank account balance
     * @return int
     */
    public function getBalance();

    /**
     * Delete account based on parameter
     * @return bool
     */
    public function closeAccount();
}