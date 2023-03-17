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
        $result_subject = Faculty::join('subjects','subjects.facultyId','=','faculty.facultyId')
                            ->where('faculty.facultyId',$request->facultyId)->get();
        $result_group = Faculty::join('groups','groups.facultyId','=','faculty.facultyId')
                            ->where('faculty.facultyId',$request->facultyId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($result_subject == '[]' && $result_group == '[]'){
                Faculty::where('facultyId',$request->facultyId)
                        ->update(['status' => $request->status]);
                $resp = array('status'=>1, 'message'=>'เปลี่ยนสถานะคณะสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'เปลี่ยนสถานะคณะไม่สำเร็จ');
            }
        return response()->json($resp);
    }

    public function deleteFaculty(Request $request){
        $result_subject = Faculty::join('subjects','subjects.facultyId','=','faculty.facultyId')
                            ->where('faculty.facultyId',$request->facultyId)->get();
        $result_group = Faculty::join('groups','groups.facultyId','=','faculty.facultyId')
                            ->where('faculty.facultyId',$request->facultyId)->get();
        $resp = array('status'=>1, 'message'=>'');
        if($result_subject == '[]' && $result_group == '[]'){
            $deleteFaculty = Faculty::where('facultyId',$request->facultyId)->delete();
            $resp = array('status'=>1, 'message'=>'ลบข้อมูลคณะสำเร็จ');
        }else{
            $resp = array('status'=>0, 'message'=>'ลบข้อมูลคณะไม่สำเร็จ');
        }
        return response()->json($resp);
    }

}
