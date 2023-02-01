<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Room_type;
use App\Models\User;
use App\Models\User_level;
use App\Models\Room;
use App\Models\Subjects;
use App\Models\Groups;
use App\Models\Tool;
use App\Models\Room_cost;
use App\Models\Reser_status;
use App\Models\Course;
use App\Models\Professors;
use App\Models\Users;
use App\Models\Faculty;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class ReservationController extends BaseController
{
    public function getreservationAll(){
        $result = Reservation::get();
        
        foreach($result as $key => $value){
            // echo $value['id_type'];

            $result_roomtype = Room_type::where('type_id',$value['type_id'])
                // ->join('room_type','room_type.id_type', '=', 'reservation.id_type')
                ->get();
            $result_status = Reser_status::where('reser_status_id', $value['reser_status_id'])
                ->get();
            $result[$key]['type_id'] = $result_roomtype;
            $result[$key]['reser_status_id'] = $result_status;
        }

        return response()->json($result);
    }

    public function getreservationid($id){
        $result = Reservation::where('reservationId',$id)->get();

        foreach($result as $key => $value){
            $result_roomtype = Room_type::where('typeId',$value['roomType'])->get();
            if($value['reservationStatus'] > 3){
                
            }else{
                $result_status = Reser_status::where('reser_status_id', $value['reservationStatus'])->get();
                $result[$key]['reservationStatus'] = $result_status[0];
            }
            $result_user = Users::where('personalId',$value['userId'])->select('username','firstNameThai','lastNameThai','levelId')->get();
            foreach($result_user as $key_user => $value_user){
                $result_level = User_level::where('levelId',$value_user['levelId'])->get();

                $result_user[$key_user]['levelName'] = $result_level[0]['levelName'];
            }
            if($value['courseId'] != 0){
                $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                                    ->where('course.courseId','like','%'.$value['courseId'].'%')
                                    ->get();
                $result[$key]['courseId'] = $result_course[0];
            }else{
                $fakecourse = (object) array('courseId' => '','subjectCode' => '[บุคคลภายนอก]','subjectName' => $value['reservationDescription'],'groupCode' => '');
                $result[$key]['courseId'] = $fakecourse;
            }
            if($result[$key]['costId'] != 0){
                $result_cost = Room_cost::where('costId',$value['costId'])->get();
                if($result_user[0]['levelId'] == 1){
                    $result[$key]['costId'] = 'ค่าใช้จ่าย '.$result_cost[0]['extePrice'].' บาท';
                }else{
                    $result[$key]['costId'] = 'ค่าใช้จ่าย '.$result_cost[0]['intePrice'].' บาท';
                } 
            }else{
                $result[$key]['costId'] = 'ไม่มีค่าใช้จ่าย';
            }
            $result[$key]['reservationDay'] = $this->setDay($result[$key]['reservationDay']);
            $result[$key]['startDate'] = $this->setDate($result[$key]['startDate']);
            $result[$key]['endDate'] = $this->setDate($result[$key]['endDate']);

            $result[$key]['startTime'] = $this->setTime($result[$key]['startTime']);
            $result[$key]['endTime'] = $this->setTime($result[$key]['endTime']);

            $result[$key]['roomType'] = $result_roomtype[0];
            $result[$key]['userId'] = $result_user[0];
        }
        return response()->json($result);
    }

    public function getreservationWhereRoom(Request $request){
        $result = Reservation::where('room_id',$request->room_id)->where('type_id',$request->type_id)->where('reser_status_id',1)->get();
        
        foreach($result as $key => $value){
            // echo $value['id_type'];

            $result_roomtype = Room_type::where('type_id',$value['type_id'])
                // ->join('room_type','room_type.id_type', '=', 'reservation.id_type')
                ->get();
            $result_course = Course::where('course_id',$value['course_id'])->get();
            // $result_status = Reser_status::where('status_id', $value['reserstatus_id'])
            //     ->get();
            $result[$key]['type_id'] = $result_roomtype;
            $result[$key]['course_id'] = $result_course;
            // $result[$key]['reserstatus_id'] = $result_status;
        }

        return response()->json($result);
    }

    public function getreservationWhereUser(Request $request){
        $result = Reservation::where('userId',Crypt::decrypt($request->token))->whereIn('reservationStatus',[2,4])->get();
        
        foreach($result as $key => $value){
            // $result_course = Course::where('courseId',$value['courseId'])->get();
            if($value['courseId'] != 0){
                $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                                    ->where('course.courseId','like','%'.$value['courseId'].'%')
                                    ->get();

                $result[$key]['courseId'] = $result_course[0]; 
            }else{
                $fakecourse = (object) array('courseId' => '','subjectCode' => '[บุคคลภายนอก]','subjectName' => $value['reservationDescription'],'groupCode' => '');
                $result[$key]['courseId'] = $fakecourse;
            }
            $result_roomtype = Room_type::where('typeId',$value['roomType'])->get();

            $result[$key]['reserDay'] = $this->setDate($value['startDate']);
            $result[$key]['reserTime'] = $this->setTime($value['startTime'])." - ".$this->setTime($value['endTime']); 
            $result[$key]['roomType'] = $result_roomtype[0];
        }

        return response()->json($result);
    }

    public function getreservationWhereUserActive(Request $request){
        $result = Reservation::where('userId',Crypt::decrypt($request->token))
                                ->whereIn('reservationStatus',[1,3])
                                ->orderBy('reservationId','desc')->get();
        
        foreach($result as $key => $value){
            if($value['courseId'] != 0){
                $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                                    ->where('course.courseId','like','%'.$value['courseId'].'%')
                                    ->get();

                $result[$key]['courseId'] = $result_course[0]; 
            }else{
                $fakecourse = (object) array('courseId' => '','subjectCode' => '[บุคคลภายนอก]','subjectName' => $value['reservationDescription'],'groupCode' => '');
                $result[$key]['courseId'] = $fakecourse;
            }
            $result_roomtype = Room_type::where('typeId',$value['roomType'])->get();

            $result[$key]['reserDay'] = $this->setDate($value['startDate']);
            $result[$key]['reserTime'] = $this->setTime($value['startTime'])." - ".$this->setTime($value['endTime']); 
            $result[$key]['roomType'] = $result_roomtype[0];
        }

        return response()->json($result);
    }


    public function Search_ReservationByUser(Request $request){
        $result = Reservation::where('userId',Crypt::decrypt($request->token))
                                ->where('roomId','like','%'.$request->roomId.'%')
                                ->where('roomType','like','%'.$request->roomType.'%')
                                ->where('startDate','like','%'.$request->reserDate.'%')
                                ->whereIn('reservationStatus',[1,3])
                                ->orderBy('reservationId','desc')->get();
        
        foreach($result as $key => $value){
            // $result_course = Course::where('courseId',$value['courseId'])->get();
            if($value['courseId'] != 0){
                $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                                    ->where('course.courseId','like','%'.$value['courseId'].'%')
                                    ->get();

                $result[$key]['courseId'] = $result_course[0]; 
            }else{
                $fakecourse = (object) array('courseId' => '','subjectCode' => '[บุคคลภายนอก]','subjectName' => $value['reservationDescription'],'groupCode' => '');
                $result[$key]['courseId'] = $fakecourse;
            }
            $result_roomtype = Room_type::where('typeId',$value['roomType'])->get();

            $result[$key]['reserDay'] = $this->setDate($value['startDate']);
            $result[$key]['reserTime'] = $this->setTime($value['startTime'])." - ".$this->setTime($value['endTime']); 
            $result[$key]['roomType'] = $result_roomtype[0];
        }

        return response()->json($result);
    }

    public function getReservationDay(Request $request){
        // $dateReser = Carbon::parse($request->reserDate)->format('D M j G:i:s T Y');
        // $exp = explode(" ",$dateReser);
        $result = Reservation::where('roomType',$request->roomType)
                                ->where('roomId',$request->roomId)
                                ->where('startDate',$request->reserDate)
                                ->where('reservationType',1)
                                // ->where('reservationStatus',3)
                                // ->orwhere('reservationDay',$exp[0])
                                ->whereIn('reservationStatus',[1,2])
                                ->orderBy('startTime', 'asc')->get();

        foreach($result as $key => $value){
            // $result_course = Course::where('courseId',$value['courseId'])->get();
            if($value['courseId'] != 0){
                $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                ->where('course.courseId','like','%'.$value['courseId'].'%')
                ->get();
                $result[$key]['courseId'] = $result_course[0]; 
            }else{
                $fakecourse = (object) array('courseId' => '','subjectCode' => '[บุคคลภายนอก]','subjectName' => $value['reservationDescription'],'groupCode' => '');
                $result[$key]['courseId'] = $fakecourse;
            }
            $result_roomtype = Room_type::where('typeId',$value['roomType'])->get();

            $result[$key]['reserDay'] = $this->setDate($value['startDate']);
            $result[$key]['reserTime'] = $this->setTime($value['startTime'])." - ".$this->setTime($value['endTime']); 
            $result[$key]['roomType'] = $result_roomtype[0];
        }

        // $date = Carbon::now()->format('D M j G:i:s T Y');
        // $exp = explode(" ",$date);
        // $createdAt = Carbon::parse($request->reserDate)->format('D M j G:i:s T Y');
        // $data = $date->format('M d Y');

        return response()->json($result);
    }

    //เพิ่มรายการจองห้อง
    public function addReservation(Request $request){
        $resp = array('status'=>1, 'message'=>'Edit success');
        $result_user = Users::where('personalId',Crypt::decrypt($request->userId))->get();
        $result_reser = Reservation::where('roomType',$request->roomType)
                                ->where('roomId',$request->roomId)
                                ->where('startDate',$request->startDate)
                                ->where('reservationType',1)
                                ->where('reservationStatus',1)->get();
        $req_startdate = explode(":",$request->startTime);
        
        foreach($result_reser as $key => $value){
            $res_startdate = explode(":",$result_reser[$key]['startTime']);
            if($res_startdate[0] == $req_startdate[0]){
                $resp = array('status'=>$res_startdate[0], 'message'=>$req_startdate[0]);
            }else{
                $resp = array('status'=>$result_reser[$key]['startTime'], 'message'=>$request->startTime);
            }
        }
        $newReser = new Reservation;
        if($result_user){
            $newReser->courseId               = $request->courseId;
            $newReser->roomId                 = $request->roomId;
            $newReser->roomType               = $request->roomType;
            $newReser->userId                 = $result_user[0]['personalId'];
            $newReser->startDate              = $request->startDate;
            $newReser->endDate                = $request->endDate;
            $newReser->startTime              = $request->startTime;
            $newReser->endTime                = $request->endTime;
            $newReser->reservationDay         = $request->reservationDay;
            $newReser->reservationDescription = $request->reservationDescription;
            $newReser->reservationType        = $request->reservationType;
            $newReser->costId                 = $request->costId;
            $newReser->reservationStatus      = 2;
            $newReser->adminId                = "";
            $newReser->reservationNote        = "";
            $newReser->notificationStatus     = 1;
            $newReser->save();
        }
        // $newReser->save();

        return response()->json($resp);
    }

    public function editReservation(Request $request){
        Reservation::where('reser_id',$request->reser_id)
        ->update([
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'start_time'        => $request->start_time,
            'end_time'          => $request->end_time,
            'reser_description' => $request->reser_description,
            'room_id'           => $request->room_id,
            'group_code'        => $request->group_code,
            'id_type'           => $request->id_type,
            'user_id'           => $request->user_id,
            'sub_code'          => $request->sub_code,
            'cost_id'           => $request->cost_id,
            'reserstatus_id'    => $request->reserstatus_id,
            'teacher_id'        => $request->teacher_id
        ]);

        $resp = array('status'=>1, 'message'=>'Edit success');
        return response()->json($resp);
    }

    public function deleteReservation(){
        
    }

    //ดึงข้อมูลคำร้องการจ้องรายวัน
    public function petitionReservationDay(){
        $result = Reservation::where('reservationType',1)->where('reservationStatus',2)->get();
        foreach($result as $key => $value){
            // $result_course = Course::where('courseId',$value['courseId'])->get();
            $result_user = Users::where('personalId',$value['userId'])->select(['username','firstNameThai','lastNameThai','levelId'])->get();
            foreach($result_user as $key_user => $value_user){
                $result_level = User_level::where('levelId',$value_user['levelId'])->get();

                $result_user[$key_user]['levelId'] = $result_level[0]['levelName'];
            }
            if($value['courseId'] != 0){
                $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                                    ->where('course.courseId','like','%'.$value['courseId'].'%')
                                    ->get();
                $result[$key]['courseId'] = $result_course[0];
            }else{
                $fakecourse = (object) array('courseId' => '','subjectCode' => '[บุคคลภายนอก]','subjectName' => $value['reservationDescription'],'groupCode' => '');
                $result[$key]['courseId'] = $fakecourse;
            }
            $result_roomtype = Room_type::where('typeId',$value['roomType'])->get();

            $result[$key]['reserDay'] = $this->setDate($value['startDate']);
            $result[$key]['reserTime'] = $this->setTime($value['startTime'])." - ".$this->setTime($value['endTime']);  
            $result[$key]['roomType'] = $result_roomtype[0];
            $result[$key]['userId'] = $result_user[0];
        }

        return response()->json($result);
    }

    public function editpetitionReservation(Request $request){
        $result_user = Users::where('personalId',Crypt::decrypt($request->adminId))->whereIn('levelId',[4,5])->get();
        Reservation::where('reservationId',$request->reservationId)
        ->update([
            'reservationStatus'  => $request->reservationStatus,
            'adminId'            => $result_user[0]['personalId'],
            'reservationNote'    => $request->reservationNote
        ]);

        $resp = array('status'=>1, 'message'=>'Success');
        return response()->json($request);
    }

    public function getTestReservationTerm($room_id){
        $results = Reservation::where('reservationType','2')
                                ->where('roomId',$room_id)
                                ->whereIn('reservationStatus',[1,4])
                                ->orderBy('startTime', 'asc')->get();

        foreach($results as $key => $value){
            $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                                    ->where('course.courseId','like','%'.$value['courseId'].'%')
                                    ->get();
            $result_room = Room::where('roomId',$value['roomId'])->get();
            foreach($result_course as $key_course => $value_course){
                $result_subject = Subjects::where('subjectCode',$value_course['subjectCode'])->get();

                $result_professor = Professors::where('id',$value_course['professorId'])->get();
            }
            // $result_course[0]['professor_id'] = $result_professor[0]['professor_name_th']." ".$result_professor[0]['professor_surname_th'];
            // $result_roomtype = Room_type::where('id_type',$value['id_type'])->get();

            $results[$key]['courseId'] = $result_course[0];
            // $results[$key]['user_id'] = $result_user;
        }

        return response()->json($results);
    }

    public function petitionReservationTerm($roomId){
        $result = Reservation::where('reservationType',2)->where('roomId',$roomId)->whereIn('reservationStatus',[2,4])->get();
        foreach($result as $key => $value){
            // $result_course = Course::where('courseId',$value['courseId'])->get();
            $result_user = Users::where('personalId',$value['userId'])->select(['username','firstNameThai','lastNameThai','levelId'])->get();
            foreach($result_user as $key_user => $value_user){
                $result_level = User_level::where('levelId',$value_user['levelId'])->get();

                $result_user[$key_user]['levelId'] = $result_level[0]['levelName'];
            }
            if($value['courseId'] != 0){
                $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                                    ->where('course.courseId','like','%'.$value['courseId'].'%')
                                    ->get();
                $result[$key]['courseId'] = $result_course[0];
            }else{
                $fakecourse = (object) array('courseId' => '','subjectCode' => '[บุคคลภายนอก]','subjectName' => $value['reservationDescription'],'groupCode' => '');
                $result[$key]['courseId'] = $fakecourse;
            }
            $result_roomtype = Room_type::where('typeId',$value['roomType'])->get();

            $result[$key]['reserDay'] = $this->setDay($value['reservationDay']);
            $result[$key]['reserTime'] = $this->setTime($value['startTime'])." - ".$this->setTime($value['endTime']);  
            $result[$key]['roomType'] = $result_roomtype[0];
            $result[$key]['userId'] = $result_user[0];
        }

        return response()->json($result);
    }

    public function edittestReservationTerm(Request $request){
        $result_user = Users::where('personalId',Crypt::decrypt($request->adminId))->whereIn('levelId',[4,5])->get();
        Reservation::where('reservationId',$request->reservationId)
        ->update([
            'reservationStatus'  => $request->reservationStatus,
        ]);

        $resp = array('status'=>1, 'message'=>'Success');
        return response()->json($request);
    }

    public function setDate($date){
        $changeDate = "";
        $defaultDate = explode("-", $date);
        $arrMonth = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฏาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม',''];
        for($i = 0; $i < count($arrMonth); $i++){
            if($defaultDate[1] == $i){
                $changeDate = $arrMonth[$i-1];
                $defaultDate[0] = $defaultDate[0]+543;
            }
        }
        return $defaultDate[2].' '.$changeDate.' '.$defaultDate[0];
    }

    public function setTime($time){
        $changTime = "";
        $defaultTime = explode(":", $time);


        return $defaultTime[0].'.'.$defaultTime[1];
    }

    //term
    public function getReservationTerm($room_id){
        $results = Reservation::where('reservationType','2')
                                ->where('roomId',$room_id)
                                ->whereIn('reservationStatus',[1,2])
                                ->orderBy('startTime', 'asc')->get();

        foreach($results as $key => $value){
            $result_course = Course::join('subjects','course.subjectCode','=','subjects.subjectCode')->select('course.courseId','course.groupCode','subjects.subjectCode','subjects.subjectName')
                                    ->where('course.courseId','like','%'.$value['courseId'].'%')
                                    ->get();
            $result_room = Room::where('roomId',$value['roomId'])->get();
            foreach($result_course as $key_course => $value_course){
                $result_subject = Subjects::where('subjectCode',$value_course['subjectCode'])->get();

                $result_professor = Professors::where('id',$value_course['professorId'])->get();
            }
            // $result_course[0]['professor_id'] = $result_professor[0]['professor_name_th']." ".$result_professor[0]['professor_surname_th'];
            // $result_roomtype = Room_type::where('id_type',$value['id_type'])->get();

            $results[$key]['courseId'] = $result_course[0];
            // $results[$key]['user_id'] = $result_user;
        }

        return response()->json($results);
    }

    public function addReservationTerm(Request $request){
        $resp = array('status'=>1, 'message'=>'Edit success');
        $result_user = Users::where('personalId',Crypt::decrypt($request->userId))->get();
        $result_reser = Reservation::where('roomType',$request->roomType)
                                ->where('roomId',$request->roomId)
                                ->where('startDate',$request->startDate)
                                ->where('reservationType',2)
                                ->where('reservationStatus',1)->get();

        $newReserTerm = new Reservation;
        $newReserTerm->courseId               = $request->courseId;
        $newReserTerm->roomId                 = $request->roomId;
        $newReserTerm->roomType               = $request->roomType;
        $newReserTerm->userId                 = $result_user[0]['personalId'];
        $newReserTerm->startDate              = date("Y-m-d");
        $newReserTerm->endDate                = date("Y-m-d");
        $newReserTerm->startTime              = $request->startTime;
        $newReserTerm->endTime                = $request->endTime;
        $newReserTerm->reservationDay         = $request->reservationDay;
        $newReserTerm->reservationDescription = $request->reservationDescription;
        $newReserTerm->reservationType        = 2;
        $newReserTerm->reservationStatus      = 2;
        $newReserTerm->costId                 = 0;
        $newReserTerm->adminId                = "";
        $newReserTerm->reservationNote        = "";
        $newReserTerm->notificationStatus     = 1;
        $newReserTerm->save();

        return response()->json($request);
    }
    

    //PDF
    public function getReservationPDF($reserId){
        $results = Reservation::where('reservationId',$reserId)->get();

        foreach($results as $key => $value){
            $result_user = Users::where('personalId',$value['userId'])->select('username','firstNameThai','lastNameThai','facultyId','levelId')->get();
                foreach($result_user as $key_user => $value_user){
                    if($value_user['facultyId'] != ''){
                        $result_faculty = Faculty::where('facultyId',$value_user['facultyId'])->get();
                        if($result_faculty){
                            $result_user[$key_user]['facultyId'] = $result_faculty[0]['facultyName'];
                        }
                    }
                }
            $result_admin = Users::where('personalId',$value['adminId'])->select('username','firstNameThai','lastNameThai','facultyId','levelId')->get();
                
            if($value['courseId'] != ''){
                $result_course = Course::where('courseId',$value['courseId'])->get();
                foreach($result_course as $key_course => $value_course){
                    $result_subject = Subjects::where('subjectCode',$value_course['subjectCode'])->get();
                    $result_group = explode("+", $value_course['groupCode']);
                    $result_course[$key_course]['subjectCode'] = $result_subject[0]['subjectCode']." ".$result_subject[0]['subjectName'];
                }
                $results[$key]['courseId'] = $result_course[0];
            }
            $result_roomtype = Room_type::where('typeId',$value['roomType'])->get();

            $results[$key]['startDate'] = $this->setDatePDF($results[$key]['startDate']);
            $results[$key]['endDate'] = $this->setDatePDF($results[$key]['endDate']);
            $results[$key]['startTime'] = $this->setTime($results[$key]['startTime']);
            $results[$key]['endTime'] = $this->setTime($results[$key]['endTime']);
            $results[$key]['roomType'] = $result_roomtype[0];
            $results[$key]['userId'] = $result_user[0];
            $results[$key]['adminId'] = $result_admin[0];
        }

        return response()->json($results);
    }

    public function setDatePDF($date){
        $arrayDate = [];
        $changeDate = "";
        $defaultDate = explode("-", $date);
        $arrMonth = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฏาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม',''];
        array_push($arrayDate, $defaultDate[2]);
        for($i = 0; $i < count($arrMonth); $i++){
            if($defaultDate[1] == $i){
                array_push($arrayDate, $arrMonth[$i-1]);
                array_push($arrayDate, $defaultDate[0]+543);
            }
        }
        return $arrayDate;
    }

    public function setDay($day){
        $arrDayThai = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
        $arrDayEng = ['Sun','Mon','The','Wed','Thu','Fri','Sat'];
        $result = "";
        for($i = 0; $i < count($arrDayEng); $i++){
            if($day == $arrDayEng[$i]){
                $result = $arrDayThai[$i];
            }
        }
        return $result;
    }

    //ค้นหาตามเวลา
    public function SearchRoomByTime(Request $request){
        $results = Reservation::where('startDate',$request->startDate)
                               ->where('startTime',$request->startTime)
                               ->where('endTime',$request->endTime)
                               ->where('reservationType',1)->get();
        $rooms = [];
        foreach($results as $key => $value){
            array_push($rooms,$value['roomId']);
            
            // $results['room'] = $results_room;
        }
        $results_room = Room::whereNotIn('roomId',$rooms)->get();
        foreach($results_room as $key => $value){
            $result_typeroom = Room_type::where('typeId',$value['typeId'])->get();

            $results_room[$key]['typeId'] = $result_typeroom[0];
        }
        if($results_room != []){
            $resp = array('status'=>1, 'message'=>'Success', 'data'=>$results_room);
        }else{
            $resp = array('status'=>0, 'message'=>'Fail', 'data'=>[]);
        }
        return response()->json($resp);
    }
}