<?php
include_once("../libs/dbfunctions.php");
include_once("../class/menu.php");
include_once('../class/category.php');
include_once('../class/sub_category.php');
$dbobject = new dbobject();
$categoryObj = new Category();
$subCategoryObj = new SubCategory();
//$sql = "SELECT DISTINCT(State) as state,stateid FROM lga order by State";
//$states = $dbobject->db_query($sql);
//
//$sql2 = "SELECT bank_code,bank_name FROM banks WHERE bank_type = 'commercial' order by bank_name";
//$banks = $dbobject->db_query($sql2);
//
//$sql_pastor = "SELECT username,firstname,lastname FROM userdata WHERE role_id = '003'";
//$pastors = $dbobject->db_query($sql_pastor);

$category = json_decode($categoryObj->getCategory(array()), TRUE);
// var_dump($category["data"]);
$sql = "SELECT * FROM product_subcategory WHERE is_parent = '1'";
$sub_cat_result = $dbobject->db_query($sql);

if (isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit') {
    $operation = 'edit';
    $subcat_id = $_REQUEST['subcat_id'];
    $sql_category = "SELECT * FROM product_subcategory WHERE id = '$subcat_id' LIMIT 1";
    $subcat = $dbobject->db_query($sql_category);
    // var_dump($subcat[]);
} else {
    $operation = 'new';
}
?>
<style>
    .show_div
    {
        display: block;
    }
    .hide_div
    {
        display: none;
    }
</style>
<link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<script>
    doOnLoad();
    var myCalendar;

    function doOnLoad() {
        myCalendar = new dhtmlXCalendarObject(["start_date"]);
        myCalendar.setSensitiveRange(null, "<?php echo date('Y-m-d') ?>");
        myCalendar.hideTime();
    }
</script>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Category Group Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
        <input type="hidden" name="op" value="SubCategory.saveSubCategory">
        <input type="hidden" name="operation" value="<?php echo $operation; ?>">
        <input type="hidden" name="id" value="<?php echo $subcat_id; ?>">
        <input type="hidden" name="is_parent" value="1">
        <div class="row">
           <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Group Name</label>
                    <input type="text" autocomplete="off" name="name" onkeyup="validateCode(this.value)" value="<?php echo $subcat[0]['name']; ?>" class="form-control" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Parent Category</label>
                    <select name="category_id"  id="product_subcat" class="form-control text-uppercase">
                        <option value="">::SELECT CATEGORY::</option>
                        <?php
                        foreach ($category['data'] as $row) {
                            $category_name = $row['name'];
                            $category_id = $row['id'];
                            $selected = ($subcat[0]['category_id'] == $category_id) ? "selected" : "";
                            echo "<option $selected value='$category_id'>$category_name</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            
        </div>
        <div class="row">
<!--
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Set as a group:</label>
                    <select name="is_parent" onchange="displayParentMode(this.value)" id="parent" class="form-control">
                        <option value="0" <?php //echo (0 == $subcat[0]['is_parent'])?"selected":"" ?>>NO</option>
                        <option value="1" <?php //echo (1 == $subcat[0]['is_parent'])?"selected":"" ?> >YES</option>
                    </select>
                </div>
            </div>
-->
<!--
            <div class="col-sm-6 <?php //echo ($subcat[0]['is_parent'] == 0)?"show_div":"hide_div"; ?>" id="parent_div"  >
                <div class="form-group">
                    <label class="form-label">Place under a group</label>
                    <select name="parent_id" id="parent_id" class="form-control">
                        
                        <?php
//                        if($operation == "edit")
//                        {
//                            echo "<option value='".$subcat[0]['parent_id']."'>".$dbobject->getitemlabel("product_subcategory","id",$subcat[0]['parent_id'],"name")."</option>";
//                        }else
//                        {
//                            echo "<option value=''>:: SELECT A CATEGORY FIRST ::</option>";
//                        }
//                        
                        ?>
                    </select>
                </div>
            </div>
-->
        </div>




        <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>

    </form>
</div>
<script>
    function saveRecord() {
        var is_quick = "<?php echo (isset($_REQUEST['is_quick']))?'1':'0'; ?>";
        var is_quick_val = "<?php echo (isset($_REQUEST['is_quick']))?$_REQUEST['is_quick']:''; ?>";
        $("#save_facility").text("Loading......");
        var dd = $("#form1").serialize();
        $.post("utilities.php", dd, function(re) {
            $("#save_facility").text("Save");
            console.log(re);
            if (re.response_code == 0) {

                $("#err").css('color', 'green')
                $("#err").html(re.response_message)
                setTimeout(() => {
                    $('#defaultModalPrimary').modal('hide');
                }, 1000)
                if(is_quick == "0")
                    {
                         getpage('subgroup_list.php', 'page');
                    }else
                    {
                        popSubcat($("#product_cat").val())
                    }
               

            } else {
                $("#err").css('color', 'red')
                $("#err").html(re.response_message)
                $("#warning").val("0");
            }

        }, 'json')
    }
    function displayParentMode(val)
    {
        if(val == 0)
            {
                $("#parent_div").show();
            }
        else{
            $("#parent_div").hide();
        }
        
    }

    //    function automatic()
    //    {
    //        if($("#auto").is(':checked'))
    //        {
    //            $("#auto_val").val(1)
    //        }else{
    //             $("#auto_val").val(0)
    //        }
    //    }
    //    
    function fetchLga(el) {
        getRegions(el);
        $("#lga-fds").html("<option>Loading Lga</option>");
        $.post("utilities.php", {
            op: 'Church.getLga',
            state: el
        }, function(re) {
            $("#lga-fds").empty();
            $("#lga-fds").html(re.state);

        }, 'json');
        //        $.blockUI();
    }

    function getRegions(state_id) {
        $("#church_region_select").html("<option>Loading....</option>");
        $.post("utilities.php", {
            op: 'Church.getRegions',
            state: state_id
        }, function(re) {
            $("#church_region_select").empty();
            $("#church_region_select").html(re);

        });
    }

    function fetchAccName(acc_no) {
        if (acc_no.length == 10) {
            var account = acc_no;
            var bnk_code = $("#bank_name").val();
            $("#acc_name").text("Verifying account number....");
            $("#account_name").val("");
            $.post("utilities.php", {
                op: "Church.getAccountName",
                account_no: account,
                bank_code: bnk_code
            }, function(res) {

                $("#acc_name").text(res);
                $("#account_name").val(res);
            });
        } else {
            $("#acc_name").text("Account Number must be 10 digits");
        }

    }
    function display_cat_group(el)
    {
        $("#parent_div").block();
        $.post('utilities.php',{op:"SubCategory.loadSubcatDropDown",cat_id:el,is_group:"1"},function(data){
           $("#parent_div").unblock();
            $("#parent_id").html(data.responseBody)
            
        },'json')
    }
</script>