<?php
// ini_set('max_execution_time', 240);
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
//$sql = "SELECT DISTINCT(State) as state,state_code FROM lga order by State asc ";
//$states = $dbobject->db_query($sql);
//
//$sql2 = "SELECT bank_code,bank_name FROM banks WHERE bank_type = 'commercial' order by bank_name";
//$banks = $dbobject->db_query($sql2);
//
//$sql_pastor = "SELECT username,firstname,lastname FROM userdata WHERE role_id = '003'";
//$pastors = $dbobject->db_query($sql_pastor);
//$sql = "SELECT * FROM company WHERE status = '1' ORDER BY company_name";
//$company = $dbobject->db_query($sql);
//$sql = "SELECT * FROM job_category WHERE status = '1'";
//$category = $dbobject->db_query($sql);

if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $id = $_REQUEST['id'];
    $sql_category = "SELECT * FROM social_media WHERE id = '$id' LIMIT 1";
    $cat = $dbobject->db_query($sql_category);
}else
{
    $operation = 'new';
}
?>
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<link rel="stylesheet" href="icons/style.css">

<script>
    doOnLoad();
    var myCalendar;
function doOnLoad()
{
   myCalendar = new dhtmlXCalendarObject(["start_date"]);
    myCalendar.setSensitiveRange( "<?php echo date('Y-m-d') ?>",null);
   myCalendar.hideTime();
}
</script>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Social Media Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="SocialMedia.saveSocialMedia">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Media Name</label>
                     <input type="text" autocomplete="off" name="name"  value="<?php echo $cat[0]['name']; ?>"  class="form-control" /> 
                    
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Media Icon</label>
                    <input type="text" name="icon" class="form-control" value="<?php echo $cat[0]['icon']; ?>" placeholder="">
                </div>
           </div>
           
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Media Link</label>
                   <input type="text" name="link" class="form-control" value="<?php echo $cat[0]['link']; ?>" placeholder="">
                </div>
           </div>
           
           
           
       </div>
       
       
      
        
        
       
       <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
        
    </form>
</div>
<script>
    function display_icon(ee)
    {
        $("#icon-display").html(`<i class="${ee}"></i>`);
    }
    function saveRecord()
    {
        var dd = $("#form1").serialize();
                    $.post('utilities.php',dd,function(re){
                        console.log(data);
                        if (re.response_code == 0) {
                            $("#save_facility").text("Save");


                                $("#err").css('color','green')
                                $("#err").html(re.response_message)
                                setTimeout(() => {
                                    $('#defaultModalPrimary').modal('hide');
                                }, 1000)
                                getpage('social_media_list.php','page');




                        } else {
                            $("#save_facility").text("Save");
                            
                            $("#err").css('color','red')
                                $("#err").html(re.response_message)
                                $("#warning").val("0");
                        }
                    },'json')
    }
    function saveRecordss()
    {
         
        $("#save_facility").text("Loading......");
        var mop = "<?php echo $operation ?>";
        if(mop == "new")
            {
               if(brandImg.selectedFiles == 0)
                {
                    alert("kindly select an image file .")
                }
                else{
                    brandImg.startUpload();
                } 
            }
        else{
            if(brandImg.selectedFiles == 0)
                {
                    var dd = $("#form1").serialize();
                    $.post('utilities.php',dd,function(re){
                        var is_quick = "<?php echo (isset($_REQUEST['is_quick']))?'1':'0'; ?>";
                        console.log(data);
                        if (re.response_code == 0) {
                            $("#save_facility").text("Save");


                                $("#err").css('color','green')
                                $("#err").html(re.response_message)
                                setTimeout(() => {
                                    $('#defaultModalPrimary').modal('hide');
                                }, 1000)
                                if(is_quick == "0")
                                {
                                    getpage('category_list.php','page');
                                }




                        } else {
                            $("#save_facility").text("Save");
                            brandImg.reset();
                            $('.ajax-file-upload-red').click();
                            $("#err").css('color','red')
                                $("#err").html(re.response_message)
                                $("#warning").val("0");
                        }
                    },'json')
                }
                else{
                    brandImg.startUpload();
                } 
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
<script src="js/jquery.uploadfile.min.js"></script>
<link rel="stylesheet" href="css/uploadfile.css">
<script>
    var brandImg = $("#extrauploads").uploadFile({
        url: "utilities.php",
        fileName: "upfile",
        showPreview: true,
        previewHeight: "100px",
        previewWidth: "100px",
        maxFileCount: 1,
        dragDropStr: "<span><b>Category Image</b></span>",
        multiple: false,
        allowedTypes: "jpg,png",
        maxFileSize: 1000000,
        autoSubmit: false,
        returnType: "json",
        onSubmit: function(files) {
            var c = confirm("Are you sure you want to set this image?");
            if (c) {
                $("#tab-1").block({
                    message: "Updating image. Kindly wait.."
                });
                return true;
            } else {
                return false;
            }

        },
        dynamicFormData: function() {
             var dd = $("#form1").serialize();
//            var data = {
//                op: $("#op").val(),
//                operation: $("#operation").val(),
//                id: $("#id").val(),
//                name: $("#name").val()
//            }
            return dd;
        },
        onSuccess: function(files, re, xhr, pd) {
            var is_quick = "<?php echo (isset($_REQUEST['is_quick']))?'1':'0'; ?>";
            console.log(data);
            if (re.response_code == 0) {
                $("#save_facility").text("Save");
//                $("#err").css('color', 'green')
//                $("#err").html(re.response_message)
//                setTimeout(() => {
//                    $('#defaultModalPrimary').modal('hide');
//                }, 1000)
//                getpage('brand_list.php', 'page');
                // server_product_id = data.data.product_id;
                // featureImg.startUpload();
                
                
                    
                    $("#err").css('color','green')
                    $("#err").html(re.response_message)
                    setTimeout(() => {
                        $('#defaultModalPrimary').modal('hide');
                    }, 1000)
                    if(is_quick == "0")
                    {
                        getpage('category_list.php','page');
                    }
                    
                
                     
                
            } else {
                $("#save_facility").text("Save");
                brandImg.reset();
                $('.ajax-file-upload-red').click();
                $("#err").css('color','red')
                    $("#err").html(re.response_message)
                    $("#warning").val("0");
            }

        }
    });
</script>
<style>
    .ajax-upload-dragdrop{
        width: 260px !important;
    }
</style>