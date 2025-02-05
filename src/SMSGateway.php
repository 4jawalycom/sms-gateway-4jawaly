<?php

namespace Jawalycom\SMSGateway4Jawaly;

use GuzzleHttp\Client;
use Exception;

class SMSGateway
{
    protected $config;
    protected $client;
    protected $baseUrl = 'https://api-sms.4jawaly.com/api/v1';

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client();
    }

    /**
     * Get the authentication headers
     */
    protected function getHeaders()
    {
        $app_hash = base64_encode("{$this->config['username']}:{$this->config['password']}");
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => "Basic {$app_hash}"
        ];
    }

    /**
     * Send SMS message
     */
    public function send($numbers, $message, $sender = null)
    {
        try {
            $sender = $sender ?? $this->config['default_sender'];
            
            $payload = [
                'messages' => [
                    [
                        'text' => $message,
                        'numbers' => is_array($numbers) ? $numbers : [$numbers],
                        'sender' => $sender
                    ]
                ]
            ];

            $response = $this->client->post($this->baseUrl . '/account/area/sms/send', [
                'headers' => $this->getHeaders(),
                'json' => $payload
            ]);

            $status_code = $response->getStatusCode();
            $response_data = json_decode($response->getBody(), true);

            if ($status_code == 200) {
                if (isset($response_data['messages'][0]['err_text'])) {
                    throw new Exception($response_data['messages'][0]['err_text']);
                }
                return [
                    'success' => true,
                    'job_id' => $response_data['job_id'],
                    'data' => $response_data
                ];
            } elseif ($status_code == 400) {
                throw new Exception($response_data['message']);
            } elseif ($status_code == 422) {
                throw new Exception('نص الرسالة فارغ');
            } else {
                throw new Exception("خطأ في الاتصال. Status code: {$status_code}");
            }
        } catch (Exception $e) {
            throw new Exception('Failed to send SMS: ' . $e->getMessage());
        }
    }

    /**
     * Get account balance and packages
     */
    public function getBalance($options = [])
    {
        try {
            $query = array_merge([
                'is_active' => 1,
                'order_by' => 'id',
                'order_by_type' => 'desc',
                'page' => 1,
                'page_size' => 10,
                'return_collection' => 1
            ], $options);

            $response = $this->client->get($this->baseUrl . '/account/area/me/packages', [
                'headers' => $this->getHeaders(),
                'query' => $query
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to get balance: ' . $e->getMessage());
        }
    }

    /**
     * Get sender names
     */
    public function getSenderNames($options = [])
    {
        try {
            $query = array_merge([
                'page_size' => 10,
                'page' => 1,
                'status' => 1,
                'return_collection' => 1
            ], $options);

            $response = $this->client->get($this->baseUrl . '/account/area/senders', [
                'headers' => $this->getHeaders(),
                'query' => $query
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to get sender names: ' . $e->getMessage());
        }
    }
}
