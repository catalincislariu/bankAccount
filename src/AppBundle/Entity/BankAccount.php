<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Repository\BankAccountRepository;

/**
 * Class BankAccounts
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="bank_accounts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BankAccountRepository")
 */
class BankAccount
{
    /**
     * @var integer
     * For the purpose of the example, account will be autoincrement & primary key
     *
     * @ORM\Column(name="account_number", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $accountNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="balance", type="integer")
     */
    private $balance;

    /**
     * @var bool
     *
     * @ORM\Column(name="closed", type="boolean")
     */
    private $closed;

    /**
     * @return int
     */
    public function getAccountNumber(): int
    {
        return $this->accountNumber;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @param int $balance
     */
    public function setBalance(int $balance)
    {
        $this->balance = $balance;
    }

    /**
     * Set closed
     *
     * @param boolean $closed
     *
     * @return BankAccount
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return boolean
     */
    public function getClosed() : bool
    {
        return $this->closed;
    }
}
