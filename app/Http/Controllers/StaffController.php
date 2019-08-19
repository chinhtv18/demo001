<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

   }

   public function updateStaff(Request $request, $id)
   {

   }

   public function deleteStaff(Request $request, $id)
   {

   }

}
