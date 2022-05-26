<?php

class JobCategory extends dbobject
{
   public function category_list($data)
    {
		$table_name    = "job_category";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
            array( 'db' => 'status',  'dt' => 2 ,'formatter'=>function($d,$row){
                return ($d == "1")?"Active":"Not Active";
            }),
            array( 'db' => 'status',  'dt' => 3,'formatter' => function($d,$row)
                {
                $id = $row['id'];
                $mss = ($d == "0")?array("Activate Category","success"):array("Deactivate Category","danger");
                return '<a class="btn btn-warning" onclick="getModal(\'setup/job_category_setup.php?op=edit&id='.$id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Category</a>  | <a class="btn btn-'.$mss[1].'" onclick="changeCatStatus(\''.$d.'\',\''.$id.'\')"  href="javascript:void(0)" >'.$mss[0].'</a>';
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
    public function saveCategory($data)
    {
        $validation = $this->validate($data,array('name'=>'required'),array('name'=>'Job Category Name'));
        if(!$validation['error'])
        {
            $data['created'] = date('Y-m-d h:i:s');
            
            if($data['operation'] == "new")
            {
                $count = $this->doInsert('job_category',$data,array('op','operation'));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Category Created Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Category Could not be Created'));
                }
            }else
            {
                $count = $this->doUpdate('job_category',$data,array('op','operation'),array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Category Update Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Category Could not be Updated'));
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