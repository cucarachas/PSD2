<?php

namespace Src\Repositories;

interface PaymentInterface
{
    function setPayment(array $payment);
    function getPayment();
    function setToken(array $token);
    function getToken();
}
