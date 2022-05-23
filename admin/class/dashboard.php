<?php


class Dashboard extends dbobject
{
    public $merchant_id = "";
    public function __construct()
    {
        $this->merchant_id = $_SESSION['merchant_sess_id'];
    }
    public function topFiveMerchant($data)
    {
       $sql                 = "SELECT COUNT(destination_acct) AS merchant_count,destination_acct FROM transaction_table WHERE response_code = '0'  GROUP BY destination_acct LIMIT 5";
       $result              = $this->db_query($sql);
       $church_arr          = array();
       $church_contribution = array();
       $color_list          = array('#47bac1','#fcc100','#f44455','#E8EAED','pink');
       $colors              = array();
       $top_five            = array();
       $total_sum           = $this->findSum($result,'merchant_count');
       $count               = 0;
       foreach($result as $row)
       {
           $merchant_name               = $this->getitemlabel("merchant_reg","merchant_id",$row['destination_acct'],"merchant_name");
           $merchant_name               = (strlen($merchant_name) > 11)?substr($merchant_name,0,11)."...":$merchant_name;
           $colors[]                    = $color_list[$count];
           $church_arr[]                = $merchant_name;
           $church_contribution[]       = round(($row['merchant_count']/$total_sum) * 360,2);
           $count++;
           $top_five['merchant_name'][] = $merchant_name;
           $top_five['church_amount'][] = $row['merchant_count'];
       }
       $data   = array('type'=>'pie','data'=>array('labels'=>$church_arr,
                     'datasets'=>array(
                                    array('data'=>$church_contribution,
                                          'backgroundColor'=>$colors,
                                          'borderColor'=>'transparent'
                                         )
                                )
                            )
                    );
       $html = "";
       for($x=0; $x<=count($top_five['merchant_name']); $x++)
       {
           $html = $html."<tr>";
           $html = $html."<td>".$top_five['merchant_name'][$x]."</td>";
           $html = $html."<td>".$top_five['church_amount'][$x]."</td>";
           $html = $html."</tr>";
       }
       
       return json_encode(array('pie'=>$data,'topfive'=>$html));

    }
    
    public function topFiveProducts($data)
    {
        $filter = ($_SESSION['role_id_sess'] == "001")?"":" AND merchant_id = '$this->merchant_id'";
        $sql    = "SELECT COUNT(product_id) AS product_count,product_id FROM orderdetails WHERE order_status = '1'  GROUP BY product_id WHERE 1 =1  $filter LIMIT 5";
           $result = $this->db_query($sql);
           $church_arr = array();
           $church_contribution = array();
           $color_list = array('#47bac1','#fcc100','#f44455','#E8EAED','pink');
           $colors = array();
           $top_five = array();
            $total_sum = $this->findSum($result,'product_count');
           $count = 0;
           foreach($result as $row)
           {
               $product_name = $this->getitemlabel("products","id",$row['product_id'],"name");
               $product_name = (strlen($product_name) > 11)?substr($product_name,0,11)."...":$product_name;
               $colors[] = $color_list[$count];
               $product_arr[] = $product_name;
               $product_contribution[] = round(($row['product_count']/$total_sum) * 360,2);
               $count++;
               $top_five['product_name'][]   = $product_name;
               $top_five['product_count'][] = $row['product_count'];
           }
           $data   = array('type'=>'pie','data'=>array('labels'=>$product_arr,
                         'datasets'=>array(
                                        array('data'=>$product_contribution,
                                              'backgroundColor'=>$colors,
                                              'borderColor'=>'transparent'
                                             )
                                    )
                                )
                        );
           $html = "";
           for($x=0; $x<=count($top_five['product_name']); $x++)
           {
               $html = $html."<tr>";
               $html = $html."<td>".$top_five['product_name'][$x]."</td>";
                $html = $html."<td>".$top_five['product_count'][$x]."</td>";
               $html = $html."</tr>";
           }

           return json_encode(array('pie'=>$data,'topfive'=>$html));
    }
    public function findSum($result,$field)
    {
        $amt = 0;
        foreach($result as $row)
        {
            $amt = $amt + $row[$field];
        }
        return $amt;
    }
    public function carousel($data)
    {
        $owl = array();
        if($_SESSION['role_id_sess'] == "001" || $_SESSION['role_id_sess'] == "005")
        {
            $filter = "";
        }
        else
        {
            $filter = " AND source_acct = '$_SESSSION[church_id_sess]'";
        }
        $sql = "SELECT SUM(transaction_amount) AS amount,source_acct FROM transaction_table WHERE 1 = 1 $filter GROUP BY source_acct ";
        $result  = $this->db_query($sql);
//        $sql2 = "SELECT SUM(transaction_amount) AS amount,church_id FROM transaction_table GROUP BY church_id ";
        foreach($result as $row)
        {
            $owl[] = array("item"=>'<div class="col-12 col-sm-6 col-xl d-flex">
							<div class="card flex-fill mb-0">
								<div class="card-body py-4">
									<div class="media">
										<div class="d-inline-block mt-2 mr-1">
											<i class="fa fa-church text-success" style="font-size:35px" ></i>
										</div>
										<div class="media-body">
											<h6 class="mb-2">'.$this->getitemlabel('church_table','church_id',$row[source_acct],'church_name').'</h6>
                                            <div class="row mb-0"">
                                                <div class="col-sm-12">
                                                    <b style="color:red">Posted:</b> &#8358;'.number_format($row[amount],2).'
                                                </div>
                                            </div>
										</div>
									</div>
								</div>
							</div>
						</div>');
        }
        
        $res = array("owl"=>$owl);
        return json_encode($res);
    }
    
    public function transactionHistoryPreviousNow($data)
    {
        
        

        
        
        $filter = ($_SESSION['role_id_sess'] == "001" || $_SESSION['role_id_sess'] == "002")?"":" AND branch_id = '$_SESSION[branch_id]'";
        $sql = "SELECT SUM(transaction_amount) AS amount, MONTH(created) AS trans_month,YEAR(created) AS trans_year  FROM transaction_table WHERE 1 = 1 $filter AND YEAR(created) = YEAR(CURDATE())  GROUP BY MONTH(created),YEAR(created) ORDER BY trans_year ";
        $result = $this->db_query($sql);
        $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
        $data   = array();
        $set = array();
        $data_current_months = array();
        $data_last_year_months = array();
        $current_year_amt = array();
        $last_year_amt = array();
        foreach($months as $k=>$v)
        {
            $amt    = 0;
            if(count($result) > 0)
            {
                foreach($result as $row)
                {
                    $index  = $row['trans_month'] - 1;
                    if($k == $index)
                        $amt = $row['amount'];
                }
            }
             $current_year_amt[] = $amt;
        }
        
        $sql = "SELECT SUM(transaction_amount) AS amount, MONTH(created) AS trans_month,YEAR(created) AS trans_year  FROM transaction_table WHERE 1 = 1 $filter AND YEAR(created) = YEAR(CURDATE()) - 1  GROUP BY MONTH(created),YEAR(created) ORDER BY trans_year ";
        $result = $this->db_query($sql);
        foreach($months as $k=>$v)
        {
            $amt    = 0;
            if(count($result) > 0)
            {
                foreach($result as $row)
                {
                    $index  = $row['trans_month'] - 1;
                    if($k == $index)
                        $amt = $row['amount'];
                }
            }
            
             $last_year_amt[] = $amt;
        }
        $data = array('type'=>'bar',
                        'data'=>array(
                        'labels'=>$months,
                        'datasets'=>array(
                            array("label"=>"Last year","backgroundColor"=>"green","borderColor"=>"red","hoverBackgroundColor"=>"orange","hoverBorderColor"=>"blue","data"=>$last_year_amt),
                            array("label"=>"This year","backgroundColor"=>"#E8EAED","borderColor"=>"#E8EAED","hoverBackgroundColor"=>"#E8EAED","hoverBorderColor"=>"#E8EAED","data"=>$current_year_amt)
                        ), 'options'=>array("maintainAspectRatio"=>false,"legend"=>array("display"=>false),"scales"=>array("yAxes"=>array(array("gridLines"=>array("display"=>false),"stacked"=>false,"ticks"=>array("stepSize"=>20) )),"xAxes"=>array("barPercentage"=>.75,"categoryPercentage"=>.5,"stacked"=>false,"gridLines"=>array("color"=>"transparent")
                        )))
            ));
        
        return json_encode($data);
    }
    public function stateHeatMap($data)
    {
//        $filter = ($_SESSION['role_id_sess'] == "001" || $_SESSION['role_id_sess'] == "002")?"":" AND branch_id = '$_SESSION[branch_id]'";
        $filter = "";
        $output = array();
        
        
            $ss = "SELECT DISTINCT(State) as unique_state,highchart_code,state_code FROM lga ";
            $ss_result = $this->db_query($ss);
        if(count($ss_result) > 0)
        {
            foreach($ss_result as $rr)
            {
                $state_code = $rr['state_code'];
                $sql = "SELECT COUNT(state_id) AS heat_count,state_id FROM transaction_table WHERE  response_code = '0' AND state_id = '$state_code' $filter  ";
                $result = $this->db_query($sql);
                $heat_c  = (int)$result[0]['heat_count'];
                $output[] = array($rr['highchart_code'],$heat_c);
                
            }
            return json_encode(array('response_code'=>0,'data'=>$output));
        }else
        {
            return json_encode(array('response_code'=>42,'data'=>$output));
        }
    }
    public function transactionCountSales($data)
    {
        
//        $filter = ($_SESSION['role_id_sess'] == 001)?"":" AND church_id = '$_SESSION[church_id_sess]'";
        $filter = ($_SESSION['role_id_sess'] == "001" || $_SESSION['role_id_sess'] == "002")?"":" AND branch_id = '$_SESSION[branch_id]'";
        
        $sql    = "SELECT SUM(transaction_amount) AS amount, COUNT(transaction_id) AS trans_count, MONTH(created) AS trans_month  FROM transaction_table WHERE 1 = 1  $filter AND YEAR(created) = YEAR(CURDATE())  GROUP BY MONTH(created)";
        $result = $this->db_query($sql);
        $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
        $data   = array();
        $data_months = array();
        $data_amount = array();
        $data_count = array();
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $index = $row['trans_month'] - 1;
                $data_months[] = $months[$index];

                    $data_amount[] = $row['amount'];
                    $data_count[] = $row['trans_count'];


            }
        }
        $data = array('type'=>'line','data'=>array(
                                        'labels'=>$data_months,
                                        'datasets'=>array(
                                            array('label'=>'Revenue (NGN)',
                                                  'fill'=>true,
                                                  'backgroundColor'=>'transparent',
                                                  'borderColor'=>'#47bac1',
                                                  'data'=>$data_amount
                                                 ),
                                            array('label'=>'Sales Count',
                                                  'fill'=>true,
                                                  'backgroundColor'=>'transparent',
                                                  'borderColor'=>'#5fc27e',
                                                  'data'=>$data_count
                                                 )
                                            )
                                        ), 'options'=>array('maintainAspectRatio'=>false,'legend'=>array('display'=>false),'tooltips'=>array('intersect'=>false),'hover'=>array('intersect'=>true),'plugins'=>array('filler'=>array('propagate'=>false)),'scales'=>array('xAxes'=>array(array('reverse'=>true,'gridLines'=>array('color'=>'rgba(0,0,0,0.05)'))),'yAxes'=>array('ticks'=>array('stepSize'=>500),'display'=>true,'borderDash'=>array(5,5),'gridLines'=>array('color'=>'rgba(0,0,0,0)','fontColor'=>'#fff'))))
                     );
        return json_encode($data);
    }
}