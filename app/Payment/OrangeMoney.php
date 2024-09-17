<?php
namespace App\Payment;

use Illuminate\Support\Facades\Http;
use App\Models\{Paiement, Demande, TypePaiement, Etape};
use Illuminate\Support\Str;

class OrangeMoney
{
    protected $baseUrl = "https://api.orange.com/";
    ///orange-money-webpay/gn/v1/webpayment
    protected $accessToken = false;
    protected $payToken;
    protected $paymentUrl;
    protected $notifToken;
    protected $merchantKey = "0fcd306a";

    protected $basicToken = "SjRYM1JFaXdld2RqalM0WHpiOTlmSWxBYmxzNTF5RG46MWZGSXpMS1FjaFpQN0R3dw==";

    protected $lassErrorMessage;
    protected $status;

    function getLassErrorMessage()
    {
        //return $this->lassErrorMessage;
        return json_decode($this->lassErrorMessage, true)['description'];
    }

    function getPayToken()
    {
        return $this->payToken;
    }

    function getPaymentUrl()
    {
        return $this->paymentUrl;
    }

    function getNotifToken()
    {
        return $this->notifToken;
    }

    function getAccessToken()
    {
        $response = Http::withHeaders([
            "Authorization" => "Basic {$this->basicToken}",
            "Content-Type" => "application/x-www-form-urlencoded",
        ])->asForm()->post("{$this->baseUrl}oauth/v3/token", [
            'grant_type' => 'client_credentials',
        ]);

        if($response->successful() AND isset($response->json()['access_token'])) {
            $this->accessToken = $response->json()['access_token'];
            return true;
        }

        $this->lassErrorMessage = $response->body();

        return false;
    }

    function getStatus() {
        return $this->status;
    }

    function makePayment(Demande $demande, TypePaiement $type, Etape $etape)
    {
        if(!$this->getAccessToken() OR !$type OR !$demande OR !$etape) return false;

        $url = "https://magel.andegn.com/";

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$this->accessToken}",
            "Content-Type" => "application/x-www-form-urlencoded",
            "Accept" => "application/json",
        ])->post("{$this->baseUrl}orange-money-webpay/gn/v1/webpayment", [
            "merchant_key" => $this->merchantKey,
            "currency" => "GNF",
            "order_id" => Str::uuid(),
            "amount" => $type->montant,
            "return_url" => $url,
            "cancel_url" => $url,
            "notif_url" => url("api/payment-om/{$type->uuid}/{$demande->uuid}/{$etape->uuid}"),
            "lang" => "fr",
            "reference" => "MAGEL - {$type->nom}"
        ]);

        if($response->successful() AND isset($response->json()['status']) AND $response->json()['status'] == 201) {
            $this->paymentUrl = $response->json()['payment_url'];
            $this->payToken = $response->json()['pay_token'];
            $this->notifToken = $response->json()['notif_token'];

            $demande->update(['notif_token' => $this->notifToken]);

            $this->status = true;

            return true;
        }

        $this->lassErrorMessage = $response->body();

        $this->status = false;

        return false;
    }
}
