<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use App\Domains\Auth\Services\FreelancerService;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Passbase\Configuration;
use Passbase\api\IdentityApi;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Passbase\models\Identity;
use Throwable;

class WebhookController extends Controller
{
    /**
     * @var IdentityApi
     */
    protected IdentityApi $apiInstance;

    /**
     * @var FreelancerService
     */
    protected FreelancerService $freelancerService;

    /**
     * @param FreelancerService $freelancerService
     */
    public function __construct(FreelancerService $freelancerService)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('X-API-KEY', config('passbase.secret_key'));
        $this->apiInstance = new IdentityApi(
            new Client(),
            $config
        );
        $this->freelancerService = $freelancerService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     * @throws GeneralException
     * @throws Throwable
     */
    public function receive_passbase_webhook(Request $request)
    {
        $webhook = $request->json()->all();
        $event_type = $webhook['event'];

        switch ($event_type) {
            case "VERIFICATION_COMPLETED":
                $this->process_verification_completed($webhook);
                break;
            case "VERIFICATION_REVIEWED":
                $this->process_verification_reviewed($webhook);
                break;
            default:
        }

        return response('Received', 200);
    }

    /**
     * @param $webhook
     * @return mixed
     * @throws GeneralException
     * @throws Throwable
     */
    private function process_verification_completed($webhook)
    {
        $data = $this->get_identity_for_id($webhook['key']);
        return $this->freelancerService->updateIdentifierPassbase($webhook['key'], $data);
    }

    /**
     * @param $webhook
     * @return mixed
     * @throws GeneralException
     * @throws Throwable
     */
    private function process_verification_reviewed($webhook)
    {
        $data = $this->get_identity_for_id($webhook['key']);
        return $this->update_verification_status($data);
    }

    /**
     * @param $data
     * @return mixed
     * @throws GeneralException
     * @throws Throwable
     */
    private function update_verification_status($data)
    {
        return $this->freelancerService->verifiedPassbase($data);
    }

    /**
     * @param $key
     * @return Identity|void
     */
    private function get_identity_for_id($key)
    {
        try {
            return $this->apiInstance->getIdentityById($key);
        } catch (Exception $e) {
            echo 'Exception when calling IdentityApi->getIdentityById: ', $e->getMessage(), PHP_EOL;
        }
    }
}
