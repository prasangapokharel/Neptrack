<?php
// HTTP Client configuration using Guzzle for high performance

require_once BASE_PATH . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpClient {
    private $client;
    
    public function __construct() {
        $this->client = new Client([
            'timeout' => 10,
            'connect_timeout' => 5,
            'http_errors' => false,
            'headers' => [
                'User-Agent' => 'DailyProteinTracker/1.0',
                'Accept' => 'application/json'
            ]
        ]);
    }
    
    public function get($url, $params = []) {
        try {
            $response = $this->client->get($url, [
                'query' => $params
            ]);
            
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody(), true)
            ];
        } catch (RequestException $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function post($url, $data = []) {
        try {
            $response = $this->client->post($url, [
                'json' => $data
            ]);
            
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody(), true)
            ];
        } catch (RequestException $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>

