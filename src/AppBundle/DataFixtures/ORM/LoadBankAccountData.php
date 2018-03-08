<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\BankAccount;

/**
 * Class LoadBankAccountData
 * @package AppBundle\DataFixtures
 */
class LoadBankAccountData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->addNewBankAccount(0, 0, 0, $manager);
        $this->addNewBankAccount(0, 0, 0, $manager);
    }

    /**
     * @param $balance
     * @param $isClosed
     * @param $i
     * @param $manager
     */
    private function addNewBankAccount($balance, $isClosed, $i, $manager)
    {
        $bankAccount = new BankAccount();
        $bankAccount->setBalance($balance);
        $bankAccount->setClosed($isClosed);
        $manager->persist($bankAccount);
        $manager->flush();

        $this->setReference("bankAccount".$i, $bankAccount);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}