<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User_level;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\URL;

class MemberController extends BaseController
{
    //เคลียร์โทเค็น
    public function clearToken($email){
        Member::where('email',$email)->update(['token' => '']);
    }

    //ตรวจสอบการอยู่ในระบบ
    public function SessionLogin(Request $request){
        $sesion = Member::where('email',$request->email)->where('token',$request->token)->where('is_admin',$request->is_admin)->get();
        if($sesion != "[]"){
            $resp = array('status'=>1, 'message'=>'Session True');
        }else{
            $resp = array('status'=>0, 'message'=>'Session Fail => Log Out');
            $this->clearToken($request->email);
        }
        return response()->json($resp);
    }

    //เข้าสู่ระบบเฉพาะบุคคลภายนอก
    public function Login(Request $request){
        $result = Member::where('email',$request->email)
                        ->where('password',md5($request->password))
                        ->where('status',1)
                        ->get();
        if($result != "[]"){
            $tokenID = Crypt::encrypt($result[0]['email']);
            Member::where('email',$request->email)->update(['token' => $tokenID]);
            $resp = array('status'=>1, 'message'=>'เข้าสู่ระบบสำเร็จ', 'data' => $result, 'token' => $tokenID);
        }else{
            $resp = array('status'=>0, 'message'=>'รหัสผ่านไม่ถูกต้อง');
        }
        return response()->json($resp);
    }

    //ตรวจสอบ username บุคคลภายในหรือภายนอก
    public function CheckTypeUser(Request $request){
        $email = explode("@rmuti",$request->email);
        // $token = str_random(60);
        $router = "https://service.eng.rmuti.ac.th/eng-login/login/?id=6&secret=RESER&msg=";
        $resp = array('status'=>0, 'message'=>'อีเมลไม่ถูกต้อง');
        if(count($email) == 1){
            $result = Member::where('email',$request->email)
                        // ->where('status',1)
                        ->select('email','levelId','status')
                        ->get();
            if($result != "[]"){
                if($result[0]['status'] == 1){
                    $resp = array('status'=>1, 'message'=>'อีเมลถูกต้อง', 'data'=>$result);
                }
                if($result[0]['status'] == 2){
                    $resp = array('status'=>3, 'message'=>'โปรดรอเจ้าหน้าอนุมัติการเข้าใช้งาน', 'data'=>$result);
                }
                // else{
                //     $resp = array('status'=>0, 'message'=>'อีเมลไม่ถูกต้อง');
                // }
            }else{
                $resp = array('status'=>0, 'message'=>'อีเมลไม่ถูกต้อง');
            }
            
            return response()->json($resp);

        }else if(count($email) >= 2){

            $resp = array('status'=>2, 'message'=>'SSO', 'link'=>$router);
            
            return response()->json($resp);
        }
    }

    //สมัครสมาชิก
    public function Register(Request $request){
        $results = Member::where('email',$request->email)->orWhere('tel',$request->tel)->get();
        $resp = array('status'=>1, 'message'=>'');
        if($results == '[]'){
            $newUser = new Member;
            $newUser->email         = $request->email;
            $newUser->password      = md5($request->password);
            $newUser->studentId     = '';
            $newUser->token         = '';
            $newUser->firstNameThai = $request->firstNameThai;
            $newUser->lastNameThai  = $request->lastNameThai;
            $newUser->tel           = $request->tel;
            $newUser->levelId       = '1';
            $newUser->is_admin      = '0';
            $newUser->status        = '2';
            $newUser->save();
            $resp = array('status'=>1, 'message'=>'ลงทะเบียนผู้ใช้สำเร็จ');
        }else{
            $resp = array('status'=>0, 'message'=>'ลงทะเบียนผู้ใช้ไม่สำเร็จ เนื่องอีเมลหรือหมายเลขโทรศัพท์ซ้ำ');
        }
        return response()->json($resp);
    }

    public function DuplicateEmail(Request $request){
        $result = Member::where('email',$request->email)->get();
        $resp = array('status'=>1, 'message'=>'');                       
        if($result == '[]'){
            $resp = array('status'=>1, 'message'=>'สามารถใช้อีเมลนี้ได้');
        }else{
            $resp = array('status'=>0, 'message'=>'อีเมลซ้ำ');
        }
        return response()->json($resp);
    }

    public function listUsers($token){
        $results = Member::whereNotIn('token',[$token])->whereIn('status',[0,1])
                            ->select('email','firstNameThai','lastNameThai','levelId','studentId','tel','status')
                            ->get();
        foreach($results as $key => $value){
            $level = User_level::where('levelId', $value['levelId'])->get();
            $results[$key]['levelId'] = $level[0]['levelName'];
        }
        return response()->json($results);
    }

    public function listUser_Mail($email){
        $results = Member::where('email',$email)
                        ->select('email','firstNameThai','lastNameThai','levelId','studentId','tel','status')
                        ->get();
        return response()->json($results);
    }

}