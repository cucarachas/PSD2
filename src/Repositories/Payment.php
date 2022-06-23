<?php

namespace Src\Repositories;

use PDO;
use PDOException;
use Src\Database\Database;

class Payment implements PaymentInterface
{
    private $db;

    public function __construct(){
        $instance = Database::getInstance();
        $this->db = $instance->getConnection();
    }

    function setPayment(array $payment)
    {
        try {
            $sql = "INSERT INTO payments (id, status, priority, debtor_iban, creditor_iban, amount, links) 
                    VALUES (:id, :status, :priority, :debtor_iban, :creditor_iban, :amount, :links)";
            $q = $this->db->prepare($sql);
            $q->execute([
                ':id' => $payment['paymentId'],
                ':status' => $payment['transactionStatus'],
                ':priority' => $payment['priorityCode'],
                ':debtor_iban' => $payment['debtorAccount']['iban'],
                ':creditor_iban' => $payment['creditorAccount']['iban'],
                ':amount' => $payment['instructedAmount']['amount'],
                ':links' => json_encode($payment['_links'], JSON_UNESCAPED_UNICODE),
            ]);

            return ['error' => false];
        }catch (PDOException $e){
            return ['error' => true, 'msg' => $e->getMessage()];
        }
    }

    function getPayment()
    {
        // TODO: Implement getPayment() method.
    }

    function getPayments(){
        $sql = "SELECT * FROM payments ORDER BY date_added DESC";
        $q = $this->db->query($sql);

        return $q->fetchAll();
    }

    function setToken(array $token)
    {
        try {
            $sql = "REPLACE INTO access_token (token, expires_in, token_type, date_expired) VALUES (:token, :expires_in, :token_type, :date_expired)";
            $q = $this->db->prepare($sql);
            $q->execute([
                ':token' => $token['access_token'],
                ':expires_in' => $token['expires_in'],
                ':token_type' => $token['token_type'],
                ':date_expired' => date('Y-m-d H:i:s', time() + $token['expires_in'])
            ]);
        }catch (PDOException $e){
            die($e->getMessage());
        }
    }

    function getToken()
    {
        try {
            $sql = "SELECT token FROM access_token";
            $q = $this->db->query($sql);

            return $q->fetchColumn();
        }catch (PDOException $e){
            die($e->getMessage());
        }
    }
}
