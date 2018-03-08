<?php

namespace AppBundle\Adapter;

use AppBundle\Entity\BankAccount;
use AppBundle\Interfaces\BankAccountInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
use AppBundle\Repository\BankAccountRepository;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class BankAccountManager
 * @package AppBundle\Adapter
 *
 * Contains multiple features that allow you to operate on your bank account
 */
class BankAccountManager extends AbstractBankAccountManager implements BankAccountInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var BankAccountRepository
     */
    private $bankAccountRepo;

    /**
     * @var int
     */
    private $accountNumber;

    /**
     * @var int
     */
    private $balance = 0;

    /**
     * @return int
     */
    public function getAccountNumber(): int
    {
        return $this->accountNumber;
    }

    /**
     * @param int $accountNumber
     */
    public function setAccountNumber(int $accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * BankAccountManager constructor.
     * @param EntityManager $entityManager
     * @param Logger $logger
     * @param BankAccountRepository $bankAccountsRepository
     */
    public function __construct(EntityManager $entityManager,Logger $logger, BankAccountRepository $bankAccountsRepository)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->bankAccountRepo = $bankAccountsRepository;
    }


    /**
     * Open new bank account
     * @return int
     */
    public function openAccount() : int
    {
        try {
            $bankAccount = new BankAccount();
            $bankAccount->setBalance(0);
            $bankAccount->setClosed(0);
            $this->entityManager->persist($bankAccount);
            $this->entityManager->flush();
            $this->accountNumber = $bankAccount->getAccountNumber();
            return $this->accountNumber;
        } catch (Exception $e) {
            $this->logger->error("Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete an existing bank account number
     * @return bool
     */
    public function closeAccount() : bool
    {
        try {
            //TODO this keeps on repeating, check later to see if it's bing enough for a util function
            $bankAccount = $this->bankAccountRepo->findOneBy(["accountNumber" => $this->accountNumber]);
            if($bankAccount != null) {
                $bankAccount->setClosed(1);
                $bankAccount->setBalance(0);
                $this->entityManager->flush();
                if($bankAccount->getClosed() == 1) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            $this->logger->error("Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve the bank account balance
     *
     * @return int
     */
    public function getBalance() : int
    {
        try {
            //TODO this keeps on repeating, check later to see if it's bing enough for a util function or a new class property
            $bankAccount = $this->bankAccountRepo->findOneBy(["accountNumber" => $this->accountNumber]);
            if($bankAccount != null) {
                if($bankAccount->getClosed() == 0) {
                    $this->balance = $bankAccount->getBalance();
                    return $this->balance;
                }
            }
            return -1;
        } catch (Exception $e) {
            $this->logger->error("Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Adds the the existing balance a value the user requests
     *
     * @param $amount
     * @return int
     */
    public function deposit($amount) : int
    {
        /* Will never get here for the moment,
         * but better to think ahead for other
         * features that may use this function
         */
        // TODO this is repeating, maybe move it to another method
        if ($amount <= 0) {
            throw new Exception("Amount has to be bigger then 0!");
        } else if ($amount > 10000) {
            throw new Exception("Amount over 10.000 has be to verified!");
        }

        try {
            $bankAccount = $this->bankAccountRepo->findOneBy(["accountNumber" => $this->accountNumber]);
            if($bankAccount != null) {
                $oldAmount = $bankAccount->getBalance();
                $newAmount = $oldAmount+$amount;
                $bankAccount->setBalance($newAmount);
                $this->balance = $newAmount;
                $this->entityManager->flush();
                if($bankAccount->getBalance() == $newAmount) {
                    return 1;
                }
            }
            return 0;
        } catch (Exception $e) {
            $this->logger->error("Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Withdraw money from the existing balance
     *
     * @param $amount
     * @return int
     */
    public function withdraw($amount) : int
    {
        /* Will never get here for the moment,
         * but better to think ahead for other
         * features that may use this function
         */
        // TODO this is repeating, maybe move it to another method
        if ($amount <= 0) {
            throw new Exception("Amount has to be bigger then 0!");
        } else if ($amount > 10000) {
            throw new Exception("Amount over 10.000 has be to verified!");
        }

        $bankAccount = $this->bankAccountRepo->findOneBy(["accountNumber" => $this->accountNumber]);
        if($bankAccount != null) {
            $oldAmount = $bankAccount->getBalance();
            $newAmount = $oldAmount - $amount;
            if ($newAmount < 0) {
                /* Will never get here for the moment,
                 * but better to think ahead for other
                 * features that may use this function
                 */
                throw new Exception("Insufficient founds!");
            }
        }

        try {
            if($bankAccount != null) {
                $bankAccount->setBalance($newAmount);
                $this->balance = $newAmount;
                $this->entityManager->flush();
                if($bankAccount->getBalance() == $newAmount) {
                    return 1;
                }
            }
            return 0;
        } catch (Exception $e) {
            $this->logger->error("Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * User has the option of an overdraft if he wants to withdraw more than his current balance
     *
     * @param $amount
     * @return int
     */
    public function overdraft($amount) : int
    {
        /* Will never get here for the moment,
         * but better to think ahead for other
         * features that may use this function
         */
        if ($amount <= 0) {
            throw new Exception("Withdraw requested value can't be negative!");
        } else if ($amount > 10000) {
            throw new Exception("Withdraw can't be bigger then 10000!");
        }

        try {
            // TODO make bankAccountObject a property that will be initialized only once
            $bankAccount = $this->bankAccountRepo->findOneBy(["accountNumber" => $this->accountNumber]);
            if($bankAccount != null) {
                $oldAmount = $bankAccount->getBalance();
                $newAmount = $oldAmount-$amount;
                if($newAmount > 0) {
                    /* Will never get here for the moment,
                     * but better to think ahead for other
                     * features that may use this function
                     */
                    throw new Exception("Sufficient fonds! No need for overdraft!");
                } else if($newAmount < -1000) {
                    throw new Exception("Overdraft can't be bigger than 1000!");
                } else {
                    $bankAccount->setBalance($newAmount);
                    $this->balance = $newAmount;
                    $this->entityManager->flush();
                    if($bankAccount->getBalance() == $newAmount) {
                        return 1;
                    }
                }
            }
            return 0;
        } catch (Exception $e) {
            $this->logger->error("Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check to see if an account number exists
     *
     * @return bool
     */
    public function isAccountNumber() : bool
    {
        // TODO make bankAccountObject a property that will be initialized only once
        $bankAccount = $this->bankAccountRepo->findOneBy(["accountNumber" => $this->accountNumber]);
        if($bankAccount != null) {
            if(!empty($bankAccount->getAccountNumber())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}