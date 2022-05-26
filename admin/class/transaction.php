<?php

class Transaction extends dbobject
{
   public function transactionList($data)
    {
		$table_name    = "transaction_table";
		$primary_key   = "transaction_id";
		$columner = array(
			array( 'db' => 'transaction_id', 'dt' => 0 ),
			array( 'db' => 'transaction_id', 'dt' => 1),
            array( 'db' => 'transaction_amount', 'dt' => 2),
            array( 'db' => 'source_acct', 'dt' => 3),
            array( 'db' => 'destination_acct', 'dt' => 4,'formatter'=>function($d,$row)
              {
                  return $this->getitemlabel('merchant_reg','merchant_id',$d,'merchant_name');
              }),
            array( 'db' => 'payment_mode', 'dt' => 5),
            array( 'db' => 'response_code', 'dt' => 6),
            array( 'db' => 'response_message', 'dt' => 7),
            array( 'db' => 'customer_id',     'dt' => 8),
            array( 'db' => 'order_id', 'dt' => 9),
            array( 'db' => 'order_id', 'dt' => 10,'formatter'=>function($d,$row)
              {
                  $customer = $row['source_acct'];
                  $split_dist = "<button class='btn btn-secondary' onclick=\"getModal('transaction_details.php?order_id=$d&customer_id=$customer','modal_div2')\" href='javascript:void(0)' data-toggle='modal' data-target='#editing_product'>View Order Details</button>";
                  return $split_dist;
              }),
            array( 'db' => 'created',  'dt' => 11 )
			);
       $filter = "";
		$filter .= ($_SESSION['role_id_sess']=="001" || $_SESSION['role_id_sess']=="005" )?"":" AND destination_acct='$_SESSION[merchant_sess_id]'";
		
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
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