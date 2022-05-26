<?php
class Banner extends dbobject{
    public function bannerList($data)
    {
        $table_name    = "banner";
		$primary_key   = "id";
		$columner = array(
                array( 'db' => 'id', 'dt' => 0 ),
                array( 'db' => 'title', 'dt' => 1),
                array( 'db' => 'image', 'dt' => 2,'formatter'=>function($d,$row){
                    $id = $row['id'];
                    return '<img src="'.$d.'" width="50" height="50" /><span class="badge badge-primary" style="display:block;cursor:pointer" onclick="getModal(\'setup/banner_image.php?op=edit&id='.$id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Image</span>';
                }),
                array( 'db' => 'subtitle', 'dt' => 3),
                array( 'db' => 'page_id',  'dt' => 4 ,'formatter'=>function($d,$row){
                    return $this->getitemlabel("product_page","id",$d,"name");
                }),
                array( 'db' => 'cta_text',  'dt' => 5),
                array( 'db' => 'cta_link',  'dt' => 6 ),
                array( 'db' => 'id', 'dt' => 7,'formatter'=>function($d,$row){
                    return '<span class="badge badge-success" style="display:block;cursor:pointer" onclick="getModal(\'setup/banner_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit</span> | <span class="badge badge-danger" style="display:block;cursor:pointer" onclick="deleteBanner(\''.$d.'\')"  href="javascript:void(0)" >Delete</span>';
                }),
                array( 'db' => 'created', 'dt' => 8, 'formatter' => function( $d,$row ) {
                    return $d;
                }
                )
			);
		$filter = "";
		// $filter = " AND merchant_id='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function deleteBanner($data){
        $id = $data['id'];
        $sql = "DELETE FROM banner WHERE id = '$id' LIMIT 1";
        $this->db_query($sql,false);
        return json_encode(array('response_code'=>'0','response_message'=>'Deleted!')); 
    }
    public function setBannerImage($data)
    {
        $file_data        = $data['_files'];
        $path        = 'uploads/';
        $image_id    = rand(1111,999999999).date('his');
        

        $ff = $this->saveMerchantImage($file_data,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            $sql       = "UPDATE banner SET image = '$full_path' WHERE id = '$data[id]' LIMIT 1";
            $count     = $this->db_query($sql,false);
            // unlink($image_location);
            return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
        }else
        {
            return json_encode(array('response_code'=>'458','response_message'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
    public function saveBanner($data)
    {
        $data['posted_user'] = $_SESSION['username_sess'];
        $validation = $this->validate($data,array('title'=>'required',"subtitle"=>"required","page_id"=>"required","posted_user"=>"required","cta_text"=>"required","cta_link"=>"required"),array('cta_text'=>'cta text',"page_id"=>"page","posted_user"=>"posted user"));
        if(!$validation['error'])
        {
            
            if($data['operation'] == "new")
            {
                
                $data['created'] = date('Y-m-d h:i:s');
                $count = $this->doInsert('banner',$data,array('op','operation','id','_files'));
                if($count > 0)
                {
                    $insert_id = $this->getInsert_id($this->myconn);
                    $file_data = $data['_files'];
                    $ff   = $this->saveMerchantImage($file_data,"uploads/","");
                    $ff   = json_decode($ff,true);
                    if($ff['response_code'] == "0")
                    {
                        $full_path = $ff['data'];
                        $sql       = "UPDATE banner SET image = '$full_path' WHERE id = '$insert_id' LIMIT 1";
                        $count = $this->db_query($sql,false);
                        return ($count == 1)?json_encode(array('response_code'=>'0','response_message'=>'Successful','data'=>array('product_id'=>$insert_id))):json_encode(array('response_code'=>'471','response_message'=>'Failed to update Banner image'));
                    }
                    else
                    {
                        return json_encode(array('response_code'=>'71','response_mesage'=>'Failed to upload Banner image'));
                    }
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Banner Could not be Created'));
                }
            }else
            {
                
                $count = $this->doUpdate('banner',$data,['op','operation','_files'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Banner Updated Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Banner Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function saveMerchantImage($data,$path,$image_id="")
    {
        $_FILES = $data;
//        var_dump($_FILES);
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Invalid parameter.'));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return json_encode(array('response_code'=>'74','response_mesage'=>'No file sent.'));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.'));
            default:
                return json_encode(array('response_code'=>'74','response_mesage'=>'Unknown errors.'));
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1000000) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.'));
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
    //    $finfo = new finfo(FILEINFO_MIME_TYPE);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            finfo_file($finfo,$_FILES['upfile']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                // 'jpeg' => 'image/jpeg',
                'png' => 'image/png'
            ),
            true
        )) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Invalid file format.'));
        }

            // You should name it uniquely.
            // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
            // On this example, obtain safe unique name from its binary data.
            $email = ($image_id == "")?date('mdhis'):$image_id;

            //@@@@@@@@@@@@@@@@@@@@@@@
            
            if (!move_uploaded_file($_FILES['upfile']['tmp_name'],sprintf($path.'%s.%s',$email,$ext))) {
                return json_encode(array('response_code'=>'50','response_mesage'=>'Failed to move uploaded file.'));
            }
            $full_path = $path.$email.'.'.$ext;
            // $myImage = new SimpleImage();
            // $myImage->load($full_path);
            // $myImage->resize(860,700);
            // $myImage->save($full_path);
            unlink($_FILES['upfile']['tmp_name']);
            return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$full_path));
        
                
            
        
    }
}