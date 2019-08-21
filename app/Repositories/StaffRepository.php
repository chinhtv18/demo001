<?php

namespace App\Repositories;

use App\Models\Staff;
use Illuminate\Support\Facades\DB;

/**
 * Class UserRepository
 * @package App\Repositories
 *
 */
class StaffRepository extends BaseRepository
{
    /**
     * StaffRepository constructor.
     * @param Staff $staff
     */
    public function __construct(Staff $staff)
    {
        $this->model = $staff;
    }
}

