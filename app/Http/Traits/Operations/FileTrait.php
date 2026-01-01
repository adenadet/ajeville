<?php
namespace App\Http\Traits\Operations;

trait FileTrait{
    public function file_delete_from_server($file){
        if (file_exists($file)) {
            unlink($file);
            return  "deleted successfully.";
        } else {
            return "does not exist.";
        }
    }
    public function file_upload_by_type($file, $type, $location, $id){
        if(($type == 'image')||($type == 'IMAGE')||($type == 'Image')){
            $new_name = $id."-".time().".".explode('/',explode(':', substr( $file, 0, strpos($file, ';')))[1])[1];
            $image = $id."-".time().".".explode('/',explode(':', substr( $file, 0, strpos($file, ';')))[1])[1];
            $upload = \Image::make($file)->save(public_path($location).'/'.$new_name);
            if ($upload){echo "Image uploaded successfully"; return $location.'/'.$new_name;}
            else {echo "Error uploading image";}
        }
        else if (($type == 'pdf') || ($type == 'PDF') ||($type == 'Pdf')){
            $new_name = $id."-".time().'.pdf';
            $dent = explode("base64,", $file);
            $dustbin = base64_decode($dent[1], true);
            file_put_contents((public_path($location).'/'.$new_name), $dustbin);
            return $location.'/'.$new_name;
        }
        else if ($type == 'xlsx'){
            $new_name = $id."-".time().'.xlsx';
            $dent = explode("base64,", $file);
            $dustbin = base64_decode($dent[1], true);
            file_put_contents((public_path($location).'/'.$new_name), $dustbin);
            return $location.'/'.$new_name;
        }

        return null;
    }

    public function file_upload_user_profile_image($file, $item_id, $location){
        if (!is_null($file)){
            $image = ((!is_null($item_id)) ? ($item_id."-") : '').time().".".explode('/',explode(':', substr($file, 0, strpos($file, ';')))[1])[1];
            \Image::make($file)->save(public_path($location.'/').$image);
            $file_url = $image;
        }
    }
}