<?php

namespace App\Http\Controllers\Api;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Images;
use Validator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\File;

class ImagesController extends BaseController
{
    public function listImages(){
        $results = Images::where('imgName','like','%'.'default-personal-card.png'.'%')->select('imgName')->get();


        return response()->json($results);
    }

    public function listImage_Id($imageId){
      $results = Images::where('imgId',$imageId)->get();
      return response()->json($results);
    }

    public function listImages_AllStatus($page){
      $results = Images::offset($page)->limit('10')->orderBy('imgId','desc')->get();
      $all_results = Images::get();
        foreach($results as $key => $value){
            $results[$key]['count'] = $all_results->count();
            
        }
        return response()->json($results);
    }

    public function Search_Images(Request $request){
      $results = Images::where('imgName','like','%'.$request->imgName.'%')->offset($request->page)->limit('10')->orderBy('imgId','desc')->get();
      $all_results = Images::where('imgName','like','%'.$request->imgName.'%')->get();
        foreach($results as $key => $value){
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

    public function addImages(Request $request){
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
            }
          }
        
        return response()->json($newImage);
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

    public function deleteImage(Request $request){
      $deleteImage = Images::where('imgId',$request->imgId)->delete();
      if($deleteImage){
        File::delete($this->public_path($this->dest.$request->imgName));
      }

      return response()->json($request);
    }
    
}