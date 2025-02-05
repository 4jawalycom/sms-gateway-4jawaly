<?php

namespace Jawalycom\SMSGateway4Jawaly;

use GuzzleHttp\Client;
use Exception;

class SMSGateway
{
    protected $config;
    protected $client;
    protected $baseUrl = 'http://www.4jawaly.net/api';

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client();
    }

    public function send($to, $message, $sender = null)
    {
        try {
            $sender = $sender ?? $this->config['default_sender'];
            $response = $this->client->post($this->baseUrl . '/sendsms.php', [
                'form_params' => [
                    'username' => $this->config['username'],
                    'password' => $this->config['password'],
                    'sender'   => $sender,
                    'numbers'  => $this->formatNumbers($to),
                    'message'  => $message,
                    'unicode'  => 'e',
                    'return'   => 'json'
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to send SMS: ' . $e->getMessage());
        }
    }

    public function getBalance()
    {
        try {
            $response = $this->client->get($this->baseUrl . '/getbalance.php', [
                'query' => [
                    'username' => $this->config['username'],
                    'password' => $this->config['password'],
                    'return'   => 'json'
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to get balance: ' . $e->getMessage());
        }
    }

    public function getSenderNames()
    {
        try {
            $response = $this->client->get($this->baseUrl . '/sender/get.php', [
                'query' => [
                    'username' => $this->config['username'],
                    'password' => $this->config['password'],
                    'return'   => 'json'
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to get sender names: ' . $e->getMessage());
        }
    }

    protected function formatNumbers($numbers)
    {
        if (is_array($numbers)) {
            return implode(',', array_map([$this, 'formatSingleNumber'], $numbers));
        }
        return $this->formatSingleNumber($numbers);
    }

    protected function formatSingleNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (substr($number, 0, 2) !== '966') {
            $number = '966' . ltrim($number, '0');
        }
        return $number;
    }
}
