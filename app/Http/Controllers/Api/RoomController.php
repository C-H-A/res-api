<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Room_type;
use App\Models\Room_cost;
use App\Models\Building;
use App\Models\Reservation;

class RoomController extends BaseController
{
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

}