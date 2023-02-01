<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Groups;
use App\Models\Faculty;
use App\Models\Education;
use App\Models\Program;

class GroupsController extends BaseController
{
    public function listGroups($page){
        $results = Groups::where('status',1)->offset($page)->limit('10')->get();
        $all_results = Groups::where('status',1)->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            $result_program = Program::where('programId',$value['programId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
            $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function listGroups_AllStatus($page){
        $results = Groups::offset($page)->limit('10')->get();
        $all_results = Groups::get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            $result_program = Program::where('programId',$value['programId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
            $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function listGroup_Id($group_code){
        $results = Groups::where('groupCode',$group_code)->get();

        return response()->json($results);
    }

    public function Search_Groups(Request $request){
        $results = Groups::where('groupCode','like','%'.$request->group_code.'%')
                            ->Where('groupName','like','%'.$request->group_name.'%')
                            ->Where('educationLevel','like','%'.$request->education_level.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('programId','like','%'.$request->programId.'%')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Groups::where('groupCode','like','%'.$request->group_code.'%')
                            ->Where('groupName','like','%'.$request->group_name.'%')
                            ->Where('educationLevel','like','%'.$request->education_level.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('programId','like','%'.$request->programId.'%')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            $result_program = Program::where('programId',$value['programId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
            $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function Search_Groups_Active(Request $request){
        $results = Groups::where('groupCode','like','%'.$request->groupCode.'%')
                            ->Where('groupName','like','%'.$request->groupName.'%')
                            ->Where('groupLevel','like','%'.$request->groupLevel.'%')
                            ->Where('groupType','like','%'.$request->groupType.'%')
                            ->Where('educationLevel','like','%'.$request->educationLevel.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            // ->Where('programId','like','%'.$request->programId.'%')
                            ->where('status','1')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Groups::where('groupCode','like','%'.$request->groupCode.'%')
                            ->Where('groupName','like','%'.$request->groupName.'%')
                            ->Where('groupLevel','like','%'.$request->groupLevel.'%')
                            ->Where('groupType','like','%'.$request->groupType.'%')
                            ->Where('educationLevel','like','%'.$request->educationLevel.'%')
                            ->Where('facultyId','like','%'.$request->facultyId.'%')
                            ->Where('programId','like','%'.$request->programId.'%')
                            ->where('status','1')
                            ->get();
        foreach($results as $key => $value){

            $result_faculty = Faculty::where('facultyId',$value['facultyId'])
                ->get();
            $result_education = Education::where('educationId',$value['educationLevel'])
                ->get();
            $result_program = Program::where('programId',$value['programId'])
                ->get();
            $results[$key]['facultyId'] = $result_faculty[0]['facultyId']." : ".$result_faculty[0]['facultyName'];
            $results[$key]['educationLevel'] = $result_education[0]['educationName'];
            $results[$key]['programId'] = $result_program[0]['programId']." : ".$result_program[0]['programName'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function addGroup(Request $request){
        $newGroup = new Groups;
        $newGroup->groupCode      = $request->group_code;
        $newGroup->groupName      = $request->group_name;
        $newGroup->educationLevel = $request->educationLevel;
        $newGroup->facultyId      = $request->facultyId;
        $newGroup->programId      = $request->programId;
        $newGroup->status         = 1;
        $newGroup->save();

        return response()->json($request);
    }

    public function editGroup(Request $request){
        Groups::where('groupCode',$request->group_code)
            ->update([
                'groupName'      => $request->group_name,
                'educationLevel' => $request->educationLevel,
                'facultyId'       => $request->facultyId,
                'programId'       => $request->programId,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusGroup(Request $request){
        Groups::where('groupCode',$request->group_code)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($resp);
    }

    public function deleteGroup(Request $request){
        $deleteGroup = Groups::where('groupCode',$request->group_code)->delete();

        return response()->json($request);
    }
}