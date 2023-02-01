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
    public function listSubjects($page){
        $results = Subjects::where('status',1)->offset($page)->limit('10')->get();
        $all_results = Subjects::where('status',1)->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            // $result_program = Program::where('programId',$value['programId'])
            //     ->get();
            // $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function listSubject_ById($sub_code){
        $results = Subjects::where('subjectCode',$sub_code)->get();

        return response()->json($results);
    }

    public function listSubjects_AllStatus($page){
        $results = Subjects::offset($page)->limit('10')->get();
        $all_results = Subjects::get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            // $result_program = Program::where('programId',$value['programId'])
            //     ->get();
            // $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
            $results[$key]['count'] = $all_results->count();
            
        }
        // $results['count'] = $all_results->count();

        return response()->json($results);
    }

    public function Search_Subjects(Request $request){
        $results = Subjects::where('subjectCode','like','%'.$request->subjectCode.'%')
                            ->Where('subjectName','like','%'.$request->subjectName.'%')
                            ->Where('educationLevel','like','%'.$request->educationLevel.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            // ->Where('programId','like','%'.$request->programId.'%')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Subjects::where('subjectCode','like','%'.$request->subjectCode.'%')
                            ->Where('subjectName','like','%'.$request->subjectName.'%')
                            ->Where('educationLevel','like','%'.$request->educationLevel.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            // ->Where('programId','like','%'.$request->programId.'%')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            // $result_program = Program::where('programId',$value['programId'])
            //     ->get();
            // $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function Search_Subjects_Active(Request $request){
        $results = Subjects::where('subjectCode','like','%'.$request->subjectCode.'%')
                            ->Where('subjectName','like','%'.$request->subjectName.'%')
                            ->Where('educationLevel','like','%'.$request->educationLevel.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            // ->Where('programId','like','%'.$request->programId.'%')
                            ->where('status','1')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Subjects::where('subjectCode','like','%'.$request->subjectCode.'%')
                            ->Where('subjectName','like','%'.$request->subjectName.'%')
                            ->Where('educationLevel','like','%'.$request->educationLevel.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->where('status','1')
                            // ->Where('programId','like','%'.$request->programId.'%')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            // $result_program = Program::where('programId',$value['programId'])
            //     ->get();
            // $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function addSubject(Request $request){
        $newSubject = new Subjects;
        $newSubject->subjectCode          = $request->subjectCode;
        $newSubject->subjectName          = $request->subjectName;
        $newSubject->facultyId            = $request->facultyId;
        $newSubject->educationLevel       = $request->educationLevel;
        // $newSubject->programId         = $request->programId;
        $newSubject->status            = 1;
        $newSubject->save();

        return response()->json($request);
    }

    public function editSubject(Request $request){
        Subjects::where('subjectCode',$request->subjectCode)
            ->update([
                'subjectName'        => $request->subjectName,
                'facultyId'          => $request->facultyId,
                'educationLevel'     => $request->educationLevel,
                // 'programId'       => $request->programId,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusSubject(Request $request){
        Subjects::where('subjectCode',$request->subjectCode)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    public function deleteSubject(Request $request){
        $deleteSubject = Subjects::where('subjectCode',$request->subjectCode)->delete();

        return response()->json($request);
    }

}