<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Room_type;
use App\Models\Room_cost;
use App\Models\Building;
use App\Models\Reservation;
use App\Models\Images;

class RoomController extends BaseController
{
    public function SearchRoom_DateTime(Request $request){
        $results = Room::where('typeId','like','%'.$request->roomType.'%')->where('status',1)->get();
        foreach($results as $key => $value){
            $result_type = Room_type::where('typeId',$value['typeId'])
                ->get();
            $result_bd = Building::where('buildingNumber',$value['buildingNumber'])
                ->get();
            $results_reser = Reservation::where('roomId',$value['roomId'])
                                        ->where('startDate',$request->startDate)
                                        ->select('startDate','startTime','endTime')
                                        ->orderBy('startTime','asc')->get();
                foreach($results_reser as $key_reser => $value_reser){
                    $db_startDate = explode(":",$value_reser['startTime'])[0]; //ตำแหน่ง เริ่มรายการจอง
                    $db_endDate = explode(":",$value_reser['endTime'])[0]; //ตำแหน่ง สิ้นสุดรายการจอง
                    $rv_startDate = explode(":",$request->startTime)[0];
                    $stop = false; //ตัวแปรหยุดการทำงาน
                    for($i = $db_startDate;$i < $db_endDate; $i++){
                        if($db_startDate <= $rv_startDate && $rv_startDate < $db_endDate){
                            $results_reser['display'] = false;
                            $stop = true;
                            break;
                        }else{
                            $results_reser['display'] = true;
                            $stop = false;
                        }
                    }
                    if($stop){ //หยุดการทำงานของ foreach
                        break;
                    }
                }

            if($results_reser != '[]'){
                $results[$key]['reservation'] = $results_reser['display'];
            }else{
                $results[$key]['reservation'] = true;
            }
            // $results[$key]['reservation'] = $results_reser;
            $results[$key]['typeId'] = $result_type[0]['typeName'];
            $results[$key]['buildingNumber'] = $result_bd[0]['buildingNumber']." : ".$result_bd[0]['buildingName'];
        }
        return response()->json($results);
    }

    public function recommendRoom(){
        $results = Room::where('status',1)->get();
        foreach($results as $key => $value){
            $results_reser = Reservation::where('roomId',$value['roomId'])->get()->count();

            
            $results[$key]['count'] = $results_reser;
        }
        return response()->json($results);
    }

    public function listAllRoom(){
        $results = Room::where('status',1)->get();
        return response()->json($results);
    }

    public function listRoomId($roomId){
        $result = Room::where('roomId',$roomId)->get();
        foreach($result as $key => $value){
            $result_img = Images::where('imgGroup',$value['roomId'])->get();
            $result_type = Room_type::where('typeId',$value['typeId'])
                ->get();
            $result_bd = Building::where('buildingNumber',$value['buildingNumber'])
                ->get();
            
            if($result_img != '[]'){
                $result[$key]['images'] = $result_img;
            }
            $result[$key]['capacity'] = $value['capacity']." ที่นั่ง";
            $result[$key]['typeId'] = $result_type[0]['typeName'];
            $result[$key]['buildingNumber'] = "อาคาร ".$result_bd[0]['buildingNumber']." : ".$result_bd[0]['buildingName'];
        }

        return response()->json($result);
    }

    public function listRooms_AllStatus(){
        $results = Room::get();
        foreach($results as $key => $value){

            $result_type = Room_type::where('typeId',$value['typeId'])
                ->get();
            $result_bd = Building::where('buildingNumber',$value['buildingNumber'])
                ->get();
            $results[$key]['typeId'] = $result_type[0]['typeName'];
            $results[$key]['buildingNumber'] = $result_bd[0]['buildingNumber']." : ".$result_bd[0]['buildingName'];
        }

        return response()->json($results);
    }

    public function listRoomById($roomId){
        $result = Room::where('roomId',$roomId)->get();
        return response()->json($result);
    }

    public function addRoom(Request $request){
        $result = Room::where('roomId',$request->roomId)->get();
        $resp = array('status'=>0, 'message'=>'');
        $newRoom = new Room;
        $newRoom->roomId           = $request->roomId;
        $newRoom->roomName         = $request->roomName;
        $newRoom->roomDescription  = $request->roomDescription;
        $newRoom->buildingNumber   = $request->buildingNumber;
        $newRoom->capacity         = $request->capacity;
        $newRoom->tools            = $request->tools;
        $newRoom->typeId           = $request->typeId;
        $newRoom->status           = 1;
        if($result == '[]'){
            $newRoom->save();
            $resp = array('status'=>1, 'message'=>'เพิ่มห้อง : '.$request->roomId.' สำเร็จ');
        }else{
            $resp = array('status'=>0, 'message'=>'เพิ่มห้องไม่สำเร็จ หมายเหตุ : '.$request->roomId.' หมายเลขห้องซ้ำ');
        }

        return response()->json($resp);
    }

    public function editRoom(Request $request){
        Room::where('roomId',$request->roomId)
            ->update([
                'roomName'        => $request->roomName,
                'roomDescription' => $request->roomDescription,
                'buildingNumber'  => $request->buildingNumber,
                'capacity'        => $request->capacity,
                'tools'           => $request->tools,
                'typeId'          => $request->typeId
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusRoom(Request $request){
        $results = Room::join('reservations','room.roomId','=','reservations.roomId')
                            ->select('room.roomId','room.roomName','reservations.reservationId')
                            ->where('room.roomId',$request->roomId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                Room::where('roomId',$request->roomId)
                    ->update(['status' => $request->status]);
                $resp = array('status'=>1, 'message'=>'เปลี่ยนสถานะห้องสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'เปลี่ยนสถานะห้องไม่สำเร็จ หมายเหตุ : '.$request->roomId.' มีรายการจองอยู่');
            }
        return response()->json($resp);
    }

        public function deleteRoom(Request $request){
        $results = Room::join('reservations','room.roomId','=','reservations.roomId')
                            ->select('room.roomId','room.roomName','reservations.reservationId')
                            ->where('room.roomId',$request->roomId)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($results == '[]'){
                $deleteRoom = Room::where('roomId',$request->roomId)->delete();
                $resp = array('status'=>1, 'message'=>'ลบห้อง : '.$request->roomId.' สำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'ลบห้องไม่สำเร็จ หมายเหตุ : '.$request->roomId.' มีรายการจองอยู่');
            }

        return response()->json($resp);
    }

}