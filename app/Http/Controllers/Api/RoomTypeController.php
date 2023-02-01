<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Room_type;

class RoomTypeController extends BaseController
{
    public function listRoomType(){
        $results = Room_type::where('status',1)->get();

        return response()->json($results);
    }

    public function listRoomType_ById($type_id){
        $result = Room_type::where('typeId',$type_id)->get();

        return response()->json($result);
    }

    public function listTypeRooms_AllStatus(){
        $results = Room_type::get();
        foreach($results as $key => $value){
            $results[$key]['count'] = $results->count();
        }
        return response()->json($results);
    }

    public function Search_TypeRoom(Request $request){
        $results = Room_type::where('typeName','like','%'.$request->type_name.'%')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Room_type::where('typeName','like','%'.$request->type_name.'%')
                            ->get();
        foreach($results as $key => $value){
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function addTypeRoom(Request $request){
        $newRoomType = new Room_type;
        $newRoomType->typeName  = $request->type_name;
        $newRoomType->status     = 1;
        $newRoomType->save();

        return response()->json($request);
    }

    public function editTypeRoom(Request $request){
        Room_type::where('typeId',$request->type_id)
            ->update([
                'typeName' => $request->type_name,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteTypeRoom(Request $request){
        $deleteRoomType = Room_type::where('typeId',$request->type_id)->delete();

        return response()->json($request);
    }

    public function changstatusTypeRoom(Request $request){
        Room_type::where('typeId',$request->type_id)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

}