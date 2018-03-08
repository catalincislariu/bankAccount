<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Adapter\BankAccountManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class DefaultControllerTest
 * @package Tests\AppBundle\Controller
 */
class DefaultControllerTest extends KernelTestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var BankAccountManager
     */
    private $bankAccount;

    /**
     * @var integer
     */
    private $accountNumber;

    /**
     * First run method
     */
    protected function setUp()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
        $this->bankAccount = $this->container->get("app.service.bank_account_manager");
    }

    /**
     * Open account test
     */
    public function testOpenAccount()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->assertTrue(is_numeric($this->accountNumber));
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * Close account test
     */
    public function testCloseAccount()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->setAccountNumber($this->accountNumber);
        $this->assertTrue($this->bankAccount->closeAccount());
    }

    /**
     * Make sure we start with balance 0
     */
    public function testBalanceIs0AtStart()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $balance = $this->bankAccount->getBalance();
        $this->assertEquals($balance, 0);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @cover deposit
     */
    public function testDeposit()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->deposit(10);
        $this->assertEquals($this->bankAccount->getBalance(), 10);
        $this->bankAccount->deposit(20);
        $this->assertEquals($this->bankAccount->getBalance(), 30);
        $this->bankAccount->deposit(40);
        $this->assertEquals($this->bankAccount->getBalance(), 70);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Amount over 10.000 has be to verified!
     */
    public function testDepositToBig()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->deposit(10001);
        $this->assertEquals($this->bankAccount->getBalance(), 10001);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Amount has to be bigger then 0!
     */
    public function testDepositNegative()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->deposit(-7);
        $this->assertEquals($this->bankAccount->getBalance(), -7);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @cover withdraw
     */
    public function testWithDraw()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->deposit(20);
        $this->bankAccount->withdraw(3);
        $this->assertEquals($this->bankAccount->getBalance(), 17);
        $this->bankAccount->withdraw(10);
        $this->assertEquals($this->bankAccount->getBalance(), 7);
        $this->bankAccount->withdraw(7);
        $this->assertEquals($this->bankAccount->getBalance(), 0);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Amount has to be bigger then 0!
     */
    public function testWithDraw0AndNegative()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->deposit(20);
        $this->bankAccount->withdraw(0);
        $this->bankAccount->withdraw(-1);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Insufficient founds!
     */
    public function testBiggerWithDrawThanBalance()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->withdraw(10);
        $this->bankAccount->withdraw(30);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @cover overdraft
     */
    public function testOverdraft()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->deposit(20);
        $this->bankAccount->overdraft(21);
        $this->assertEquals($this->bankAccount->getBalance(), -1);
        $this->bankAccount->overdraft(99);
        $this->assertEquals($this->bankAccount->getBalance(), -100);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Withdraw can't be bigger then 10000!
     */
    public function testOverdraftToBig()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->overdraft(10001);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Withdraw requested value can't be negative!
     */
    public function testOverdraftNegative()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $this->bankAccount->overdraft(-1);
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
    }

    /**
     * @cover isAccountNumber
     */
    public function testIsAccountNumber()
    {
        $this->accountNumber = $this->bankAccount->openAccount();
        $accountExists = $this->bankAccount->isAccountNumber($this->accountNumber);
        $this->assertTrue($accountExists);
    }

    /**
     * Run at the end after tests
     */
    protected function tearDown()
    {
        // Delete account
        $this->bankAccount->closeAccount($this->accountNumber);
        $this->bankAccount = null;
        $this->container = null;
        $this->accountNumber = null;
    }
}
