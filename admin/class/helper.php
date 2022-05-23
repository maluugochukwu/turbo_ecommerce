<?php

class Helper extends dbobject
{
    public function getLga($data)
    {
        $state  = $data['state'];
        
        $sql    = "SELECT Lga,Lgaid FROM lga WHERE state_code = '$state' order by Lga";
        $result = $this->db_query($sql);
        $output   = "";
        foreach($result as $row)
        {
            $output.= "<option value='".$row['Lgaid']."'>".$row['Lga']."</option>";
        }
      
        
        return json_encode(array('state'=>$output));
    }
    public function communityList($data)
    {
        $table_name    = "lga_communities";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1),
            array( 'db' => 'state_id', 'dt' => 2,'formatter'=>function($d,$row){
				return $this->getitemlabel('lga','state_code',$d,'State');
              }),
              array( 'db' => 'id', 'dt' => 3,'formatter'=>function($d,$row){
				return "<button class='btn btn-warning' onclick=\"getModal('setup/community_setup.php?id=$d&op=edit','modal_div')\"  href=\"javascript:void(0)\" data-toggle=\"modal\" data-target=\"#defaultModalPrimary\">Edit</button> | <button class='btn btn-danger' onclick=\"deleteCommunity('$d')\" >Delete</button>";
              }),
            array( 'db' => 'created',  'dt' => 4 )
			);
		$filter = "";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function deleteCommunity($data)
    {
        $id    = $data['id'];
        $sql   = "DELETE FROM lga_communities WHERE id = '$id' LIMIT 1";
        $count = $this->db_query($sql,false);
        return json_encode(array('responseCode'=>0,'responseMessage'=>'Deleted successfully'));
    }
    public function saveCommunity($data)
    {
        $data['created'] = date('Y-m-d h:i:s');
        if($data['operation'] == "new")
        {
            $count  = $this->doInsert('lga_communities',$data,array('op','operation','_files'));
            if($count > 0)
            {
                return json_encode(array('responseCode'=>0,'responseMessage'=>'Saved successfully'));
            }
            else
            {
                return json_encode(array('responseCode'=>75,'responseMessage'=>'Could not save'));
            }
        }else
        {
            $count  = $this->doUpdate('lga_communities',$data,array('op','operation','_files'),array('id'=>$data['id']));
            if($count > 0)
            {
                return json_encode(array('responseCode'=>0,'responseMessage'=>'Saved successfully'));
            }
            else
            {
                return json_encode(array('responseCode'=>75,'responseMessage'=>'Could not save'));
            }
        }
        
    }
    public function shippingRegionsgetLga($data)
    {
        $state  = $data['state'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        
        $sql = "SELECT lga_list FROM shipping_regions WHERE state = '$state' AND merchant_id = '$merchant_id'";
        $result = $this->db_query($sql);
        if(count($result) > 0)
        {
            $str = "";
            foreach($result as $row)
            {
                $str = $str.$row['lga_list'].",";
            }
            $str = substr($str,0,-1);
            $str_filter = "AND Lgaid NOT IN ($str)";
        }else
        {
            $str_filter = "";
        }
        
        $sql    = "SELECT Lga,Lgaid FROM lga WHERE state_code = '$state' $str_filter  order by Lga";
        $result = $this->db_query($sql);
        $output   = "";
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $output.= "<option value='".$row['Lgaid']."'>".$row['Lga']."</option>";
            }
        }else
        {
            $output = "<option value=''>NO LGA AVALAIBLE</option>";
        }
        
      
        
        return json_encode(array('state'=>$output));
    }
    public function getShippingCommunities($data)
    {
        $state  = $data['state'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        
        $sql = "SELECT lga_list FROM shipping_regions WHERE state = '$state' AND merchant_id = '$merchant_id'";
        $result = $this->db_query($sql);
        if(count($result) > 0)
        {
            $str = "";
            foreach($result as $row)
            {
                $str = $str.$row['lga_list'].",";
            }
            $str = substr($str,0,-1);
            $str_filter = "AND id NOT IN ($str)";
        }else
        {
            $str_filter = "";
        }
        
        $sql    = "SELECT name,id FROM lga_communities WHERE state_id = '$state' $str_filter  order by name";
        $result = $this->db_query($sql);
        $output   = "";
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $output.= "<option value='".$row['id']."'>".$row['name']."</option>";
            }
        }else
        {
            $output = "<option value=''>NO COMMUNITIES AVALAIBLE</option>";
        }
        
      
        
        return json_encode(array('state'=>$output));
    }
    public function shippingRegionsgetLga_v2($data)
    {
        $state  = $data['state'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        
        $sql = "SELECT lga AS lga_list FROM shipping_qty WHERE state = '$state' AND merchant_id = '$merchant_id'";
        $result = $this->db_query($sql);
        if(count($result) > 0)
        {
            $str = "";
            foreach($result as $row)
            {
                $str = $str.$row['lga_list'].",";
            }
            $str = substr($str,0,-1);
            $str_filter = "AND Lgaid NOT IN ($str)";
        }else
        {
            $str_filter = "";
        }
        
        $sql    = "SELECT Lga,Lgaid FROM lga WHERE state_code = '$state' $str_filter  order by Lga";
        file_put_contents("smart.txt",$sql);
        $result = $this->db_query($sql);
        $output   = "";
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $output.= "<option value='".$row['Lgaid']."'>".$row['Lga']."</option>";
            }
        }else
        {
            $output = "<option value=''>NO LGA AVALAIBLE</option>";
        }
        
      
        
        return json_encode(array('state'=>$output));
    }
}