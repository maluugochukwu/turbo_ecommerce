<?php
class Template extends dbobject
{
    
    public function template_list($data)
    {
        $table_name    = "templates";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'title',  'dt' => 1 ),
			array( 'db' => 'category',  'dt' => 2,'formatter'=>function($d,$row){
                
                return $this->getitemlabel("template_category","id",$d,"name");
            } ),
			array( 'db' => 'subcategory',  'dt' => 3,'formatter'=>function($d,$row){
                
                return $this->getitemlabel("template_subcategory","id",$d,"name");
            } ),
            array( 'db' => 'id',  'dt' => 4,'formatter' => function( $d,$row )
                    {
                
						return '<a class="btn btn-info" onclick="getModal(\'config_placeholders.php?temp_id='.$d.'\',\'modal_div2\')"  href="javascript:void(0)" data-toggle="modal" data-target="#editing_product" >Configure Placeholders</a> | <a class="btn btn-warning" onclick="getModal(\'setup/template_setup.php?op=edit&id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Template</a> | <a class="btn btn-danger" onclick="deleteMenu(\''.$d.'\')"  href="javascript:void(0)" >Delete Template</a>';
					} ),
            array( 'db' => 'created', 'dt' => 5, 'formatter' => function( $d,$row )
                    {
						return $d;
					}
				)
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    public function getTemplateSubcategory($data)
    {
        $cat_id = $data['cat_id'];
        $sql    = "SELECT * FROM template_subcategory WHERE cat_id = '$cat_id' ";
        $subcat = $this->db_query($sql);
        $html   = "";
        if(count($html) > 0)
        {
            foreach ($subcat as $row) 
            {
                $html = $html."<option value='".$row['id']."'>".$row['name']."</option>";
            }
        }
        else
        {
            $html = "<option value=''>:: No subcategory available for this category ::</option>";
        }
        
        return $html;
    }
    public function saveTemplate($data)
    {
        $validation = $this->validate($data,array("title"=>"required","body"=>"required","category"=>"required","subcategory"=>"required","price"=>"required|int"));
        if(!$validation['error'])
        {
            $data['created'] = date("Y-m-d h:i:s");
            if($data['operation'] == "new")
            {
                $result = $this->doInsert("templates",$data,['op','operation','id']);
                if($result > 0)
                {
                    $insert_id    = $this->getInsert_id();
                    $placeholders = $this->foramtData($data['body'],$insert_id);
                    return json_encode(array('response_code'=>0,'response_message'=>'Template Created Successfully '.$placeholders));
                }
                else
                {
                    return json_encode(array('response_code'=>88,'response_message'=>'Template could not be created'));
                }
            }
            else
            {
                $result = $this->doUpdate("templates",$data,['op','operation','id'],['id'=>$data['id']]);
                if($result > 0)
                {
                    $placeholders = $this->foramtData($data['body'],$data['id'],"edit");
                    return json_encode(array('response_code'=>0,'response_message'=>'Template updated Successfully'));
                }
                else
                {
                    return json_encode(array('response_code'=>88,'response_message'=>'Template could not be updated'));
                }
            }
        }
        else
        {
            return json_encode(array("response_code"=>34,"response_message"=>$validation['messages'][0]));
        }
    }
    public function foramtData($string,$template_id,$operation = "new")
    {
        $string_in_arr = explode(" ",$string);
        $placeholders = [];

        if($operation == "edit")
        {
            $sql    = "DELETE  FROM template_placeholder WHERE template_id = '$template_id' ";
            $result = $this->db_query($sql,false);
        }
        foreach($string_in_arr as $value)
        {
            $first_braces = strpos($value,"{{");
            $second_braces = strpos($value,"}}");
            if((!$first_braces) && (!$second_braces))
            {
               
            }
            else
            {
                $label = $this->getPlaceholderLabel($value);
                $sql    = "INSERT INTO template_placeholder (marker,template_id,data_type,is_list,data_value,created,label) VALUES('$value','$template_id','TEXT','0','',NOW(),'$label')";
                $result = $this->db_query($sql,false);
                $placeholders[] = $value;
            }
        }
        return $placeholders;
    }
    public function getPlaceholderLabel($value)
    {
        $v = substr($value,2,-2);
        return $v;
    }
    
    public function configPlaceholder($data)
    {
        $loop = count($data['marker']);
        $template_id = $data['template_id'];
        for($x=0; $x<=$loop; $x++)
        {
            $marker      = $data['marker'][$x];
            $data_type   = $data['data_type'][$x];
            $list_values = $data['list_values'][$x];
            $data_value  = $data['data_value'][$x];
            $extra_info  = $data['extra_info'][$x];
            $is_list     = ($data['data_type'][$x] == "LIST")?1:0;
            $sql         = "UPDATE template_placeholder SET data_type ='$data_type', is_list = '$is_list', data_value='$data_value', list_values='$list_values', extra_info = '$extra_info' WHERE marker = '$marker' AND template_id = '$template_id' ";
            $result      = $this->db_query($sql,false);
        }
        return json_encode(array('response_code'=>0,'response_message'=>'Configuration updated Successfully'));
    }
    public function showPlaceholders($data)
    {
        $string_in_arr = explode(" ",$data['body']);
        foreach($string_in_arr as $value)
        {
            $first_braces = strpos($value,"{{");
            $second_braces = strpos($value,"}}");
            if((!$first_braces) && (!$second_braces) )
            {
                
            }
            else
            {
                $placeholders[] = $value;
                $output = "";
                $counter = 1;
                foreach($placeholders as $value)
                {
                    $output = $output.$counter.". ".$value."\n";
                    $counter++;
                }
            }
        }
        return $output;
    }
    
}