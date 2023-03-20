<?php
use Illuminate\Support\Str;
use App\Http\Controllers\Admin\RoomController;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function ()   {
    return Str::random(32);
});
//Auth //
$router->post('/userlogin','Api\UsersController@UserLogin'); //
$router->post('/checkuser','Api\UsersController@CheckUsername'); //
$router->get('/login-receive','Api\UsersController@ReceiveLogin'); //
$router->post('/tokensso','Api\UsersController@tokenSSO'); //
$router->get('/logout-receive','Api\UsersController@ReceiveLogout'); //

//Register //
$router->post('/duplicatepersonal','Api\UsersController@DuplicatePersonalId'); //
$router->post('/duplicateusername','Api\UsersController@DuplicateUsername'); //
$router->post('/duplicateEmail','Api\MemberController@DuplicateEmail'); //
$router->post('/register','Api\UsersController@Register'); //
$router->post('/user/register','Api\MemberController@Register'); //

//Login
$router->post('/user/checktype','Api\MemberController@CheckTypeUser'); //
$router->post('/user/login','Api\MemberController@Login'); //
$router->post('/user/session','Api\MemberController@SessionLogin'); //

//Users
$router->get('/listusers','Api\UsersController@listUsers');
$router->post('/search_users', 'Api\UsersController@Search_Users');
$router->get('/getuserAll','Api\UsersController@getuserAll');
$router->get('/getuserid/{id}','Api\UsersController@getuserid');
$router->post('/getuserbyid','Api\UsersController@getUserById');
$router->get('/userpetitionid/{id}','Api\UsersController@userPetitonId');
$router->get('/petitionregister','Api\UsersController@petitionRegister');
$router->post('/adduser','Api\UsersController@AddUser');
$router->post('/edituser','Api\UsersController@editUser');
$router->post('/deleteuser', 'Api\UsersController@deleteUser');
$router->post('/approveuser', 'Api\UsersController@approveUser');
$router->post('/changestatususer', 'Api\UsersController@changstatusUser');

//User Level
$router->get('/listuserlevel','Api\UsersController@listUserLevel');

//Reservation
$router->get('/petitionreservation','Api\ReservationController@petitionReservationDay');
$router->get('/petitionreservationterm/{roomId}','Api\ReservationController@petitionReservationTerm');
$router->post('/editpetitionReservation','Api\ReservationController@editpetitionReservation');
$router->post('/edittestreservationterm','Api\ReservationController@edittestReservationTerm');
$router->post('/listeventreservation','Api\ReservationController@eventReservation');

$router->get('/getreservationAll','Api\ReservationController@getreservationAll');
$router->get('/getreservationid/{id}','Api\ReservationController@getreservationid');
$router->post('/getreservationWhereRoom','Api\ReservationController@getreservationWhereRoom');
$router->post('/addReservation', 'Api\ReservationController@addReservation');
$router->post('/editReservation', 'Api\ReservationController@editReservation');
$router->post('/deleteReservation', 'Api\ReservationController@deleteReservation');

$router->post('/getreservationWhereUser','Api\ReservationController@getreservationWhereUser');
$router->post('/getreservationWhereUserActive','Api\ReservationController@getreservationWhereUserActive');
$router->post('/searchreservationbyuser','Api\ReservationController@Search_ReservationByUser');

//Reservation Day
$router->get('/recommendroom','Api\RoomController@recommendRoom');
$router->post('/getreservationday','Api\ReservationController@getReservationDay');
$router->post('/reservationtime','Api\ReservationController@SearchRoomByTime');

//Reservation Term
$router->post('/reserTermDay','Api\ReservationController@reservationTermByDay');
$router->post('/reserTerm','Api\ReservationController@reservationTerm');
$router->get('/getreservationTerm/{roomId}','Api\ReservationController@getReservationTerm');
$router->get('/testreservationTerm/{roomId}','Api\ReservationController@getTestReservationTerm');
$router->post('/addReservationTerm', 'Api\ReservationController@addReservationTerm');

//Room
$router->get('/listallroom', 'Api\RoomController@listAllRoom');
$router->get('/listRoom/{page}', 'Api\RoomController@listRoom');
$router->get('/searchroom', 'Api\RoomController@SearchRoom');
$router->get('/listRoomId/{id}', 'Api\RoomController@listRoomId');
$router->get('/listroom_allstatus/{page}', 'Api\RoomController@listRooms_AllStatus');
$router->post('/search_room', 'Api\RoomController@Search_Room');
$router->get('/listRoomByType/{type_id}', 'Api\RoomController@listRoomByType');
$router->post('/listRoomByTypePage', 'Api\RoomController@listRoomByType_Page');
$router->post('/addRoom', 'Api\RoomController@addRoom');
$router->post('/editRoom', 'Api\RoomController@editRoom');
$router->post('/deleteRoom', 'Api\RoomController@deleteRoom');
$router->post('/changestatusroom', 'Api\RoomController@changstatusRoom');
$router->post('/searchroomdatetime', 'Api\RoomController@SearchRoom_DateTime');

//RoomType
$router->get('/listType', 'Api\RoomTypeController@listRoomType');
$router->get('/listType_ById/{type_id}', 'Api\RoomTypeController@listRoomType_ById');
$router->get('/listtyperooms_allstatus', 'Api\RoomTypeController@listTypeRooms_AllStatus');
$router->post('/search_type', 'Api\RoomTypeController@Search_TypeRoom');
$router->post('/addroom_type', 'Api\RoomTypeController@addTypeRoom');
$router->post('/editroom_type', 'Api\RoomTypeController@editTypeRoom');
$router->post('/deleteroom_type', 'Api\RoomTypeController@deleteTypeRoom');
$router->post('/changestatustyperoom', 'Api\RoomTypeController@changstatusTypeRoom');

//RoomCost
$router->get('/listCost', 'Api\RoomCostController@listRoomCost');
$router->post('/calculatecost', 'Api\RoomCostController@calculateCost');

//Groups
$router->get('/listgroups/{page}', 'Api\GroupsController@listGroups');
$router->get('/listgroup_id/{group_code}', 'Api\GroupsController@listGroup_Id');
$router->get('/listgroups_allstatus/{page}', 'Api\GroupsController@listGroups_AllStatus');
$router->post('/search_groups', 'Api\GroupsController@Search_Groups');
$router->post('/search_groups_active', 'Api\GroupsController@Search_Groups_Active');
$router->post('/add_group', 'Api\GroupsController@addGroup');
$router->post('/edit_group', 'Api\GroupsController@editGroup');
$router->post('/delete_group', 'Api\GroupsController@deleteGroup');
$router->post('/change_status_group', 'Api\GroupsController@changstatusGroup');

//Subject
$router->get('/listsubjects/{page}', 'Api\SubjectsController@listSubjects');
$router->get('/listsubject_id/{sub_code}', 'Api\SubjectsController@listSubject_ById');
$router->get('/listsubjects_allstatus/{page}', 'Api\SubjectsController@listSubjects_AllStatus');
$router->post('/search_subjects', 'Api\SubjectsController@Search_Subjects');
$router->post('/search_subjects_active', 'Api\SubjectsController@Search_Subjects_Active');
$router->post('/add_subject', 'Api\SubjectsController@addSubject');
$router->post('/edit_subject', 'Api\SubjectsController@editSubject');
$router->post('/delete_subject', 'Api\SubjectsController@deleteSubject');
$router->post('/change_status_subject', 'Api\SubjectsController@changstatusSubject');

//Faculty
$router->get('/listfaculty', 'Api\FacultyController@listFaculty');
$router->get('/listfaculty_id/{faculty_id}', 'Api\FacultyController@listFaculty_ById');
$router->get('/listfaculty_allstatus', 'Api\FacultyController@listFaculty_AllStatus');
$router->post('/search_faculty', 'Api\FacultyController@Search_Faculty');
$router->post('/search_faculty_active', 'Api\FacultyController@Search_Faculty_Active');
$router->post('/add_faculty', 'Api\FacultyController@addFaculty');
$router->post('/edit_faculty', 'Api\FacultyController@editFaculty');
$router->post('/delete_faculty', 'Api\FacultyController@deleteFaculty');
$router->post('/change_status_faculty', 'Api\FacultyController@changstatusFaculty');

//Department
$router->get('/listdepartment', 'Api\DepartmentController@listDepartment');
$router->get('/listdepartment_id/{department_id}', 'Api\DepartmentController@listDepartment_ById');
$router->get('/listdepartment_faculty/{faculty_id}', 'Api\DepartmentController@listDepartment_ByFaculty');
$router->get('/listdepartment_allstatus', 'Api\DepartmentController@listDepartment_AllStatus');
$router->post('/search_department', 'Api\DepartmentController@Search_Department');
$router->post('/search_department_active', 'Api\DepartmentController@Search_Department_Active');
$router->post('/add_department', 'Api\DepartmentController@addDepartment');
$router->post('/edit_department', 'Api\DepartmentController@editDepartment');
$router->post('/delete_department', 'Api\DepartmentController@deleteDepartment');
$router->post('/change_status_department', 'Api\DepartmentController@changstatusDepartment');

//Education
$router->get('/listeducation', 'Api\EducationController@listEducation');
$router->get('/listeducation_id/{department_id}', 'Api\EducationController@listEducation_ById');
$router->get('/listeducation_allstatus', 'Api\EducationController@listEducation_AllStatus');
$router->post('/search_education', 'Api\EducationController@Search_Education');
$router->post('/search_education_active', 'Api\EducationController@Search_Education_Active');
$router->post('/add_education', 'Api\EducationController@addEducation');
$router->post('/edit_education', 'Api\EducationController@editEducation');
$router->post('/delete_education', 'Api\EducationController@deleteEducation');
$router->post('/change_status_education', 'Api\EducationController@changstatusEducation');

//Professors
$router->get('/listprofessors/{page}', 'Api\ProfessorsController@listProfessors');
$router->get('/listprofessor_id/{professor_id}', 'Api\ProfessorsController@listProfessor_ById');
$router->get('/listprofessors_allstatus/{page}', 'Api\ProfessorsController@listProfessors_AllStatus');
$router->post('/search_professors', 'Api\ProfessorsController@Search_Professors');
$router->post('/search_professors_active', 'Api\ProfessorsController@Search_Professors_Active');
$router->post('/addprofessor', 'Api\ProfessorsController@addProfessor');
$router->post('/editprofessor', 'Api\ProfessorsController@editProfessor');
$router->post('/deleteprofessor', 'Api\ProfessorsController@deleteProfessor');
$router->post('/changestatusprofessor', 'Api\ProfessorsController@changstatusProfessor');

//Building
$router->get('/listbuilding', 'Api\BuildingController@listBuilding');
$router->get('/listbuildingnumber/{building_number}', 'Api\BuildingController@getBuildingNumber');
$router->get('/listbuilding_allstatus', 'Api\BuildingController@listBuilding_AllStatus');
$router->post('/search_building', 'Api\BuildingController@Search_Building');
$router->post('/addbuilding', 'Api\BuildingController@addBuilding');
$router->post('/editbuilding', 'Api\BuildingController@editBuilding');
$router->post('/deletebuilding', 'Api\BuildingController@deleteBuilding');
$router->post('/changestatusbuilding', 'Api\BuildingController@changstatusBuilding');

//Course
$router->get('/listcourse/{page}', 'Api\CourseController@listCourse');
$router->get('/listcourse_id/{id}', 'Api\CourseController@listCourse_Id');
$router->get('/listcourses_allstatus/{page}', 'Api\CourseController@listCourses_AllStatus');
$router->get('/search_course/{sub_code}', 'Api\CourseController@searchCourse');
$router->post('/search_course', 'Api\CourseController@Search_Course');
$router->post('/search_course_active', 'Api\CourseController@Search_Course_Active');
$router->post('/add_course', 'Api\CourseController@addCourse');
$router->post('/edit_course', 'Api\CourseController@editCourse');
$router->post('/delete_course', 'Api\CourseController@deleteCourse');
$router->post('/change_status_course', 'Api\CourseController@changstatusCourse');

//Tools
$router->get('/listtools', 'Api\ToolsController@listTools');
$router->get('/listtool_Id/{tool_id}', 'Api\ToolsController@listTool_Id');
$router->post('/addtool', 'Api\ToolsController@addTool');
$router->post('/edittool', 'Api\ToolsController@editTool');
$router->post('/deletetool', 'Api\ToolsController@deleteTool');

//Images
$router->get('/listimages', 'Api\ImagesController@listImages');
$router->get('/listimage_id/{id}', 'Api\ImagesController@listImage_Id');
$router->get('/listimages_allstatus/{page}', 'Api\ImagesController@listImages_AllStatus');
$router->post('/search_images', 'Api\ImagesController@Search_Images');
$router->get('file/downloadFile', 'Api\ImagesController@downloadFile');
$router->post('/addImages', 'api\ImagesController@addImages');
$router->post('/deleteImage', 'Api\ImagesController@deleteImage');

//Slide
$router->get('/listslide', 'Api\SlideController@listSlide');
$router->get('/listslide_id/{id}', 'Api\SlideController@listSlide_Id');
$router->get('/listslide_page/{page}', 'Api\SlideController@listSlide_Page');
$router->get('/listslide_allstatus/{page}', 'Api\SlideController@listSlide_AllStatus');
$router->post('/search_slide', 'Api\SlideController@Search_Slide');
$router->get('file/downloadFile', 'Api\SlideController@downloadFile');
$router->post('/addslide', 'api\SlideController@addSlide');
$router->post('/deleteslide', 'Api\SlideController@deleteSlide');

//Program
$router->get('/listprogram', 'Api\ProgramController@listProgram');
$router->get('/listprogram_id/{program_id}', 'Api\ProgramController@listProgram_ById');
$router->get('/listprogram_allstatus', 'Api\ProgramController@listProgram_AllStatus');
$router->post('/search_program', 'Api\ProgramController@Search_Program');
$router->post('/search_program_active', 'Api\ProgramController@Search_Program_Active');
$router->post('/add_program', 'Api\ProgramController@addProgram');
$router->post('/edit_program', 'Api\ProgramController@editProgram');
$router->post('/delete_program', 'Api\ProgramController@deleteProgram');
$router->post('/change_status_program', 'Api\ProgramController@changstatusProgram');

//Term
$router->get('/listtermno', 'Api\TermController@listTermNo');
$router->get('/listterm_id/{id}', 'Api\TermController@listTerm_Id');
$router->get('/listterm_allstatus/{page}', 'Api\TermController@listTerm_AllStatus');
$router->post('/search_term', 'Api\TermController@Search_Term');
$router->post('/addterm', 'api\TermController@addTerm');
$router->post('/editterm', 'Api\TermController@editTerm');
$router->post('/deleteterm', 'Api\TermController@deleteTerm');
$router->post('/changestatusterm', 'Api\TermController@changestatusTerm');

//Time
$router->post('/timestart', 'Api\RoomController@listTimeStart');
$router->get('/timeend', 'Api\RoomController@listTimeEnd');

//PDF
$router->get('/getreservationpdf/{reser_id}','Api\ReservationController@getReservationPDF');

//ROOM
$router->get('/user/rooms', 'Api\RoomController@listAllRoom');
$router->get('/user/roomid/{roomId}', 'Api\RoomController@listRoomId');
$router->get('/user/rooms/recommend', 'Api\RoomController@recommendRoom');
$router->post('user/rooms/searchdatetime', 'Api\RoomController@SearchRoom_DateTime');
$router->get('/admin/rooms', 'Api\RoomController@listRooms_AllStatus');
$router->get('/admin/roomid/{roomId}', 'Api\RoomController@listRoomById');
$router->post('/admin/rooms/add', 'Api\RoomController@addRoom');
$router->post('/admin/rooms/edit', 'Api\RoomController@editRoom');
$router->post('/admin/rooms/del', 'Api\RoomController@deleteRoom');
$router->post('/admin/rooms/change', 'Api\RoomController@changstatusRoom');

//TYPE
$router->get('/user/type', 'Api\RoomTypeController@listTypeRoom');
$router->get('/admin/type', 'Api\RoomTypeController@listTypeRooms_AllStatus');
$router->get('/admin/typeid/{typeId}', 'Api\RoomTypeController@listTypeRoom_ById');
$router->post('/admin/type/add', 'Api\RoomTypeController@addTypeRoom');
$router->post('/admin/type/edit', 'Api\RoomTypeController@editTypeRoom');
$router->post('/admin/type/del', 'Api\RoomTypeController@deleteTypeRoom');
$router->post('/admin/type/change', 'Api\RoomTypeController@changstatusTypeRoom');

//BUILDING
$router->get('/user/building', 'Api\BuildingController@listBuilding');
$router->get('/admin/building', 'Api\BuildingController@listBuilding_AllStatus');
$router->get('/admin/buildingno/{buildingNumber}', 'Api\BuildingController@listBuildingNumber');
$router->post('/admin/building/add', 'Api\BuildingController@addBuilding');
$router->post('/admin/building/edit', 'Api\BuildingController@editBuilding');
$router->post('/admin/building/del', 'Api\BuildingController@deleteBuilding');
$router->post('/admin/building/change', 'Api\BuildingController@changstatusBuilding');

//COURSE
$router->get('/user/course', 'Api\CourseController@listCourse');
$router->get('/admin/course', 'Api\CourseController@listCourses_AllStatus');
$router->get('/admin/courseid/{courseId}', 'Api\CourseController@listCourse_Id');
$router->post('/admin/course/add', 'Api\CourseController@addCourse');
$router->post('/admin/course/edit', 'Api\CourseController@editCourse');
$router->post('/admin/course/del', 'Api\CourseController@deleteCourse');
$router->post('/admin/course/change', 'Api\CourseController@changstatusCourse');

//SUBJECT
$router->get('/user/subject', 'Api\SubjectsController@listSubjects');
$router->get('/admin/subject', 'Api\SubjectsController@listSubjects_AllStatus');
$router->get('/admin/subjectid/{subjectId}', 'Api\SubjectsController@listSubject_ById');
$router->post('/admin/subject/add', 'Api\SubjectsController@addSubject');
$router->post('/admin/subject/edit', 'Api\SubjectsController@editSubject');
$router->post('/admin/subject/del', 'Api\SubjectsController@deleteSubject');
$router->post('/admin/subject/change', 'Api\SubjectsController@changstatusSubject');

//PROFESSORS
$router->get('/user/professor', 'Api\ProfessorsController@listProfessors');
$router->get('/admin/professor', 'Api\ProfessorsController@listProfessors_AllStatus');
$router->get('/admin/professorid/{professorId}', 'Api\ProfessorsController@listProfessor_ById');
$router->post('/admin/professor/add', 'Api\ProfessorsController@addProfessor');
$router->post('/admin/professor/edit', 'Api\ProfessorsController@editProfessor');
$router->post('/admin/professor/del', 'Api\ProfessorsController@deleteProfessor');
$router->post('/admin/professor/change', 'Api\ProfessorsController@changstatusProfessor');

//GROUP
$router->get('/user/group', 'Api\GroupsController@listGroups');
$router->get('/admin/group', 'Api\GroupsController@listGroups_AllStatus');
$router->get('/admin/groupcode/{groupCode}', 'Api\GroupsController@listGroup_Id');
$router->post('/admin/group/add', 'Api\GroupsController@addGroup');
$router->post('/admin/group/edit', 'Api\GroupsController@editGroup');
$router->post('/admin/group/del', 'Api\GroupsController@deleteGroup');
$router->post('/admin/group/change', 'Api\GroupsController@changstatusGroup');

//FACULTY
$router->get('/user/faculty', 'Api\FacultyController@listFaculty');
$router->get('/admin/faculty', 'Api\FacultyController@listFaculty_AllStatus');
$router->get('/admin/facultyid/{facultyId}', 'Api\FacultyController@listFaculty_ById');
$router->post('/admin/faculty/add', 'Api\FacultyController@addFaculty');
$router->post('/admin/faculty/edit', 'Api\FacultyController@editFaculty');
$router->post('/admin/faculty/del', 'Api\FacultyController@deleteFaculty');
$router->post('/admin/faculty/change', 'Api\FacultyController@changstatusFaculty');

//EDUCATION
$router->get('/user/education', 'Api\EducationController@listEducation');
$router->get('/admin/education', 'Api\EducationController@listEducation_AllStatus');
$router->get('/admin/educationid/{educationId}', 'Api\EducationController@listEducation_ById');
$router->post('/admin/education/add', 'Api\EducationController@addEducation');
$router->post('/admin/education/edit', 'Api\EducationController@editEducation');
$router->post('/admin/education/del', 'Api\EducationController@deleteEducation');
$router->post('/admin/education/change', 'Api\EducationController@changstatusEducation');

//USER
$router->get('/user/petition', 'Api\MemberController@listEducation');
$router->get('/user/all/{token}', 'Api\MemberController@listUsers');
$router->get('/user/mail/{email}', 'Api\MemberController@listUser_Mail');
$router->post('/user/edit', 'Api\MemberController@editUser');
$router->post('/user/del', 'Api\MemberController@deleteUser');
$router->post('/user/change', 'Api\MemberController@changstatusUser');

Route::get('storage/{filename}', function ($filename)
{
    return Image::make(storage_path('public/images/' . $filename))->response();
});