<?php

namespace Src\Controllers;

use Src\Controllers\API\NBG;
use Src\Database\Database;
use Src\Repositories\Payment;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AppController
{
    private $twig;

    public function __construct(){
        $loader = new FilesystemLoader(getenv('APP_PUBLIC'));
        $this->twig = new Environment($loader);
    }

    public function index(){
        $client = new NBG();

        // $client->token();

        $sandbox = $client->sandbox();
        $sandbox = json_decode($sandbox, true);

        echo $this->twig->render('index.twig', $sandbox);
    }

    public function payments(){
        $db = new Payment();
        $payments = $db->getPayments();

        $nbg = new NBG();
        foreach ($payments as $key => $payment){
            $payments[$key]['links'] = json_decode($payment['links'], true);
            $status_info = $nbg->status($payments[$key]['links']['status']['href']);
            if (!isset($status_info['error'])){
                $payments[$key]['status'] = $status_info['transactionStatus'];
            }
        }

        echo $this->twig->render('payments.twig', ['payments' => $payments]);
    }

    public function payment(){
        $nbg = new NBG();
        $payment = $nbg->payment($_POST);
        if ($payment){
            $db = new Payment();
            $db->setPayment(json_decode($payment, true));

            echo json_encode(['error' => false]);
        }else{
            echo json_encode(['error' => true]);
        }
    }

}
