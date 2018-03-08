<?php

namespace AppBundle\Controller;

use AppBundle\Adapter\BankAccount;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        die("This application is terminal only, please go to terminal and type bin/console bank:account!");
    }
}
