<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Groups;
use App\Models\Faculty;
use App\Models\Education;
use App\Models\Program;
use App\Models\Course;

class GroupsController extends BaseController
{
    public function listGroups(){
        $results = Groups::where('status',1)->get();
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

    public function listGroups_AllStatus(){
        $results = Groups::get();
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

    public function listGroup_Id($groupCode){
        $results = Groups::where('groupCode',$groupCode)->get();
        return response()->json($results);
    }

    public function addGroup(Request $request){
        $results = Groups::where('groupCode',$request->groupCode)->get();
        $newGroup = new Groups;
        $newGroup->groupCode      = $request->groupCode;
        $newGroup->groupName      = $request->groupName;
        $newGroup->educationLevel = $request->educationLevel;
        $newGroup->facultyId      = $request->facultyId;
        $newGroup->status         = 1;
        $resp = array('status'=>1, 'message'=>'');
        if($results == '[]'){
            $newGroup->save();
            $resp = array('status'=>1, 'message'=>'เพิ่มกลุ่มเรียนสำเร็จ');
        }else{
            $resp = array('status'=>0, 'message'=>'เพิ่มกลุ่มเรียนไม่สำเร็จ หมายเหตุ : '.$request->groupCode.' เป็นกลุ่มเรียนในระบบอยู่แล้ว');
        }
        return response()->json($resp);
    }

    public function editGroup(Request $request){
        Groups::where('groupCode',$request->groupCode)
            ->update([
                'groupName'      => $request->groupName,
                'educationLevel' => $request->educationLevel,
                'facultyId'       => $request->facultyId,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusGroup(Request $request){
        $results = Course::where('groupCode','like','%'.$request->groupCode.'%')->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                Groups::where('groupCode',$request->groupCode)
                        ->update([
                            'status' => $request->status
                        ]);
                $resp = array('status'=>1, 'message'=>'เปลี่ยนสถานะกลุ่มเรียนสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'เปลี่ยนสถานะกลุ่มเรียนไม่สำเร็จ');
            }
        return response()->json($resp);
    }

    public function deleteGroup(Request $request){
        $results = Course::where('groupCode','like','%'.$request->groupCode.'%')->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                $deleteGroup = Groups::where('groupCode',$request->groupCode)->delete();
                $resp = array('status'=>1, 'message'=>'ลบกลุ่มเรียนสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'ลบกลุ่มเรียนไม่สำเร็จ');
            }

        return response()->json($resp);
    }
}