<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\User_level;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\URL;

class UsersController extends BaseController
{
    public function listUsers(){
        $results = Users::whereIn('status',[1,0])->whereIn('levelId',[1,2,3,4])->get();
        foreach($results as $key => $value){

            $result_level = User_level::where('levelId',$value['levelId'])
                ->get();
            
            $results[$key]['levelId'] = $result_level[0];
        }

        return response()->json($results);
    }

    public function Search_Users(Request $request){
        $results = Users::where('username','like','%'.$request->username.'%')
                            ->Where('personalId','like','%'.$request->personalId.'%')
                            ->Where('firstNameThai','like','%'.$request->firstNameThai.'%')
                            ->Where('lastNameThai','like','%'.$request->lastNameThai.'%')
                            ->Where('firstNameEng','like','%'.$request->firstNameEng.'%')
                            ->Where('lastNameEng','like','%'.$request->lastNameEng.'%')
                            ->Where('levelId','like','%'.$request->id_level.'%')
                            ->Where('levelId','not like','%'.'5%')
                            ->WhereIn('status',[1,3])
                            ->offset($request->page)->limit('10')
                            ->get();
        $all_results = Users::where('username','like','%'.$request->username.'%')
                            ->Where('personalId','like','%'.$request->personalId.'%')
                            ->Where('firstNameThai','like','%'.$request->firstNameThai.'%')
                            ->Where('lastNameThai','like','%'.$request->lastNameThai.'%')
                            ->Where('firstNameEng','like','%'.$request->firstNameEng.'%')
                            ->Where('lastNameEng','like','%'.$request->lastNameEng.'%')
                            ->Where('levelId','like','%'.$request->id_level.'%')
                            ->Where('levelId','not like','%'.'5%')
                            ->WhereIn('status',[1,3])
                            ->get();
                    foreach($results as $key => $value){

                        $result_level = User_level::where('levelId',$value['levelId'])
                            ->get();
                                
                        $results[$key]['levelId'] = $result_level[0];
                        $results[$key]['count'] = $all_results->count();
                    }

        return response()->json($results);
    }

    public function getUserById(Request $request){
        $result = Users::where('personalId',Crypt::decrypt($request->token))
                        ->select('username','personalId','prename','firstNameThai','lastNameThai','firstNameEng','lastNameEng','mail','address')->get();
        return response()->json($result);
    }

    public function getuserAll(){
        $result = Users::get();

        // return redirect('https://service.eng.rmuti.ac.th/eng-login/login/?id=6&secret=RESER&msg=');
        return response()->json($result);
    }

    public function getuserid($id){
        $result = Users::where('personalId',$id)->get();
        return response()->json($result);
    }

    public function userPetitonId($id){
        $result = Users::where('personalId',$id)->where('status','2')->get();
        foreach($result as $key => $value){

            // $result_level = User_level::where('id',$value['id_level'])
            //     ->get();
            
            $result[$key]['firstNameThai'] = $value['firstNameThai'].' '.$value['lastNameThai'];
            $result[$key]['firstNameEng'] = $value['firstNameEng'].' '.$value['lastNameEng'];
        }
        return response()->json($result);
    }

    public function petitionRegister(){
        $results = Users::where('status','2')->get();
        foreach($results as $key => $value){

            $result_level = User_level::where('levelId',$value['levelId'])
                ->get();
            
            $results[$key]['levelId'] = $result_level[0];
        }
        return response()->json($results);
    }

    public function approveUser(Request $request){
        Users::where('personalId',$request->user_id)
            ->update([
                'status' =>$request->status,
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    //ตรวจสอบ ปชช ซ้ำ
    public function DuplicatePersonalId(Request $request){
        $result = Users::where('personalId',$request->personalId)->get();
        $resp = array('status'=>1, 'message'=>'');                       
        if($result == '[]'){
            $resp = array('status'=>1, 'message'=>'สามารถใช้หมายเลขบัตรประชาชนนี้ได้');
        }else{
            $resp = array('status'=>0, 'message'=>'หมายเลขบัตรประชาชนซ้ำ');
        }
        return response()->json($resp);
    }
    //ตรวจสอบ username ซ้ำ
    public function DuplicateUsername(Request $request){
        $result = Users::where('username',$request->username)->get();
        $resp = array('status'=>1, 'message'=>'');                       
        if($result == '[]'){
            $resp = array('status'=>1, 'message'=>'สามารถใช้ชื่อผู้ใช้งานนี้ได้');
        }else{
            $resp = array('status'=>0, 'message'=>'ชื่อผู้ใช้งานซ้ำ');
        }
        return response()->json($resp);
    }
    //สมัครสมาชิก
    public function Register(Request $request){
        $results = Users::where('personalId',$request->personalId)->orWhere('username',$request->username)->get();
        $resp = array('status'=>1, 'message'=>'');
        if($results == '[]'){
            $newUser = new Users;
            $newUser->personalId    = $request->personalId;
            $newUser->username      = $request->username;
            $newUser->password      = md5($request->password);
            $newUser->studentId     = '';
            $newUser->prename       = '';
            $newUser->firstNameEng  = $request->firstNameEng;
            $newUser->lastNameEng   = $request->lastNameEng;
            $newUser->firstNameThai = $request->firstNameThai;
            $newUser->lastNameThai  = $request->lastNameThai;
            $newUser->facultyId     = '';
            $newUser->programId     = '';
            $newUser->mail          = $request->mail;
            $newUser->address       = $request->address;
            $newUser->tel           = $request->tel;
            $newUser->birthDay      = $request->birthDay;
            $newUser->levelId       = '1';
            $newUser->status        = '2';
            $newUser->save();
            $resp = array('status'=>1, 'message'=>'ลงทะเบียนผู้ใช้สำเร็จ');
        }else{
            $resp = array('status'=>0, 'message'=>'ลงทะเบียนผู้ใช้ไม่สำเร็จ เนื่องจากหมายเลขบัตรประชาชนหรือชื่อผู้ใช้งานซ้ำ');
        }

        return response()->json($resp);
    }

    public function AddUser(Request $request){
        $newUser = new Users;
        // $newUser->user_id       = $request->user_id;
        $newUser->personalId    = $request->personalId;
        $newUser->username      = $request->username;
        $newUser->password      = md5($request->password);
        $newUser->firstNameEng  = $request->firstNameEng;
        $newUser->lastNameEng   = $request->lastNameEng;
        $newUser->firstNameThai = $request->firstNameThai;
        $newUser->lastNameThai  = $request->lastNameThai;
        $newUser->prename       = $request->prename;
        $newUser->mail          = $request->mail;
        $newUser->address       = $request->address;
        $newUser->tel           = $request->tel;
        // $newUser->group_code    = $request->group_code;
        $newUser->birthDay     = $request->birth_day;
        $newUser->levelId      = $request->id_level;
        $newUser->save();

        return response()->json($newUser);
    }

    public function editUser(Request $request){
        Users::where('personalId',$request->user_id)
            ->update([
                'firstNameThai' => $request->firstNameThai,
                'lastNameThai'  => $request->lastNameThai,
                'firstNameEng'  => $request->firstNameEng,
                'lastNameEng'   => $request->lastNameEng,
                'mail'          => $request->mail,
                // 'birthDay'     => $request->birth_day,
                'address'       => $request->address,
                'tel'           => $request->tel,
                'levelId'       => $request->user_level,
            ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }
    //ตรวจสอบ username บุคคลภายในหรือภายนอก
    public function CheckUsername(Request $request){
        $username = explode(".",$request->username);
        // $token = str_random(60);
        $router = "https://service.eng.rmuti.ac.th/eng-login/login/?id=6&secret=RESER&msg=";

        if(count($username) == 1){
            $result = Users::where('username',$request->username)
                        ->where('status',1)
                        ->select('username','username','levelId')
                        ->get();
            if($result != "[]"){
                $resp = array('status'=>1, 'message'=>'ชื่อผู้ใช้งานถูกต้อง', 'data'=>$result);
            }else{
                $resp = array('status'=>0, 'message'=>'ชื่อผู้ใช้งานไม่ถูกต้อง');
            }
            
            return response()->json($resp);

        }else if(count($username) > 1 && strlen($username[1]) == 2){

            $resp = array('status'=>2, 'message'=>'SSO', 'link'=>$router);
            
            return response()->json($resp);
        }
    }
    //เข้าสู่ระบบเฉพาะบุคคลภายนอก
    public function UserLogin(Request $request){
        $result = Users::where('username',$request->username)
                        ->where('password',md5($request->password))
                        ->where('status',1)
                        ->get();
        if($result != "[]"){
            $tokenID = Crypt::encrypt($result[0]['personalId']);
            $resp = array('status'=>1, 'message'=>'เข้าสู่ระบบสำเร็จ', 'data' => $result, 'token' => $tokenID);
        }else{
            $resp = array('status'=>0, 'message'=>'รหัสผ่านไม่ถูกต้อง');
        }
        return response()->json($resp);
    }
    //รับข้อมูลจาก SSO
    public function ReceiveLogin(Request $requests){

        $router = "https://service.eng.rmuti.ac.th/eng-login/login/?id=6&secret=RESER&msg=";

        $server = "http://service.eng.rmuti.ac.th/eng-login/xmlrpc/";

        // Application ID`
        $app_id = "6";

        // Secret Key
        $secret = "RESER";

        $request = xmlrpc_encode_request("getDecrypt", array($app_id, $secret, $requests->attribs));
			$context = stream_context_create(array('http' => array(
				'method' => "POST",
				'header' => "Content-Type: text/xml",
				'content' => $request
			)));
			$file = file_get_contents($server, false, $context);
			$response = xmlrpc_decode($file);
            // return $response;
            // $msg = explode(",",$response);
            $attribs = preg_replace(array("/\[/","/\]/"),'',$response);

            $attribs = preg_replace("/\'/",'"',$attribs);

            $attribs = json_decode($attribs, true);
        
        $dataUsers = Users::where('username',$attribs["uid"])->get();
        $tokenID = "";
        if($dataUsers != "[]"){
            $tokenID = $dataUsers[0]['personalId'];
        }else{
            $newUser = new Users;
            $newUser->personalId    = $attribs["personalId"];
            $newUser->username      = $attribs["uid"];
            $newUser->password      = "-";
            // $newUser->studentId     = $attribs["studentId"]; //อาจารย์ไม่มี
            $newUser->prename       = $attribs["prename"];
            $newUser->firstNameEng  = $attribs["cn"];
            $newUser->lastNameEng   = $attribs["sn"];
            $newUser->firstNameThai = $attribs["firstNameThai"];
            $newUser->lastNameThai  = $attribs["lastNameThai"];
            // $newUser->facultyId     = $attribs["facultyId"]; //อาจารย์ไม่มี
            $newUser->mail          = $attribs["mail"];
            // $newUser->programId     = $attribs["programId"]; //อาจารย์ไม่มี
            $newUser->address       = "";
            $newUser->tel           = "-";
            $newUser->birthDay      = date("Y-m-d");
            $newUser->status        = 1;
            if($attribs["title"] == "Students"){
                $newUser->studentId     = $attribs["studentId"];
                $newUser->facultyId     = $attribs["facultyId"];
                $newUser->programId     = $attribs["programId"];
                $newUser->levelId      = "2";
            }else{
                $newUser->studentId     = "";
                $newUser->facultyId     = "";
                $newUser->programId     = "";
                $newUser->levelId      = "3";
            }
            if ($newUser->save()){
                $tokenID = $attribs["personalId"];
            }
            
        }

        // return response()->json($attribs);
        return redirect()->to('http://203.158.201.68/auth/signin/?token='.Crypt::encrypt($tokenID));
        
    }
    //สร้าง token เข้าสู่ระบบสำหรับ SSO
    public function tokenSSO(Request $request){
        $result = Users::where('personalId',Crypt::decrypt($request->token))->get();
        $resp = array('status'=>1, 'data'=>$result);
        return response()->json($resp);
    }
    //ออกจากระบบ SSO
    public function ReceiveLogout(){
        return redirect()->to('http://203.158.201.68/auth/signin');
    }
    
    public function changstatusUser(Request $request){
        Users::where('personalId',$request->user_id)
            ->update([
                'status' => $request->status
            ]);

        $resp = array('status'=>1, 'message'=>'Change Status Success');
        return response()->json($request);
    }

    public function deleteUser(Request $request){
        $deleteUser = Users::where('personalId',$request->user_id)->delete();

        return response()->json($request);
    }

    //Level User

    public function listUserLevel(){
        $result = User_level::Where('levelId','not like','%'.'5%')->get();

        return response()->json($result);
    }

}