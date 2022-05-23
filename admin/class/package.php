<?php

class Package extends dbobject
{
   public function package_list($data)
    {
		$table_name    = "package";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
			array( 'db' => 'items',  'dt' => 2, 'formatter' => function( $d,$row ) {
                $arr = json_decode($d,TRUE);
                
						return implode(', ',$arr) ;
					} ),
            array( 'db' => 'id', 'dt' => 3, 'formatter' => function( $d,$row ) {
						return '<button class="btn btn-warning" onclick="getModal(\'setup/package_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit</button>';
					} ),
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
    public function savePackage($data)
    {
        $validation = $this->validate($data,array('name'=>'required','items'=>'required'),array('name'=>'Name'));
        if(!$validation['error'])
        {
            $data['created'] = date('Y-m-d h:i:s');
            $data['items'] = json_encode(explode(',',$data['items']));
            if($data['operation'] == "new")
            {
                
                $count = $this->doInsert('package',$data,array('op','operation','id'));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Package Created Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Package Could not be Created'));
                }
            }else
            {
                $count = $this->doUpdate('package',$data,['op','operation'],array('id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Package Update Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Package Could not be Updated'));
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