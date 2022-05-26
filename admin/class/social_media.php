<?php
class SocialMedia extends dbobject
{
    private $response   = "";
    public function __construct()
    {
    }
   
    public function saveSocialMedia($data)
    {
        $data['created']     = date("Y-m-d h:i:s");
        // $data['merchant_id'] = $_SESSION['merchant_sess_id'];
        if($data['operation'] == "new")
        {
            
            $count   = $this->doInsert("social_media",$data,array('operation','op','id','_files'));
            if($count > 0)
            {
                 
                return json_encode(array('response_code'=>0,'response_message'=>'social media Created Successfully '.$id));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'social media Creation Failed'));
            }
        }else
        {
            $count   = $this->doUpdate("social_media",$data,array('operation','op','id'),array('id'=>$data['id']));
            
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'social media Updated Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>"No update made"));
            }
        }
    }
    
    public function deleteJob($data)
    {
        $id   = $data['id'];
        $sql  = "DELETE FROM job_posting WHERE id = '$id'";
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
    public function socialMediaList($data)
    {
        $table_name    = "social_media";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name',  'dt' => 1 ),
			array( 'db' => 'icon',  'dt' => 2, 'formatter'=>function($d,$row){
                return $d; //"<i class='$d'></i>";
            } ),
			array( 'db' => 'link',  'dt' => 3),
			array( 'db' => 'id',  'dt' => 4, 'formatter'=>function($d,$row){
                return "<button class='btn btn-primary'>Edit</button>";
            }),
            array( 'db' => 'created', 'dt' => 5, 'formatter' => function($d,$row)
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
    public function saveMerchantImage($data, $user_id, $path, $image_id = "")
    {
        $_FILES = $data;
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            return json_encode(array('response_code' => '74', 'response_mesage' => 'Invalid parameter.'));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return json_encode(array('response_code' => '74', 'response_mesage' => 'No file sent.'));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return json_encode(array('response_code' => '74', 'response_mesage' => 'Exceeded filesize limit.'));
            default:
                return json_encode(array('response_code' => '74', 'response_mesage' => 'Unknown errors.'));
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1000000) {
            return json_encode(array('response_code' => '74', 'response_mesage' => 'Exceeded filesize limit.'));
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
        //    $finfo = new finfo(FILEINFO_MIME_TYPE);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            finfo_file($finfo, $_FILES['upfile']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png'
            ),
            true
        )) {
            return json_encode(array('response_code' => '74', 'response_mesage' => 'Invalid file format.'));
        }

        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        $email = ($image_id == "") ? date('mdhis') : $image_id;

        $s3Client = $this->createAwsS3($_ENV['AWS_ACCESS_KEY'],$_ENV['AWS_ACCESS_SECRET']);
                // echo "point1";
                $environment = ($_SERVER['SERVER_NAME'] == "store200.com")?"":"demo/";
        try {
            $result = $s3Client->putObject([
                'Bucket' => $_ENV['AWS_BUCKET'],
                'Key' => $environment.sprintf($path.'%s.%s',$email,$ext),
                'ContentLength' => $_FILES['upfile']['size'],
                'SourceFile' => $_FILES['upfile']['tmp_name'],
                // 'ACL'   => 'public-read'
            ]);
            // var_dump($result);
            $aws_full_path = $result['ObjectURL'];
            if (!filter_var($aws_full_path, FILTER_VALIDATE_URL))
            {
                return json_encode(array('response_code'=>'540','response_mesage'=>"Invalid url"));
            }
            
        } catch (AwsException $e) {
            // echo $e->getMessage();
            return json_encode(array('response_code'=>'540','response_mesage'=>$e->getMessage()));
        }
        //@@@@@@@@@@@@@@@@@@@@@@@@
        
        unlink($_FILES['upfile']['tmp_name']);
        return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$aws_full_path));
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