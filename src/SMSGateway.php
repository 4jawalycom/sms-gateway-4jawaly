<?php

namespace Jawalycom\SMSGateway4Jawaly;

use GuzzleHttp\Client;
use Exception;

/**
 * فئة بوابة إرسال الرسائل النصية
 * SMS Gateway Class
 * ایس ایم ایس گیٹ وے کلاس
 */
class SMSGateway
{
    protected $config;
    protected $client;
    protected $baseUrl = 'https://api-sms.4jawaly.com/api/v1';

    /**
     * تهيئة الفئة مع الإعدادات
     * Initialize class with configuration
     * کنفیگریشن کے ساتھ کلاس کو شروع کریں
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client();
    }

    /**
     * الحصول على ترويسات المصادقة
     * Get authentication headers
     * مصادقت کے ہیڈرز حاصل کریں
     */
    protected function getHeaders()
    {
        $app_hash = base64_encode("{$this->config['api_key']}:{$this->config['api_secret']}");
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => "Basic {$app_hash}"
        ];
    }

    /**
     * نتيجة إرسال الرسائل النصية
     * SMS Sending Result Class
     * ایس ایم ایس بھیجنے کا نتیجہ
     */
    private function createSMSResult()
    {
        return new class {
            private $total_success = 0;
            private $total_failed = 0;
            private $errors = [];
            private $job_ids = [];

            public function addBatchResult($numbers, $response, $status_code) {
                if ($status_code == 200) {
                    if (isset($response["job_id"])) {
                        $this->job_ids[] = $response["job_id"];
                    }
                    
                    if (isset($response["messages"][0]["err_text"])) {
                        $this->addError($numbers, $response["messages"][0]["err_text"]);
                        $this->total_failed += count($numbers);
                    } else {
                        $success_count = count($numbers);
                        if (isset($response["messages"][0]["error_numbers"]) && !empty($response["messages"][0]["error_numbers"])) {
                            foreach ($response["messages"][0]["error_numbers"] as $error) {
                                $this->addError([$error["number"]], $error["error"]);
                                $success_count--;
                                $this->total_failed++;
                            }
                        }
                        $this->total_success += $success_count;
                    }
                } else {
                    $this->addError($numbers, "خطأ في الإرسال - Status code: {$status_code}");
                    $this->total_failed += count($numbers);
                }
            }

            private function addError($numbers, $error) {
                if (!isset($this->errors[$error])) {
                    $this->errors[$error] = [];
                }
                $this->errors[$error] = array_merge($this->errors[$error], $numbers);
            }

            public function generateReport() {
                $report = "";
                
                $report .= "\n=== ملخص الإرسال | Sending Summary | بھیجنے کا خلاصہ ===\n";
                $report .= "إجمالي الرسائل الناجحة | Total Success | کل کامیابی: " . $this->total_success . "\n";
                $report .= "إجمالي الرسائل الفاشلة | Total Failed | کل ناکامی: " . $this->total_failed . "\n";
                
                if (!empty($this->errors)) {
                    $report .= "\n=== تفاصيل الأخطاء | Error Details | خرابیوں کی تفصیلات ===\n";
                    foreach ($this->errors as $error => $numbers) {
                        $report .= "الخطأ | Error | خرابی: " . $error . "\n";
                        $report .= "الأرقام المتأثرة | Affected Numbers | متاثرہ نمبر: " . implode(", ", $numbers) . "\n\n";
                    }
                }
                
                return $report;
            }

            public function toArray() {
                return [
                    'success' => $this->total_failed === 0,
                    'total_success' => $this->total_success,
                    'total_failed' => $this->total_failed,
                    'job_ids' => $this->job_ids,
                    'errors' => $this->errors
                ];
            }
        };
    }

    /**
     * إرسال رسالة SMS
     * Send SMS message
     * ایس ایم ایس پیغام بھیجیں
     * 
     * @param array|string $numbers الأرقام | Numbers | نمبرز
     * @param string $message الرسالة | Message | پیغام
     * @param string|null $sender اسم المرسل | Sender name | بھیجنے والے کا نام
     * @return array نتيجة الإرسال | Sending result | بھیجنے کا نتیجہ
     */
    public function send($numbers, $message, $sender = null)
    {
        try {
            $sender = $sender ?? $this->config['default_sender'];
            $numbers = is_array($numbers) ? $numbers : [$numbers];
            
            // إنشاء كائن النتيجة | Create result object | نتیجہ آبجیکٹ بنائیں
            $result = $this->createSMSResult();
            
            // تقسيم الأرقام إلى مجموعات | Split numbers into groups | نمبروں کو گروپوں میں تقسیم کریں
            $batch_size = count($numbers) <= 5 ? 1 : (count($numbers) <= 100 ? 5 : 100);
            $number_chunks = array_chunk($numbers, $batch_size);
            
            // تهيئة multi curl | Initialize multi curl | ملٹی کرل کو شروع کریں
            $mh = curl_multi_init();
            $channels = [];
            
            // إعداد طلب لكل مجموعة | Setup request for each group | ہر گروپ کے لئے درخواست تیار کریں
            foreach ($number_chunks as $chunk_index => $number_chunk) {
                $payload = [
                    'messages' => [
                        [
                            'text' => $message,
                            'numbers' => $number_chunk,
                            'sender' => $sender
                        ]
                    ]
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/account/area/sms/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Basic ' . base64_encode("{$this->config['api_key']}:{$this->config['api_secret']}")
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                curl_multi_add_handle($mh, $ch);
                $channels[$chunk_index] = [
                    'handle' => $ch,
                    'numbers' => $number_chunk
                ];
            }
            
            // تنفيذ الطلبات بشكل متوازي | Execute requests in parallel | درخواستوں کو متوازی طور پر چلائیں
            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            
            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($mh) != -1) {
                    do {
                        $mrc = curl_multi_exec($mh, $active);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }
            
            // معالجة النتائج | Process results | نتائج کی پروسیسنگ
            foreach ($channels as $chunk_index => $channel) {
                $ch = $channel['handle'];
                $numbers = $channel['numbers'];
                
                $response = curl_multi_getcontent($ch);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $response_json = json_decode($response, true);
                
                // تحديث النتيجة | Update result | نتیجہ کو اپ ڈیٹ کریں
                $result->addBatchResult($numbers, $response_json, $status_code);
                
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }
            
            curl_multi_close($mh);
            
            return $result->toArray();
            
        } catch (Exception $e) {
            throw new Exception('Failed to send SMS: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على رصيد الحساب والباقات
     * Get account balance and packages
     * اکاؤنٹ بیلنس اور پیکجز حاصل کریں
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
     * الحصول على أسماء المرسلين
     * Get sender names
     * بھیجنے والوں کے نام حاصل کریں
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
