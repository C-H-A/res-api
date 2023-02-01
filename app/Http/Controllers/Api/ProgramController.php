<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Faculty;
use App\Models\Department;

class ProgramController extends BaseController
{
    public function listProgram(){
        $results = Program::where('status',1)->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }

        return response()->json($results);
    }

    public function listProgram_AllStatus(){
        $results = Program::get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }

        return response()->json($results);
    }

    public function listProgram_ById($programId){
        $results = Program::where('programId',$programId)->get();

        return response()->json($results);
    } 

    public function Search_Program(Request $request){
        $results = Program::where('programId','like','%'.$request->programId.'%')
                            ->Where('programName','like','%'.$request->programName.'%')
                            ->Where('departmentId','like','%'.$request->departmentId.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }

        return response()->json($results);
    }

    public function Search_Program_Active(Request $request){
        $results = Program::where('programId','like','%'.$request->programId.'%')
                            ->Where('programName','like','%'.$request->programName.'%')
                            ->Where('departmentId','like','%'.$request->departmentId.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->where('status','1')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
        }

        return response()->json($results);
    }

    public function addProgram(Request $request){
        $newProgram = new Program;
        $newProgram->programId     = $request->programId;
        $newProgram->programName   = $request->programName;
        $newProgram->departmentId  = $request->departmentId;
        $newProgram->facultyId     = $request->facultyId;
        $newProgram->status        = 1;
        $newProgram->save();

        return response()->json($request);
    }

    public function editProgram(Request $request){
        Program::where('programId',$request->programId)
            ->update([
                'programName' => $request->programName,
                'departmentId' => $request->departmentId,
                'facultyId'       => $request->facultyId,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusProgram(Request $request){
        Program::where('programId',$request->programId)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    public function deleteProgram(Request $request){
        $deleteProgram = Program::where('programId',$request->programId)->delete();

        return response()->json($request);
    }


}