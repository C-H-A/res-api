<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Subjects;
use App\Models\Faculty;
use App\Models\Education;
use App\Models\Program;

class SubjectsController extends BaseController
{
    public function listSubjects(){
        $results = Subjects::where('status',1)->get();
        foreach($results as $key => $value){
            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
        }

        return response()->json($results);
    }

    public function listSubject_ById($subjectId){
        $results = Subjects::where('subjectId',$subjectId)->get();

        return response()->json($results);
    }

    public function listSubjects_AllStatus(){
        $results = Subjects::get();
        foreach($results as $key => $value){
            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
        }

        return response()->json($results);
    }

    public function addSubject(Request $request){
        $newSubject = new Subjects;
        $newSubject->subjectCode          = $request->subjectCode;
        $newSubject->subjectName          = $request->subjectName;
        $newSubject->facultyId            = $request->facultyId;
        $newSubject->educationLevel       = $request->educationLevel;
        $newSubject->status               = 1;
        $newSubject->save();

        return response()->json($request);
    }

    public function editSubject(Request $request){
        Subjects::where('subjectId',$request->subjectId)
            ->update([
                'subjectCode'        => $request->subjectCode,
                'subjectName'        => $request->subjectName,
                'facultyId'          => $request->facultyId,
                'educationLevel'     => $request->educationLevel,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusSubject(Request $request){
        $results = Subjects::join('course','subjects.subjectId','=','course.subjectId')
                            ->where('subjects.subjectId',$request->subjectId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                Subjects::where('subjectId',$request->subjectId)
                            ->update(['status' => $request->status]);
                $resp = array('status'=>1, 'message'=>'แก้ไขข้อมูลรายวิชาสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'แก้ไขข้อมูลรายวิชาไม่สำเร็จ');
            }
        return response()->json($resp);
    }

    public function deleteSubject(Request $request){
        $results = Subjects::join('course','subjects.subjectId','=','course.subjectId')
                            ->where('subjects.subjectId',$request->subjectId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                $deleteSubject = Subjects::where('subjectId',$request->subjectId)->delete();
                $resp = array('status'=>1, 'message'=>'ลบข้อมูลรายวิชาสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'ลบข้อมูลรายวิชาไม่สำเร็จ');
            }                    
        return response()->json($resp);
    }

}