<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Education;

class DepartmentController extends BaseController
{
    public function listDepartment(){
        $results = Department::where('status',1)->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }

        return response()->json($results);
    }

    public function listDepartment_AllStatus(){
        $results = Department::get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }

        return response()->json($results);
    }

    public function listDepartment_ById($departmentId){
        $results = Department::where('departmentId',$departmentId)->get();

        return response()->json($results);
    }

    public function listDepartment_ByFaculty($facultyId){
        $results = Department::where('facultyId',$facultyId)->get();

        return response()->json($results);
    }

    public function Search_Department(Request $request){
        $results = Department::where('departmentId','like','%'.$request->departmentId.'%')
                            ->Where('departmentName','like','%'.$request->departmentName.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }

        return response()->json($results);
    }

    public function Search_Department_Active(Request $request){
        $results = Department::where('departmentId','like','%'.$request->departmentId.'%')
                            ->Where('departmentName','like','%'.$request->departmentName.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->where('status','1')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }

        return response()->json($results);
    }

    public function addDepartment(Request $request){
        $newDepartment = new Department;
        $newDepartment->departmentId     = $request->departmentId;
        $newDepartment->departmentName   = $request->departmentName;
        $newDepartment->facultyId         = $request->facultyId;
        $newDepartment->status            = 1;
        $newDepartment->save();

        return response()->json($request);
    }

    public function editDepartment(Request $request){
        Department::where('departmentId',$request->departmentId)
            ->update([
                'departmentName' => $request->departmentName,
                'facultyId'       => $request->facultyId,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusDepartment(Request $request){
        Department::where('departmentId',$request->departmentId)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    public function deleteDepartment(Request $request){
        $deleteDepartment = Department::where('departmentId',$request->departmentId)->delete();

        return response()->json($request);
    }

}