<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();





if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $blog_id = $_REQUEST['id'];
    $sql_blog = "SELECT * FROM testimonial WHERE id = '$blog_id' LIMIT 1";
    $blog = $dbobject->db_query($sql_blog);
}else
{
    $operation = 'new';
}
// $r = false;
// $b = false;
// if(!$r && $b)
// {

// }
?>
<style>
    .ck-editor__editable_inline {
    min-height: 200px;
}
</style>
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
 <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>

 <!-- <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" /> -->
 <script src="js/ckeditor.js"></script>
 <!-- <script src="https://cdn.ckeditor.com/ckeditor5/29.1.0/classic/ckeditor.js"></script> -->
 <script src="https://cdn.ckeditor.com/ckeditor5/29.2.0/classic/ckeditor.js"></script>
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
    <h4 class="modal-title" style="font-weight:bold">Testimonial Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Testimonial.saveTestimonial">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $blog_id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['full_name']; ?>" name="full_name" >
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Designation</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['designation']; ?>" name="designation" >
                </div>
           </div>
       </div>
       
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Testimonial Content</label>
                    <div id="introduction"></div>
                </div>
            </div>
        </div>
        
        <?php
            if($operation == "new")
            {
        ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                                <label for=""> Image</label>
                                <div id="extraupload"></div>
                        </div>
                    </div>
                </div>
       <?php
            }
       ?>
      
        
       
       <div class="server_message"></div>
       <?php
            if($operation == "new")
            {
        ?>
                <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
        <?php
            }
            else
            {
        ?>
            <button id="save_facility" onclick="editRecord()" class="btn btn-primary mb-1">Submit</button>
        <?php
            }
        ?>
    </form>
</div>
<script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
<script>
    var content,introduction;
        

                var ck_story;
    $("document").ready(function(){
        ClassicEditor
        .create( document.querySelector( '#introduction' ) )
        .then((editor)=>{
            ck_story = editor
                    ck_story.setData("<?php echo $blog[0]['content']; ?>");
        })
        .catch( error => {
            console.error( error );
        } );
    })            
                            
                </script>
<script>
    function showsubcat(val)
    {
        if(val != "")
        {
            $.post("utilities.php",{op:"Template.getTemplateSubcategory",cat_id:val},function(record){
                    $("#subcategory").html(record);
            });
        }
    }
    function editRecord()
    {
        $("#defaultModalPrimary").block();
        var ddd = $("#form1").serialize();
        ddd = ddd + "&content="+ck_story.getData()
        $.post('utilities.php',ddd,function(rr){
            $("#defaultModalPrimary").unblock();
             if(rr.response_code == 0)
                {
                    swal({
                        text:rr.response_message,
                        icon:"success"
                    }).then((rs)=>{
                        getpage('blog_list.php','page');
                        $("#defaultModalPrimary").modal('hide');
                    })
                    
                }else{
                    $(".server_message").text(rr.response_message);
                }
        },'json')
    }
    function selection(){
        // var text = "";
        // if (window.getSelection) {
        //     text = window.getSelection().toString();
        // } else if (document.selection && document.selection.type != "Control") {
        //     text = document.selection.createRange().text;
        // }
        // console.log(text);
    }
    
    var coverImg = $("#extraupload").uploadFile({
                    url:"utilities.php",
                    fileName:"upfile",
                    showPreview:true,
                    previewHeight: "100px",
                    previewWidth: "100px",
                    maxFileCount:1,
                    multiple:false,
                    allowedTypes:"jpg,png,jpeg",
                    maxFileSize:1000000,
                    autoSubmit:false,
                    returnType:"json",
                    onSubmit:function(files)
                    {
                        $.blockUI({message:"Saving Blog information. Kindly wait.."});
                    },
                    dynamicFormData: function()
                    {
                        
                        $("#save_facility").text("Loading......");
                        var dd = $("#form1").serialize();
                        dd = dd + "&content="+ck_story.getData()
                        return dd;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $.unblockUI();
                        console.log(data);
                         $("#save_facility").text("Save");
                               
                        if(data.response_code == 0)
                        {
                            $('.server_message').css('color','green');
                            $('.server_message').html(data.response_message);
                            $("#defaultModalPrimary").modal('hide');
                            getpage('testimonial_list.php','page');
                        }else
                        {
                            
                            $('.server_message').css('color','red');
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
                alert("kindly select an image file for Blog.")
            }
        else{
                coverImg.startUpload();
            }
    }
</script>