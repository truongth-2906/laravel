<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Events\User\UserCreated;
use App\Domains\Auth\Models\User;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class EmployerService.
 */
class EmployerService extends BaseService
{
    /**
     * EmployerService constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * @param array $data
     * @param $companyService
     * @param $portfolioService
     * @return User|mixed
     * @throws GeneralException
     */
    public function store(array $data = [], $companyService, $portfolioService)
    {
        DB::beginTransaction();

        try {
            $data = $this->formatData($data);
            $user = $this->createUser($data);
            if ($data['logo']) {
                $companyService->update($data);
            }

            if ($portfolioService !== null) {
                $portfolioService->setImagePortfolio($user->id, $data);
            }
            $user->syncRoles($data['roles'] ?? []);

            if (!config('base.access.user.only_roles')) {
                $user->syncPermissions($data['permissions'] ?? []);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }

        event(new UserCreated($user));

        DB::commit();

        // They didn't want to auto verify the email, but do they want to send the confirmation email to do so?
        if (!isset($data['email_verified'])
            && isset($data['send_confirmation_email'])
            && $data['send_confirmation_email'] === $this->model::IS_ACTIVE
        ) {
            $user->sendEmailVerificationNotification();
        }

        return $user;
    }

    /**
     * @param array $data
     * @param $id
     * @param $companyService
     * @return mixed|User
     * @throws GeneralException
     */
    public function update(array $data = [], $id, $companyService, $portfolioService)
    {
        DB::beginTransaction();

        try {
            $dataFormat = $this->formatData($data);
            if(is_null($data['password'])){
                unset($dataFormat['password']);
            }
            $user = $this->getById($id)->update($dataFormat);
            if ($dataFormat['logo']) {
                $companyService->update($dataFormat);
            }
            $portfolioService->updatePortfolio($dataFormat, $id, TYPE_USER);
            $portfolioService->updateFileName($id, $dataFormat['file_name'], TYPE_USER);
            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * @param array $data
     * @return User
     */
    protected function createUser(array $data = []): User
    {
        return $this->model::create([
            'type' => $data['type'] ?? $this->model::TYPE_EMPLOYER,
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => $data['password'] ?? null,
            'provider' => $data['provider'] ?? null,
            'provider_id' => $data['provider_id'] ?? null,
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'active' => $data['active'] ?? true,
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'country_id' => $data['country_id'],
            'timezone_id' => $data['timezone_id'],
            'company_id' => $data['company_id'],
            'sector_id' => $data['sector_id'],
            'bio' => $data['bio'],
            'phone_number' => $data['phone_number'],
            'calling_code_id' => $data['calling_code_id']
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function formatData(array $data = []): array
    {
        return [
            'type' => $data['type'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'country_id' => $data['country_id'],
            'sector_id' => $data['sector_id'],
            'timezone_id' => $data['timezone_id'],
            'bio' => $data['bio'],
            'company_id' => $data['company_id'] ?? null,
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'active' => isset($data['active']) && $data['active'] === $this->model::IS_ACTIVE,
            'logo' => $data['logo'] ?? [],
            'file_upload' => $data['file_upload'] ?? [],
            'portfolios_delete' => $data['portfolios_delete'] ?? [],
            'file_name' => $data['file_name'] ?? [],
            'phone_number' => $data['phone_number'],
            'calling_code_id' => $data['calling_code']
        ];
    }

    /**
     * @param array $data
     * @param $id
     * @param $companyService
     * @param $portfolioService
     * @return bool|User
     * @throws GeneralException
     */
    public function updateByEmployer(array $data = [], $id, $companyService, $portfolioService)
    {
        DB::beginTransaction();

        try {
            $data = $this->formatDataSettingEmployer($data);
            $user = $this->getById($id)->update($data);
            if ($data['logo']) {
                $companyService->update($data);
            }
            $portfolioService->updatePortfolio($data, $id, TYPE_USER);
            $portfolioService->updateFileName($id, $data['file_name'], TYPE_USER);
            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function formatDataSettingEmployer(array $data = []): array
    {
        return [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'name' => $data['firstname'] . ' ' . $data['lastname'],
            'country_id' => $data['country_id'],
            'sector_id' => $data['sector_id'],
            'timezone_id' => $data['timezone_id'],
            'bio' => $data['bio'],
            'company_id' => $data['company_id'] ?? null,
            'email' => $data['email'],
            'logo' => $data['logo'] ?? [],
            'file_upload' => $data['file_upload'] ?? [],
            'portfolios_delete' => $data['portfolios_delete'] ?? [],
            'file_name' => $data['file_name'] ?? [],
            'phone_number' => $data['phone_number'],
            'calling_code_id' => $data['calling_code']
        ];
    }

    /**
     * @param int $id
     * @return Model
     */
    public function getById($id)
    {
        return $this->model::employers()->where('id', $id)->first();
    }

    /**
     * @param $request
     * @param $type
     * @return mixed
     */
    public function getDataExport($request, $type)
    {
        return $this->model::search($request, $type)->with('callingCode', 'company', 'sector', 'country', 'utc')->get();
    }
}
