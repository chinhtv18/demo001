<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use App\Repositories\StaffRepository;

/**
 * Class StaffService
 * @package App\Services
 *
 */
class StaffService extends BaseService
{
    /**
     * @var StaffRepository
     */
    protected $staffRepos;

    /**
     * StaffService constructor.
     * @param StaffRepository $staffRepos
     */
    public function __construct(StaffRepository $staffRepos)
    {
        $this->staffRepos = $staffRepos;
    }

    public function getStaffs()
    {
        $staffList = $this->staffRepos->all();
        return $this->apiResponse($staffList, 'Get Staff list success', parent::SUCCESS_STATUS);
    }

    public function getStaffDetail($staffId)
    {
        $staffDetail = $this->staffRepos->getById($staffId);
        if (empty($staffDetail)) {
            return $this->apiResponse([], 'Staff not found', parent::ERROR_STATUS);
        }
        return $this->apiResponse($staffDetail, 'Get Staff detail success', parent::SUCCESS_STATUS);
    }

    public function createStaff($data)
    {
        $validator = $this->makeValidator($data);
        if ($validator->fails()) {
            $messages = $this->responseMessage($validator->errors()->toArray());
            return $this->apiResponse([], $messages, parent::ERROR_STATUS, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->storeStaff($data);
    }

    public function updateStaff($data, $id)
    {
        $validator = $this->makeValidator($data, $id);
        if ($validator->fails()) {
            $messages = $this->responseMessage($validator->errors()->toArray());
            return $this->apiResponse([], $messages, parent::ERROR_STATUS, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->storeStaff($data, $id);

    }

    public function storeStaff($data, $id = null)
    {
        if (!empty($id)) {
            $staffUpdate = $this->staffRepos->getById($id);
            if (empty($staffUpdate)) {
                return $this->apiResponse([], 'Staff not found', parent::ERROR_STATUS);
            }
            $result = $this->staffRepos->updateById($id, $data);
            $message = 'update staff success';
        } else {
            $result = $this->staffRepos->create($data);
            $message = 'create staff success';
        }
        return $this->apiResponse($result, $message, parent::SUCCESS_STATUS);
    }

    /**
     * Delete Staff
     *
     * @param $customerId
     * @return \Illuminate\Http\JsonResponsetestGetStaffListReturnOk
     * @throws \Exception
     */
    public function deleteStaff($staffId)
    {
        $staffDelete = $this->staffRepos->getById($staffId);
        if (empty($staffDelete)) {
            return $this->apiResponse([], 'Staff not found', parent::ERROR_STATUS);
        }
        $staffDelete->delete();
        return $this->apiResponse([], 'Delete staff success', parent::SUCCESS_STATUS);
    }

    private function makeValidator($data, $id = null) {
        if ($id) {
            $rules = [
                'email' => 'required|email|unique:staffs,email,' . $id,
                'first_name' => 'required',
                'last_name' => 'required'
            ];
        } else {
            $rules = [
                'email' => 'required|email|unique:staffs',
                'first_name' => 'required',
                'last_name' => 'required'
            ];
        }
        return Validator::make($data, $rules);
    }

}
