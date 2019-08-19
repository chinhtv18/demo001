<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
   public function getStaffs()
   {
       try {
           $staffs = Staff::query()->get();
           return $this->apiResponse($staffs, 'Get staff list success');
       } catch (\Exception $ex) {
           return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }
   }

   public function getStaffDetail(Request $request, $id)
   {
       try {
            $staff = Staff::query()->find($id);
            if (empty($staff)) {
                return $this->apiResponse([], 'Staff not found', self::ERROR_STATUS);
            }

            return $this->apiResponse($staff, 'Get staff detail success', self::SUCCESS_STATUS);

       } catch (\Exception $ex) {
           return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }
   }

   public function createStaff(Request $request)
   {
        try {
            $data = $request->all();
            $validator = $this->makeValidator($data);
            if ($validator->fails()) {
                $messages = $this->responseMessage($validator->errors()->toArray());
                return $this->apiResponse([], $messages, parent::ERROR_STATUS, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $this->storeStaff($data);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
   }

   public function updateStaff(Request $request, $id)
   {
       try {
           $data = $request->all();
           $validator = $this->makeValidator($data, $id);
           if ($validator->fails()) {
               $messages = $this->responseMessage($validator->errors()->toArray());
               return $this->apiResponse([], $messages, parent::ERROR_STATUS, Response::HTTP_UNPROCESSABLE_ENTITY);
           }
           return $this->storeStaff($data, $id);

       } catch (\Exception $ex) {
           return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }
   }

   public function storeStaff($data, $id = null)
   {
       if (!empty($id)) {
           $staffUpdate = Staff::query()->where('id', $id)->first();
           if (empty($staffUpdate)) {
               return $this->apiResponse([], 'Staff not found', parent::ERROR_STATUS);
           }
           $result = $staffUpdate->update($data);
           $message = 'update staff success';
       } else {
           $result = Staff::create($data);
           $message = 'create staff success';
       }
       return $this->apiResponse([$result], $message, parent::SUCCESS_STATUS);
   }

   public function deleteStaff(Request $request, $id)
   {
       try {
           $staffDelete = Staff::query()->where('id', $id)->first();
           if (empty($staffDelete)) {
               return $this->apiResponse([], 'Staff not found', parent::ERROR_STATUS);
           }
           $staffDelete->delete();
           return $this->apiResponse([], 'Delete customer success', parent::SUCCESS_STATUS);
       } catch (\Exception $ex) {
           return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }
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
