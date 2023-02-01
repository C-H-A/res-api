<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Professors;
use App\Models\Faculty;
use App\Models\Program;

class ProfessorsController extends BaseController
{
    public function listProfessors($page){
        $results = Professors::where('status',1)->offset($page)->limit('10')->get();
        $all_results = Professors::where('status',1)->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_program = Program::where('programId',$value['programId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function listProfessor_ById($professor_id){
        $results = Professors::where('id',$professor_id)->get();

        return response()->json($results);
    }

    public function listProfessors_AllStatus($page){
        $results = Professors::offset($page)->limit('10')->get();
        $all_results = Professors::get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_program = Program::where('programId',$value['programId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function Search_Professors(Request $request){
        $results = Professors::where('firstNameThai','like','%'.$request->firstNameThai.'%')
                            ->Where('lastNameThai','like','%'.$request->lastNameThai.'%')
                            ->Where('firstNameEng','like','%'.$request->firstNameEng.'%')
                            ->Where('lastNameEng','like','%'.$request->lastNameEng.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('programId','like','%'.$request->programId.'%')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Professors::where('firstNameThai','like','%'.$request->firstNameThai.'%')
                            ->Where('lastNameThai','like','%'.$request->lastNameThai.'%')
                            ->Where('firstNameEng','like','%'.$request->firstNameEng.'%')
                            ->Where('lastNameEng','like','%'.$request->lastNameEng.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('programId','like','%'.$request->programId.'%')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_program = Program::where('programId',$value['programId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function Search_Professors_Active(Request $request){
        $results = Professors::where('firstNameThai','like','%'.$request->firstNameThai.'%')
                            ->Where('lastNameThai','like','%'.$request->lastNameThai.'%')
                            ->Where('firstNameEng','like','%'.$request->firstNameEng.'%')
                            ->Where('lastNameEng','like','%'.$request->lastNameEng.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('programId','like','%'.$request->programId.'%')
                            ->where('status',1)
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Professors::where('firstNameThai','like','%'.$request->firstNameThai.'%')
                            ->Where('lastNameThai','like','%'.$request->lastNameThai.'%')
                            ->Where('firstNameEng','like','%'.$request->firstNameEng.'%')
                            ->Where('lastNameEng','like','%'.$request->lastNameEng.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('programId','like','%'.$request->programId.'%')
                            ->where('status',1)
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_program = Program::where('programId',$value['programId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function addProfessor(Request $request){
        $newProfessor = new Professors;
        $newProfessor->prename            = $request->prename;
        $newProfessor->firstNameThai      = $request->firstNameThai;
        $newProfessor->lastNameThai       = $request->lastNameThai;
        $newProfessor->firstNameEng       = $request->firstNameEng;
        $newProfessor->lastNameEng        = $request->lastNameEng;
        $newProfessor->programId          = $request->programId;
        $newProfessor->facultyId          = $request->facultyId;
        $newProfessor->status             = 1;
        $newProfessor->save();

        return response()->json($request);
    }

    public function editProfessor(Request $request){
        Professors::where('id',$request->professor_id)
            ->update([
                'prename'          => $request->prename,
                'firstNameThai'    => $request->firstNameThai,
                'lastNameThai'     => $request->lastNameThai,
                'firstNameEng'     => $request->firstNameEng,
                'lastNameEng'      => $request->lastNameEng,
                'facultyId'        => $request->facultyId,
                'programId'        => $request->programId,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusProfessor(Request $request){
        Professors::where('id',$request->professor_id)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    public function deleteProfessor(Request $request){
        $deleteSubject = Professors::where('id',$request->professor_id)->delete();

        return response()->json($request);
    }
}