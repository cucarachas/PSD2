<?php
namespace Src\Controllers\API;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use PDOException;
use Src\Database\Database;
use Src\Repositories\Payment;

class NBG
{
    private $http;

    public function __construct()
    {
        $this->http = new Client();
    }

    public function token(){
        try {
            $options = [
                'form_params' => [
                    'client_id' => getenv('NBG_CLIENT_ID'),
                    'client_secret' => getenv('NBG_CLIENT_SECRET'),
                    'grant_type' => getenv('NBG_TOKEN_GRANT_TYPE'),
                    'scope' => getenv('NBG_TOKEN_SCOPE')
                ]
            ];
            $response = $this->http->post(getenv('NBG_TOKEN_URL'), $options);

            if ($response->getStatusCode() == 200){
                $a = json_decode($response->getBody(), true);

                $payment = new Payment();
                $payment->setToken($a);
            }

        }catch (ClientException $e){
            die($e->getMessage());
        } catch (GuzzleException $e) {
            die($e->getMessage());
        }
    }

    public function sandbox(){
        try {
            $token = (new Payment())->getToken();
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Client-Id' => getenv('NBG_CLIENT_ID'),
                    'Accept' => 'application/json'
                ]
            ];

            $response = $this->http->get(getenv('NBG_SANDBOX_URL').getenv('NBG_SANDBOX_ID'), $options);
            if ($response->getStatusCode() == 200){
                return $response->getBody();
            }
        }catch (ClientException $e){
            die($e->getMessage());
        } catch (GuzzleException $e) {
            die($e->getMessage());
        }
    }

    public function status($url){
        $token = (new Payment())->getToken();
        try {
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'X-Request-ID' => getenv('NBG_PAYMENT_GUID'),
                    'PSU-IP-Address' => getenv('NBG_PAYMENT_IP'),
                    'sandbox-id' => 'my-payments-sandbox',
                    'Client-Id' => getenv('NBG_CLIENT_ID'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ];

            $response = $this->http->get($url, $options);

            if ($response->getStatusCode() == 200){
                return json_decode($response->getBody(), true);
            }
        }catch (ClientException $e){
            return ['error' => true, 'msg' => $e->getMessage()];
        } catch (GuzzleException $e) {
            return ['error' => true, 'msg' => $e->getMessage()];
        }
    }

    public function payment($values){
        try {
            $token = (new Payment())->getToken();
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'X-Request-ID' => getenv('NBG_PAYMENT_GUID'),
                    'PSU-IP-Address' => getenv('NBG_PAYMENT_IP'),
                    'sandbox-id' => 'my-payments-sandbox',
                    'Client-Id' => getenv('NBG_CLIENT_ID'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'body' => json_encode(
                    [
                        'endToEndIdentification' => 'endToEndIdentification',
                        'debtorAccount' => [
                            'iban' => $values['debtor'],
                            'currency' => 'EUR'
                        ],
                        'instructedAmount' => [
                            'currency' => 'EUR',
                            'amount' => number_format($values['amount'], 2, '.', '')
                        ],
                        'creditorAccount' => [
                            'iban' => $values['creditor'],
                            'currency' => 'EUR'
                        ],
                        'creditorName' => 'CREDITOR NAME',
                        'remittanceInformationUnstructured' => 'Payment from App',
                        'chargeBearer' => 'Shared',
                        'priorityCode' => 'Normal'
                    ]
                )
            ];

            $response = $this->http->post(getenv('NBG_PAYMENT_URL'), $options);
            if ($response->getStatusCode() == 201){ // Created
                return $response->getBody();
            }
        }catch (ClientException $e){
            die($e->getMessage());
        } catch (GuzzleException $e) {
            die($e->getMessage());
        }
    }
}
