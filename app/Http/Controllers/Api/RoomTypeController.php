<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Room_type;

class RoomTypeController extends BaseController
{
    public function listTypeRoom(){
        $results = Room_type::where('status',1)->get();
        return response()->json($results);
    }

    public function listTypeRoom_ById($typeId){
        $result = Room_type::where('typeId',$typeId)->get();
        return response()->json($result);
    }

    public function listTypeRooms_AllStatus(){
        $results = Room_type::get();
        return response()->json($results);
    }

    public function addTypeRoom(Request $request){
        $newRoomType = new Room_type;
        $newRoomType->typeName  = $request->typeName;
        $newRoomType->status    = 1;
        $newRoomType->save();

        return response()->json($request);
    }

    public function editTypeRoom(Request $request){
        Room_type::where('typeId',$request->typeId)
            ->update([
                'typeName' => $request->typeName,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteTypeRoom(Request $request){
        $results = Room_type::join('room','room_type.typeId','=','room.typeId')
                            ->select('room_type.typeId','room_type.typeName','room.roomId')
                            ->where('room_type.typeId',$request->typeId)->get();
        $resp = array('status'=>0, 'message'=>'Delete Fail');
            if($results == '[]'){
                $deleteRoomType = Room_type::where('typeId',$request->typeId)->delete();
                $resp = array('status'=>1, 'message'=>'ลบประเภทห้องสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'ลบประเภทห้องไม่สำเร็จ เนื่องจากกำลังถูกใช้งาน');
            }

        return response()->json($resp);
    }

    public function changstatusTypeRoom(Request $request){
        $results = Room_type::join('room','room_type.typeId','=','room.typeId')
                            ->select('room_type.typeId','room_type.typeName','room.roomId')
                            ->where('room_type.typeId',$request->typeId)->get();
        $resp = array('status'=>1, 'message'=>'Change Status Success');
            if($results == '[]'){
                Room_type::where('typeId',$request->typeId)
                        ->update([
                            'status' => $request->status
                        ]);
                $resp = array('status'=>1, 'message'=>'เปลี่ยนสถานะประเภทห้องสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'เปลี่ยนสถานะประเภทห้องไม่สำเร็จ');
            }

        return response()->json($resp);
    }

}