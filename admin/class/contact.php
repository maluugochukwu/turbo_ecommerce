<?php

class Contact extends dbobject
{
   public function contactList($data)
    {
		$table_name    = "contact_info";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'type', 'dt' => 1 ),
			array( 'db' => 'value', 'dt' => 2 ),
			array( 'db' => 'id', 'dt' => 3,'formatter'=>function($d,$row){
                return '<a class="btn btn-primary" onclick="getModal(\'setup/contact_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Contact</a>';
            } ),
			array( 'db' => 'created',  'dt' => 4 )
			
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function subcategoryList($data)
    {
		$table_name    = "template_subcategory";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'cat_id', 'dt' => 1,'formatter'=>function($d,$row){
                $category = $this->getitemlabel("template_category","id",$d,"name");
                return $category;
            } ),
			array( 'db' => 'name', 'dt' => 2 ),
			array( 'db' => 'created',  'dt' => 3 )
			
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function saveContact($data)
    {
        $validation = $this->validate($data,["type"=>"required","value"=>"required"]);
        if(!$validation['error'])
        {
            $data['created'] = date("Y-m-d h:i:s");
            if($data['operation'] == "new")
            {
                    $count = $this->doInsert("contact_info",$data,['op','operation','id']);
                    if($count > 0)
                    {
                        return json_encode(array('response_code'=>0,'response_message'=>'Contact Created Successfully'));
                    }else
                    {
                        return json_encode(array('response_code'=>996,'response_message'=>'Contact could not be created'));
                    }
            }else
            {
                $count = $this->doUpdate("contact_info",$data,['op','operation','id'],["id"=>$data['id']]);
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Contact updated Successfully'));
                }else
                {
                    return json_encode(array('response_code'=>996,'response_message'=>'Contact could not be updated'));
                }
            }
        }else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }

    public function saveSubcategory($data)
    {
        $validation = $this->validate($data,["name"=>"required","cat_id"=>"required"],["cat_id"=>"category"]);
        if(!$validation['error'])
        {
            $data['created'] = date("Y-m-d h:i:s");
            if($data['operation'] == "new")
            {
                    $count = $this->doInsert("template_subcategory",$data,['op','operation','id']);
                    if($count > 0)
                    {
                        return json_encode(array('response_code'=>0,'response_message'=>'Subcategory Created Successfully'));
                    }else
                    {
                        return json_encode(array('response_code'=>996,'response_message'=>'Subcategory could not be created'));
                    }
            }else
            {
                $count = $this->doUpdate("template_subcategory",$data,['op','operation','id'],["id"=>$data['id']]);
                if($count > 0)
                {
                    return json_encode(array('response_code'=>0,'response_message'=>'Subcategory updated Successfully'));
                }else
                {
                    return json_encode(array('response_code'=>996,'response_message'=>'Subcategory could not be updated'));
                }
            }
        }else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    
}