<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Crypto extends Controller
{
    /**
     * @throws GuzzleException
     */
    public function getCryptoExchangeRates(Request $request) : JsonResponse {
        $client = new Client();

        $cryptoData = $client->get('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,litecoin&vs_currencies=usd,eur');
        $cryptoData = json_decode($cryptoData->getBody()->getContents(), true);

        return new JsonResponse(["exchangeRates" => $cryptoData]);
    }
}
