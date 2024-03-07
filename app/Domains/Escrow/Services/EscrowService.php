<?php

namespace App\Domains\Escrow\Services;

use App\Domains\Escrow\Rules\CreateTransaction;
use App\Domains\Transaction\Models\Transaction;
use DB;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Str;

/**
 * Class EscrowService.
 */
class EscrowService
{
    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $defaultPath;

    /** @var string */
    protected $payPath;

    /** @var string */
    protected $authorization;

    /** @var array */
    protected $baseHeaders;

    protected const AGREE_ACTION = 'agree';
    protected const CANCEL_ACTION = 'cancel';
    protected const SHIP_ACTION = 'ship';
    protected const RECEIVE_ACTION = 'receive';
    protected const ACCEPT_ACTION = 'accept';
    protected const TYPE_BROKEN = 'partner';
    protected const TYPE_SELLER = 'seller';
    protected const TYPE_BUYER = 'buyer';
    protected const TYPE_MILESTONE = 'milestone';
    protected const TYPE_BROKEN_FEE = 'partner_fee';
    protected const TYPE_PUSH = 'push';
    public const BROKERAGE_FEE_PERCENTAGE_WITH_EMPLOYER = 11 / 100;
    public const BROKERAGE_FEE_PERCENTAGE_WITH_FREELANCER = 5 / 100;
    protected const REDIRECT_TYPE = 'automatic';
    protected const INSPECTION_PERIOD = 86400;
    protected const FEE_SPILT = '0.5';

    /**
     * constructor function
     */
    public function __construct()
    {
        $this->setup();
    }

    /**
     * @return void
     */
    protected function setup()
    {
        $this->baseUrl = trim(config('escrow.base_url'), '/');

        $this->defaultPath = '/' . trim(config('escrow.default_path'), '/');

        $this->payPath = '/' . trim(config('escrow.pay_path'), '/');

        $this->authorization = $this->makeAuthorization(config('escrow.email'), config('escrow.api_key'));

        $this->baseHeaders = [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @return string
     */
    protected function getEscrowAuthenticationRoute()
    {
        return $this->baseUrl . $this->defaultPath . '/customer/me';
    }

    /**
     * @return string
     */
    protected function getEscrowCreateTransactionRoute()
    {
        return $this->baseUrl . $this->payPath;
    }

    /**
     * @param int $transactionId
     * @return string
     */
    protected function getEscrowTransactionRoute(int $transactionId)
    {
        return $this->baseUrl . $this->defaultPath . '/transaction/' . $transactionId;
    }

    /**
     * @return string
     */
    protected function getEscrowWebhookRoute()
    {
        return $this->baseUrl . $this->defaultPath . '/customer/me/webhook';
    }

    /**
     * @return string
     */
    protected function getWebhookCallbackRoute()
    {
        return route('frontend.escrow.webhook.callback', ['token' => config('escrow.webhook_verification_key')]);
    }

    /**
     * @param int $webhookId
     * @return string
     */
    protected function deleteWebhookRoute(int $webhookId)
    {
        return $this->getEscrowWebhookRoute() . '/' . $webhookId;
    }

    /**
     * @param int $transactionId
     * @param int $itemId
     * @return string
     */
    protected function getItemEscrowTransactionRoute(int $transactionId, int $itemId)
    {
        return $this->baseUrl . $this->defaultPath . '/transaction/' . $transactionId . '/item/' . $itemId;
    }

    /**
     * @param int $transactionId
     * @return string
     */
    protected function getDisbursementMethodsRoute(int $transactionId)
    {
        return $this->baseUrl . $this->defaultPath . '/transaction/' . $transactionId . '/disbursement_methods';
    }

    /**
     * @param int $jobId
     * @return string
     */
    protected function getRedirectUrl(int $jobId)
    {
        return route('frontend.employer.payments.wait_for_redirect', ['job' => $jobId]);
    }

    /**
     * @return string
     */
    protected function getEscrowCreateCustomerRoute()
    {
        return $this->baseUrl . $this->defaultPath . '/customer';
    }

    /**
     * @param string $reference
     * @return string
     */
    protected function getEscrowGetFundingPageRoute(string $reference)
    {
        return $this->baseUrl . $this->payPath . '?reference=' . $reference;
    }

    /**
     * @param string $email
     * @param string $apiKey
     * @return string
     */
    public function makeAuthorization(string $email, string $apiKey)
    {
        return "$email:$apiKey";
    }

    /**
     * @param string $customerEmail
     * @return array
     */
    protected function getHeadersWithAsCustomer(string $customerEmail)
    {
        return array_merge(
            $this->baseHeaders,
            ['As-Customer' => $customerEmail]
        );
    }

    /**
     * @param string $email
     * @param string $apiKey
     * @return bool
     */
    public function attemptAccount(string $email, string $apiKey)
    {
        $responses = $this->authenticationAccount($email, $apiKey);

        return $responses['statusCode'] == Response::HTTP_OK;
    }

    /**
     * @param string $email
     * @param string $apiKey
     * @return array
     */
    public function authenticationAccount(string $email, string $apiKey)
    {
        $response = Http::withHeaders($this->baseHeaders)
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->makeAuthorization($email, $apiKey)
                ]
            ])
            ->get($this->getEscrowAuthenticationRoute());

        return [
            'statusCode' => $response->status(),
            'content' => $response->collect(),
            'headers' => $response->headers(),
        ];
    }

    /**
     * @param array $attribute
     * @return \Illuminate\Support\Collection|bool
     */
    public function createTransaction(array $attributes)
    {
        try {
            $attributesProcessed = $this->attributesProcessing($attributes);
            $transaction = $this->sendRequestCreateTransaction($attributesProcessed);
            $transaction->put('reference', $attributesProcessed['reference']);

            return $transaction;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param array $attributes
     * @return \Illuminate\Support\Collection
     */
    public function sendRequestCreateTransaction(array $attributes)
    {
        $response = Http::withHeaders($this->baseHeaders)
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                    CURLOPT_POSTFIELDS => json_encode($attributes)
                ]
            ])
            ->post($this->getEscrowCreateTransactionRoute());

        throw_if(
            $response->status() != Response::HTTP_CREATED,
            Exception::class,
            'Create transaction failed.',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        return $response->collect();
    }

    /**
     * @param int $transactionId
     * @param string $customerEmail
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionDetail(int $transactionId, string $customerEmail)
    {
        $response = Http::withHeaders($this->getHeadersWithAsCustomer($customerEmail))
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                ]
            ])
            ->get($this->getEscrowTransactionRoute($transactionId));

        throw_if(
            $response->status() != Response::HTTP_OK,
            Exception::class,
            'Transaction not found.',
            Response::HTTP_NOT_FOUND
        );

        return $response->collect();
    }

    /**
     * @param int $transactionId
     * @param int $transactionId
     * @return bool
     */
    public function cancelTransaction(int $transactionId, string $message = 'The transaction is cancelled.')
    {
        $response = Http::withHeaders($this->baseHeaders)
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                    CURLOPT_POSTFIELDS => json_encode([
                        'action' => $this::CANCEL_ACTION,
                        'cancel_information' => [
                            'cancellation_reason' => $message,
                        ]
                    ])
                ]
            ])
            ->patch($this->getEscrowTransactionRoute($transactionId));

        return $response->status() == Response::HTTP_OK;
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function attributesProcessing(array $attributes)
    {
        $attributes['amount'] = floatval($attributes['amount']);
        $validator = Validator::make($attributes, CreateTransaction::rules($attributes['freelancer_id'], $attributes['employer_id']));

        throw_if(
            $validator->fails(),
            ValidationException::class,
            'Validated attributes failed.',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        return [
            'currency' => $attributes['currency'] ?? config('escrow.default_currency'),
            'reference' => $this->makeReference(40),
            'return_url' => $this->getRedirectUrl($attributes['job_id']),
            'redirect_type' => $this::REDIRECT_TYPE,
            'description' => "This is a payment transaction for " . $attributes['freelancer_name'] . " wages and fees for " . $attributes['job_name'] . " job.",
            'items' => [
                [
                    'type' => $this::TYPE_MILESTONE,
                    'description' => 'Pay for ' . $attributes['freelancer_name'],
                    'schedule' => [
                        [
                            'payer_customer' => $attributes['employer_email'],
                            'amount' => $attributes['amount'],
                            'beneficiary_customer' => $attributes['freelancer_email'],
                        ]
                    ],
                    'fees' => [
                        [
                            'payer_customer' => $attributes['employer_email'],
                            'type' => 'escrow',
                            'split' => $this::FEE_SPILT,
                        ],
                        [
                            'payer_customer' => $attributes['freelancer_email'],
                            'type' => 'escrow',
                            'split' => $this::FEE_SPILT,
                        ]
                    ],
                    'title' => 'Wage',
                    'inspection_period' => $this::INSPECTION_PERIOD,
                    'quantity' => '1',
                ],
                [
                    "type" => $this::TYPE_BROKEN_FEE,
                    "schedule" => [
                        [
                            "payer_customer" => $attributes['employer_email'],
                            "beneficiary_customer" => "me",
                            "amount" => round($attributes['amount'] * $this::BROKERAGE_FEE_PERCENTAGE_WITH_EMPLOYER, 2),
                        ]
                    ],
                ],
                [
                    "type" => $this::TYPE_BROKEN_FEE,
                    "schedule" => [
                        [
                            "payer_customer" => $attributes['freelancer_email'],
                            "beneficiary_customer" => "me",
                            "amount" => round($attributes['amount'] * $this::BROKERAGE_FEE_PERCENTAGE_WITH_FREELANCER, 2),
                        ]
                    ]
                ],
            ],
            'parties' => [
                [
                    "role" => $this::TYPE_BROKEN,
                    "customer" => "me",
                    'agreed' => 'true',
                ],
                [
                    'customer' => $attributes['freelancer_email'],
                    'role' => $this::TYPE_SELLER,
                    'agreed' => 'true',
                    'lock_email' => 'true',
                ],
                [
                    'customer' => $attributes['employer_email'],
                    'role' => $this::TYPE_BUYER,
                    'agreed' => 'true',
                    'lock_email' => 'true',
                    'initiator' => 'true'
                ],
            ],
        ];
    }

    /**
     * @return bool
     */
    public function addingWebhookIfNotExists()
    {
        try {
            if (!$this->isAddedWebhook()) {
                return $this->addingWebhook();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function addingWebhook()
    {
        $response = Http::withHeaders($this->baseHeaders)
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                    CURLOPT_POSTFIELDS => json_encode([
                        'url' => $this->getWebhookCallbackRoute(),
                    ])
                ]
            ])
            ->post($this->getEscrowWebhookRoute());

        return $response->status() == Response::HTTP_CREATED;
    }

    /**
     * @return bool
     */
    public function isAddedWebhook()
    {
        return $this->listWebhookRegistered()->isNotEmpty();
    }

    /**
     * @param Transaction $transactionId
     * @return bool
     * @throws \Throwable
     */
    public function payNow(Transaction $transaction)
    {
        try {
            DB::beginTransaction();
            $detailTransaction = $this->getTransactionDetail($transaction->escrow_transaction_id, $transaction->sender->escrow_email);
            $statuses = collect($detailTransaction->get('items')[0]['status'] ?? []);
            $route = $this->getItemEscrowTransactionRoute($transaction->escrow_transaction_id, $transaction->item_escrow_id);

            if ($statuses->get('shipped') === false) {
                $transaction->update([
                    'status' => Transaction::SHIP
                ]);
                $this->deliveryConfirmationTransaction($route, $transaction->receiver->escrow_email);
                DB::commit();
            }
            if ($statuses->get('received') === false) {
                $transaction->update([
                    'status' => Transaction::RECEIVER
                ]);
                $this->receiveTransaction($route, $transaction->sender->escrow_email);
                DB::commit();
            }
            if ($statuses->get('accepted') === false) {
                $transaction->update([
                    'status' => Transaction::ACCEPT
                ]);
                $this->acceptTransaction($route, $transaction->sender->escrow_email);
                DB::commit();
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param int $transactionId
     * @param string $customerEmail
     * @return \Illuminate\Support\Collection
     * @throws \Throwable
     */
    public function getDisbursementMethodsTransaction(int $transactionId, string $customerEmail)
    {
        $response = Http::withHeaders($this->getHeadersWithAsCustomer($customerEmail))
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                ]
            ])
            ->get($this->getDisbursementMethodsRoute($transactionId));

        throw_if(
            $response->status() != Response::HTTP_OK,
            Exception::class,
            'Transaction not found.',
            Response::HTTP_NOT_FOUND
        );

        return $response->collect();
    }

    /**
     * @param int $transactionId
     * @param int $savedDisbursementMethodId
     * @param string $customerEmail
     * @return \Illuminate\Support\Collection
     * @throws \Throwable
     */
    public function selectDisbursementMethodsTransaction(int $transactionId, int $savedDisbursementMethodId, string $customerEmail)
    {
        $response = Http::withHeaders($this->getHeadersWithAsCustomer($customerEmail))
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                    CURLOPT_POSTFIELDS => json_encode([
                        'id' => $savedDisbursementMethodId,
                    ])
                ]
            ])
            ->post($this->getDisbursementMethodsRoute($transactionId));
        throw_if(
            $response->status() != Response::HTTP_OK,
            Exception::class,
            'This freelancer has not added a method to receive money yet.',
            Response::HTTP_NOT_FOUND
        );

        return $response->collect();
    }

    /**
     * @param string $route
     * @param string $customerEmail
     * @return \Illuminate\Support\Collection
     * @throws \Throwable
     */
    public function deliveryConfirmationTransaction(string $route, string $customerEmail)
    {
        $response = Http::withHeaders($this->getHeadersWithAsCustomer($customerEmail))
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                    CURLOPT_POSTFIELDS => json_encode([
                        'action' => $this::SHIP_ACTION,
                        'shipping_information' => [
                            'authorization_type' => $this::TYPE_PUSH
                        ],
                    ])
                ]
            ])
            ->patch($route);

        throw_if(
            $response->status() != Response::HTTP_OK,
            Exception::class,
            'Authorization push transaction fail.',
            Response::HTTP_NOT_FOUND
        );

        return $response->collect();
    }

    /**
     * @param string $route
     * @param string $customerEmail
     * @return \Illuminate\Support\Collection
     * @throws \Throwable
     */
    public function receiveTransaction(string $route, string $customerEmail)
    {
        $response = Http::withHeaders($this->getHeadersWithAsCustomer($customerEmail))
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                    CURLOPT_POSTFIELDS => json_encode([
                        'action' => $this::RECEIVE_ACTION,
                    ])
                ]
            ])
            ->patch($route);

        throw_if(
            $response->status() != Response::HTTP_OK,
            Exception::class,
            'Receive transaction fail.',
            Response::HTTP_NOT_FOUND
        );

        return $response->collect();
    }

    /**
     * @param string $route
     * @param string $customerEmail
     * @return \Illuminate\Support\Collection
     * @throws \Throwable
     */
    public function acceptTransaction(string $route, string $customerEmail)
    {
        $response = Http::withHeaders($this->getHeadersWithAsCustomer($customerEmail))
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization,
                    CURLOPT_POSTFIELDS => json_encode([
                        'action' => $this::ACCEPT_ACTION,
                    ])
                ]
            ])
            ->patch($route);

        throw_if(
            $response->status() != Response::HTTP_OK,
            Exception::class,
            'Accept transaction fail.',
            Response::HTTP_NOT_FOUND
        );

        return $response->collect();
    }

    /**
     * @param string $email
     * @return bool
     */
    public function createCustomer(string $email)
    {
        try {
            $response = Http::withHeaders($this->baseHeaders)
                ->withOptions([
                    'curl' => [
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_USERPWD => $this->authorization,
                        CURLOPT_POSTFIELDS => json_encode([
                            "email" => $email,
                        ])
                    ]
                ])
                ->post($this->getEscrowCreateCustomerRoute());

            throw_if(
                $response->status() != Response::HTTP_OK,
                Exception::class,
                'Create customer failed.',
                Response::HTTP_NOT_FOUND
            );

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param int $length
     * @return string
     */
    protected function makeReference(int $length)
    {
        $reference = Str::random($length);

        if (!Transaction::where('reference', $reference)->exists()) {
            return $reference;
        }

        return $this->makeReference($length);
    }

    /**
     * @param string $customerEmail
     * @param string $reference
     * @return string
     */
    public function getFundingPage(string $customerEmail, string $reference)
    {
        $response = Http::withHeaders($this->getHeadersWithAsCustomer($customerEmail))
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization
                ]
            ])
            ->get($this->getEscrowGetFundingPageRoute($reference));

        throw_if(
            $response->status() != Response::HTTP_OK,
            Exception::class,
            'Transaction not found.',
            Response::HTTP_NOT_FOUND
        );

        return $response->collect()->get('landing_page', '');
    }

    public function approvingPayment(int $transactionId, $email, $apiKey)
    {
        $response = Http::withHeaders($this->baseHeaders)
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->makeAuthorization($email, $apiKey),
                    CURLOPT_POSTFIELDS => json_encode([
                        'method' => 'wire_transfer',
                        'amount' => 274.49,
                    ]),
                ]
            ])
            ->post($this->approvingPaymentRoute($transactionId));

        return true;
    }

    /**
     * @param int $transactionId
     * @return string
     */
    protected function approvingPaymentRoute(int $transactionId)
    {
        return 'https://integrationhelper.escrow-sandbox.com/v1/transaction/' . $transactionId . '/payments_in';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function listWebhookUrl()
    {
        $response = Http::withHeaders($this->baseHeaders)
            ->withOptions([
                'curl' => [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_USERPWD => $this->authorization
                ]
            ])
            ->get($this->getEscrowWebhookRoute());

        return collect($response->collect()->get('webhooks', []));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function listWebhookRegistered()
    {
        return $this->listWebhookUrl()->where('url', $this->getWebhookCallbackRoute())->pluck('url');
    }

    /**
     * @return bool
     */
    public function refreshAndRegisterWebhook()
    {
        try {
            $appWebhookCallbackPath = $this->splitPathRoute($this->getWebhookCallbackRoute());
            $webhooks = $this->listWebhookUrl()->filter(function ($item) use ($appWebhookCallbackPath) {
                $path = $this->splitPathRoute($item['url']);
                return $path === $appWebhookCallbackPath;
            });

            if ($webhooks->isNotEmpty()) {
                $this->deleteWebhook($webhooks->pluck('id'));
            }

            return $this->addingWebhook();
            return $webhooks;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $route
     * @return string
     */
    protected function splitPathRoute(string $route)
    {
        $path = rtrim(parse_url($route, PHP_URL_PATH), '\/');
        $path = substr($path, 0, strrpos($path, '/'));

        return $path;
    }

    /**
     * @param int|array|\Illuminate\Support\Collection $id
     * @return void
     */
    public function deleteWebhook($id)
    {
        if (!($id instanceof Collection)) {
            $id = (array) $id;
            $id = collect($id);
        }
        $id->each(function ($webhookId) {
            $response = Http::withHeaders($this->baseHeaders)
                ->withOptions([
                    'curl' => [
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_USERPWD => $this->authorization
                    ]
                ])
                ->delete($this->deleteWebhookRoute($webhookId));
        });
    }
}
