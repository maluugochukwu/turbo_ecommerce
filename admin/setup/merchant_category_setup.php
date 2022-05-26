<?php
include_once("../libs/dbfunctions.php");
include_once("../class/menu.php");
$dbobject = new dbobject();
//$sql = "SELECT DISTINCT(State) as state,stateid FROM lga order by State";
//$states = $dbobject->db_query($sql);
//
//$sql2 = "SELECT bank_code,bank_name FROM banks WHERE bank_type = 'commercial' order by bank_name";
//$banks = $dbobject->db_query($sql2);
//
//$sql_pastor = "SELECT username,firstname,lastname FROM userdata WHERE role_id = '003'";
//$pastors = $dbobject->db_query($sql_pastor);
$sql = "SELECT * FROM font_awsome ";
$fonts = $dbobject->db_query($sql);



if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $cat_id = $_REQUEST['cat_id'];
    $sql_category = "SELECT * FROM job_industry WHERE id = '$cat_id' LIMIT 1";
    $cat = $dbobject->db_query($sql_category);
    
    $sql   = "SELECT * FROM job_industry WHERE parent_id = '#' AND id <> '$cat_id'";
$jb_qy = $dbobject->db_query($sql);
}else
{
    $sql   = "SELECT * FROM job_industry WHERE parent_id = '#'";
    $jb_qy = $dbobject->db_query($sql);
    $operation = 'new';
}
?>
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
<script>
    doOnLoad();
    var myCalendar;
function doOnLoad()
{
   myCalendar = new dhtmlXCalendarObject(["start_date"]);
    myCalendar.setSensitiveRange(null, "<?php echo date('Y-m-d') ?>");
   myCalendar.hideTime();
}
</script>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Merchant Category Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Merchant.saveMerchantCategory">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $cat_id; ?>">
       <div class="row">
           <div class="col-sm-12">
               <div class="form-group">
                    <label class="form-label">Category Name</label>
                    <input type="text" autocomplete="off" name="name" value="<?php echo $cat[0]['name']; ?>"  class="form-control" />
                </div>
           </div>
           
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group" >
                    <label for="" class="form-label">Set as parent/sub category</label>
                    <select name="parent_id" onchange="determine_group(this.value)"  id="" class="form-control">
                        <option value="#">:: Set as parent category ::</option>
                        <?php
                        foreach($jb_qy as $row)
                        {
                            $selected = ($row['id'] == $cat[0]['parent_id'])?"selected":"";
                            echo "<option $selected value='".$row['id']."'>".$row['name']."</option>";
                        }
                        ?>
                    </select>
                </div>
           </div>
           <div class="col-sm-6" id="cat_group_div" style="display:none">
               <div class="form-group" >
                    <label for="" class="form-label">Select a category group</label>
                    <select name="parent_id" id="cat_group" class="form-control">
                        
                    </select>
                </div>
           </div>
       </div>
       <div class="row" id="show_icon">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Select an icon</label>
                    <select name="icon" onchange="display_icon(this.value)" id="icon" class="form-control">
                       <option value="">::PLEASE SELECT AN ICON::</option>
                        <?php
                            foreach($fonts as $row)
                            {
                                $selected = ($cat[0]['icon'] == $row['code'])?"selected":"";
                                echo "<option $selected value='".$row['code']."'>".str_replace("fa fa-","",$row['code'])."</option>";
                            }
                        ?>
                    </select>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group" >
                    <label for="" class="form-label"></label>
                    <div id="icon-display" align="center" style="font-size:20px">
                        <?php echo "<i class='fa ".$cat[0]['icon']."'></i>"; ?>
                    </div>
                </div>
           </div>
       </div>
       <div class="row">
           
       </div>
       
        
        
       
       <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
        
    </form>
</div>
<script>
    function determine_group(ee)
    {
        if(ee != "#")
            {
                $("#show_icon").hide();
                $("#cat_group_div").show();
                $.post("utilities.php",{op:"Merchant.getMerchantSubCat",cat_id:ee},function(data){
                    console.log(data)
                    var jj = data.data;
                    var opts = "";
                    if(data.response_code == 0)
                        {
                            jj.forEach((item)=>{
                                opts = opts + "<option value='"+item.id+"'>"+item.name+"</option>"
                            })
                            $("#cat_group").html(opts)
                        }else{
                            $("#cat_group").html("<option>null</option>")
                        }
                    
                },'json')
            }else{
                $("#show_icon").show();
                $("#cat_group_div").hide();
            }
    }
    function saveRecord()
    {
        $("#save_facility").text("Loading......");
        var dd = $("#form1").serialize();
        $.post("utilities.php",dd,function(re)
        {
            $("#save_facility").text("Save");
            console.log(re);
            if(re.response_code == 0)
                {
                    
                    $("#err").css('color','green')
                    $("#err").html(re.response_message)
                    getpage('merchant_category_list.php','page');
                    
                }
            else
                {
                     $("#err").css('color','red')
                    $("#err").html(re.response_message)
                    $("#warning").val("0");
                }
                
        },'json')
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
    function fetchLga(el)
    {
        getRegions(el);
        $("#lga-fds").html("<option>Loading Lga</option>");
        $.post("utilities.php",{op:'Church.getLga',state:el},function(re){
            $("#lga-fds").empty();
            $("#lga-fds").html(re.state);
            
        },'json');
//        $.blockUI();
    }
    function getRegions(state_id)
    {
        $("#church_region_select").html("<option>Loading....</option>");
        $.post("utilities.php",{op:'Church.getRegions',state:state_id},function(re){
            $("#church_region_select").empty();
            $("#church_region_select").html(re);
            
        });
    }
    function display_icon(ee)
    {
        $("#icon-display").html(`<i class="${ee}"></i>`);
    }
    
    function fetchAccName(acc_no)
    {
        if(acc_no.length == 10)
            {
                var account  = acc_no;
                var bnk_code = $("#bank_name").val();
                $("#acc_name").text("Verifying account number....");
                $("#account_name").val("");
                $.post("utilities.php",{op:"Church.getAccountName",account_no:account,bank_code:bnk_code},function(res){
                    
                    $("#acc_name").text(res);
                    $("#account_name").val(res);
                });
            }else{
                $("#acc_name").text("Account Number must be 10 digits");
            }
        
    }
</script>