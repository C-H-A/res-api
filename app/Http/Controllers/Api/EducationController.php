<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Education;

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

    public function listEducation_ById($facultyId){
        $results = Education::where('educationId',$facultyId)->get();

        return response()->json($results);
    }

    public function Search_Education(Request $request){
        $results = Education::where('educationName','like','%'.$request->educationName.'%')
                            ->get();

        return response()->json($results);
    }

    public function Search_Education_Active(Request $request){
        $results = Education::where('educationName','like','%'.$request->educationName.'%')
                            ->where('status','1')
                            ->get();

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
        Education::where('educationId',$request->educationId)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    public function deleteEducation(Request $request){
        $deleteFaculty = Education::where('educationId',$request->educationId)->delete();

        return response()->json($request);
    }

}