<?php

class Region extends dbobject
{
   public function regionList($data)
    {
		$table_name    = "region";
		$primary_key   = "country_id";
		$columner = array(
			array( 'db' => 'country_id', 'dt' => 0 ),
			array( 'db' => 'country_id', 'dt' => 1 , 'formatter' => function( $d,$row ) {
                return $this->getitemlabel('country','id',$d,'name');
            }),
            array( 'db' => 'country_id', 'dt' => 2 , 'formatter' => function( $d,$row ) {
                return "<button class='btn btn-primary' onclick=\"getModal('setup/region_setup.php?id=$d&op=edit','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary' >Edit</button>";
            }),
			array( 'db' => 'created',     'dt' => 3, 'formatter' => function( $d,$row ) {
						return $d;
					}
				)
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function saveRegion($data)
    {
        $validation = $this->validate($data,array('country_id'=>'required'),array('country_id'=>'Country Name'));
        if(!$validation['error'])
        {
            $data['created'] = date('Y-m-d h:i:s');
            
            if($data['operation'] == "new")
            {
                $count = $this->doInsert('region',$data,array('op','operation','id'));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Region Created Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Region Could not be Created'));
                }
            }else
            {
                $count = $this->doUpdate('region',$data,['op','operation','id'],array('country_id'=>$data['id']));
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Region Update Successfully')); 
                }else
                {
                    return json_encode(array('response_code'=>291,'response_message'=>'Region Could not be Updated'));
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