<?php
include_once("response.php");
class SubCategory extends dbobject
{
    private $response   = "";
    public function __construct()
    {
        $this->response = new Response();
    }
   public function loadSubcatDropDown($data)
   {
       $cat_id = $data['cat_id'];
       $group_id = $data['is_group'];
       $sql    = "SELECT * FROM product_subcategory WHERE category_id = '$cat_id' AND is_parent = '$group_id' ";
       $result = $this->db_query($sql);
       $view = "";
       if(count($result) > 0)
       {
           foreach($result as $row)
           {
               $view = $view."<option value='".$row['id']."'>".$row['name']."</option>";
           }
           return json_encode(array('responseCode'=>0,'responseMessage'=>'Found','responseBody'=>$view));
       }
       else
       {
//           $message = ($group_id == "1")?"NO CATEGORY GROUP FOUND":"NO SUBCATEGORY FOUND";
           $message = "NO CATEGORY GROUP FOUND";
           return json_encode(array('responseCode'=>77,'responseMessage'=>'None Found','responseBody'=>"<option value=''>:: NO SUBCATEGORY FOUND ::</option>"));
       }
   }
    public function saveSubCategory($data)
    {
        $data['created'] = date("Y-m-d h:i:s");
        $data['merchant_id'] = $_SESSION['merchant_sess_id'];
        if($data['operation'] == "new")
        {
            
            $data["status"] = 1;
            $count   = $this->doInsert("product_subcategory",$data,array('operation','op','id'));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Subcategory Created Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'Subcategory Creation Failed'));
            }
        }else
        {
            $count   = $this->doUpdate("product_subcategory",$data,array('operation','op','id'),array('id'=>$data['id']));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Subcategory Updated Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'No update made'));
            }
        }
    }
    
    // public function deleteSubCategory($data)
    // {
    //     $menu_id = $data['menu_id'];
    //     $sql     = "DELETE FROM menu WHERE menu_id = '$menu_id'";
    //     $this->db_query($sql,false);
    //     $sql     = "DELETE FROM menu_group WHERE menu_id = '$menu_id'";
    //     $this->db_query($sql,false);
    //     return $this->response->publishResponse("0","Deleted successfully","");
    // }
    
    public function subCategoryList($data)
    {
        $table_name    = "product_subcategory";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'category_id',  'dt' => 1, 'formatter' => function($d, $row){
                $category = $this->getitemlabel("product_categories", "id", $d, "name");
                return "<span class='text-uppercase'>".$category."</span>";
            }),
			array( 'db' => 'name',  'dt' => 2 ),
            array( 'db' => 'description',  'dt' => 3 ),
            array( 'db' => 'status',   'dt' => 4, 'formatter'=>function($id, $row){
                if($row['status'] == 0){
                    return "<span style='cursor:pointer' class='badge badge-danger'>INACTIVE</span>";
                }else {
                    return "<span style='cursor:pointer' class='badge badge-success'>ACTIVE</span>";
                }
            }),
			array( 'db' => 'created', 'dt' => 5, 'formatter' => function( $d,$row ) {
						return $d;
					}
                ),
            array( 'db' => 'id',  'dt' => 6,'formatter' => function( $d,$row ) {
                $status_result = $row["status"] != "1"? 'ACTIVATE' : 'DEACTIVATE';
                $lock = $status_result != "DEACTIVATE" ? "disabled": "";

                return '<a class="btn btn-warning dropdown-toggle" data-toggle="dropdown" href="#">
                            Action
                            <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item '.$lock.'" onclick="getModal(\'setup/subcategory_setup.php?op=edit&subcat_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">EDIT</a>
                            <a class="dropdown-item " onclick="changeSubCategoryStatus('.$d.','.$row['status'].')" href="javascript:void(0)">'.$status_result.'</a>
                            <a class="dropdown-item '.$lock.'" onclick="deleteSubCategory('.$d.')" href="javascript:void(0)">DELETE</a>
                        </div>
                        ';
            
                // return '<a class="btn btn-warning" onclick="getModal(\'setup/subcategory_setup.php?op=edit&subcat_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Subcategory</a>';
            } )
		);
		$filter = "";
        $filter = " AND merchant_id='$_SESSION[merchant_sess_id]' AND is_parent = '0'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function categoryGroupList($data)
    {
        $table_name    = "product_subcategory";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'category_id',  'dt' => 1, 'formatter' => function($d, $row){
                $category = $this->getitemlabel("product_categories", "id", $d, "name");
                return "<span class='text-uppercase'>".$category."</span>";
            }),
			array( 'db' => 'name',  'dt' => 2 ),
            array( 'db' => 'description',  'dt' => 3 ),
            array( 'db' => 'status',   'dt' => 4, 'formatter'=>function($id, $row){
                if($row['status'] == 0){
                    return "<span style='cursor:pointer' class='badge badge-danger'>INACTIVE</span>";
                }else {
                    return "<span style='cursor:pointer' class='badge badge-success'>ACTIVE</span>";
                }
            }),
			array( 'db' => 'created', 'dt' => 5, 'formatter' => function( $d,$row ) {
						return $d;
					}
                ),
            array( 'db' => 'id',  'dt' => 6,'formatter' => function( $d,$row ) {
                $status_result = $row["status"] != "1"? 'ACTIVATE' : 'DEACTIVATE';
                $lock = $status_result != "DEACTIVATE" ? "disabled": "";

                return '<a class="btn btn-warning dropdown-toggle" data-toggle="dropdown" href="#">
                            Action
                            <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item '.$lock.'" onclick="getModal(\'setup/categorygroup_setup.php?op=edit&subcat_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">EDIT</a>
                            <a class="dropdown-item " onclick="changeSubCategoryStatus('.$d.','.$row['status'].')" href="javascript:void(0)">'.$status_result.'</a>
                            <a class="dropdown-item '.$lock.'" onclick="deleteSubCategory('.$d.')" href="javascript:void(0)">DELETE</a>
                        </div>
                        ';
            
                // return '<a class="btn btn-warning" onclick="getModal(\'setup/subcategory_setup.php?op=edit&subcat_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Subcategory</a>';
            } )
		);
		$filter = "";
        $filter = " AND merchant_id='$_SESSION[merchant_sess_id]' AND is_parent = '1'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    public function getSubCategory($data)
    {
        // var_dump($data);
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql    = "SELECT * FROM product_subcategory WHERE merchant_id='$merchant_id' AND status = 1 AND category_id = ".$data["category_id"];
        // echo $sql."\n";
        $result = $this->db_query($sql);
        $options = array();
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $options[] = array('id'=>$row['id'],'name'=>$row['name'],'merchant_id'=>$row['merchant_id']);
            }
            return json_encode(array('responseCode'=>0,'data'=>$options));
        }
        else
        {
            return json_encode(array('responseCode'=>77,'data'=>''));
        }
        
    }

    public function changeSubCategoryStatus($data){
        $subcategory_id = $data['subcategory_id'];
        $status   = ($data['current_status'] == 1)?"0":"1";
        $sql      = "UPDATE product_subcategory SET status = '$status' WHERE id = '$subcategory_id' LIMIT 1";
        $cc = $this->db_query($sql,false);
        if($cc)
        {
            return json_encode(array('response_code'=>0,'response_message'=>'Action on subcategory is now effective'));
        }else
        {
            return json_encode(array('response_code'=>432,'response_message'=>'Action failed'));
        }
        
    }

    public function deleteSubCategory($data){
        $subcategory_id = $data['subcategory_id'];
        $sql      = "DELETE FROM product_subcategory WHERE id = '$subcategory_id'";
        $cc = $this->db_query($sql,false);
        if($cc)
        {
            return json_encode(array('response_code'=>0,'response_message'=>'Action on subcategory is now effective'));
        }else
        {
            return json_encode(array('response_code'=>432,'response_message'=>'Action failed'));
        }
    }
  
}