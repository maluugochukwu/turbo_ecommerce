<?php
include_once("../libs/dbfunctions.php");
//include_once("../class/notification.php");
//$nn = new EmailTemplate();
//echo $nn->emailVerification(array('body'=>'<h2>Hello Ugo,</h2>Thank you.','title'=>'email verify'));
$dbobject = new dbobject();
//$ss = "SELECT role_id,role_name FROM role WHERE role_id = '003'";
//$ee = $dbobject->db_query($ss);
//$c_type[] = array('id'=>$ee[0]['role_id'],'name'=>$ee[0]['role_name']);
if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation  = 'edit';
    $id         = $_REQUEST['id'];
    $sql        = "SELECT * FROM testimonial WHERE id = '$id' LIMIT 1";
    $product  = $dbobject->db_query($sql);
//    var_dump($product);
}else
{
    $operation = 'new';
}
?>
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<script>
    doOnLoad();
    var myCalendar;
function doOnLoad()
{
   myCalendar = new dhtmlXCalendarObject(["start_date"]);
   myCalendar.hideTime();
}
</script>
<style>
    fieldset 
    { 
    display: block;
    margin-left: 2px;
    margin-right: 2px;
    padding-top: 0.35em;
    padding-bottom: 0.625em;
    padding-left: 0.75em;
    padding-right: 0.75em;
    border: 1px solid #ccc;
    }
    
    legend
    {
        font-size: 14px;
        padding: 5px;
        font-weight: bold;
    }
    .pointer{
        cursor: pointer;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold"><?php echo $product[0]['full_name']; ?> Image</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 " style="background:#f5f9fc">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       
       <div class="tab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#tab-1" data-toggle="tab" role="tab">Primary Image</a></li>
<!--                <li class="nav-item"><a class="nav-link" href="#tab-2" data-toggle="tab" onclick="rreload()" role="tab">Other Image(s)</a></li>-->
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-1" role="tabpanel">
                   <div class="row">
                       <div class="col-sm-5">
                           <img class="img-thumbnail" src="<?php echo $product[0]['logo']; ?>" alt="">
                        </div>
                        <div class="col-sm-7">
                            <h5>Set Image</h5><div id="extraupload"></div>
                        </div>
                   </div>
                   <!-- <input type="hidden" id="image" value="<?php //echo $product[0]['picture']; ?>" /> -->
                   <input type="hidden" id="id"    value="<?php echo $product[0]['id']; ?>" />
                </div>
                <div class="tab-pane" id="tab-2" role="tabpanel">
                    <table id="prod_img" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
               
            </div>
        </div>
       
<!--
        <div id="err"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary">Submit</button>
-->
    </form>
</div>

<script src="js/jquery.uploadfile.min.js"></script>
<link rel="stylesheet" href="css/uploadfile.css">
<script>
        function rreload()
        {
            $("#tab-2").block();
            $.post('setup/image_list.php',{product_id:'<?php echo $p_id ?>'},function(ee){
                $("#tab-2").unblock();
                $("#tab-2").html(ee);
                
                
            })
        }
    var coverImg = $("#extraupload").uploadFile({
                    url:"utilities.php",
                    fileName:"upfile",
                    showPreview:true,
                    previewHeight: "100px",
                    previewWidth: "100px",
                    maxFileCount:1,
                    dragDropStr:"<span><b>Drop Blog Image here</b></span>",
                    multiple:false,
                    allowedTypes:"jpg,png,jpeg",
                    maxFileSize:1000000,
                    autoSubmit:true,
                    returnType:"json",
                    onSubmit:function(files)
                    {
                        var c = confirm("Are you sure you want to set this image?");
                        if(c)
                            {
                                $("#tab-1").block({message:"Updating image. Kindly wait.."});
                                return true;
                            }else{
                                return false;
                            }
                        
                    },
                    dynamicFormData: function()
                    {
                        var ops = "<?php echo $operation; ?>";
                        var data = {op:'Testimonial.setTestimonialImage',id:$('#id').val(),operation:"<?php echo $operation; ?>" }
                        return data;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $("#tab-1").unblock();
                        console.log(data);
                        if(data.response_code == 0)
                            {
                                // alert(data.response_message);
                                swal({
                                    text:data.response_message,
                                    icon:"success"
                                }).then((rs)=>{
                                    getpage('testimonial_list.php','page');
                                    $("#defaultModalPrimary").modal('hide');
                                })
                            }else
                            {
                                coverImg.reset();
                                $('.ajax-file-upload-red').click();
                            }
                    }
                });

    function remove_img(link,id)
    {
        var c = confirm("Are you sure you want to remove this image?");
        if(c)
        {
            $("#tab-2").block({message:"Removing image. Kindly wait.."});
            $.post('utilities.php',{op:'Product.removeImage',image_id:id,image_location:link},function(ww){
                $("#tab-2").unblock();
                alert(ww.response_message);
                rreload();
            },'json');
        }
        
    }
    function saveRecord()
    {
//       if(coverImg.selectedFiles == 0)
//                    {
//                        alert("kindly select an image file for product.")
//                    }
//                else{
//                        coverImg.startUpload();
//                    }
    }
    
            
  
</script>
<style>
    .ajax-upload-dragdrop{
        width: 270px !important;
    }
    .ajax-file-upload-statusbar{
        width: 270px !important;
    }
</style>