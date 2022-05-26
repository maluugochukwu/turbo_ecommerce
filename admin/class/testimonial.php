<?php

class Testimonial extends dbobject
{
   public function testimonialList($data)
    {
		$table_name    = "testimonial";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'full_name', 'dt' => 1 ),
			array( 'db' => 'logo', 'dt' => 2, 'formatter' => function( $d,$row ) {
                $id = $row['id'];
                    return '<img class="img-thumbnail" src="'.$d.'" width="50" height="50" /><span class="badge badge-success" style="display:block;cursor:pointer" onclick="getModal(\'setup/testimonial_image.php?op=edit&id='.$id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Image</span>';
            }  ),
            array( 'db' => 'id',     'dt' => 3, 'formatter' => function( $d,$row ) {
                return '<a class="badge badge-warning" onclick="getModal(\'setup/testimonial_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit </a> | <a class="badge badge-danger" onclick="deleteTestimonial(\''.$d.'\')"  href="javascript:void(0)" >Delete </a>';
                
                }
            ),
			array( 'db' => 'created',     'dt' => 4, 'formatter' => function( $d,$row ) {
						return $d;
					}
                )
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function deleteTestimonial($data)
    {
        $id = $data['id'];
        $sql = "DELETE FROM testimonial WHERE id= '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(['response_code'=>0,'response_message'=>'Deleted successfully']);
        }else
        {
            return json_encode(['response_code'=>77,'response_message'=>'Could not delete testimonial']);
        }
    }
    public function saveTestimonial($data)
    {
        $validation = $this->validate($data,array('full_name'=>'required',"content"=>"required"),array('full_name'=>'full name'));
        if(!$validation['error'])
        {
            
            if($data['operation'] == "new")
            {
                
                $data['created'] = date('Y-m-d h:i:s');
                $count = $this->doInsert('testimonial',$data,array('op','operation','id','_files'));
                if($count > 0)
                {
                    $insert_id = mysqli_insert_id($this->myconn);
                    $file_data = $data['_files'];
                    // $path = './uploads/';
                    $ff   = $this->saveMerchantImage($file_data,"uploads/","");
                    $ff   = json_decode($ff,true);
//                    var_dump($ff);
                    if($ff['response_code'] == "0")
                    {
                        $full_path = $ff['data'];
                        $sql       = "UPDATE testimonial SET logo = '$full_path' WHERE id = '$insert_id' LIMIT 1";
                        $count = $this->db_query($sql,false);
                        return ($count == 1)?json_encode(array('response_code'=>'0','response_message'=>'Successful','data'=>array('product_id'=>$insert_id))):json_encode(array('response_code'=>'471','response_message'=>'Failed to update  image'));
                    }
                    else
                    {
                        return json_encode(array('response_code'=>'71','response_mesage'=>'Failed to upload  image'));
                    }
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Testimonial Could not be Created'));
                }
            }else
            {
                
                $count = $this->doUpdate('testimonial',$data,['op','operation','_files'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Testimonial Updated Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Testimonial Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    
    
    public function setTestimonialImage($data)
    {
        $file_data        = $data['_files'];
        $path        = 'uploads/';
        $image_id    = rand(1111,999999999).date('his');
        

        $ff = $this->saveMerchantImage($file_data,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            $sql       = "UPDATE testimonial SET logo = '$full_path' WHERE id = '$data[id]' LIMIT 1";
            $count     = $this->db_query($sql,false);
            // unlink($image_location);
            return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
        }else
        {
            return json_encode(array('response_code'=>'458','response_message'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
    public function saveBlogCategory($data)
    {
        $validation = $this->validate($data,["name"=>"required"]);
        if(!$validation['error'])
        {
            $data['created'] = date("Y-m-d h:i:s");
            if($data['operation'] == "new")
            {
                    $count = $this->doInsert("blog_category",$data,['op','operation','id']);
                    if($count > 0)
                    {
                        return json_encode(array('response_code'=>0,'response_message'=>'Category Created Successfully'));
                    }else
                    {
                        return json_encode(array('response_code'=>996,'response_message'=>'Category could not be created'));
                    }
            }else
            {
                $count = $this->doUpdate("blog_category",$data,['op','operation','id'],["id"=>$data['id']]);
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Category updated Successfully'));
                }else
                {
                    return json_encode(array('response_code'=>996,'response_message'=>'Category could not be updated'));
                }
            }
        }else
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
            $myImage = new SimpleImage();
            $myImage->load($full_path);
            $myImage->resize(860,700);
            $myImage->save($full_path);
            unlink($_FILES['upfile']['tmp_name']);
            return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$full_path));
        
                
            
        
    }
    public function generateUrl()
    {
        $chars = "123456789abcdefghijklmnopqrstuvwxyz";
        $res = "";
        for ($i = 0; $i < 10; $i++) 
        {
            $res .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        return substr(base64_encode($res),0,-2);
    }
    public function getNextRoleId()
    {
        $sql    = "select CONCAT('00',max(role_id) +1) as rolee FROM role";
        $result = $this->db_query($sql);
        return $result[0]['rolee'];
        
    }
}