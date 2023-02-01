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
    public function listCourse($page){
        $strpro = "";
        $results = Course::where('status','1')->offset($page)->limit('10')->get();
        $all_results = Course::where('status','1')->get();
        foreach($results as $key => $value){

            $results_professor_id = explode('+',$value['professorId']);
            $result_professor = Professors::whereIn('id', $results_professor_id)
                ->get();
            $result_group = Groups::where('groupCode', $value['groupCode'])
                ->get();
            $result_subject = Subjects::where('subjectCode',$value['subjectCode'])
                ->get();
            $strpro = "";
            foreach($result_professor as $key_pro => $value_pro){
                $strpro = $strpro.$value_pro['firstNameThai']." ".$value_pro['lastNameThai'].", ";
            }
            $results[$key]['professorId'] = $strpro;
            $results[$key]['subjectCode'] = $result_subject;
            // $results[$key]['group_code'] = $result_group;
            $results[$key]['count'] = $all_results->count();
        }
        return response()->json($results);
    }

    public function listCourse_Id($id){
        $result = Course::where('courseId',$id)->get();

        foreach($result as $key => $value){
            $result_subject = Subjects::where('subjectCode',$value['subjectCode'])
                ->get();
            $results_professor_id = explode('+',$value['professorId']);
            $result_professor = Professors::whereIn('id', $results_professor_id)
                ->get();
            $results_group_id = explode('+',$value['groupCode']);
            $result_group = Groups::whereIn('groupCode', $results_group_id)
                ->get();

            $result[$key]['subjectCode'] = $result_subject;
            $result[$key]['professorId'] = $result_professor;
            $result[$key]['groupCode'] = $result_group;
        }
        $date = Carbon::now()->format('D M j G:i:s T Y');
        $exp = explode(" ",$date);
        // $data = $date->format('M d Y');

        return response()->json($result);
    }

    public function listCourses_AllStatus($page){
        $strpro = "";
        $results = Course::offset($page)->limit('10')->get();
        $all_results = Course::get();
        foreach($results as $key => $value){

            $results_professor_id = explode('+',$value['professorId']);
            $result_professor = Professors::whereIn('id', $results_professor_id)
                ->get();
            $result_group = Groups::where('groupCode', $value['groupCode'])
                ->get();
            $result_subject = Subjects::where('subjectCode',$value['subjectCode'])
                ->get();
            $strpro = "";
            foreach($result_professor as $key_pro => $value_pro){
                $strpro = $strpro.$value_pro['firstNameThai']." ".$value_pro['lastNameThai'].", ";
            }
            $results[$key]['professorId'] = $strpro;
            $results[$key]['subjectCode'] = $result_subject;
            // $results[$key]['group_code'] = $result_group;
            $results[$key]['count'] = $all_results->count();
        }
        return response()->json($results);
    }

    public function searchCourse($sub_code){
        $results = Course::where('sub_code',$sub_code)->get();

        foreach($results as $key => $value){

            $result_subject = Subjects::where('sub_code',$value['sub_code'])
                ->get();
            $result_professor = Professors::where('id', $value['professor_id'])
                ->get();
            $result_group = Groups::where('group_code', $value['group_code'])
                ->get();
            $results[$key]['sub_code'] = $result_subject;
            $results[$key]['professor_id'] = $result_professor;
            $results[$key]['group_code'] = $result_group;
        }

        return response()->json($results);
    }

    //ค้นหาคอร์สเรียน
    public function Search_Course(Request $request){
        $strpro = "";
        $results = Course::join('subjects','course.subjectCode', '=','subjects.subjectCode')->join('professor','course.professorId','=','professor.id')->select('course.*','subjects.subjectCode', 'subjects.subjectName', 'subjects.educationLevel','subjects.facultyId','professor.firstNameThai','professor.lastNameThai')
                            ->where('subjects.educationLevel','like','%'.$request->educationLevel.'%')
                            ->where('subjects.facultyId','like','%'.$request->facultyId.'%')
                            ->where('subjects.subjectCode','like','%'.$request->subjectCode.'%')
                            ->where('subjects.subjectName','like','%'.$request->subjectName.'%')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Course::join('subjects','course.subjectCode', '=','subjects.subjectCode')->join('professor','course.professorId','=','professor.id')->select('course.*','subjects.subjectCode', 'subjects.subjectName', 'subjects.educationLevel','subjects.facultyId','professor.firstNameThai','professor.lastNameThai')
                            ->where('subjects.educationLevel','like','%'.$request->educationLevel.'%')
                            ->where('subjects.facultyId','like','%'.$request->facultyId.'%')
                            ->where('subjects.subjectCode','like','%'.$request->subjectCode.'%')
                            ->where('subjects.subjectName','like','%'.$request->subjectName.'%')
                            ->get();

        foreach($results as $key => $value){

            $results_professor_id = explode('+',$value['professorId']);
            $result_professor = Professors::whereIn('id', $results_professor_id)
                ->get();
            $result_group = Groups::where('groupCode', $value['groupCode'])
                ->get();
            $result_subject = Subjects::where('subjectCode',$value['subjectCode'])
                ->get();
            $strpro = "";
            foreach($result_professor as $key_pro => $value_pro){
                $strpro = $strpro.$value_pro['firstNameThai']." ".$value_pro['lastNameThai'].", ";
            }
            $results[$key]['professorId'] = $strpro;
            $results[$key]['subjectCode'] = $result_subject;
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function Search_Course_Active(Request $request){
        $strpro = "";
        $results = Course::join('subjects','course.subjectCode', '=','subjects.subjectCode')->join('professor','course.professorId','=','professor.id')->select('course.*','subjects.subjectCode', 'subjects.subjectName', 'subjects.educationLevel','subjects.facultyId','professor.firstNameThai','professor.lastNameThai')
                            ->where('subjects.educationLevel','like','%'.$request->educationLevel.'%')
                            ->where('subjects.facultyId','like','%'.$request->facultyId.'%')
                            ->where('subjects.subjectCode','like','%'.$request->subjectCode.'%')
                            ->where('subjects.subjectName','like','%'.$request->subjectName.'%')
                            ->where('course.status','1')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Course::join('subjects','course.subjectCode', '=','subjects.subjectCode')->join('professor','course.professorId','=','professor.id')->select('course.*','subjects.subjectCode', 'subjects.subjectName', 'subjects.educationLevel','subjects.facultyId','professor.firstNameThai','professor.lastNameThai')
                            ->where('subjects.educationLevel','like','%'.$request->educationLevel.'%')
                            ->where('subjects.facultyId','like','%'.$request->facultyId.'%')
                            ->where('subjects.subjectCode','like','%'.$request->subjectCode.'%')
                            ->where('subjects.subjectName','like','%'.$request->subjectName.'%')
                            ->where('course.status','1')
                            ->get();

        foreach($results as $key => $value){

            $results_professor_id = explode('+',$value['professorId']);
            $result_professor = Professors::whereIn('id', $results_professor_id)
                ->get();
            $result_group = Groups::where('groupCode', $value['groupCode'])
                ->get();
            $result_subject = Subjects::where('subjectCode',$value['subjectCode'])
                ->get();
            $strpro = "";
            foreach($result_professor as $key_pro => $value_pro){
                $strpro = $strpro.$value_pro['firstNameThai']." ".$value_pro['lastNameThai'].", ";
            }
            $results[$key]['professorId'] = $strpro;
            $results[$key]['subjectCode'] = $result_subject;
            $results[$key]['count'] = $all_results->count();
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
                // 'programId'       => $request->programId,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteCourse(Request $request){
        $deleteCourse = Course::where('courseId',$request->courseId)->delete();

        return response()->json($request);
    }

    public function changstatusCourse(Request $request){
        Course::where('courseId',$request->courseId)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

}