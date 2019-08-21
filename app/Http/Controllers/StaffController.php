<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Services\StaffService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * @var CustomerController
     */
    protected $staffSrv;

    /**
     * CustomerController constructor.
     * @param CustomerService $customerSrv
     */
    public function __construct(StaffService $staffSrv)
    {
        $this->staffSrv = $staffSrv;
    }

   public function getStaffs()
   {
       try {
           return $this->staffSrv->getStaffs();
       } catch (\Exception $ex) {
           return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }
   }

   public function getStaffDetail(Request $request, $id)
   {
       try {
           return $this->staffSrv->getStaffDetail($id);
       } catch (\Exception $ex) {
           return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }
   }

   public function createStaff(Request $request)
   {
        try {
            $data = $request->all();
            return $this->staffSrv->createStaff($data);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
   }

   public function updateStaff(Request $request, $id)
   {
       try {
           $data = $request->all();
           return $this->staffSrv->updateStaff($data, $id);
       } catch (\Exception $ex) {
           return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }
   }

   public function deleteStaff(Request $request, $id)
   {
       try {
          return $this->staffSrv->deleteStaff($id);
       } catch (\Exception $ex) {
           return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
       }
   }
}
