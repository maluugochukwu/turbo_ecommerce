<?php

class Dress extends dbobject
{
   public function dressList($data)
    {
		$table_name    = "dress";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
			array( 'db' => 'price',  'dt' => 2, 'formatter' => function( $d,$row ) {
						return "&#8358; ".number_format($d);
					} ),
			array( 'db' => 'image',  'dt' => 3, 'formatter' => function( $d,$row ) {
						return '<img class="img-thumbnail" src="'.$d.'" width="50" height="50" /><br/><span style="cursor:pointer" class="badge badge-primary" onclick="getModal(\'setup/dress_image.php?op=edit&id='.$row[id].'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary"><i class="fa fa-edit"></i> Edit Image</span>';
					} ),
			array( 'db' => 'caution_fee',  'dt' => 4, 'formatter' => function( $d,$row ) {
						return "&#8358; ".number_format($d);
					}  ),
// 			array( 'db' => 'status',  'dt' => 5, 'formatter'=> function($d,$row){
//                 $state   = ($d == 1)?"badge-danger":"badge-success";
//                 $message = ($d == 1)?"Booked":"Available";
// //                $book = ($d != 1)?"<span style='cursor:pointer' class='badge badge-secondary'><i class='fa fa-book'></i> Book Dress</span>":"";
//                 return "<span class='badge $state'>$message</span>";
//             } ),
            array( 'db' => 'location',  'dt' => 5, 'formatter' => function( $d,$row ) {
						return $this->getitemlabel('branch','id',$d,'name');
					} ),
			array( 'db' => 'created',     'dt' => 6, 'formatter' => function( $d,$row ) {
						return $d;
					}
				),
            array( 'db' => 'transaction_id',  'dt' => 7, 'formatter' => function( $d,$row ) {
                        // return ($d != "")?'<button class="btn btn-secondary btn-sm" onclick="getModal(\'setup/booking_details.php?op=edit&id='.$d.'\',\'modal_div2\')"  href="javascript:void(0)" data-toggle="modal" data-target="#editing_product"><i class="fa fa-shopping-cart"></i> View Order</button>':"";
                        return '<span style="cursor:pointer" class="badge badge-info" onclick="getpage(\'transaction_list.php?dress_id='.$row['id'].'\',\'page\')" ><i class="fa fa-eye"></i> View dress orders</span>';
					} ),
            array( 'db' => 'id',  'dt' => 8, 'formatter' => function( $d,$row ) {
                        return '<span style="cursor:pointer" class="badge badge-warning" onclick="getModal(\'setup/dress.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary"><i class="fa fa-edit"></i> Edit Dress</span>';
                        
					} )
			);
		$filter = "";
        $filter = ($_SESSION['role_id_sess'] == '001' || $_SESSION['role_id_sess'] == '002')?"":" AND region = '$_SESSION[region_sess]'";
        if(isset($data['region']))
        {
            if($data['region'] != "")
            {
                $filter = $filter." AND region = '$data[region]'";
            }
        }
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    
    public function returnDress($data)
    {
        // set the return_dress to 1 in transaction table
        // set status of dress to 0 and set transaction id to null
        // insert into deductions if there are any deductions
        
        $this->doUpdate('transaction_table',array('is_returned'=>1,'return_date'=>date('Y-m-d h:i:s')),[],array('transaction_id'=>$data['transaction_id']));
        $this->doUpdate('dress',array('status'=>0,'transaction_id'=>null),[],array('id'=>$data['dress_id']));
        if($data['is_deduction'] == "1")
        {
//            var_dump($data['deduction']);
            foreach($data['deduction']['name'] as $key=>$val)
            {
                $name = $val;
                $amount = $data['deduction']['amount'][$key];
    $this->doInsert('deductions',array('deduction_name'=>$name,'amount'=>$amount,'transaction_id'=>$data['transaction_id'],'created'=>date('Y-m-d h:i:s')),[]);
            }
            
        }
        return json_encode(array('response_code'=>0,'response_message'=>'Saved successfully'));
    }
    public function bookDress($data)
    {
        //insert to transaction table
        //insert to customer table
        //update dress table
        //then if need update deductions table
        $dress_det                          = $this->getDressDetails($data['dress_id']);
        $actual_dress_price                 = $dress_det['dress_fee'];
        
        $dress_det['dress_fee']             = $data['dress_price'];
        $trans_fields['destination_acct']   = "RENT A DRESS";
        $trans_fields['source_acct']        = $data['source_acct'];
        $trans_fields['transaction_desc']   = "Booking for a dress";
//        $trans_fields['transaction_amount'] = (isset($data['discount']))?($data['discount_price'] + $data['caution_fee'] + $data['extra_item_price']):($dress_det['dress_fee'] + $data['caution_fee'] + $data['extra_item_price']);
        $trans_fields['transaction_amount'] = $data['dress_price'] + $data['caution_fee'] + $data['extra_item_price'];
        // $trans_fields['transaction_amount'] = number_format($trans_fields['transaction_amount']);
        $trans_fields['dress_amount']       = $data['dress_price'];
        $trans_fields['caution_fee']        = $data['caution_fee'];
        $trans_fields['extra_item_price']   = $data['extra_item_price'];
        $trans_fields['extra_item']         = $data['extra_item'];
        $trans_fields['wedding_date']       = $data['wedding_date'];
        $trans_fields['is_extra_item']      = $data['is_extra_item'];
        
        $trans_fields['response_code']      = 0;
        $trans_fields['response_message']   = "success";
//        $trans_fields['payment_mode']       = "CASH";
        
        $trans_fields['charge_currency']    = "NAIRA";
        $trans_fields['payment_channel']    = $data['payment_channel'];
//        $trans_fields['payment_medium']     = $data['payment_medium'];
        $trans_fields['is_payment_full']    = $data['is_payment_full'];
        $trans_fields['items']              = $data['items'];
        $trans_fields['return_date']        = $data['return_date'];
        $trans_fields['pickup_date']        = $data['pickup_date'];
        $trans_fields['dress_id']           = $data['dress_id'];
        $trans_fields['additional_request'] = $data['additional_request'];
        $trans_fields['payment_mode']       = $data['payment_mode'];
        $trans_fields['discount_price']     = ($actual_dress_price > $data['dress_price'])?$data['dress_price']:0;
        $trans_fields['is_discounted']      = ($actual_dress_price > $data['dress_price'])?1:0;//(isset($data['discount']))?1:0;
        $trans_fields['posted_by']          = $_SESSION['username_sess'];
        $trans_fields['branch_id']          = $data['branch_id'];
        // $trans_fields['region']             = ($_SESSION['role_id_sess'] == "002")?$data['region']:$_SESSION['region_sess'];
        $trans_fields['region']             = $data['region'];
        $trans_fields['state_id']           = $this->getitemlabel('branch','id',$trans_fields['branch_id'],'state_id');
        
        $validation = $this->validate($data, array('source_acct'=>'required', 'return_date'=>'required', 'pickup_date'=>'required', 'dress_id'=>'required','phone'=>'required|int|min:11','first_name'=>'required|min:2','last_name'=>'required|min:2','address'=>'required','bank_name'=>'required','account_no'=>'required' ), array('account_no'=>'Account Number','account_name'=>'Account Name','source_acct'=>'Email','bank_name'=>'Bank Name','dress_id'=>'Dress','return_date'=>'Return Date','pickup_date'=>'Pickup Date','last_name'=>'Last Name','first_name'=>'First Name'));
//        if((isset($data['discount']) && $data['discount_price'] == "") || (isset($data['discount']) && $data['discount_price'] >= $dress_det['dress_fee']))
//        {
//            return json_encode(array("response_code"=>34,"response_message"=>"Enter the discounted price, discount price should be less than ".$dress_det['dress_fee']));
//        }
        
//        if($data['is_extra_item'] == "yes" && ($data['is_extra_item']))
        if(($data['is_extra_item'] == "yes" && $data['extra_item'] == "") || ($data['is_extra_item'] == "yes" && $data['extra_item_price'] == ""))
        {
            return json_encode(array("response_code"=>34,"response_message"=>"Enter value for extra items, enter value for extra item price."));
        }
        if($trans_fields['is_payment_full'] == "no")
        {
//            $aamt = (isset($data['discount']))?$data['discount_price']:$dress_det['dress_fee'];
            $aamt = $data['dress_price'];
            if($data['amount_paid'] >= $aamt)
            {
                return json_encode(array("response_code"=>124,"response_message"=>"Partial payment is suppose to be less than the dress price "));
            }
        }
        if($_SESSION['role_id_sess'] == "002" && $data['branch_id'] == "")
        {
            return json_encode(array("response_code"=>34,"response_message"=>"Select Dress Location"));
        }
        
        $customer_insert_data = array('email'=>$data['source_acct'],'phone'=> $data['phone'] ,'first_name'=>$data['first_name'],'last_name'=>$data['last_name'],'address'=>$data['address'],'bank_name'=>$data['bank_name'],'account_name'=>$data['account_name'],'account_no'=>$data['account_no'],'created'=>date("Y-m-d h:i:s"),'phone_2'=>$data['phone_2']);
        if(!$validation['error'])
        {
            if($data['operation'] == "new")
            {
                $trans_fields['created']            = date("Y-m-d h:i:s");
                $trans_fields['transaction_id']     = $this->generateOrderID();
                $trans_record                       = $this->doInsert('transaction_table',$trans_fields,[]);

                $customer_record                    = $this->doInsert('customers',$customer_insert_data,[]);
                if($trans_record > 0)
                {
                    if($data['is_payment_full'] == "no")
                    {
                        $this->doInsert('installment_payment',array("booking_id"=>$trans_fields['transaction_id'],"amount_paid"=>$data['amount_paid'],"posted_by"=>$trans_fields['posted_by'],"created"=>$trans_fields['created'],"branch_id"=>$data['branch_id']),[]);
                    }

                    $notificationObj = new Notification();
                    $subscribers = $notificationObj->getNotificationSubscribers("TRANSACTION");
                    $templateObj     = new EmailTemplate(); 
                    $region_name = $this->getitemlabel("country","id",$trans_fields['region'],"name");
                    $branch_name = $this->getitemlabel("branch","id",$trans_fields['branch_id'],"name");
                    $dress_name = $this->getitemlabel("dress","id",$data['dress_id'],"name");
                    $amount_paid = ($trans_fields['is_payment_full'] == "no")?$data['amount_paid']:$trans_fields['transaction_amount'];
                    // file_put_contents("email_log.txt",json_encode($subscribers));
                    foreach($subscribers as $row)
                    {
                        $firstname = $row['f_name'];
                        $email = $row['email'];
                        $message         = $templateObj->emailVerificationNew(array('title'=>'Booking Alert','body'=>"<b>Hello ".$firstname."!</b><p>This is to notify you that a booking has been made in ".$branch_name." ".$region_name."</p><p>Dress Code:".$dress_name." </p><p>Total Amount: ".$trans_fields['transaction_amount']."</p><p>Amount Paid: ".$amount_paid."</p><small>Kindly contact the admin to unsubscribe to this notification.</small>"));

                        $notificationObj->sendNotification(array('method'=>'email'))->sendHtml(array('subject'=>'Booking Alert','to'=>$email,'message'=>$message));
                    }
                    


                    return json_encode(array('response_code'=>0,'response_message'=>'Saved successfully','data'=>array('id'=>$trans_fields['transaction_id'])));
                }else
                {
                    return json_encode(array('response_code'=>78,'response_message'=>'Could not save booking record'));
                }
            }else
            {
                $trans_current_data = $this->getCurrentData('transaction_table','transaction_id',$data['trans_id']);
                $cust_current_data = $this->getCurrentData('customers','email',$data['source_acct']);
                $trans_fields['is_payment_full'] = $this->getitemlabel('transaction_table','transaction_id',$data['trans_id'],'is_payment_full');
                
                $trans_record                       = $this->doUpdate('transaction_table',$trans_fields,[],array('transaction_id'=>$data['trans_id']));
                $cust_record                        = $this->doUpdate('customers',$customer_insert_data,[],array('email'=>$data['source_acct']));
                if($trans_record > 0)
                {
                    $this->logData($trans_current_data,$trans_fields,["table_name"=>'transaction_table',"table_id"=>$data['trans_id'],"table_alias"=>'Dress Booking'],['transaction_id','created','transaction_amount']);
                    $this->logData($cust_current_data,$customer_insert_data,["table_name"=>'customers',"table_id"=>$data['source_acct'],"table_alias"=>'Customer Details'],['created']);
                    return json_encode(array('response_code'=>0,'response_message'=>'Updated successfully','data'=>array('id'=>$data['trans_id'])));
                }
                else
                {
                    return json_encode(array('response_code'=>78,'response_message'=>'Could not update booking record'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function getCurrentData($table_name,$table_field,$table_id)
    {
        $sql = "SELECT * FROM $table_name WHERE  $table_field = '$table_id' LIMIT 1";
        $result = $this->db_query($sql);
        return $result[0];
    }
    public function logData($current_data,$insert_data,Array $option, Array $exempt = [])
    {
        $result       = $this->doInsert("log_table",array("username"=>$_SESSION['username_sess'],"table_name"=>$option['table_name'],"table_id"=>$option['table_id'],"table_alias"=>$option['table_alias'],"created"=>date("Y-m-d h:i:s")),[]);
        $insert_id    = $this->getInsert_id();
        // $current_data = $this->getCurrentData($option['table_name'],$option['table_field'],$option['table_id']);
        if($result == "1")
        {
            $difference = array_diff($insert_data,$current_data);
            
            // var_dump($insert_data);
            // var_dump($current_data);
            // var_dump($difference);
            foreach($difference as $key=>$value)
            {
                if(!in_array($key,$exempt))
                {
                    $this->doInsert("log_details",array("log_id"=>$insert_id,"field_name"=>$key,"previous_data"=>$current_data[$key],"current_data"=>$value,"field_alias"=>""),[]);
                }
            }
        }
    }
    public function getDressDetails($dress_id)
    {
        $sql = "SELECT * FROM dress WHERE id = '$dress_id' LIMIT 1";
        $result = $this->db_query($sql);
        $arr = array('dress_fee'=>$result[0]['price'],'caution_fee'=>$result[0]['caution_fee']);
        return $arr;
        
    }
    public function getDressAmountDetails($data)
    {
        $dress_id = $data['dress_id'];
        $sql      = "SELECT price FROM dress WHERE id = '$dress_id' LIMIT 1";
        $result   = $this->db_query($sql);
        $point_pos = strpos($result[0]['price'],".");
        return substr($result[0]['price'],0,$point_pos);
    }
    public function getBranch($data)
    {
        $region = $data['region'];
        $sql = "SELECT * FROM branch WHERE region = '$region'";
        $result = $this->db_query($sql);
        $option = "";
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $option = $option."<option value='".$row['id']."'>".$row['name']."</option>";
            }
        }else
        {
            $option = "<option value=''>:: No branch available ::</option>";
        }
        
        return $option;
    }
    public function getAvailableDress($data)
    {
        $pickup_date = $data['pickup_date'];
        $special_roles = array("001","002");
        $region_filter = (in_array($_SESSION['role_id_sess'],$special_roles))?"":" AND region = '$_SESSION[region_sess]'";
         $sql = "SELECT * from dress WHERE (status = '0' OR ( status = '1' AND id IN(SELECT dress_id from transaction_table WHERE DATEDIFF('$pickup_date', return_date) > 0  AND is_returned = '0' ) ) ) $region_filter";
        $result = $this->db_query($sql);
        $options = "";
        if(count($result) > 0)
        {
            $options .= "<option value=''>:: SELECT A DRESS ::</option>";
          foreach($result as $row)
            {
                $options .= "<option value='$row[id]'>$row[name]</option>";
            }  
        }else
        {
            $options = "<option value=''>No dress available</option>";
        }
        
        return $options;
    }
    public function updateDressImage($data)
    {
        
//        file_put_contents("image_keeper.txt",json_encode($data),FILE_APPEND);
        $upload_type      = $data['u_type'];
        $image_location   = $data['image_location']; // used to unset the image from the product folder
        $file_data        = $data['_files'];
//        $merchant_id = $_SESSION['merchant_sess_id'];
        $path        = 'uploads/';
//        foreach($data['_files'] as $file_data)
        $image_id    = rand(1111,999999999).date('his');
        
        $ff = $this->saveMerchantImage($file_data,$merchant_id,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            $dress_id = $data['dress_id'];
            $sql       = "UPDATE dress SET image = '$full_path' WHERE id = '$dress_id' LIMIT 1";
            $count     = $this->db_query($sql,false);
            // unlink($image_location);
            return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
            
            
            
        }else
        {
            return json_encode(array('response_code'=>'458','response_message'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
    public function generateOrderID()
    {
        $chars = "0123456789";
        $yr    = date("Y");
        $res = "";
        for ($i = 0; $i < 8; $i++) 
        {
            $res .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        $yr_chunck = str_split($yr);
        $res_chunck = str_split($res);
        // var_dump($yr_chunck); 
        $order_id = "";
        foreach($res_chunck as $key=> $val)
        {
            if($key == "2")
            {
                $order_id .= $yr_chunck[0].$val;
            }
            elseif($key == "4")
            {
                $order_id .= $yr_chunck[1].$val;
            }
            elseif($key == "6")
            {
                $order_id .= $yr_chunck[2].$val;
            }
            elseif($key == "1")
            {
                $order_id .= $yr_chunck[3].$val;
            }else
            {
                $order_id .= $val;
            }
        }
        $sql   = "SELECT transaction_id FROM transaction_table WHERE transaction_id = '$order_id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            $order_id = $this->generateOrderID();
        }
        return $order_id;
    }
    public function getAccountName($data)
    {
//        var_dump($data);
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
    public function getDress($data)
    {
        $dress_id = $data['dress_id'];
        $sql = "SELECT * FROM dress WHERE id = '$dress_id' LIMIT 1";
        $result = $this->db_query($sql);
        $output = array();
        foreach($result as $row)
        {
            $output['status']['message'] = ($row['status'] == 1)?"Booked":"Available";
            $output['status']['code'] = $row['status'];
            $output['price'] = number_format($row['price']);
            $output['caution_fee'] = number_format($row['caution_fee']);
            $output['total'] = number_format($row['price'] + $row['caution_fee']);
            $output['name'] = $row['name'];
            $items                  = $this->getitemlabel('package','id', $row['package_id'],'items');
            
            $output['packages'] = json_decode($items,true);
            if(count($output['packages']) > 0)
            {
                foreach($output['packages'] as $val)
                {
                    $output['packages_formatted'] .= "<li >".$val."</li>";
                }
            }
            
            $output['return_date'] = ($row['status'] == 1)? $this->getitemlabel('transaction_table','transaction_id', $row['transaction_id'],'return_date'):"";
        }
        return json_encode($output);
    }
    public function saveDress($data)
    {
//        var_dump($data);
        $validation = $this->validate($data, array('name'=>'required', 'price'=>'required|int', 'caution_fee'=>'required|int', 'location'=>'required' ), array('caution_fee'=>'caution fee'));
        if(!$validation['error'])
        {
            $data['created'] = date('Y-m-d h:i:s');
            
            if($data['operation'] == "new")
            {
//                $data['role_id'] = $this->getNextRoleId();
//                $data['role_enabled'] = "1";
                $count = $this->doInsert('dress',$data,array('op','operation','_files'));
                if($count > 0)
                {
                    $insert_id = mysqli_insert_id($this->myconn);
                    $file_data = $data['_files'];
                    $path = './uploads/';
                    $ff   = $this->saveMerchantImage($file_data,"",$path,$product_id);
                    $ff   = json_decode($ff,true);
//                    var_dump($ff);
                    if($ff['response_code'] == "0")
                    {
                        $full_path = $ff['data'];
                        $sql       = "UPDATE dress SET image = '$full_path' WHERE id = '$insert_id' LIMIT 1";
                        $count = $this->db_query($sql,false);
                        return ($count == 1)?json_encode(array('response_code'=>'0','response_message'=>'Successful','data'=>array('product_id'=>$product_id))):json_encode(array('response_code'=>'471','response_message'=>'Failed to update product image'));
                    }
                    else
                    {
                        json_encode(array('response_code'=>'71','response_mesage'=>'Failed to upload product image'));
                    }
                    return json_encode(array('response_code'=>0,'response_message'=>'Dress Created Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Dress Could not be Created'));
                }
            }else
            {
                $count = $this->doUpdate('dress',$data,['op','operation'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Dress Update Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Dress Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    
    public function saveMerchantImage($data,$user_id,$path,$image_id="")
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
            $myImage->resize(330,330);
            $myImage->save($full_path);
            unlink($_FILES['upfile']['tmp_name']);
            return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$full_path));
        
                
            
        
    }
    
    public function getNextRoleId()
    {
        $sql    = "select CONCAT('00',max(role_id) +1) as rolee FROM role";
        $result = $this->db_query($sql);
        return $result[0]['rolee'];
        
    }
}