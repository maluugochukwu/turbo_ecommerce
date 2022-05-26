<?php

class Product extends dbobject
{
   public function productList($data)
    {
		$table_name    = "products";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
            array( 'db' => 'logo', 'dt' => 2,'formatter'=>function($d,$row){
                $id = $row['id'];
                return '<img src="'.$d.'" width="50" height="50" /><span class="badge badge-primary" style="display:block;cursor:pointer" onclick="getModal(\'setup/product_image.php?op=edit&id='.$id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Image</span>';
            }),
            array( 'db' => 'mascot', 'dt' => 3,'formatter'=>function($d,$row){
                $id = $row['id'];
                return '<img src="'.$d.'" width="50" height="50" /><span class="badge badge-primary" style="display:block;cursor:pointer" onclick="getModal(\'setup/mascot_image.php?op=edit&id='.$id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Mascot Image</span>';
            }),
			array( 'db' => 'product_category_id', 'dt' => 4, 'formatter' => function( $d,$row ) {
                    return $this->getitemlabel("product_category","id",$d,"name");
            }  ),
            array( 'db' => 'page_id', 'dt' => 5, 'formatter' => function( $d,$row ) {
                return $this->getitemlabel("product_page","id",$d,"name");
            }  ),
            array( 'db' => 'slogan', 'dt' => 6 ),
        array( 'db' => 'id', 'dt' => 7, 'formatter' => function( $d,$row ) {
            $feature_state = $row['is_featured'];
            $feature_class = ($feature_state==1)?"btn btn-danger":"btn btn-success";
            $feature_text = ($feature_state==1)?"Unfeature":"Feature";

            $product_advert_state = $row['product_advert'];
            $product_advert_class = ($product_advert_state==1)?"btn btn-danger":"btn btn-success";
            $product_advert_text = ($product_advert_state==1)?"Unadvertise":"Advertise";

            return '<button class="'.$feature_class.'" onclick="setFeature(\''.$feature_state.'\',\''.$d.'\')"  href="javascript:void(0)" >'.$feature_text.'</button> | <button class="btn btn-warning" onclick="getModal(\'setup/product_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit </button> | <button class="btn btn-danger" onclick="deleteProduct(\''.$d.'\')"  href="javascript:void(0)" >Delete </button> | <button class="btn btn-info" onclick="getModal(\'setup/product_features.php?op=edit&product_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary" >Add Features </button> | <button class="'.$product_advert_class.'" onclick="setAdvert(\''.$product_advert_state.'\',\''.$d.'\')"  href="javascript:void(0)" >'.$product_advert_text.'</button>';
            }  ),
			array( 'db' => 'created',     'dt' => 8, 'formatter' => function( $d,$row ) {
						return $d;
					}
                ),
                array( 'db' => 'is_featured', 'dt' => -1 ),
                array( 'db' => 'product_advert', 'dt' => -1 ),
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function setFeature($data)
    {
        $feature_state = ($data['state'] == '1')?0:1;
        $product_id = $data['id'];
        $sql = "UPDATE products SET is_featured = '$feature_state' WHERE id = '$product_id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            $this->db_query("UPDATE products SET is_featured = 0 WHERE id <> '$product_id' ",false);
            return json_encode(["response_code"=>0,"response_message"=>"Updated successfully"]);
        }
        else
        {
            return json_encode(["response_code"=>63,"response_message"=>"Could not Updated blog"]);
        }
    }
    public function setAdvert($data)
    {
        $advert_state = ($data['state'] == '1')?0:1;
        $product_id   = $data['id'];
        $sql          = "UPDATE products SET product_advert = '$advert_state' WHERE id = '$product_id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(["response_code"=>0,"response_message"=>"Updated successfully"]);
        }
        else
        {
            return json_encode(["response_code"=>63,"response_message"=>"Could not Updated product"]);
        }
    }
    public function setProductImage($data)
    {
        $file_data        = $data['_files'];
        $path        = 'uploads/';
        $image_id    = rand(1111,999999999).date('his');
        

        $ff = $this->saveMerchantImage($file_data,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            $sql       = "UPDATE products SET logo = '$full_path' WHERE id = '$data[id]' LIMIT 1";
            $count     = $this->db_query($sql,false);
            // unlink($image_location);
            return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
        }else
        {
            return json_encode(array('response_code'=>'458','response_message'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
    public function setMascotImage($data)
    {
        $file_data        = $data['_files'];
        $path        = 'uploads/';
        $image_id    = rand(1111,999999999).date('his');
        

        $ff = $this->saveMerchantImage($file_data,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            $sql       = "UPDATE products SET mascot = '$full_path' WHERE id = '$data[id]' LIMIT 1";
            $count     = $this->db_query($sql,false);
            // unlink($image_location);
            return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
        }else
        {
            return json_encode(array('response_code'=>'458','response_message'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
    public function setProductCategoryImage($data)
    {
        $file_data        = $data['_files'];
        $path        = 'uploads/';
        $image_id    = rand(1111,999999999).date('his');
        

        $ff = $this->saveMerchantImage($file_data,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            $sql       = "UPDATE product_category SET bg_image = '$full_path' WHERE id = '$data[id]' LIMIT 1";
            $count     = $this->db_query($sql,false);
            // unlink($image_location);
            return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
        }else
        {
            return json_encode(array('response_code'=>'458','response_message'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
   public function productCategoryList($data)
    {
		$table_name    = "product_category";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
			array( 'db' => 'bg_image', 'dt' => 2, 'formatter' => function( $d,$row ) {
                $id = $row['id'];
                return '<img src="'.$d.'" width="50" height="50" /><span class="badge badge-primary" style="display:block;cursor:pointer" onclick="getModal(\'setup/product_category_image.php?op=edit&id='.$id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Category Image</span>';
            }  ),
			array( 'db' => 'posted_user', 'dt' => 3, 'formatter' => function( $d,$row ) {
                    return $this->getitemlabel("userdata","username",$d,"firstname");
            }  ),
            
        array( 'db' => 'id', 'dt' => 4, 'formatter' => function( $d,$row ) {
            return '<button class="btn btn-warning" onclick="getModal(\'setup/product_category_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit </button> ';
            }  ),
			array( 'db' => 'created',     'dt' => 5, 'formatter' => function( $d,$row ) {
						return $d;
					}
                )
			);
		$filter = "";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
   public function productPageList($data)
    {
		$table_name    = "product_page";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
			array( 'db' => 'url', 'dt' => 2),
			array( 'db' => 'main_bg_color', 'dt' => 3, 'formatter' => function( $d,$row ) {
                return "<div style='border-radius:50%; height:50px; width:50px; background-color:$d'></div>";
            }  ),
			array( 'db' => 'secondary_bg_color', 'dt' => 4, 'formatter' => function( $d,$row ) {
                    return "<div style='border-radius:50%; height:50px; width:50px; background-color:$d'></div>";
            }  ),
			array( 'db' => 'keywords', 'dt' => 5 ),
            
        array( 'db' => 'id', 'dt' => 6, 'formatter' => function( $d,$row ) {
            return '<button class="btn btn-warning" onclick="getModal(\'setup/product_page_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit </button> | <button class="btn btn-danger" onclick="deleteProductPage(\''.$d.'\')"  href="javascript:void(0)" >Delete </button>';
            }  ),
			array( 'db' => 'created',     'dt' => 7, 'formatter' => function( $d,$row ) {
						return $d;
					}
                )
			);
		$filter = "";
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
    public function deleteProduct($data)
    {
        $id = $data['id'];
        $sql = "DELETE FROM products WHERE id= '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(['response_code'=>0,'response_message'=>'Deleted successfully']);
        }else
        {
            return json_encode(['response_code'=>77,'response_message'=>'Could not delete testimonial']);
        }
    }
    public function deleteProductPage($data)
    {
        // imagecreatefrompng();
        $id = $data['id'];
        $sql = "DELETE FROM product_page WHERE id= '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(['response_code'=>0,'response_message'=>'Deleted successfully']);
        }else
        {
            return json_encode(['response_code'=>77,'response_message'=>'Could not delete testimonial']);
        }
    }
    public function deleteFeature($data)
    {
        $id = $data['feature_id'];
        $product_id = $data['product_id'];
        $sql = "DELETE FROM product_feature WHERE id= '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            $html = $this->getProductFeatureList($product_id);
            return json_encode(['response_code'=>0,'response_message'=>'Deleted successfully','data'=>$html]);
        }else
        {
            return json_encode(['response_code'=>77,'response_message'=>'Could not delete product feature']);
        }
    }
    public function saveProduct($data)
    {
        $validation = $this->validate($data,array('name'=>'required',"product_category_id"=>"required"),array('product category'=>'full name'));
        if(!$validation['error'])
        {
            
            if($data['operation'] == "new")
            {
                
                $data['created'] = date('Y-m-d h:i:s');
                $count = $this->doInsert('products',$data,array('op','operation','id','_files'));
                if($count > 0)
                {
                    $insert_id = $this->getInsert_id($this->myconn);
                    $file_data = $data['_files'];
                    $ff   = $this->saveMerchantImage($file_data,"uploads/","");
                    $ff   = json_decode($ff,true);
//                    var_dump($ff);
                    if($ff['response_code'] == "0")
                    {
                        $full_path = $ff['data'];
                        $sql       = "UPDATE products SET logo = '$full_path' WHERE id = '$insert_id' LIMIT 1";
                        $count = $this->db_query($sql,false);
                        return ($count == 1)?json_encode(array('response_code'=>'0','response_message'=>'Successful','data'=>array('product_id'=>$insert_id))):json_encode(array('response_code'=>'471','response_message'=>'Failed to update  image'));
                    }
                    else
                    {
                        return json_encode(array('response_code'=>'71','response_mesage'=>'Failed to upload image'));
                    }
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Product Could not be Created'));
                }
            }else
            {
                
                $count = $this->doUpdate('products',$data,['op','operation','_files'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Product Updated Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Product Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function saveProductCategory($data)
    {
        $data['posted_user'] = $_SESSION['username_sess'];
        $validation = $this->validate($data,array('name'=>'required',"posted_user"=>"required"),array('posted_user'=>'Posted User'));
        if(!$validation['error'])
        {
            
            if($data['operation'] == "new")
            {
                
                $data['created'] = date('Y-m-d h:i:s');
                $count = $this->doInsert('product_category',$data,array('op','operation','id','_files'));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Product Category Saved Successfully'));
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Product Category Could not be Created'));
                }
            }else
            {
                
                $count = $this->doUpdate('product_category',$data,['op','operation','_files'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Product Category Updated Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Product Category Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function saveProductPage($data)
    {
        // $data['posted_user'] = $_SESSION['username_sess'];
        $data['url'] = $this->generatePageUrl($data['name']);
        $validation = $this->validate($data,array('name'=>'required',"main_bg_color"=>"required","secondary_bg_color"=>"required","keywords"=>"required"),array('main_bg_color'=>'Main background color','secondary_bg_color'=>'Secondary background color'));
        if(!$validation['error'])
        {
            
            if($data['operation'] == "new")
            {
                
                $data['created'] = date('Y-m-d h:i:s');
                $count = $this->doInsert('product_page',$data,array('op','operation','id','_files'));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Product page Saved Successfully'));
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Product page Could not be Created'));
                }
            }else
            {
                
                $count = $this->doUpdate('product_page',$data,['op','operation','_files','id'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Product page Updated Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Product page Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function generatePageUrl($str)
    {
        $url = strtolower(trim($str));
        $url = preg_replace('/[0-9\@\.\;\'\"]+/', '', $url);
        $url = str_replace(" ","-",$url);
        return $url;
    }
    public function saveProductFeature($data)
    {
        $product_id         = $data['product_id'];
        $data['created'] = date("Y-m-d h:i:s");
        $validation = $this->validate($data,array("description"=>"required"),array());
        if(!$validation['error'])
        {
            $count = $this->doInsert("product_feature",$data,["op","operation","_files"]);
            if($count > 0)
            {
                
                $insert_id = $this->getInsert_id($this->myconn);
                $html = $this->getProductFeatureList($product_id);
                $file_data = $data['_files'];
                // $path = './uploads/';
                $ff   = $this->saveMerchantImage($file_data,"uploads/","");
                $ff   = json_decode($ff,true);
//                    var_dump($ff);
                if($ff['response_code'] == "0")
                {
                    $full_path = $ff['data'];
                    $sql       = "UPDATE product_feature SET image = '$full_path' WHERE id = '$insert_id' LIMIT 1";
                    $count = $this->db_query($sql,false);
                    return ($count == 1)?json_encode(array('response_code'=>'0','response_message'=>'Successful','data'=>$html)):json_encode(array('response_code'=>'471','response_message'=>'Failed to update product feature image'));
                }
                else
                {
                    return json_encode(array('response_code'=>'71','response_mesage'=>'Failed to upload product feature image'));
                }
              
            }else
            {
                return json_encode(["response_code"=>819,"response_message"=>"Could not save product feature"]);
            }
        }
        else
        {
            return json_encode(["response_code"=>89,"response_message"=>$validation['messages'][0]]);
        }
    }
    public function getProductFeatureList($product_id)
    {
        $html = "";
        $counter = 0;
        $result = $this->db_query("SELECT * FROM product_feature WHERE product_id = '$product_id'");
        foreach ($result as $row) {
            $counter++;
            $html .= "<tr><td>".$counter."</td><td>".$row['name']."</td><td>".$row['description']."</td><td><img src='".$row['image']."' width='50' height='50' /></td><td>". date("jS M h:i:s",strtotime($row['created']))."</td><td><button onclick='deleteFeature(\"".$row['id']."\",\"".$row['product_id']."\")' class='btn btn-danger'>Delete</button></td></tr>";
        }
        return $html;
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
            // $myImage = new SimpleImage();
            // $myImage->load($full_path);
            // $myImage->resize(860,700);
            // $myImage->save($full_path);
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