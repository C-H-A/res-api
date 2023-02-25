<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Professors;
use App\Models\Faculty;
use App\Models\Program;

class ProfessorsController extends BaseController
{
    public function listProfessors(){
        $results = Professors::where('status',1)->get();
        foreach($results as $key => $value){
            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }
        return response()->json($results);
    }

    public function listProfessor_ById($professorId){
        $results = Professors::where('professorId',$professorId)->get();

        return response()->json($results);
    }

    public function listProfessors_AllStatus(){
        $results = Professors::get();
        foreach($results as $key => $value){
            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }
        return response()->json($results);
    }

    public function addProfessor(Request $request){
        $results = Professors::where('fullName',$request->fullName)->get();
        $resp = array('status'=>1, 'message'=>'');
        $newProfessor = new Professors;
        $newProfessor->fullName       = $request->fullName;
        $newProfessor->facultyId      = $request->facultyId;
        $newProfessor->status         = 1;
        if($results == '[]'){
            $newProfessor->save();
            $resp = array('status'=>1, 'message'=>'เพิ่มข้อมูลอาจารย์สำเร็จ');
        }else{
            $resp = array('status'=>0, 'message'=>'เพิ่มข้อมูลอาจารย์สำเร็จไม่สำเร็จ หมายเหตุ : มีอาจารย์ชื่อและนามสกุลนี้อยู่ในระบบแล้ว');
        }
        return response()->json($resp);
    }

    public function editProfessor(Request $request){
        $results = Professors::where('fullName',$request->fullName)->get();
        $resp = array('status'=>1, 'message'=>'');
        if($results == '[]'){
            Professors::where('professorId',$request->professorId)
                        ->update([
                            'fullName'     => $request->fullName,
                            'facultyId'    => $request->facultyId,
                        ]);
            $resp = array('status'=>1, 'message'=>'แก้ไขข้อมูลเพิ่มอาจารย์สำเร็จ');
        }else{
            $resp = array('status'=>0, 'message'=>'แก้ไขข้อมูลอาจารย์สำเร็จไม่สำเร็จ หมายเหตุ : มีอาจารย์ชื่อและนามสกุลนี้อยู่ในระบบแล้ว');
        }
        return response()->json($resp);
    }

    public function changstatusProfessor(Request $request){
        $results = Professors::join('course','professors.professorId','=','course.professorId')
                            ->where('professors.professorId',$request->professorId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                Professors::where('professorId',$request->professorId)->update(['status' => $request->status]);
                $resp = array('status'=>1, 'message'=>'เปลี่ยนสถานะอาจารย์สำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'เปลี่ยนสถานะอาจารย์ไม่สำเร็จ');
            }
        return response()->json($resp);
    }

    public function deleteProfessor(Request $request){
        $results = Professors::join('course','professors.professorId','=','course.professorId')
                            ->where('professors.professorId',$request->professorId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                $deleteProfessors = Professors::where('professorId',$request->professorId)->delete();
                $resp = array('status'=>1, 'message'=>'ลบอาจารย์สำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'ลบอาจารย์ไม่สำเร็จ');
            }
        return response()->json($resp);
    }
}