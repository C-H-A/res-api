<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Images;
use Validator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\File;

class SlideController extends BaseController
{
    public function listSlide(){
        $results = Slide::get();

        return response()->json($results);
    }

    public function listSlide_Id($slideId){
      $results = Slide::where('slideId',$slideId)->where('status',1)->get();
        foreach($results as $key => $value){
          $results_image_id = explode('+',$value['slideImg']);
          $result_image = Images::whereIn('imgId',$results_image_id)
                ->get();

          $results[$key]['slideImg'] = $result_image;
          // $results[$key]['count'] = $all_results->count();
        }
        $resp = array('status'=>1, 'data'=>'');                       
        if($results == '[]'){
            $resp = array('status'=>0, 'data'=>'ไม่พบข้อมูล');
        }else{
            $resp = array('status'=>1, 'data'=>$results);
        }
      return response()->json($resp);
    }

    public function listSlide_Page($slidePage){
      $results = Slide::where('slidePage',$slidePage)->where('status',1)->orderBy('slideId','desc')->get();
        foreach($results as $key => $value){
          $results_image_id = explode('+',$value['slideImg']);
          $result_image = Images::whereIn('imgId',$results_image_id)
                ->get();

          $results[$key]['slideImg'] = $result_image;
          // $results[$key]['count'] = $all_results->count();
        }
        $resp = array('status'=>1, 'data'=>'');                       
        if($results == '[]'){
            $resp = array('status'=>0, 'data'=>'ไม่พบข้อมูล');
        }else{
            $resp = array('status'=>1, 'data'=>$results);
        }
      
      return response()->json($resp);
    }

    public function listSlide_AllStatus($page){
      $results = Slide::offset($page)->limit('10')->orderBy('slideId','desc')->get();
      $all_results = Slide::get();
        foreach($results as $key => $value){
          $results_image_id = explode('+',$value['slideImg']);
          $result_image = Images::whereIn('imgId',$results_image_id)
                ->get();

          $results[$key]['slideImg'] = $result_image;
          $results[$key]['count'] = $all_results->count();
        }
        return response()->json($results);
    }

    public function Search_Slide(Request $request){
      $results = Slide::where('slideName','like','%'.$request->slideName.'%')->offset($request->page)->limit('10')->orderBy('slideId','desc')->get();
      $all_results = Slide::where('slideName','like','%'.$request->slideName.'%')->get();
        foreach($results as $key => $value){
          $results_image_id = explode('+',$value['slideImg']);
          $result_image = Images::whereIn('imgId',$results_image_id)
                ->get();

          $results[$key]['slideImg'] = $result_image;
          $results[$key]['count'] = $all_results->count();
        }
        return response()->json($results);
    }

    public function downloadFile(Request $request){
      $headers = ['Content-Type' => 'application/pdf'];
      $path = $this->public_path('public/images/system pond.pdf');
      $name = 'PDF File';

      // $pathToFile = $this->public_path('public/images/20200420104304_5e9d7cb809ecf_AAAA.png');
      return response()->download($path, $name, $headers);
      // return response()->file($path, $headers);
    }

    private $dest = 'public/images/';
    private $link_name = 'default.png';

    public function addSlide(Request $request){
      $slide_images = '';
      $file = $request->file('images');
      $validator = Validator::make($request->all(), [
          'images' => 'mimes:jpeg,bmp,png|max:10240',
        ]);

        if ($request->hasFile('images') && $request->path != '0') {
          $image = $request->file('images');
          foreach($request->file('images') as $key => $img){
            $this->link_name = $this->setUniqidImageName($img);
            $img->move($this->public_path($this->dest), $this->link_name);

            $newImage = new Images;
            $newImage->imgName   = $this->link_name;
            $newImage->imgPath   = $this->dest;
            $newImage->save();
            if($slide_images != ''){
              $slide_images = $slide_images.'+'.$newImage['id'];
            }else{
              $slide_images = $newImage['id'];
            }
          }
          $newSlide = new Slide;
          $newSlide->slideName   = $request->slideName;
          $newSlide->slideImg    = $slide_images;
          $newSlide->slidePage   = $request->slidePage;
          $newSlide->status      = 1;
          $newSlide->save();
        }
      
      return response()->json($newSlide);
  }

    private function setUniqidImageName($image = '0')
    {
      $result = $this->link_name;
      if ($image != '0') {
        $origin_name = $image->getClientOriginalName();
        $ext_img = $image->getClientOriginalExtension();
        $exp_name = explode('.'.$ext_img,$origin_name);
        // $org_name = $this->$exp_name[0];
        $name_img = Images::where('imgName','like','%'.$exp_name[0].'%')->select('imgName')->get()->count();
        if($name_img > 0){
          $result = $this->link_name = $exp_name[0].'('.$name_img.').'.$ext_img;
        }else{
          $result = $this->link_name = $origin_name;
        }
      }

      return $result;
    }

    function public_path($path = null)
    {
        return rtrim(app()->basePath($path), '/');
    }

    public function deleteSlide(Request $request){
      $results = Slide::where('slideId',$request->slideId)->get();
      $results_image_id = explode('+',$results[0]['slideImg']);
      $resultsImage = Images::whereIn('imgId',$results_image_id)->get();
      $deleteImage = Images::whereIn('imgId',$results_image_id)->delete();
        foreach($resultsImage as $key => $value){
          if($deleteImage != 0){
            File::delete($this->public_path($this->dest.$value['imgName']));
          }
        }
        $deleteSlide = Slide::where('slideId',$request->slideId)->delete();

      return response()->json($deleteSlide);
    }
    
}