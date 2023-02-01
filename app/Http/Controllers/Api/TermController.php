<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Term;

class TermController extends BaseController
{
    public function listTermNo(){
        $results = Term::get();
        $termNo = array(0);
        foreach($results as $key => $value){
            array_push($termNo,$value['termNo']);
            if($termNo[$key] == $value['termNo']){
                unset($termNo[$key]);
            }
        }
        $obj = (object)$termNo;
        return response()->json($obj);
    }

    public function listTerm_Id($termId){
        $results = Term::where('termId',$termId)->get();
        foreach($results as $key => $value){
            $results_term_id = explode('+',$value['termId']);

            $results[$key]['Id'] = $results_term_id[0].'/'.$results_term_id[1];
          }
        return response()->json($results);
    }
  
    public function listTerm_AllStatus($page){
        $results = Term::offset($page)->limit('10')->orderBy('termYear','desc')->get();
        $all_results = Term::get();
          foreach($results as $key => $value){
            $results_term_id = explode('+',$value['termId']);

            $results[$key]['Id'] = $results_term_id[0].'/'.$results_term_id[1];
            $results[$key]['count'] = $all_results->count();
          }
        //   $results['No'] = $all_results->count();
          return response()->json($results);
    }

    public function Search_Term(Request $request){
        $results = Term::where('termNo','like','%'.$request->termNo.'%')->where('termYear','like','%'.$request->termYear.'%')->offset($request->page)->limit('10')->orderBy('termYear','desc')->get();
        $all_results = Term::where('termNo','like','%'.$request->termNo.'%')->where('termYear','like','%'.$request->termYear.'%')->get();
          foreach($results as $key => $value){
            $results_term_id = explode('+',$value['termId']);

            $results[$key]['Id'] = $results_term_id[0].'/'.$results_term_id[1];
            $results[$key]['count'] = $all_results->count();
          }
          return response()->json($results);
    }

    public function addTerm(Request $request){
        $newTerm = new Term;
        $newTerm->termId            = $request->termNo.'+'.$request->termYear;
        $newTerm->termNo            = $request->termNo;
        $newTerm->termYear          = $request->termYear;
        $newTerm->termDescription   = $request->termDescription;
        $newTerm->status            = 0;
        $newTerm->save();

        if($newTerm != 1){
            $request = "มีภาคเรียนอยู่แล้ว";
        }
        return response()->json($request);
    }

    public function editTerm(Request $request){
        Term::where('termId',$request->termId)
            ->update([
                'termNo'          => $request->termNo,
                'termYear'        => $request->termYear,
                'termDescription' => $request->termDescription
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function changestatusTerm(Request $request){
        Term::where('termId',$request->termId)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    public function deleteTerm(Request $request){
        $deleteTerm = Term::where('termId',$request->termId)->delete();

        return response()->json($request);
    }
}