<?php

class Customer extends dbobject
{
   public function customerList($data)
    {
		$table_name    = "customers";
		$primary_key   = "email";
		$columner = array(
			array( 'db' => 'email', 'dt' => 0 ),
			array( 'db' => 'first_name', 'dt' => 1 ),
			array( 'db' => 'last_name',  'dt' => 2 ),
			array( 'db' => 'address',     'dt' => 3),
			array( 'db' => 'phone',     'dt' => 4),
			array( 'db' => 'email',     'dt' => 5),
			array( 'db' => 'bank_name',     'dt' => 6,'formatter'=>function($d,$row){
                return $this->getitemlabel('banks','bank_code',$d,'bank_name');
            }),
			array( 'db' => 'account_no',     'dt' => 7),
			array( 'db' => 'account_name',     'dt' => 8),
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    
}