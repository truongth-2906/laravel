<?php

namespace App\Domains\Category\Services;

use App\Domains\Category\Models\Category;
use App\Services\BaseService;

/**
 * Class CategoryService.
 */
class CategoryService extends BaseService
{
    /**
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function pluckIdAndName()
    {
        return $this->model->pluck('name', 'id');
    }
}
