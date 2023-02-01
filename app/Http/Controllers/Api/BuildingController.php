<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Building;

class BuildingController extends BaseController
{
    public function listBuilding(){
        $results = Building::get();

        return response()->json($results);
    }

    public function getBuildingNumber($building_number){
        $results = Building::where('building_number',$building_number)->get();

        return response()->json($results);
    }

    public function listBuilding_AllStatus(){
        $results = Building::get();
        foreach($results as $key => $value){
            $results[$key]['count'] = $results->count();
        }
        return response()->json($results);
    }

    public function Search_Building(Request $request){
        $results = Building::where('building_number','like','%'.$request->building_number.'%')
                            ->where('building_name','like','%'.$request->building_name.'%')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Building::where('building_number','like','%'.$request->building_number.'%')
                            ->where('building_name','like','%'.$request->building_name.'%')
                            ->get();
        foreach($results as $key => $value){
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function addBuilding(Request $request){
        $newBuilding = new Building;
        $newBuilding->building_number  = $request->building_number;
        $newBuilding->building_name    = $request->building_name;
        $newBuilding->status           = 1;
        $newBuilding->save();

        return response()->json($request);
    }

    public function editBuilding(Request $request){
        Building::where('building_number',$request->building_number)
            ->update([
                'building_name' => $request->building_name
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteBuilding(Request $request){
        $deleteBuilding = Building::where('building_number',$request->building_number)->delete();

        return response()->json($request);
    }

    public function changstatusBuilding(Request $request){
        Building::where('building_number',$request->building_number)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

}