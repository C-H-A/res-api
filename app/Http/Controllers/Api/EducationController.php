<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Education;
use App\Models\Groups;

class EducationController extends BaseController
{
    public function listEducation(){
        $results = Education::where('status',1)->get();

        return response()->json($results);
    }

    public function listEducation_AllStatus(){
        $results = Education::get();

        return response()->json($results);
    }

    public function listEducation_ById($educationId){
        $results = Education::where('educationId',$educationId)->get();

        return response()->json($results);
    }

    public function addEducation(Request $request){
        $newEducation = new Education;
        $newEducation->educationName   = $request->educationName;
        $newEducation->status          = 1;
        $newEducation->save();

        return response()->json($request);
    }

    public function editEducation(Request $request){
        Education::where('educationId',$request->educationId)
            ->update([
                'educationName' => $request->educationName,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusEducation(Request $request){
        $group = Education::join('groups','groups.educationLevel','=','education.educationId')
                            ->where('education.educationId',$request->educationId)->get();
        $subject = Education::join('subjects','subjects.educationLevel','=','education.educationId')
                            ->where('education.educationId',$request->educationId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($subject == '[]' && $subject == '[]'){
                Education::where('educationId',$request->educationId)
                            ->update(['status' => $request->status]);
                $resp = array('status'=>1, 'message'=>'เปลี่ยนสถานะระดับการศึกษาสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'เปลี่ยนสถานะระดับการศึกษาไม่สำเร็จ');
            }
        return response()->json($resp);
    }

    public function deleteEducation(Request $request){
        $group = Education::join('groups','groups.educationLevel','=','education.educationId')
                            ->where('education.educationId',$request->educationId)->get();
        $subject = Education::join('subjects','subjects.educationLevel','=','education.educationId')
                            ->where('education.educationId',$request->educationId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($subject == '[]' && $subject == '[]'){
                $deleteFaculty = Education::where('educationId',$request->educationId)->delete();
                $resp = array('status'=>1, 'message'=>'ลบระดับการศึกษาสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'ลบระดับการศึกษาไม่สำเร็จ');
            }
        return response()->json($resp);
    }

}