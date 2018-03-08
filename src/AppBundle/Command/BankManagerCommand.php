<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BankManagerCommand
 * @package AppBundle\Command
 */
class BankManagerCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName("bank:account")
            ->setDescription("access my bank account");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper("question");
        $container = $this->getContainer();

        $questionData = [
            "Please chose option:",
            [ 1 => "Create new Bank Account",
              2 => "Select existing bank account by entering unique identifier",
              3 => "Close existing Bank Account",
              4 => "Get Bank Account balance",
              5 => "Make a deposit to your Bank Account",
              6 => "Make a withdraw on your Bank Account",
              7 => "Ask for an overdraft on your Bank Account",
              8 => "Exit"
            ],
            0
        ];
        $option = $container->get("app.service.utils")->getUserInput($helper, $questionData, $input, $output);

        $bankAccount = $container->get("app.service.bank_account_manager");

        switch($option) {
            // Open Account
            case "Create new Bank Account":
                $output->write("\n");
                $accountNumber = $bankAccount->openAccount();
                if($accountNumber) {
                    $output->writeln("Success, you're new bank account number is " . $accountNumber . ". Next requests will be using this account number.");
                    $bankAccount->setAccountNumber($accountNumber);
                    $output->writeln("Your current balance is 0.");
                } else {
                    $output->writeln("Sorry, can't create more accounts at the moment. Please contact support!");
                }
                $output->write("\n");
                break;
            // Select Account
            case "Select existing bank account by entering unique identifier":
                $output->write("\n");
                $question = "Please enter your account Number!\n";
                $accountNumber = $container->get("app.service.utils")->getUserInput($helper, $question, $input, $output);
                $bankAccount->setAccountNumber($accountNumber);
                $output->writeln("Next requests will be executed on bank account number ".$bankAccount->getAccountNumber());
                $output->write("\n");
                break;
            // Close Account
            case "Close existing Bank Account":
                $output->write("\n");
                if($bankAccount->isAccountNumber()) {
                    $bankAccount->closeAccount();
                    $output->writeln("Account number ".$bankAccount->getAccountNumber()." was closed!");
                } else {
                    $output->writeln("Sorry, we can't find your account number, please try again later!");
                }
                $output->write("\n");
                break;
            // Get Balance
            case "Get Bank Account balance":
                $output->write("\n");
                if($bankAccount->isAccountNumber()) {
                    if($bankAccount->getBalance() == -1) {
                        $output->writeln("Account ".$bankAccount->getAccountNumber()." is closed!");
                    } else {
                        $output->writeln("Your balance is " . $bankAccount->getBalance());
                    }
                } else {
                    $output->writeln("Sorry, we can't find your account number, please try again!");
                }
                $output->write("\n");
                break;
            // Deposit
            case "Make a deposit to your Bank Account":
                $output->write("\n");
                if($bankAccount->isAccountNumber()) {
                    if($bankAccount->getBalance() == -1) {
                        $output->writeln("Account ".$bankAccount->getAccountNumber()." is closed!");
                    } else {
                        $output->writeln("Your current balance is " . $bankAccount->getBalance());
                        $question = "Please enter the amount you want to deposit!\n";
                        // TODO this keeps on repeating, need a function
                        $amount = $container->get("app.service.utils")->getUserInput($helper, $question, $input, $output);
                        if($amount <= 0) {
                            $output->writeln("You deposit amount must be bigger than 0, please try again with different value!");
                        } else if($amount > 10000){
                            $output->writeln("You deposit amount can't be bigger than 10.000, please try again with different value!");
                        } else {
                            if($bankAccount->deposit($amount)) {
                                $output->writeln("Your bank amount was updated. Your current balance is " . $bankAccount->getBalance());
                            } else {
                                $output->writeln("Sorry, something went wrong, our support team will investigate this!");
                            }
                        }
                    }
                } else {
                    $output->writeln("Sorry, we can't find your account number, please try again!");
                }
                $output->write("\n");
                break;
            // Withdraw
            case "Make a withdraw on your Bank Account":
                $output->write("\n");
                if($bankAccount->isAccountNumber()) {
                    if($bankAccount->getBalance() == -1) {
                        $output->writeln("Account ".$bankAccount->getAccountNumber()." is closed!");
                    } elseif ($bankAccount->getBalance() < 0) {
                        $output->writeln("You need to ask an overdraft! Your current balance is " . $bankAccount->getBalance());
                    } else {
                        $output->writeln("Your current balance is " . $bankAccount->getBalance());
                        $question = "Please enter the amount you want to withdraw!\n";
                        // TODO this keeps on repeating, need a function
                        $amount = $container->get("app.service.utils")->getUserInput($helper, $question, $input, $output);
                        if($amount <= 0) {
                            $output->writeln("You withdraw amount must be bigger than 0, please try again with different value!");
                        } else if($amount > 10000){
                            $output->writeln("You withdraw amount can't be bigger than 10.000, please try again with different value!");
                        } else if ($amount > $bankAccount->getBalance()) {
                            $output->writeln("Insufficient fonds! Your current balance is " . $bankAccount->getBalance());
                        } else {
                            if($bankAccount->withdraw($amount)) {
                                $output->writeln("Your bank amount was updated. Your current balance is " . $bankAccount->getBalance());
                            } else {
                                $output->writeln("Sorry, something went wrong, our support team will investigate this!");
                            }
                        }
                    }
                } else {
                    $output->writeln("Sorry, we can't find your account number, please try again!");
                }
                $output->write("\n");
                break;
            // Overdraft
            case "Ask for an overdraft on your Bank Account":
                $output->write("\n");
                if($bankAccount->isAccountNumber()) {
                    if($bankAccount->getBalance() == -1) {
                        $output->writeln("Account ".$bankAccount->getAccountNumber()." is closed!");
                    } elseif ($bankAccount->getBalance() == -1000) {
                            $output->writeln("Overdraft limit reached! Your current balance is " . $bankAccount->getBalance());
                    } else {
                        $output->writeln("Your current balance is " . $bankAccount->getBalance());
                        $question = "Please enter the amount you want to withdraw!\n";
                        // TODO this keeps on repeating, need a function
                        $amount = $container->get("app.service.utils")->getUserInput($helper, $question, $input, $output);
                        $balance = $bankAccount->getBalance();
                        $overdraft = $balance - $amount;
                        if($amount <= 0) {
                            $output->writeln("You withdraw amount must be bigger than 0, please try again with different value!");
                        } else if($amount > 10000){
                            $output->writeln("You withdraw amount can't be bigger than 10.000, please try again with different value!");
                        } else if ($amount <= $bankAccount->getBalance()) {
                            $output->writeln("Sufficient fonds! No need for overdraft! Your current balance is " . $bankAccount->getBalance());
                        } else {
                            if($overdraft < -1000){
                                $output->writeln("We are sorry, we can't accept an overdraft bigger than 1000!");
                            } else {
                                $bankAccount->overdraft($amount);
                                $output->writeln("Your bank amount was updated. Your overdraft is " . $bankAccount->getBalance()*-1);
                            }
                        }
                    }
                } else {
                    $output->writeln("Sorry, we can't find your account number, please try again!");
                }
                $output->write("\n");
                break;
            case "Exit":
                exit();
                break;
            default:
                $output->writeln("Please chose one of the given option!");
        }
        $this->execute($input, $output);
    }
}