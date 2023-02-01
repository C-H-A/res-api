<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Room_cost;
use App\Models\Room;
use App\Models\Room_type;

class RoomCostController extends BaseController
{
    public function listRoomCost(){
        $results = Room_cost::get();
        foreach($results as $key => $value){
            $result_roomtype = Room_type::where('typeId',$value['typeId'])
            ->get();

            $results[$key]['typeId'] = $result_roomtype[0];
        }

        return response()->json($results);
    }

    public function listRoomType_ById($type_id){
        $result = Room_type::where('type_id',$type_id)->get();

        return response()->json($result);
    }

    public function listTypeRooms_AllStatus(){
        $results = Room_type::get();

        return response()->json($results);
    }

    public function calculateCost(Request $request){
        $result_room = Room::where('roomId',$request->roomId)->where('status',1)->get();
        $results = Room_cost::where('capacity','>=',$result_room[0]['capacity'])->where('typeId',$request->typeId)->orderBy('capacity','asc')->limit(1)->get();
        $resp = array('status'=>1, 'message'=>'Change Status Success');
        if($results == '[]'){
            $resp = array('status'=>0, 'message'=>'ไม่มีค่าใช้จ่ายระบุไว้อัตราค่าใช้จ่าย โปรดติดต่อเจ้าหน้าที่');
        }else if($results != '[]' && $request->levelId == 1){
            $results[0]['Price'] = $results[0]['extePrice'];
            $resp = array('status'=>1, 'message'=>'ค่าใช้จ่ายในการเช่าสถานที่ '.$results[0]['extePrice'].' บาท', 'data'=>$results);
        }else if($results != '[]' && $request->levelId >= 2){
            $results[0]['Price'] = $results[0]['intePrice'];
            $resp = array('status'=>1, 'message'=>'ค่าใช้จ่ายในการเช่าสถานที่ '.$results[0]['intePrice'].' บาท','data'=>$results);
        }
        return response()->json($resp);
    }

    public function addTypeRoom(Request $request){
        $newRoomType = new Room_type;
        $newRoomType->type_name  = $request->type_name;
        $newRoomType->status     = 1;
        $newRoomType->save();

        return response()->json($request);
    }

    public function editTypeRoom(Request $request){
        Room_type::where('type_id',$request->type_id)
            ->update([
                'type_name' => $request->type_name,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteTypeRoom(Request $request){
        $deleteRoomType = Room_type::where('type_id',$request->type_id)->delete();

        return response()->json($request);
    }

    public function changstatusTypeRoom(Request $request){
        Room_type::where('type_id',$request->type_id)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

}