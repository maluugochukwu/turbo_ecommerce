<?php

class Branch extends dbobject
{
   public function branchList($data)
    {
		$table_name    = "branch";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
			array( 'db' => 'address',  'dt' => 2 ),
			array( 'db' => 'region',  'dt' => 3, 'formatter' => function( $d,$row ) {
                return $this->getitemlabel('country','id',$d,'name');
            }  ),
            array( 'db' => 'state_id', 'dt' => 4, 'formatter' => function( $d,$row ) {
						return $this->getitemlabel('states','id',$d,'name');
					} ),
            array( 'db' => 'id', 'dt' => 5, 'formatter' => function( $d,$row ) {
						return '<button class="btn btn-warning"  onclick="getModal(\'setup/branch_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit</button>';
					} ),
			array( 'db' => 'created',     'dt' => 6, 'formatter' => function( $d,$row ) {
						return $d;
					}
				)
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function saveBranch($data)
    {
        $validation = $this->validate($data,array('name'=>'required','address'=>'required','state_id'=>'required'),array('name'=>'Name'));
        if(!$validation['error'])
        {
            $data['created'] = date('Y-m-d h:i:s');
            
            if($data['operation'] == "new")
            {
                $count = $this->doInsert('branch',$data,array('op','operation','id'));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Branch Created Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Branch Could not be Created'));
                }
            }else
            {
                $count = $this->doUpdate('branch',$data,['op','operation'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Branch Update Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Branch Could not be Updated'));
                }
            }
            
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function getLocation($data)
    {
        $region = $data['region'];
        $sql = "SELECT * FROM states WHERE country_id = '$region'";
        $result = $this->db_query($sql);
        $option = "";
        foreach($result as $row)
        {
            $option = $option."<option value='".$row['id']."'>".$row['name']."</option>";
        }
        return $option;
    }
    public function getNextRoleId()
    {
        $sql    = "select CONCAT('00',max(role_id) +1) as rolee FROM role";
        $result = $this->db_query($sql);
        return $result[0]['rolee'];
        
    }
}