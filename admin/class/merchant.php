<?php
class Merchant extends dbobject
{
    public $frontendBaseUrl = "";
    public $applicationName = "";
    public function __construct()
    {
        $this->frontendBaseUrl = $_ENV['FRONTEND_BASE_URL'];
        $this->applicationName = $_ENV['APPLICATION_NAME'];
    }
    
   public function merchantList($data)
    {
		$table_name    = "merchant_reg";
		$primary_key   = "merchant_id";
		$columner = array(
			array( 'db' => 'merchant_id', 'dt' => 0 ),
			array( 'db' => 'merchant_id', 'dt' => 1),
			array( 'db' => 'merchant_logo', 'dt' => 2,'formatter'=>function($d,$row){
				return "<img src='$d' class='img-thumbnail' width='50px' height='50px' />";
			  }),
            array( 'db' => 'merchant_name', 'dt' => 3),
            array( 'db' => 'merchant_phone','dt' => 4),
            array( 'db' => 'merchant_email', 'dt' => 5),
            array( 'db' => 'active_merchant', 'dt' => 6, 'formatter'=>function($d,$row){
              return ($d == 1)?"Yes":"No";
            }),
            array( 'db' => 'main_url', 'dt' => 7),
			array( 'db' => 'merchant_id', 'dt' => 8,'formatter'=>function($d,$row){
				return "NGN ".$this->getitemlabel('customer_balance','username',$d,'current_balance');
			  }),
            array( 'db' => 'account_no', 'dt' => 9),
            array( 'db' => 'merchant_id',     'dt' => 10, 'formatter' => function( $d,$row ) {
                $split_dist = "<button class='btn btn-success' onclick=\"getModal('setup/merchant_setup.php?merchant_id=$d&op=edit','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>Edit</button>";
						return $split_dist;
					}
				),
            array( 'db' => 'created',  'dt' => 11 )
			);
		$filter = "";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
	}
    public function getMerchantSubCat($data)
    {
        $cat_id = $data['cat_id'];
        $sql = "SELECT * FROM merchant_group_category WHERE cat_id = '$cat_id'";
        $result = $this->db_query($sql);
        $response = array();
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $response[] = array("id"=>$row['group_id'],"name"=>$row['group_name']);
            }
            return json_encode(array("response_code"=>0,"response_message"=>"OK","data"=>$response));
        }
        else
        {
            return json_encode(array("response_code"=>43,"response_message"=>"No group found","data"=>$response));
        }
    }
    public function merchantCategory($data)
    {
		$table_name    = "job_industry";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1),
			array( 'db' => 'id', 'dt' => 2,'formatter'=>function($d,$row){
				return "<button class='btn btn-success' onclick=\"getModal('setup/merchant_category_setup.php?cat_id=$d&op=edit','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>Edit</button>";
			  }),
           array( 'db' => 'id', 'dt' => 3,'formatter'=>function($d,$row){
				$sql = "select merchant_id FROM merchant_reg WHERE industry = '$d'";
               $count = $this->db_query($sql);
               $t_nn = count($count);
               $delete = ($t_nn == 0)?"<div><span onclick='delete_merch_cat(\"$d\")' style='cursor:pointer' class='badge badge-danger'><i class='fa fa-trash'></i> Delete Category</span></div>":"<div><span  onclick=\"getModal('setup/merchant_category_transfer.php?id=$d','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary' style='cursor:pointer' class='badge badge-info'><i class='fa fa-exchange'></i> Change Merchants under this Category</span></div>";
               return $t_nn." merchant(s)".$delete;
			  }),
            array( 'db' => 'created',  'dt' => 4 )
			);
		$filter = "";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
	}
    public function deleteMerchantCategory($data)
    {
        $id = $data['id'];
        $sql = "DELETE FROM job_industry WHERE id = '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(array("response_code"=>0,"response_message"=>"Merchant category deleted successfully")); 
        }else
        {
            return json_encode(array("response_code"=>265,"response_message"=>"Merchant category failed to delete"));
        }
    }
    public function transferMerchantCategory($data)
    {
        $new_id = $data['ji_name'];
        $old_id = $data['ji_name_old'];
        $sql = "UPDATE merchant_reg SET industry = '$new_id' WHERE industry = '$old_id'";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(array("response_code"=>0,"response_message"=>"Merchant transfered successfully")); 
        }else
        {
            return json_encode(array("response_code"=>265,"response_message"=>"Merchant transfered failed "));
        }
    }
    public function saveMerchantCategory($data)
    {
        $operation = $data['operation'];
        if($operation == "new")
        {
            $data['id'] = $this->genPrimaryKey('job_industry','id');
            $data['created'] = date('Y-m-d h:i:s');
            $count = $this->doInsert('job_industry',$data,array('op','operation'));
        }else
        {
            $count = $this->doUpdate('job_industry',$data,array('op','operation','id'),array('id'=>$data['id']));
        }
        if($count > 0)
        {
            return json_encode(array("response_code"=>0,"response_message"=>"Merchant category saved successfully")); 
        }else
        {
            return json_encode(array("response_code"=>265,"response_message"=>"Merchant category failed to save"));
        }
    }
    public function genPrimaryKey($table_name,$field)
    {
        $sql = "SELECT max(CAST(id as UNSIGNED)) + 1 AS p_key FROM $table_name";
        $result = $this->db_query($sql);
        return $result[0]['p_key'];
    }
	public function saveMerchant($data)
	{
		$validation = $this->validate($data,
                    array(
                        'merchant_email'=>'required|email',
                        'merchant_address'=>'required',
                        'merchant_phone'=>'required|int',
                        'bank_code'=>'required',
                        'account_no'=>'int',
                        'firstname'=>'required',
                        'lastname'=>'required',
                        'merchant_details'=>'required',
                        'industry'=>'required'
                    ),
                    array('merchant_email'=>'Email','lastname'=>'Last Name','firstname'=>'First Name')
                   );
        if(!$validation['error'])
        {
			$data['created'] = date("Y-m-d h:i:s");
            if($data['operation'] == 'new')
            {
                $validation = $this->validate($data,
                    array(
                        'merchant_name'=>'required|unique:merchant_reg.merchant_name'
                    ),
                    array('merchant_name'=>'Business Name')
                   );
                if(!$validation['error'])
                {
                    $data['merchant_id'] = "VUV-".date('mdhisy');
                    $count = $this->doInsert('merchant_reg',$data,array('operation','op','id','_files','merchant_password','merchant_confirm_password','user_type'));
                    if($count == 1)
                    {
    //					$this->createWallet($data['merchant_email']);
                        $this->createMerchantFolder($data['merchant_id']);
                        
                        $ff = $this->saveMerchantImage($data['_files'],$data['merchant_id'],$data['merchant_id']);
                        $ff = json_decode($ff,true);
                        if($ff['response_code'] == "0")
                        {
                            $full_path = $ff['data'];
                            $sql = "UPDATE merchant_reg SET merchant_logo = '$full_path' WHERE merchant_id='$data[merchant_id]' LIMIT 1";
                            $this->db_query($sql,false);
                            $this->createUser($data);
                            $this->createMerchantNotification($data);
                            
                            $notificationObj = new Notification();
                            $templateObj     = new EmailTemplate(); 
                            $firstname       = $data['merchant_name'];
//                            $lastname        = $data['lastName'];
                            $email           = $data['merchant_email'];
                            $message         = $templateObj->emailVerificationNew(array('title'=>'Welcome','body'=>'<b>Hello '.$firstname.'!</b><p>Thank you for choosing '.$this->applicationName.', .</p> <p>Your Login details:</p><p> Username: '.$email.' </p><p> Password: '.$data[merchant_password].' </p><p> <a href="'.$this->frontendBaseUrl.'/admin" style="color: #ffffff; background: #1b9f1e; font-weight: 900; font-style: normal; padding: 12px 28px; font-size: 12pt; border-radius: 5px; height: 20px; line-height: 1.25; position: relative; bottom: 2px; margin-left: 0px; margin-right: 0px; text-decoration: none; word-spacing: 1px; display: block; text-align: center"> Visit Store</a> </p><div>
                            </div><br/><br/><p>Best Regards,</p><b>'.$this->applicationName.' Team!</b>'));
                            
                            $notificationObj->sendNotification(array('method'=>'email'))->sendHtml(array('subject'=>'WELCOME ONBOARD','to'=>$email,'message'=>$message));
                            
                            
                            
                            return json_encode(array("response_code"=>0,"response_message"=>"Registration successful."));
                        }

                    }
                }
                else
                {
                    return json_encode(array("response_code"=>374,"response_message"=>$validation['messages'][0]));
                }
            }else
            {
                $count = $this->doUpdate('merchant_reg',$data,array('operation','op','id','_files','user_type'),array('merchant_id'=>$data['id']));
                $user_arr = array('firstname'=>$data['firstname'],'lastname'=>$data['lastname']);
                $count2 = $this->doUpdate('userdata',$user_arr,array(),array('username'=>$data['merchant_email']));
                if(isset($data['_files']))
                {
                    $ff = $this->saveMerchantImage($data['_files'],$data['id'],$data['id']);
//                    file_put_contents('track_img.txt',$ff);
                    $ff = json_decode($ff,true);
                    if($ff['response_code'] == "0")
                    {
                        $full_path = $ff['data'];
                        $sql = "UPDATE merchant_reg SET merchant_logo = '$full_path' WHERE merchant_id='$data[id]' LIMIT 1";
                        $this->db_query($sql,false);
                    }
                }
            }
            if($count == 1)
            {
				

                return json_encode(array("response_code"=>0,"response_message"=>"Merchant details saved successfully"));
            }
            else
            {
                return json_encode(array("response_code"=>74,"response_message"=>"Did not save nor update any record"));
            }
        }else
        {
            return json_encode(array("response_code"=>374,"response_message"=>$validation['messages'][0]));
        }
	}
	public function createUser($merch)
	{
		$data['day_1'] = "on";
		$data['day_2'] = "on";
		$data['day_3'] = "on";
		$data['day_4'] = "on";
		$data['day_5'] = "on";
		$data['day_6'] = "on";
		$data['day_7'] = "on";
		$data['passchg_logon'] = "on";

		$data['operation'] = "new";
		$data['firstname'] = $merch['firstname'];
		$data['lastname'] = $merch['lastname'];
		$data['mobile_phone'] = $merch['merchant_phone'];
		$data['merchant_id'] = $merch['merchant_id'];
		$data['sex'] = "male";
		$data['role_id'] = ($data['user_type'] == "mega_user")?"002":"003";
		$data['username'] = $merch['merchant_email'];
		$data['password'] = $merch['merchant_password'];
		$data['confirm_password'] = $merch['merchant_confirm_password'];
		$userObj = new Users();
        $resp    = $userObj->register($data);
        return $resp;
	}
    public function createMerchantNotification($data)
    {
        $phone   = $data['merchant_phone'];
        $email   = $data['merchant_email'];
        $type    = array('EMAIL','SMS');
        $pip     = array(array('purpose'=>'COMPLAINT','type'=>$type), array('purpose'=>'REFUND ORDERS','type'=>$type), array('purpose'=>'PAID ORDERS','type'=>$type));
        $merchant_id = $data['merchant_id'];
        foreach($pip as $val)
        {
            foreach($val['type'] as $el)
            {
                $address = ($el == "EMAIL")?$email:$phone;
                $status = ($el == "EMAIL")?"1":"0";
                $sql     = "INSERT INTO notification (merchant_id,address,type,purpose,created,status) VALUES('$merchant_id', '$address', '$el', '$val[purpose]',NOW(),'$status' )";
                $result  = $this->db_query($sql);
            }
        }
        
    }
    public function createMerchantFolder($merchant_id)
    {
        
        if(mkdir('uploads/'.$merchant_id))
        {
            if(mkdir('uploads/'.$merchant_id.'/logo'))
            {
                if(mkdir('uploads/'.$merchant_id.'/products'))
                {
                    if(mkdir('uploads/'.$merchant_id.'/banner'))
                    {
                        if(mkdir('uploads/'.$merchant_id.'/brand'))
                        {
                            if(mkdir('uploads/'.$merchant_id.'/category'))
                            {
                                
                                return true;
                            }
                            else
                            {
                                return false;
                            }
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        
        
    }
	public function saveMerchantImage($data,$user_id,$image_id="")
    {
        $_FILES = $data;
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
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            finfo_file($finfo,$_FILES['upfile']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png'
            ),
            true
        )) {
            throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Invalid file format.')));
        }

        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
       $email = ($image_id == "")?date('mdhis'):$image_id;
        $path = './uploads/'.$user_id.'/logo/';
        if (!move_uploaded_file(
            $_FILES['upfile']['tmp_name'],
            sprintf($path.'%s.%s',
                $email,
                $ext
            )
        )) {
            throw new RuntimeException(json_encode(array('response_code'=>'50','response_mesage'=>'Failed to move uploaded file.')));
        }
        $full_path = $path.$email.'.'.$ext;
        return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$full_path));
        
    }
	public function getFileDetails($data)
	{
		// $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		// echo finfo_file($finfo, "./uploads/merchant_logos/0526081608.jpg");
		// finfo_close($finfo);
		 $size = filesize('./uploads/merchant_logos/0526081608.jpg');
		return json_encode(array('name'=>'0526081608','path'=>'./uploads/merchant_logos/0526081608.jpg','size'=>$size));
        
	}
	public function createWallet($merchant_id)
	{
		$sql = "INSERT INTO customer_balance (username,previous_balance,current_balance,created) VALUES('$merchant_id','0','0',NOW())";
		$count = $this->db_query($sql,false);
		return $count;
	}
    public function getAccountName($data)
    {
        $account_number = $data['account_no'];
        $bank_code = $data['bank_code'];
        $token          = json_decode($this->getToken(),true);
        $account        = json_decode($this->validateAccount($token['token'],$account_number,$bank_code),true);
        if(is_array($account['data']))
        {
            echo $account['data']['account_name'];
        }else
        {
            echo "Unable to verify account, try again.";
        }
        
    }
    
}