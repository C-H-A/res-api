<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Tools;

class ToolsController extends BaseController
{
    public function listTools(){
        $results = Tools::get();

        return response()->json($results);
    }

    public function listTool_Id($tool_id){
        $results = Tools::where('id',$tool_id)->get();

        return response()->json($results);
    }

    public function addTool(Request $request){
        $newTool = new Tools;
        $newTool->tool_name      = $request->tool_name;
        $newTool->tool_number    = $request->tool_number;
        $newTool->save();

        return response()->json($request);
    }

    public function editTool(Request $request){
        Tools::where('id',$request->tool_id)
            ->update([
                'tool_name' => $request->tool_name,
                'tool_number' => $request->tool_number
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteTool(Request $request){
        $deleteTool = Tools::where('id',$request->tool_id)->delete();

        return response()->json($request);
    }
}