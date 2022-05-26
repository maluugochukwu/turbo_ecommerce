<?php
class Templates extends dbobject
{
    private $response   = "";
    public function __construct()
    {
    }
   
    public function saveTemplate($data)
    {
        $data['created']     = date("Y-m-d h:i:s");
        // $data['merchant_id'] = $_SESSION['merchant_sess_id'];
        if($data['operation'] == "new")
        {
            $data['id'] = date('Ymdhis');
            $count   = $this->doInsert("templates",$data,array('operation','op','_files'));
            if($count > 0)
            {
//                 $path = 'uploads/788'.date('Ymdhis')."/";
                $ff = json_decode($this->saveMerchantImage($data['_files'],$data['id'],$data['id']), TRUE);
                $sql = "UPDATE templates SET file_path = '" . $ff["data"] . "' WHERE id = '" . $data['id']."'";
//                 echo $sql."\n";
                $this->db_query($sql, false);
                return json_encode(array('response_code'=>0,'response_message'=>'Template Created Successfully '.$id));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'Template Creation Failed'));
            }
        }else
        {
            $count   = $this->doUpdate("templates",$data,array('operation','op','id'),array('id'=>$data['id']));
            
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Template Updated Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>"No update made"));
            }
        }
    }
    
    public function deleteTemplate($data)
    {
        $id   = $data['id'];
        $sql  = "DELETE FROM templates WHERE id = '$id'";
        $this->db_query($sql,false);
        
        return json_encode(array('responseCode'=>0,'responseMessage'=>'SUCCESS'));
    }
    public function setFeatureStatus($data)
    {
        $status = $data['status'];
        $id = $data['id'];
        $sql = "UPDATE product_categories SET is_featured = '$status' WHERE id = $id LIMIT 1 ";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(array('responseCode'=>0,'responseMessage'=>'SUCCESS'));
        }else
        {
            return json_encode(array('responseCode'=>33,'responseMessage'=>'nO UPDATE MADE'));
        }
        
    }
    public function templateList($data)
    {
        $table_name    = "templates";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'title',  'dt' => 1 ),
			array( 'db' => 'id',  'dt' => 2,'formatter' =>function($d,$row){
                 return '<a class="badge badge-warning" onclick="getModal(\'setup/template_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Template</a>';
            } ),
			
            array( 'db' => 'created', 'dt' => 3, 'formatter' => function($d,$row)
                {
                    return $d;
                }
            )
			);
		$filter = "";
    //    $filter = " AND merchant_id='$_SESSION['merchant_sess_id']'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
   
    public function getCategory($data)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
//        $sql    = "SELECT * FROM product_categories WHERE merchant_id='$merchant_id'";
        $sql    = "SELECT * FROM product_categories ";
        $result = $this->db_query($sql);
        $options = array();
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $options[] = array('id'=>$row['id'],'name'=>$row['name'],'merchant_id'=>$row['merchant_id']);
            }
            return json_encode(array('responseCode'=>0,'data'=>$options));
        }
        else
        {
            return json_encode(array('responseCode'=>77,'data'=>''));
        }
        
    }
    public function getSubcategory($data)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
//        $sql    = "SELECT * FROM product_subcategory WHERE merchant_id='$merchant_id'";
        $sql    = "SELECT * FROM product_subcategory ";
        $result = $this->db_query($sql);
        $options = array();
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $options[] = array('id'=>$row['id'],'name'=>$row['name'],'merchant_id'=>$row['merchant_id']);
            }
            return json_encode(array('responseCode'=>0,'data'=>$options));
        }
        else
        {
            return json_encode(array('responseCode'=>77,'data'=>''));
        }
    }
    
    public function saveMerchantImage($data,$user_id,$image_id="")
    {
        $_FILES = $data;
        file_put_contents('ddd.txt',json_encode($_FILES));
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Invalid parameter.')));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'No file sent.')));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.')));
            default:
                throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Unknown errors.')));
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1000000) {
            throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.')));
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
    //    $finfo = new finfo(FILEINFO_MIME_TYPE);
//        $finfo = finfo_open(FILEINFO_MIME_TYPE);
//        if (false === $ext = array_search(
//            finfo_file($finfo,$_FILES['upfile']['tmp_name']),
//            array(
//                'jpg' => 'image/jpeg',
//                'png' => 'image/png'
//            ),
//            true
//        )) {
//            throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Invalid file format.')));
//        }

        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
       $email = ($image_id == "")?date('mdhis'):$image_id;
        $path = './uploads/'.basename($_FILES["upfile"]["name"]);
        if (!move_uploaded_file($_FILES['upfile']['tmp_name'],$path)) {
            throw new RuntimeException(json_encode(array('response_code'=>'50','response_mesage'=>'Failed to move uploaded file.')));
        }
        $full_path = $path;
        return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$full_path));
        
    }
    
    public function getProductsBySubcategory($data)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $subcat_id = $data['subcat_id'];
        $sql    = "SELECT * FROM products WHERE  sub_category = '$subcat_id'";
        $result = $this->db_query($sql);
        $options = array();
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $options[] = array('id'=>$row['id'],'name'=>$row['name'],'merchant_id'=>$row['merchant_id']);
            }
            return json_encode(array('responseCode'=>0,'data'=>$options));
        }
        else
        {
            return json_encode(array('responseCode'=>77,'data'=>''));
        }
    }
  
}