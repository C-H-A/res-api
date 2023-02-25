<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Course;
use App\Models\Subjects;
use App\Models\Professors;
use App\Models\Groups;

class CourseController extends BaseController
{
    public function listCourse(){
        $results = Course::where('status','1')->get();
        foreach($results as $key => $value){
            $arr_professor = explode(',',$value['professorId']);
            $professor = Professors::whereIn('professorId', $arr_professor)->get();
            $profes = '';
                    foreach($professor as $keyp => $valuep){
                        $profes = 'อ.'.$valuep['fullName'].', '.$profes;
                    }
                    $profes = substr($profes, 0, -2);

            $subject = Subjects::where('subjectCode',$value['subjectCode'])->get();
            
            $results[$key]['professorId'] = $profes;
            $results[$key]['subjectCode'] = "[".$subject[0]['subjectCode']."] : ".$subject[0]['subjectName'];
        }
        return response()->json($results);
    }

    public function listCourse_Id($courseId){
        $result = Course::where('courseId',$courseId)->get();
        return response()->json($result);
    }

    public function listCourses_AllStatus(){
        $results = Course::get();
        foreach($results as $key => $value){
            $arr_professor = explode(',',$value['professorId']);
            $professor = Professors::whereIn('professorId', $arr_professor)->get();
            $profes = '';
                    foreach($professor as $keyp => $valuep){
                        $profes = 'อ.'.$valuep['fullName'].', '.$profes;
                    }
                    $profes = substr($profes, 0, -2);

            $subject = Subjects::where('subjectCode',$value['subjectCode'])->get();
            
            $results[$key]['professorId'] = $profes;
            $results[$key]['subjectCode'] = "[".$subject[0]['subjectCode']."] : ".$subject[0]['subjectName'];
        }
        return response()->json($results);
    }

    public function addCourse(Request $request){
        $newCourse = new Course;
        $newCourse->subjectCode     = $request->subjectCode;
        $newCourse->professorId     = $request->professorId;
        $newCourse->groupCode       = $request->groupCode;
        $newCourse->status          = 1;
        $newCourse->save();

        return response()->json($request);
    }

    public function editCourse(Request $request){
        Course::where('courseId',$request->courseId)
            ->update([
                'subjectCode'        => $request->subjectCode,
                'professorId'        => $request->professorId,
                'groupCode'          => $request->groupCode,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteCourse(Request $request){
        $results = Course::join('reservations','course.courseId','=','reservations.courseId')
                            ->where('course.courseId',$request->courseId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                $deleteCourse = Course::where('courseId',$request->courseId)->delete();
                $resp = array('status'=>1, 'message'=>'ลบคอร์สเรียนสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'ลบคอร์สเรียนไม่สำเร็จ');
            }
        return response()->json($resp);
    }

    public function changstatusCourse(Request $request){
        $results = Course::join('reservations','course.courseId','=','reservations.courseId')
                            ->where('course.courseId',$request->courseId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                Course::where('courseId',$request->courseId)
                        ->update(['status' => $request->status]);
                $resp = array('status'=>1, 'message'=>'เปลี่ยนสถานะคอร์สเรียนสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'เปลี่ยนสถานะคอร์สเรียนไม่สำเร็จ');
            }
        return response()->json($resp);
    }

}