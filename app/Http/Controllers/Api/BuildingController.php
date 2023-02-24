<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Building;

class BuildingController extends BaseController
{
    public function listBuilding(){
        $results = Building::where('status',1)->get();

        return response()->json($results);
    }

    public function listBuildingNumber($buildingNumber){
        $results = Building::where('buildingNumber',$buildingNumber)->get();

        return response()->json($results);
    }

    public function listBuilding_AllStatus(){
        $results = Building::get();
        return response()->json($results);
    }

    public function addBuilding(Request $request){
        $result = Building::where('buildingNumber',$request->buildingNumber)->get();
        $resp = array('status'=>0, 'message'=>'');
        $newBuilding = new Building;
        $newBuilding->buildingNumber  = $request->buildingNumber;
        $newBuilding->buildingName    = $request->buildingName;
        $newBuilding->status           = 1;
        if($result == '[]'){
            $newBuilding->save();
            $resp = array('status'=>1, 'message'=>'เพิ่มอาคาร : '.$request->buildingNumber.' สำเร็จ');
        }else{
            $resp = array('status'=>0, 'message'=>'เพิ่มอาคารไม่สำเร็จ หมายเหตุ : '.$request->buildingNumber.' หมายเลขอาคารซ้ำ');
        }

        return response()->json($resp);
    }

    public function editBuilding(Request $request){
        Building::where('buildingNumber',$request->buildingNumber)
            ->update([
                'buildingName' => $request->buildingName
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteBuilding(Request $request){
        $result = Building::join('room','building.buildingNumber','=','room.buildingNumber')
                            ->select('building.buildingNumber','building.buildingNumber','room.roomid')
                            ->where('building.buildingNumber',$request->buildingNumber)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($result == '[]'){
                $deleteBuilding = Building::where('buildingNumber',$request->buildingNumber)->delete();
                $resp = array('status'=>1, 'message'=>'ลบอาคารสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'ลบอาคารไม่สำเร็จ หมายเหตุ : อาคาร '.$request->buildingNumber.' มีรายการห้องอยู่');
            }

        return response()->json($resp);
    }

    public function changstatusBuilding(Request $request){
        $result = Building::join('room','building.buildingNumber','=','room.buildingNumber')
                            ->select('building.buildingNumber','building.buildingNumber','room.roomid')
                            ->where('building.buildingNumber',$request->buildingNumber)->get();
        $resp = array('status'=>1, 'message'=>'');
            if($result == '[]'){
                Building::where('buildingNumber',$request->buildingNumber)
                        ->update(['status' => $request->status]);
                $resp = array('status'=>1, 'message'=>'เปลี่ยนสถานะอาคารสำเร็จ');
            }else{
                $resp = array('status'=>0, 'message'=>'เปลี่ยนสถานะอาคารไม่สำเร็จ หมายเหตุ : อาคาร '.$request->buildingNumber.' มีรายการห้องอยู่');
            }
        return response()->json($resp);
    }

}