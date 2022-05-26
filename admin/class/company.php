<?php

class Company extends dbobject
{
   public function company_list($data)
    {
		$table_name    = "company";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'company_name', 'dt' => 1 ),
            array( 'db' => 'status',  'dt' => 2 ,'formatter'=>function($d,$row){
                return ($d == "1")?"Active":"Not Active";
            }),
            array( 'db' => 'status',  'dt' => 3,'formatter' => function($d,$row)
                {
                $id = $row['id'];
                $mss = ($d == "0")?array("Activate Company","success"):array("Deactivate Company","danger");
                return '<a class="btn btn-warning" onclick="getModal(\'setup/company_setup.php?op=edit&id='.$id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Category</a>  | <a class="btn btn-'.$mss[1].'" onclick="changeCatStatus(\''.$d.'\',\''.$id.'\')"  href="javascript:void(0)" >'.$mss[0].'</a>';
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
    public function changeCatStatus($data)
    {
        $status_change = ($data['status'] == "1")?0:1;
        $id = $data['id'];
        $sql = "UPDATE job_category SET status = '$status_change' WHERE id = '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            return json_encode(array('response_code'=>0,'response_message'=>'Category status updated Successfully'));
        }else{
            return json_encode(array('response_code'=>44,'response_message'=>'Failed to  updated Category status'));
        }
       
    }
    public function saveCompany($data)
    {
        $validation = $this->validate($data,array('company_name'=>'required'),array('company_name'=>'company name'));
        if(!$validation['error'])
        {
            $data['created'] = date('Y-m-d h:i:s');
            
            if($data['operation'] == "new")
            {
                $count = $this->doInsert('company',$data,array('op','operation'));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'company created successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Company Could not be Created'));
                }
            }else
            {
                $count = $this->doUpdate('company',$data,array('op','operation'),array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Company Update Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Company Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function getNextRoleId()
    {
        $sql    = "select CONCAT('00',max(role_id) +1) as rolee FROM role";
        $result = $this->db_query($sql);
        return $result[0]['rolee'];
        
    }
}