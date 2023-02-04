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
            $result_bd = Building::where('building_number',$value['buildingNumber'])
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
            $results[$key]['buildingNumber'] = $result_bd[0]['building_number']." : ".$result_bd[0]['building_name'];
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

    public function listRoom($page){
        $results = Room::where('status',1)->offset($page)->limit('10')->get();
        $all_results = Room::where('status',1)->get();
        foreach($results as $key => $value){
            $result_type = Room_type::where('typeId',$value['typeId'])
                ->get();
            $result_bd = Building::where('building_number',$value['buildingNumber'])
                ->get();
            $results[$key]['typeId'] = $result_type[0]['typeName'];
            $results[$key]['buildingNumber'] = $result_bd[0]['building_number']." : ".$result_bd[0]['building_name'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function listRoomId($id){
        $result = Room::where('roomId',$id)->get();
        foreach($result as $key => $value){
            $result_img = Images::where('imgGroup',$value['roomId'])->get();
            $result_type = Room_type::where('typeId',$value['typeId'])
                ->get();
            $result_bd = Building::where('building_number',$value['buildingNumber'])
                ->get();
            
            if($result_img != '[]'){
                $result[$key]['images'] = $result_img;
            }
            $result[$key]['capacity'] = $value['capacity']." ที่นั่ง";
            $result[$key]['typeId'] = $result_type[0]['typeName'];
            $result[$key]['buildingNumber'] = "อาคาร ".$result_bd[0]['building_number']." : ".$result_bd[0]['building_name'];
        }

        return response()->json($result);
    }

    public function listRooms_AllStatus($page){
        $results = Room::offset($page)->limit('10')->get();
        $all_results = Room::get();
        foreach($results as $key => $value){

            $result_type = Room_type::where('typeId',$value['typeId'])
                ->get();
            $result_bd = Building::where('building_number',$value['buildingNumber'])
                ->get();
            $results[$key]['typeId'] = $result_type[0]['typeName'];
            $results[$key]['buildingNumber'] = $result_bd[0]['building_number']." : ".$result_bd[0]['building_name'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function SearchRoom(Request $request){
        $result = Room::Where('roomType',$request->type)->OrWhere('floor_number',$request->floor)->Where('roomId',$request->roomnum)->get();
        return response()->json($result);
    }

    public function Search_Room(Request $request){
        $results = Room::where('roomId','like','%'.$request->room_id.'%')
                            ->Where('roomName','like','%'.$request->room_name.'%')
                            ->Where('buildingNumber','like','%'.$request->bd_num)
                            ->Where('typeId','like','%'.$request->type_id.'%')
                            ->Where('capacity','like','%'.$request->capacity.'%')
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Room::where('roomId','like','%'.$request->room_id.'%')
                            ->Where('roomName','like','%'.$request->room_name.'%')
                            ->Where('buildingNumber','like','%'.$request->bd_num)
                            ->Where('typeId','like','%'.$request->type_id.'%')
                            ->Where('capacity','like','%'.$request->capacity.'%')
                            ->get();
        foreach($results as $key => $value){

            $result_type = Room_type::where('typeId',$value['typeId'])
                ->get();
            $result_bd = Building::where('building_number',$value['buildingNumber'])
                ->get();
            $results[$key]['typeId'] = $result_type[0]['typeName'];
            $results[$key]['buildingNumber'] = $result_bd[0]['building_number']." : ".$result_bd[0]['building_name'];
            $results[$key]['count'] = $all_results->count();
        }

        return response()->json($results);
    }

    public function listRoomByType($typeId){
        $result = Room::where('typeId',$typeId)->where('status',1)->get();

            foreach($result as $key => $value){
                $result_roomtype = Room_type::where('typeId',$value['typeId'])->get();
                $result_roomcost = Room_cost::where('costId',$value['costId'])->get();

                $result[$key]['typeId'] = $result_roomtype[0];
                if($result_roomcost != "[]"){
                    $result[$key]['costId'] = $result_roomcost[0];
                    
                }else{
                    $result[$key]['costId'] = array("extePrice"=>"กรุณาติดต่อขอรายละเอียดเพิ่มเติมกับเจ้าหน้าที่", "intePrice"=>0);
                }
            }
        return response()->json($result);
    }

    public function listRoomByType_Page(Request $request){
        $results = Room::where('typeId',$request->typeId)->where('status',1)->offset($request->page)->limit('10')->get();
        $all_results = Room::where('typeId',$request->typeId)->where('status',1)->get();
        
        foreach($results as $key => $value){
            $result_type = Room_type::where('typeId',$value['typeId'])
                ->get();
            $result_bd = Building::where('building_number',$value['buildingNumber'])
                ->get();
            $results[$key]['typeId'] = $result_type[0]['typeName'];
            $results[$key]['buildingNumber'] = $result_bd[0]['building_number']." : ".$result_bd[0]['building_name'];
            $results[$key]['count'] = $all_results->count();
        }
        return response()->json($results);
    }

    public function addRoom(Request $request){
        $newRoom = new Room;
        $newRoom->roomId          = $request->room_id;
        $newRoom->roomName        = $request->room_name;
        $newRoom->roomDescription = $request->room_description;
        $newRoom->buildingNumber  = $request->bd_num;
        $newRoom->capacity         = $request->capacity;
        $newRoom->tools            = $request->tools;
        $newRoom->typeId          = $request->type_id;
        $newRoom->status           = 1;
        $newRoom->save();

        return response()->json($request);
    }

    public function editRoom(Request $request){
        Room::where('roomId',$request->room_id)
            ->update([
                'roomName'        => $request->room_name,
                'roomDescription' => $request->room_description,
                'buildingNumber'  => $request->bd_num,
                'capacity'        => $request->capacity,
                'tools'           => $request->tools,
                'typeId'          => $request->type_id
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changstatusRoom(Request $request){
        Room::where('roomId',$request->room_id)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

        public function deleteRoom(Request $request){
        $deleteRoom = Room::where('roomId',$request->roomId)->delete();

        return response()->json($request);
    }

    //Room Type
    public function listRoomType(){
        $results = Room_type::get();

        return response()->json($results);
    }

    public function listRoomType_ById($type_id){
        $result = Room_type::where('type_id',$type_id)->get();

        return response()->json($result);
    }

    public function listTimeStart(Request $request){
        date_default_timezone_set('Asia/Bangkok');
        $today = date("Y-m-d");
        $timetoday = date("H:i:s");
        $timearr = array(
                    ["value"=>"","time"=>"--- เลือกเวลาเริ่มต้น ---","disabled"=>false],
                    ["value"=>"08:00:00","time"=>"08:00","disabled"=>false],
                    ["value"=>"09:00:00","time"=>"09:00","disabled"=>false],
                    ["value"=>"10:00:00","time"=>"10:00","disabled"=>false],
                    ["value"=>"11:00:00","time"=>"11:00","disabled"=>false],
                    ["value"=>"12:00:00","time"=>"12:00","disabled"=>false],
                    ["value"=>"13:00:00","time"=>"13:00","disabled"=>false],
                    ["value"=>"14:00:00","time"=>"14:00","disabled"=>false],
                    ["value"=>"15:00:00","time"=>"15:00","disabled"=>false],
                    ["value"=>"16:00:00","time"=>"16:00","disabled"=>false],
                    ["value"=>"17:00:00","time"=>"17:00","disabled"=>false],
                    ["value"=>"18:00:00","time"=>"18:00","disabled"=>false],
                    ["value"=>"19:00:00","time"=>"19:00","disabled"=>false],
                    ["value"=>"20:00:00","time"=>"20:00","disabled"=>false],
                    ["value"=>"21:00:00","time"=>"21:00","disabled"=>false],
                    ["value"=>"22:00:00","time"=>"22:00","disabled"=>false],
                    );
        
        if($today == $request->date){
            for($i = 0; $i <= sizeof($timearr)-1; $i++){
                if((explode(":",$timetoday)[0]) > (explode(":",$timearr[$i]['value'])[0])){
                    $timearr[$i]['disabled'] = true;
                }else{
                    $timearr[$i]['disabled'] = false;
                }
            }
        }else{
            for($i = 0; $i <= sizeof($timearr)-1; $i++){
                $timearr[$i]['disabled'] = false;
            }
        }
        return response()->json($timearr);
    }

    public function listTimeEnd(Request $request){
        date_default_timezone_set('UTC');
        $today = date("Y-m-d");
        $timetoday = date("H:i:s");
        $timearr = array(
                    ["value"=>"08:00:00","time"=>"08:00","disabled"=>false],
                    ["value"=>"09:00:00","time"=>"09:00","disabled"=>false],
                    ["value"=>"10:00:00","time"=>"10:00","disabled"=>false],
                    ["value"=>"11:00:00","time"=>"11:00","disabled"=>false],
                    ["value"=>"12:00:00","time"=>"12:00","disabled"=>false],
                    ["value"=>"13:00:00","time"=>"13:00","disabled"=>false],
                    ["value"=>"14:00:00","time"=>"14:00","disabled"=>false],
                    ["value"=>"15:00:00","time"=>"15:00","disabled"=>false],
                    ["value"=>"16:00:00","time"=>"16:00","disabled"=>false],
                    ["value"=>"17:00:00","time"=>"17:00","disabled"=>false],
                    ["value"=>"18:00:00","time"=>"18:00","disabled"=>false],
                    ["value"=>"19:00:00","time"=>"19:00","disabled"=>false],
                    ["value"=>"20:00:00","time"=>"20:00","disabled"=>false],
                    ["value"=>"21:00:00","time"=>"21:00","disabled"=>false],
                    ["value"=>"22:00:00","time"=>"22:00","disabled"=>false],
                    );
        if($today == $request->date){
            for($i = 0; $i <= sizeof($timearr); $i++){
                if($timearr[$i]['value'] == $request->time){
                    $timearr[$i]['disabled'] == true;
                }
            }
        }

        return response()->json($timearr);
    }

}