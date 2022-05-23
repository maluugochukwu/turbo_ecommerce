<?php

class Transaction extends dbobject
{
   public function transactionList($data)
    {
		$table_name    = "transaction_table";
		$primary_key   = "transaction_id";
		$columner = array(
			array( 'db' => 'transaction_id', 'dt' => 0 ),
			array( 'db' => 'transaction_id', 'dt' => 1,'formatter'=>function($d,$row){
                $sql    = "SELECT id from log_table WHERE table_id = '$d' ";
                $result = $this->db_query($sql);
                $count = count($result);
                $count_display = ($count > 0)?"":"display:none";
                return "<b>".$d."</b><div><span style='cursor:pointer' class='badge badge-info' onclick=\"printer('$d')\"><i class='fa fa-print'></i> print receipt</span> &nbsp; <span style='cursor:pointer;$count_display' class='badge badge-primary' onclick=\"getpage('log_list.php?table_id=$d','page')\"><i class='fa fa-user'></i> This record was updated $count time(s)</span></div>";
            }),
            array( 'db' => 'transaction_amount', 'dt' => 2,'formatter'=>function($d,$row){
                $bb =($row['is_discounted'] == "1")?"<div><small class='badge badge-warning'>Discounted</small></div>":"";
                $cc =($row['is_payment_full'] == "no")?'<div><small onclick="getModal(\'setup/complete_payment.php?id='.$row[transaction_id].'&dress_id='.$row['dress_id'].'\',\'modal_div\')" style="cursor:pointer"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary" class="badge badge-danger">Partial payment</small></div>':"";
                return "&#8358; ".number_format($d,2).$bb.$cc;
//                
            }),
            array( 'db' => 'source_acct', 'dt' => 3,'formatter'=>function($d,$row){
                return '<span style="cursor:pointer" class="badge badge-secondary" onclick="getModal(\'customer_details.php?id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary"><i class="fa fa-eye"></i> '.$d.'</span>';
            }),
//            array( 'db' => 'destination_acct', 'dt' => 4),
            array( 'db' => 'dress_id', 'dt' => 4,'formatter'=>function($d,$row){
                $is_collected = ($row['is_collected'] == "1")?"<span class='badge badge-success'>Collected</span>":"<span class='badge badge-danger'>Not Collected</span>";
                return "<b style='font-size:16px'>".$this->getitemlabel('dress','id',$d,'name')."</b><div>".$is_collected."</div>";
            }),
            array( 'db' => 'dress_amount', 'dt' => 5,'formatter'=>function($d,$row){
//                return "&#8358; ".number_format($d,2);
                $actual_dress_price = $this->getitemlabel('dress','id',$row['dress_id'],'price');
                $rtt = ($row['is_discounted'] == "1")?"<b>".number_format($row['discount_price'])."</b><br /><strike style='color:red'>".number_format($actual_dress_price)."</strike>":number_format($d);
                return "&#8358; ".$rtt;
            }),
            array( 'db' => 'caution_fee',     'dt' => 6,'formatter'=>function($d,$row){
                return "&#8358; ".number_format($d,2);
            }),
            array( 'db' => 'region',  'dt' => 7, 'formatter'=>function($d,$row){
                return $this->getitemlabel("country","id",$d,"name");
            }),
            
            array( 'db' => 'branch_id',     'dt' => 8,'formatter'=>function($d,$row){
                return $this->getitemlabel('branch','id',$d,'name');
            }),
            array( 'db' => 'pickup_date', 'dt' => 9,'formatter'=>function($d,$row){
                return date("F jS, Y",strtotime($d));
            }),
            array( 'db' => 'return_date', 'dt' => 10,'formatter'=>function($d,$row)
              {
                  
                  return date("F jS, Y",strtotime($d));
              }),
              array( 'db' => 'is_returned',     'dt' => 11,'formatter'=>function($d,$row){
                $date1=date_create(date("Y-m-d"));
                  $date2=date_create($row['return_date']);
                  $diff=date_diff($date1,$date2);
                  $difference = $diff->format("%R%a");
                  $df = "";
                  if($d != 1)
                  {
                      if($difference < 0)
                      {
                          $df = "<span class='badge badge-danger'><i class='fa fa-exclamation'></i> Overdue</span>";
                      }else
                      {
                          $df = "<small>".$difference." days left</small>";
                      }
                  }
                $output = ($d == 0)?"<span class='badge badge-danger'>Not returned</span>":"<span class='badge badge-success'> Returned</span>";
                return ($row['is_collected'] == 1)?$output.$df:"";
            }),
            array( 'db' => 'posted_by',  'dt' => 12 ),
            array( 'db' => 'payment_mode',  'dt' => 13 ),
            array( 'db' => 'created',  'dt' => 14 ),
            array( 'db' => 'transaction_id',  'dt' => 15,'formatter'=>function($d,$row)
              {
                  $return_dress = ($row['is_returned'] == 0 && $row['is_collected'] == 1)?'<button class="btn btn-info" onclick="getModal(\'setup/return_dress.php?trans_id='.$d.'&op=edit&id='.$row[dress_id].'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary" >Return Dress</button>':"";
                  $edit_dress = '<button class="btn btn-warning" onclick="getModal(\'setup/book_dress.php?trans_id='.$d.'&op=edit&id='.$row[dress_id].'\',\'modal_div2\')"  href="javascript:void(0)" data-toggle="modal" data-target="#editing_product" >Edit Dress</button>';
                  return $return_dress.$edit_dress;
              } ),
            array( 'db' => 'is_discounted',  'dt' => -1 ),
            array( 'db' => 'discount_price',  'dt' => -1 ),
            array( 'db' => 'is_collected',  'dt' => -1 ),
            array( 'db' => 'is_payment_full',  'dt' => -1 ),
            array( 'db' => 'wedding_date',  'dt' => -1 ),
            array( 'db' => 'items',  'dt' => -1 ),
            array( 'db' => 'extra_item',  'dt' => -1 ),
            array( 'db' => 'extra_item_price',  'dt' => -1 ),
			);
      
		
		$datatableEngine = new engine();
       $filter = "";
       if($data['status'] != "")
       {
           if($data['status'] == '2')
           {
               $filter = $filter." AND  CURDATE() > return_date AND is_returned = '0'"; 
           }else
           {
               $filter = $filter." AND is_returned = '$data[status]'";
           }
       }
//       if($data['branch'] != "")
//       {
//           $filter = $filter." AND branch_id = '$data[branch]'";
//       }
       if($data['dress'] != "")
       {
           $filter = $filter." AND dress_id = '$data[dress]'";
       }
       if($data['is_collected'] != "")
       {
           $filter = $filter." AND is_collected = '$data[is_collected]'";
       }
       if($data['payment_mode'] != "")
       {
           $filter = $filter." AND payment_mode = '$data[payment_mode]'";
       }
       if($data['booking_id'] != "")
       {
           $filter = $filter." AND transaction_id = '$data[booking_id]'";
       }
	   $filter_2 = ($_SESSION['role_id_sess'] == "001" || $_SESSION['role_id_sess'] == "002")?"":" AND region = '$_SESSION[region_sess]'";
       $filter = $filter.$filter_2;
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function completePayment($data)
    {
        $validation = $this->validate($data, array('amount_paid'=>'required|int'), array('amount_paid'=>'Paying'));
        if(!$validation['error'])
        {
            $amount_paid  = $data['amount_paid'];
            $posted_by  = $_SESSION['username_sess'];
            $trans_id     = $data['trans_id'];
            $booking_amount = $this->getBookingAmount($trans_id);
            $prev_payment   = $this->getSumInstallmentAmount($trans_id);
            if(($amount_paid + $prev_payment) > $booking_amount)
            {
                return json_encode(array("response_code"=>344,"response_message"=>"The system detected an overpayment. Customer is owing ".($booking_amount - $prev_payment)));
            }
            if(($amount_paid + $prev_payment) == $booking_amount)
            {
                $this->doUpdate('transaction_table',array('is_payment_full'=>'yes'),[],array('transaction_id'=>$trans_id));
                $this->doInsert('installment_payment',array("booking_id"=>$trans_id,"amount_paid"=>$amount_paid,"posted_by"=>$posted_by,"created"=>date('Y-m-d h:i:s'),"branch_id"=>""),[]);
                return json_encode(array("response_code"=>0,"response_message"=>"Payment is now complete"));
            }elseif(($amount_paid + $prev_payment) < $booking_amount)
            {
                $this->doInsert('installment_payment',array("booking_id"=>$trans_id,"amount_paid"=>$amount_paid,"posted_by"=>$posted_by,"created"=>date('Y-m-d h:i:s'),"branch_id"=>""),[]);
                return json_encode(array("response_code"=>0,"response_message"=>"Payment saved"));
            }
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function getSumInstallmentAmount($booking_id)
    {
        $sql = "SELECT SUM(amount_paid) AS paid FROM installment_payment where booking_id = '$booking_id'";
        $result = $this->db_query($sql);
        return $result[0]['paid'];
    }
    public function getBookingAmount($trans_id)
    {
        $sql = "SELECT transaction_amount FROM transaction_table WHERE transaction_id = '$trans_id' LIMIT 1";
        $result = $this->db_query($sql);
        return $result[0]['transaction_amount'];
    }
    public function collectDress($data)
    {
        $trans_id = $data['trans_id'];
        $collected_by = $data['collected_by'];
        $dress = $this->doUpdate('transaction_table',array('is_collected'=>'1','collected_date'=>date('Y-m-d h:i:s'),'collected_by'=>$collected_by),array(),array('transaction_id'=>$trans_id));

    }
    public function checkCart($data)
    {
        $data = array("total"=>"", 
                      "item"=>array( 
                                  array("quantity"=>5, 
                                        "product"=>array(
                                            "id"=>"", 
                                            "price"=>""
                                        ), 
                                        "variant"=>array(
                                            "id"=>"", 
                                            "price"=>"" 
                                        )
                                       )
                                )
                       );
    }
    public function logOrderTransaction($data)
    {
        $merchant_id = $data['merchant_id'];
        $customerid = $data['customer_id'];
        $desc       = $data['trans_desc'];
        $p_mode       = $data['payment_mode'];
        $p_mode       = $_SERVER['REMOTE_ADDR'];
        $trans_id   = date("Ymdhis");
        $order_id   = $this->generateOrderID($customerid);
        $cart_item  = $data['payload'];
//        var_dump($cart_item);
        $shipping_fee = $this->getShippingFee($cart_item);
        $amount       = $this->getCartTotalAmount($cart_item);
        $total_amount = $shipping_fee + $amount;
        $amount     = $this->getCartTotalAmount($cart_item) + $this->getShippingFee($cart_item);
        $sql = "INSERT INTO transaction_table (transaction_id,order_id,transaction_amount,total_shipping_price,source_acct,destination_acct,trans_type,transaction_desc,response_code,response_message,payment_mode,posted_ip,merchant_id,customer_id,customer_state,customer_lga,created,posted_user) VALUES('$trans_id','$order_id','$total_amount','$shipping_fee','$customerid','$merchant_id','trans_type','$desc','99','Initialized','$p_mode','$p_mode','$merchant_id','$customerid','customer_state','customer_lga',NOW(),'posted_user')";
        $result = $this->db_query($sql);
        
        $sql2 = "INSERT INTO orderdetails () VALUES()";
        
    }
    public function getCartTotalAmount($cart_item)
    {
        $amount = 0;
        foreach($cart_item as $row)
        {
            
            $id_check = $this->checkProductID($row['id']);
            if($id_check == 1)
            {
                $variantArr   = $row['variant'];
                $checkVariant = json_decode($this->shouldIncludeVariant($row['id']),TRUE,$variantArr);
                if($checkVariant['responseCode'] == 0) // product has variant
                {
                    $db_variant = $checkVariant['responseBody'];
                    if($db_variant[$variantArr['id']]['price'] == $variantArr['price'])
                    {
                        $amount = $amount + $db_variant[$variantArr['id']]['price'];
                    }else
                    {
                        return json_encode(array("responseCode"=>62,"responseMessage"=>"Invalid amount (".$variantArr['price'].") was specified for ".$db_variant[$variantArr['id']]['name']));
                    }
                }
                elseif($checkVariant['responseCode'] == 1) // product has no variant
                {
                    $price     = $this->getitemlabel("products","id",$row['id'],"discount_price");
                    if($price != $row['price'])
                    {
                        return json_encode(array("responseCode"=>62,"responseMessage"=>"Invalid amount (".$row['price'].") was specified for ".$row['label']));
                    }else
                    {
                        $amount = $amount + $price;
                    }
                }
                else
                {
                    return json_encode($checkVariant);
                }
            }else
            {
                return json_encode(array("responseCode"=>64,"responseMessage"=>$row['label']." does not exist for this merchant. "));
            }
        }
        return $amount;
    }
    public function checkProductID($id)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql         = "SELECT id FROM products WHERE id = '$id' AND merchant_id = '$merchant_id' LIMIT 1";
        $count       = $this->db_query($sql,false);
        return $count;
    }
    
    public function checkProductStock($id)
    {
        $status = $this->getitemlabel('products','id',$id,'stock_status');
        if($status == "In Stock")
        {
            return json_encode(array("responseCode"=>0,"responseMessage"=>"In Stock"));
        }else
        {
            return json_encode(array("responseCode"=>26,"responseMessage"=>"Out of Stock"));
        }
    }
    public function checkProductVariantStock($id)
    {
        $status = $this->getitemlabel('product_variant','id',$id,'stock_status');
        if($status == "In Stock")
        {
            return json_encode(array("responseCode"=>0,"responseMessage"=>"In Stock"));
        }else
        {
            return json_encode(array("responseCode"=>26,"responseMessage"=>"Out of Stock"));
        }
    }
    public function shouldIncludeVariant($id,$variantArr)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql         = "SELECT product_id,id,name,price FROM product_variant WHERE product_id = '$id' AND merchant_id = '$merchant_id'";
        $result      = $this->db_query($sql);
        if(count($result) > 0 ) // this product has a variant
        {
            $variant     = array();
            $variant_ids = array();
            foreach($result as $row)
            {
                $variant_ids = $row['id'];
                $variant[$row['id']] = array("productID"=>$row['product_id'], "variantID"=>$row['id'], "name"=>$row['name'], "price"=>$row['price']);
            }

            if(!isset($variantArr['id'])) // this product has a variant but you didn't select a variant
            {
                return json_encode(array("responseCode"=>48,"responseMessage"=>"This product should have variant included","responseBody"=>null));
            }
            else
            {
                if(in_array($variantArr['id'], $variant_ids)) // the user selected a correct/variant id that exist
                {
                    return json_encode(array("responseCode"=>0,"responseMessage"=>"OK","responseBody"=>$variant));
                }
                else
                {
                    return json_encode(array("responseCode"=>78,"responseMessage"=>"The selected product variant niether exist under this product nor merchant.","responseBody"=>null));
                }
            }
        }
        else
        {
            return json_encode(array("responseCode"=>1,"responseMessage"=>"This product does not require a variant","responseBody"=>null));
        }
    }
    public function getOrderSummary()
    {
        
    }
    public function getShippingFee($cart_item,$shipping_destination)
    {
        $shipping_cost = 0;
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql         = "SELECT shipping_rule_type_id,id,lga_list FROM shipping_regions WHERE merchant_id = '$merchant_id' AND FIND_IN_SET('$shipping_destination', lga_list) <> 0  ";
        $result      = $this->db_query($sql);
        $id = $result[0]['id'];
        $rule_id = $result[0]['shipping_rule_type_id'];
        // check if it's free or flat shipping
        if($rule_id == 1 || $rule_id == 4) // || $rule_id == 5
        {
            $sql    = "SELECT shipping_fee FROM shipping_fee_prices WHERE shipping_region_id = '$id' AND merchant_id = '$merchant_id' LIMIT 1 ";
            $result = $this->db_query($sql);
            $db_fee = $result[0]['shipping_fee'];
            foreach($cart_item as $ee)
            {
                $shipping_cost = $shipping_cost + $db_fee;
            }
        }
        else
        {
            if($rule_id == 3)
            {
                foreach($cart_item as $ee)
                {
                    $prod_price     = $ee['price'];
                    $sql            = "SELECT shipping_fee FROM shipping_fee_prices WHERE $prod_price BETWEEN maximum_value AND minimum_value AND shipping_region_id = '$id' AND merchant_id = '$merchant_id' ";
                    $result         = $this->db_query($sql);
                    foreach($result as $row)
                    {
                        $shipping_cost  = $shipping_cost + $row['shipping_fee'];
                    }
                    
                } 
            }
            if($rule_id == 3)
            {
                foreach($cart_item as $ee)
                {
                    $prod_weight     = $ee['weight'];
                    $sql            = "SELECT shipping_fee FROM shipping_fee_prices WHERE $prod_weight BETWEEN maximum_value AND minimum_value AND shipping_region_id = '$id' AND merchant_id = '$merchant_id' ";
                    $result         = $this->db_query($sql);
                    foreach($result as $row)
                    {
                        $shipping_cost  = $shipping_cost + $row['shipping_fee'];
                    }
                    
                } 
            }
        }
        return $shipping_cost;
    }
    public function getProductWeight($p_id)
    {
        
    }
    public function getProductPrice($p_id)
    {
        
    }
    public function generateOrderID($customerid)
    {
        $orderObj = new Orders();
        return $orderObj->generateOrderID($customerid);
    }
    public function setCustomerOrder($data)
    {
        $sql = "INSERT INTO orderdetails () VALUES()";
    }
    
}