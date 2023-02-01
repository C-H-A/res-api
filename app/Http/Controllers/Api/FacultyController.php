<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Department;

class FacultyController extends BaseController
{
    public function listFaculty(){
        $results = Faculty::where('status',1)->get();

        return response()->json($results);
    }

    public function listFaculty_AllStatus(){
        $results = Faculty::get();

        return response()->json($results);
    }

    public function listFaculty_ById($facultyId){
        $results = Faculty::where('facultyId',$facultyId)->get();

        return response()->json($results);
    }

    public function Search_Faculty(Request $request){
        $results = Faculty::where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('facultyName','like','%'.$request->facultyName.'%')
                            ->get();

        return response()->json($results);
    }

    public function Search_Faculty_Active(Request $request){
        $results = Faculty::where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('facultyName','like','%'.$request->facultyName.'%')
                            ->where('status','1')
                            ->get();

        return response()->json($results);
    }

    public function addFaculty(Request $request){
        $newFaculty = new Faculty;
        $newFaculty->facultyId     = $request->facultyId;
        $newFaculty->facultyName   = $request->facultyName;
        $newFaculty->status         = 1;
        $newFaculty->save();

        return response()->json($request);
    }

    public function editFaculty(Request $request){
        Faculty::where('facultyId',$request->facultyId)
            ->update([
                'facultyName' => $request->facultyName,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusFaculty(Request $request){
        $results = Department::where('facultyId',$request->facultyId)->get();
        if($results == 0){

        }
        Faculty::where('facultyId',$request->facultyId)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    public function deleteFaculty(Request $request){
        $deleteFaculty = Faculty::where('facultyId',$request->facultyId)->delete();

        return response()->json($request);
    }

    public function checkDelFaculty(Request $request){
        $results = Department::where('facultyId',$request->facultyId)->get();
        if($results != '[]'){
            $resp = array('status'=>0, 'message'=>'ไม่สามารถทำรายการลบข้อมูลคณะรหัส : '.$request->facultyId.' ได้');
        }else{
            $resp = array('status'=>1, 'message'=>'ยืนยันการลบข้อมูลคณะรหัส : '.$request->facultyId.' หรือไม่');
        }
        return response()->json($resp);
    }
}
