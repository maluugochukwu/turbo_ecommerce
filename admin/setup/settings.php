<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
$sql = "SELECT * FROM merchant_page_settings WHERE merchant_id = '$_SESSION[merchant_sess_id]'";
$settings = $dbobject->db_query($sql);

if(isset($_GET['operation']))
{
    $operation = "edit";
}
else
{
    $operation ="new";
}
?>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">General Settings</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
   <div class="server_message"></div>
    <form action="" id="settings_form" onsubmit="return false">
        <input type="hidden" name="op" value="web_report.saveGeneralSettings">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label for="">Primary Color</label>
                    <input type="color" value="<?php echo $settings[0]['primary_color']; ?>"  class="form-control" name="primary_color" />
                </div>
           </div>
           <div class="col-sm-6">
                <div class="form-group">
                     <label for="">Secondary Color</label>
                    <input type="color" value="<?php echo $settings[0]['secondary_color']; ?>"   class="form-control" name="secondary_color" />
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                     <label for="">Menu's Font Color</label>
                    <input type="color" value="<?php echo $settings[0]['menu_font_color']; ?>"  class="form-control" name="menu_font_color" />
                </div>
           </div>

       <div class="col-sm-6">
                <div class="form-group">
                     <label for="">Adjust Logo Size</label>
                   <input class="form-control" type="range" min="1" onchange="displayVal(this.value)" name="logo_max_width" max="100" value="<?php echo $settings[0]['logo_max_width']; ?>">
                   <small id="showval"><?php echo $settings[0]['logo_max_width']; ?></small><small>px</small>
                </div>
           </div>
       </div>
       
       <div class="row">
           <div class="col-sm-6">
                <div class="form-group">
                     <label for="sh_display">Show Display Name
                     <input type="checkbox" id="sh_display" class="" name="show_display_name_logo_old" <?php echo ($settings[0]['show_display_name_logo'] == '1')?'checked':''; ?> />
                     
                    </label>
                    <input type="hidden" id="show_display_name_logo" name="show_display_name_logo" value="<?php echo $settings[0]['show_display_name_logo']; ?>">
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                   <label for="">Banner Image</label>
                   <div id="extraupload"></div>
               </div>
           </div>
       </div>
       <div class="row">
<!--
           <div class="col-sm-6">
               <div class="form-group">
                     <label for="">Logo</label>
                    <input type="file"  class="form-control" name="display_name" />
                </div>
           </div>
-->
           
       </div>
        
      <button class="btn btn-success" id="save_facility" onclick="saveRecord()">SAVE</button>
    </form>
</div>
<script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
<script>
    
    $("#sh_display").click(function(){
        if($("#sh_display").is(':checked'))
        {
            $("#show_display_name_logo").val(1);
        }else{
            $("#show_display_name_logo").val(0);
        }
    })
    
    function displayVal(v)
    {
        $("#showval").text(v);
    }
    
    var coverImg = $("#extraupload").uploadFile({
                    url:"utilities.php",
                    fileName:"upfile",
                    showPreview:true,
                    previewHeight: "100px",
                    previewWidth: "100px",
                    maxFileCount:1,
                    multiple:false,
                    allowedTypes:"jpg,png",
                    maxFileSize:1000000,
                    autoSubmit:false,
                    returnType:"json",
                    onSubmit:function(files)
                    {
                        $.blockUI({message:"Saving product information. Kindly wait.."});
                    },
                    dynamicFormData: function()
                    {
                        
                        $("#save_facility").text("Loading......");
                        var dd = $("#settings_form").serialize();
                        
                        return dd;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $.unblockUI();
                        console.log(data);
                         $("#save_facility").text("Save");
                               
                        if(data.response_code == 0)
                        {
                            $('.server_message').html(data.response_message);
                            getpage('product_list.php','page');
                        }else
                        {
                            $('.server_message').html(data.response_message);
                            coverImg.reset();
                            $('.ajax-file-upload-red').click();
                        }
//                        featureImg.startUpload();
                    }
                });
    
    function saveRecord()
    {
        if(coverImg.selectedFiles == 0)
            {
                alert("kindly select an image file for Banner.")
            }
        else{
                coverImg.startUpload();
            }
    }
</script>
<style>
    .ajax-upload-dragdrop, .ajax-file-upload-filename, .ajax-file-upload-statusbar{
                width: auto !important;
            }
</style>