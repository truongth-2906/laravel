<?php

namespace App\Domains\OldSystemUser\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\UserService;
use App\Domains\Category\Services\CategoryService;
use App\Domains\OldSystemUser\Models\OldSystemUser;
use App\Services\BaseService;
use DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\LazyCollection;
use Log;
use Str;

/**
 * Class OldSystemUserService.
 */
class OldSystemUserService extends BaseService
{
    public const PATH_USER_CSV = '/';

    /** @var UserService */
    protected $userService;

    /** @var CategoryService */
    protected $categoryService;

    public function __construct(
        OldSystemUser $oldSystemUser,
        UserService   $userService,
        CategoryService $categoryService
    ) {
        $this->model = $oldSystemUser;
        $this->userService = $userService;
        $this->categoryService = $categoryService;
    }

    /**
     * @return bool
     */
    public function createUserFromOldSystem()
    {
        try {
            $listFile = Storage::disk('user')->listContents();
            foreach ($listFile as $file) {
                if ($file['extension'] == 'csv') {
                    $emailArr = $this->userService->getEmailAllUser();
                    $path = Storage::disk('user')->path($file['path']);
                    throw_if(
                        !file_exists($path) || !is_readable($path),
                        Exception::class,
                        'File do not open'
                    );

                    $data = [];
                    $record = [];
                    $dataNotExist = [];
                    $recordNew = [];

                    $dataFile = $this->getCollectionDataFile($path);
                    $headerLine = null;
                    $dataFile->chunk(CHUNK_COLLECTION_USER)->each(function ($value) use ($emailArr, &$headerLine, &$data, &$record, &$dataNotExist, &$recordNew) {
                        if (!$headerLine) {
                            $headerLine = $value->first();
                        }
                        foreach ($value as $key => $item) {
                            if ($key > 0 && $item) {
                                $imgUrl = $item[array_keys($headerLine, 'Image Featured')[0]];
                                if ($imgUrl) {
                                    $upFileToAzure = $this->uploadFileToAzure($imgUrl, $key);
                                    if ($upFileToAzure) {
                                        $record['avatar_url'] = $upFileToAzure;
                                        $recordNew['avatar_url'] = $upFileToAzure;
                                    } else {
                                        $record['avatar_url'] = null;
                                        $recordNew['avatar_url'] = null;
                                        continue;
                                    }
                                } else {
                                    $record['avatar_url'] = null;
                                    $recordNew['avatar_url'] = null;
                                }
                                $header = array_replace($headerLine, array(array_keys($headerLine, 'ID')[0] => 'old_id'));
                                foreach ($header as $col) {
                                    $record[$col] = $item[array_keys($header, $col)[0]];
                                    if ($emailArr->where('email', $item[array_keys($headerLine, 'Author Email')[0]])->isEmpty()) {
                                        $recordNew[$col] = $item[array_keys($header, $col)[0]];
                                    }
                                }
                                $record['created_at'] = now();
                                $record['updated_at'] = now();
                                array_push($data, $record);
                                array_push($dataNotExist, $recordNew);
                            }
                        }
                    });
                    $this->truncate();
                    $this->createUser($this->formatDataOldUser($data));
                    if ($this->formatDataUser($dataNotExist)) {
                        Storage::disk('user')->delete($file['path']);
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function truncate()
    {
        return $this->model::truncate();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createUser($data)
    {
        return $this->model->insert($data);
    }

    /**
     * @param $imgUrl
     * @param $key
     * @return false|string
     */
    protected function uploadFileToAzure($imgUrl, $key)
    {
        try {
            $arrayUrl = explode('/', $imgUrl);
            $name = end($arrayUrl);
            Storage::disk('public')->put(
                $name,
                file_get_contents($imgUrl)
            );

            $fileStorage = Storage::disk('public')->path($name);
            $fileUpload = new UploadedFile(
                $fileStorage,
                $name,
                'image/gif',
                TRUE
            );
            $extension = explode('.', $name);
            $nameFile = now()->timestamp . $key . '.' . end($extension);
            $fileUpload->storeAs('/public/users', $nameFile, 'azure');
            Storage::disk('public')->delete($name);
            return $nameFile;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $data
     * @return array
     */
    protected function formatDataOldUser($data)
    {
        $result = [];
        foreach ($data as $value) {
            $column = [];
            $column['old_id'] = $value['old_id'] ?? null;
            $column['author_mail'] = $value['Author Email'] ?? null;
            $column['title'] = $value['Title'] ?? null;
            $column['content'] = $value['Content'] ?? null;
            $column['excerpt'] = $value['Excerpt'] ?? null;
            $column['date'] = $value['Date'] ?? null;
            $column['post_type'] = $value['Post Type'] ?? null;
            $column['permalink'] = intval($value['Permalink'] ?? null);
            $column['image_url'] = $value['Image URL'] ?? null;
            $column['image_title'] = $value['Image Title'] ?? null;
            $column['image_caption'] = $value['Image Caption'] ?? null;
            $column['image_description'] = $value['Image Description'] ?? null;
            $column['image_alt_text'] = $value['Image Alt Text'] ?? null;
            $column['image_featured'] = $value['Image Featured'] ?? null;
            $column['attachment_url'] = $value['Attachment URL'] ?? null;
            $column['packages_badges'] = $value['Packages badges'] ?? null;
            $column['specialization'] = $value['Specialization'] ?? null;
            $column['categories'] = $value['Categories'] ?? null;
            $column['hourly_rate_filter'] = $value['Hourly rate filter'] ?? null;
            $column['skills'] = $value['Skills'] ?? null;
            $column['locations'] = $value['Locations'] ?? null;
            $column['languages'] = $value['Languages'] ?? null;
            $column['freelancer_english_level'] = $value['Freelancer English Level'] ?? null;
            $column['freelancer_type'] = $value['Freelancer Type'] ?? null;
            $column['is_verified'] = $value['_is_verified'] ?? null;
            $column['hourly_rate_settings'] = $value['_hourly_rate_settings'] ?? null;
            $column['user_type'] = $value['_user_type'] ?? null;
            $column['employees'] = $value['_employees'] ?? null;
            $column['followers'] = $value['_followers'] ?? null;
            $column['per_hour_rate'] = $value['_perhour_rate'] ?? null;
            $column['rating_filter'] = $value['rating_filter'] ?? null;
            $column['freelancer_type_2'] = $value['_freelancer_type'] ?? null;
            $column['featured_timestamp'] = $value['_featured_timestamp'] ?? null;
            $column['english_level'] = $value['_english_level'] ?? null;
            $column['have_avatar'] = $value['_have_avatar'] ?? null;
            $column['profile_health_filter'] = $value['_profile_health_filter'] ?? null;
            $column['tag_line'] = $value['_tag_line'] ?? null;
            $column['address'] = $value['_address'] ?? null;
            $column['latitude'] = $value['_latitude'] ?? null;
            $column['longitude'] = $value['_longitude'] ?? null;
            $column['fw_options'] = $value['fw_options'] ?? null;
            $column['profile_blocked'] = $value['_profile_blocked'] ?? null;
            $column['project_notification'] = $value['_project_notification'] ?? null;
            $column['linked_profile'] = $value['_linked_profile'] ?? null;
            $column['expiry_string'] = $value['_expiry_string'] ?? null;
            $column['skills_names'] = $value['_skills_names'] ?? null;
            $column['profile_strength'] = $value['profile_strength'] ?? null;
            $column['identity_verified'] = $value['identity_verified'] ?? null;
            $column['gender'] = $value['_gender'] ?? null;
            $column['country'] = $value['_country'] ?? null;
            $column['department'] = $value['_department'] ?? null;
            $column['yoast_wpseo_primary_languages'] = $value['_yoast_wpseo_primary_languages'] ?? null;
            $column['yoast_wpseo_content_score'] = $value['_yoast_wpseo_content_score'] ?? null;
            $column['yoast_wpseo_estimated_reading_time_minutes'] = $value['_yoast_wpseo_estimated-reading-time-minutes'] ?? null;
            $column['skills_2'] = $value['_skills'] ?? null;
            $column['experience'] = $value['_experience'] ?? null;
            $column['educations'] = $value['_educations'] ?? null;
            $column['awards'] = $value['_awards'] ?? null;
            $column['projects'] = $value['_projects'] ?? null;
            $column['max_price'] = $value['_max_price'] ?? null;
            $column['wrc_extra_meta'] = $value['_wrc_extra_meta'] ?? null;
            $column['wrc_extra_meta_user_email'] = $value['_wrc_extra_meta_user_email'] ?? null;
            $column['wrc_extra_meta_primary_skills'] = $value['_wrc_extra_meta_primary_skills'] ?? null;
            $column['wrc_extra_meta_secondary_skills'] = $value['_wrc_extra_meta_secondary_skills'] ?? null;
            $column['invitation_count'] = $value['_invitation_count'] ?? null;
            $column['verification_attachments'] = $value['verification_attachments'] ?? null;
            $column['wp_old_slug'] = $value['_wp_old_slug'] ?? null;
            $column['saved_projects'] = $value['_saved_projects'] ?? null;
            $column['following_employers'] = $value['_following_employers'] ?? null;
            $column['status'] = $value['Status'] ?? null;
            $column['author_id'] = $value['Author ID'] ?? null;
            $column['author_username'] = $value['Author Username'] ?? null;
            $column['author_firstname'] = $value['Author First Name'] ?? null;
            $column['author_lastname'] = $value['Author Last Name'] ?? null;
            $column['slug'] = $value['Slug'] ?? null;
            $column['format'] = $value['Format'] ?? null;
            $column['template'] = $value['Template'] ?? null;
            $column['parent'] = $value['Parent'] ?? null;
            $column['parent_slug'] = $value['Parent Slug'] ?? null;
            $column['order'] = $value['Order'] ?? null;
            $column['comment_status'] = $value['Comment Status'] ?? null;
            $column['ping_status'] = $value['Ping Status'] ?? null;
            $column['post_modified_date'] = $value['Post Modified Date'] ?? null;
            $column['avatar_url'] = $value['avatar_url'] ?? null;
            array_push($result, $column);
        }

        return $result;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function formatDataUser($data)
    {
        $categories = $this->categoryService->pluckIdAndName();
        $otherCategoryId = $categories->search('Others');
        $email = [];
        foreach ($data as $value) {
            try {
                $column = [];
                $categoriesInsert = [];
                if (isset($value['Author Email']) && !in_array($value['Author Email'], $email)) {
                    foreach (['_wrc_extra_meta_primary_skills', '_wrc_extra_meta_secondary_skills'] as $key) {
                        if ($value[$key]) {
                            $categoryId = $categories->search($value[$key]);
                            if ($categoryId !== false) {
                                $categoriesInsert[] = $categoryId;
                            } elseif ($otherCategoryId && !in_array($otherCategoryId, $categoriesInsert)) {
                                $categoriesInsert[] = $otherCategoryId;
                            }
                        }
                    }
                    $column['email'] = $value['Author Email'];
                    $column['type'] = $value['Post Type'] == 'employers' ? User::TYPE_EMPLOYER : User::TYPE_FREELANCER;
                    $column['name'] = $value['Title'] ?? null;
                    $column['firstname'] = $value['Author First Name'] ?? null;
                    $column['lastname'] = $value['Author Last Name'] ?? null;
                    $column['tag_line'] = $value['_tag_line'] ?? null;
                    $column['bio'] = $value['Content'] ?? null;
                    $column['avatar'] = $value['avatar_url'] ?? null;
                    $column['rate_per_hours'] = intval($value['_perhour_rate'] ?? null);
                    $column['active'] = $value['_is_verified'] == 'yes' ? User::IS_ACTIVE : User::UN_ACTIVE;
                    $column['email_verified_at'] = now();
                    $column['password'] = Hash::make(Str::random($this->model::PASSWORD_LENGTH));
                    $column['available'] = User::AVAILABLE;
                    $column['to_be_logged_out'] = User::TO_BE_LOGGED_OUT_DEFAULT;
                    $column['created_at'] = now();
                    $column['updated_at'] = now();
                    $this->userService->addUserFromOldSystem($column, $categoriesInsert);
                    array_push($email, $value['Author Email']);
                }
            } catch (Exception $e) {
                Log::info('Import Error: ' . $e->getMessage());
                echo 'Import user ' . $value['Author Email'] . ': ' . $e->getMessage() . PHP_EOL;
            }
        }

        return true;
    }

    /**
     * @param $path
     * @return LazyCollection
     */
    protected function getCollectionDataFile($path)
    {
        return LazyCollection::make(function () use ($path) {
            $handle = fopen($path, 'r');
            while (($row = fgetcsv($handle, 0, $this->detectCSVFileDelimiter($path))) !== false) {
                yield $lines[] = $this->decodeData($row);
            }
            fclose($handle);
        });
    }

    /**
     *  @param mixed $data
     *  @return mixed
     */
    public function decodeData($data)
    {
        $dataFilter = [];
        foreach ($data as $key => $value) {
            $value = $this->removeUTF8Bom($value);
            $typeEncoding = mb_detect_encoding($value, ['ASCII', 'UTF-8'], true);
            if (empty($typeEncoding)) {
                $dataFilter[$key] = preg_replace("/^\s+|\s+$/u", "", mb_convert_encoding($value, 'UTF-8'));
            } else {
                $dataFilter[$key] = preg_replace("/^\s+|\s+$/u", "", mb_convert_encoding($value, 'UTF-8', $typeEncoding));
            }
        }
        return $dataFilter;
    }

    /**
     * @param string $text
     * @return string
     */
    protected function removeUTF8Bom(string $text)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    /**
     *  @param string $filename
     *  @return string
     */
    public static function detectCSVFileDelimiter($filename)
    {
        $delimiters = [',' => 0, ';' => 0, "\t" => 0, '|' => 0];
        $firstLine = '';
        $handle = fopen($filename, 'r');

        if ($handle) {
            $firstLine = fgets($handle);
            fclose($handle);
        }

        if ($firstLine) {
            foreach ($delimiters as $delimiter => &$count) {
                $count = count(str_getcsv($firstLine, $delimiter));
            }

            return array_search(max($delimiters), $delimiters);
        } else {
            return key($delimiters);
        }
    }

    /**
     * @param array $columns
     * @return void
     */
    public function dataMigrationByColumns(array $columns)
    {
        try {
            DB::beginTransaction();
            $lazyData = LazyCollection::make($this->model->select('id', 'author_mail', ...$columns)->get());
            $lazyData->chunk(100)->each(function ($users) use ($columns) {
                $users->each(function ($user) use ($columns) {
                    $attributes = [];
                    foreach ($columns as $column) {
                        $attributes[$column] = $user->getAttribute($column);
                    }
                    $this->userService->firstAndUpdate(
                        [
                            'email' => $user->author_mail,
                        ],
                        $attributes
                    );
                });
            });
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
