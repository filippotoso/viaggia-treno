<?php

namespace FilippoToso\ViaggiaTrenoAPI;

use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Exception\BadResponseException;
use Carbon\Carbon;

class Client
{

    /**
     * Execute an HTTP GET request to Qwant API
     * @param  String $url The url of the API endpoint
     * @return Array|FALSE  The result of the request
     */
    protected function getJSON($url) {

        $client = new HTTPClient();

        try {
            $res = $client->request('GET', $url, [
                'headers' => [
                    'Accept'     => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
                ],
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Execute an HTTP GET request to Qwant API
     * @param  String $url The url of the API endpoint
     * @return Array|FALSE  The result of the request
     */
    protected function get($url) {

        $client = new HTTPClient();

        try {
            $res = $client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
                ],
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        return (string)$res->getBody();

    }


    /**
     * Execute an HTTP POST request to Qwant API
     * @param  String $url The url of the API endpoint
     * @param  Array $data The parameters of the request
     * @return Array|FALSE  The result of the request
     */
    protected function postJSON($url, $data) {

        $client = new HTTPClient();

        try {
            $res = $client->request('POST', $url, [
                'json' => $data,
                'headers' => [
                    'Accept'     => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
                ],
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Execute an HTTP POST request to Qwant API
     * @param  String $url The url of the API endpoint
     * @param  Array $data The parameters of the request
     * @return Array|FALSE  The result of the request
     */
    protected function post($url, $data) {

        $client = new HTTPClient();

        try {
            $res = $client->request('POST', $url, [
                'json' => $data,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0',
                ],
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        return (string)$res->getBody();

    }

    /**
     * Generate an API url based on the provided path
     * @method getUrl
     * @param  String $path The api call path
     * @return String      The result API url
     */
    protected function getUrl($path) {
        return sprintf('http://www.viaggiatreno.it/viaggiatrenonew/resteasy/viaggiatreno/%s', $path);

    }

    /**
     * Get the number of train and starting station
     * @method cercaNumeroTrenoTrenoAutocomplete
     * @param  String  $number The number of the train or station
     * @return Array   Array with train, station and station code
     */
    public function cercaNumeroTrenoTrenoAutocomplete($number) {
        $url = $this->getUrl(sprintf('cercaNumeroTrenoTrenoAutocomplete/%s', urlencode($number)));
        $content = $this->get($url);

        $data = FALSE;
        $pattern = '#(\d+)\s*-\s*([^\|]+)\|(\d+)-([^\s]+)#si';
        if (preg_match($pattern, $content, $matches)) {
            $data = [
                'train' => $matches[1],
                'station' => $matches[2],
                'station_code' => $matches[4],
            ];
        }

        return $data;

    }

    /**
     * Get the train current status
     * @method andamentoTreno
     * @param  String         $station The starting station of the train (check cercaNumeroTrenoTrenoAutocomplete())
     * @param  String         $train   The train code
     * @return Array                   The train status
     */
    public function andamentoTreno($station, $train) {
        $url = $this->getUrl(sprintf('andamentoTreno/%s/%s', urlencode($station), urlencode($train)));
        $data = $this->getJSON($url);
        return $data;
    }

    /**
     * Get the train stops details
     * @method andamentoTreno
     * @param  String         $station The starting station of the train (check cercaNumeroTrenoTrenoAutocomplete())
     * @param  String         $train   The train code
     * @return Array                   The train stops details
     */
    public function tratteCanvas($station, $train) {
        $url = $this->getUrl(sprintf('tratteCanvas/%s/%s', urlencode($station), urlencode($train)));
        $data = $this->getJSON($url);
        return $data;
    }

    /**
     * Get the train departures from the provided station details
     * @method partenze
     * @param  String                  $station  The station for which get the departures
     * @param  String|DateTime|Carbon  $time     The time of departure
     * @return Array                   The train stops details
     */
    public function partenze($station, $time) {

        if (is_string($time)) {
            $time = Carbon::parse($time);
        } elseif (is_a($time, DateTime::class)) {
            $time = Carbon::instance($time);
        } elseif (!is_a($time, Carbon::class)) {
            throw new Exception('Invalid time.');
        }

        // DON'T encode the date, otherwise it will not work (don't ask me why)!
        $url = $this->getUrl(sprintf('partenze/%s/%s', urlencode($station), $time->format('D M j Y H:i:s \G\M\TO')));
        $data = $this->getJSON($url);
        return $data;

    }

}
