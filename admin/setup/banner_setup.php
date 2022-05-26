<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();

$sql = "SELECT * FROM product_page";
$page = $dbobject->db_query($sql);



if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $banner_id = $_REQUEST['id'];
    $sql_blog = "SELECT * FROM banner WHERE id = '$banner_id' LIMIT 1";
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
    <h4 class="modal-title" style="font-weight:bold">Banner Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Banner.saveBanner">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $banner_id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['title'] ?>" name="title" >
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Subtitle</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['subtitle'] ?>" name="subtitle" >
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Call To Action Text</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['cta_text'] ?>" name="cta_text" >
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">CTA Link</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['cta_link'] ?>" name="cta_link" >
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">CTA Font Color</label>
                    <input type="color" class="form-control" value="<?php echo $blog[0]['cta_color'] ?>" name="cta_color" >
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">CTA Background Color</label>
                    <input type="color" class="form-control" value="<?php echo $blog[0]['cta_bg'] ?>" name="cta_bg" >
                </div>
           </div>
       </div>
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Page</label>
                    <select name="page_id" id="page_id"  class="form-control">
                        <?php
                            if(count($page) > 0)
                            {
                                echo "<option value=''>:: Select a Page ::</option>";
                                foreach ($page as $row) 
                                {
                                    $selected = ($row['id'] == $blog[0]['page_id'])?"selected":"";
                                    echo "<option $selected value='".$row['id']."'>".$row['name']."</option>";
                                }
                            }
                            else
                            {
                                echo "<option value=''>:: No page available ::</option>";
                            }
                        ?>
                    </select>
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
                    <label for="">Banner Image</label>
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
        <link rel="stylesheet" href="css/bootstrap-tagsinput.css" />
        <script src="js/bootstrap-tagsinput.js"></script>
<script>
    String.prototype.replaceAt = function(index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}
    var content,introduction;
        

                var ck_story;
    $("document").ready(function(){
        ClassicEditor
        .create( document.querySelector( '#introduction' ) )
        .then((editor)=>{
            ck_story = editor
                    ck_story.setData("<?php echo $blog[0]['body']; ?>");
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
        // var ccd = ck_story.getData();
        // ccd = ccd.replace(/"/g,'" ');
        // ccd = ccd.replace(/&nbsp;/g,"");
        // console.log(ccd,"logger")
        $("#defaultModalPrimary").block();
        var ddd = $("#form1").serialize();
        // ddd = ddd + "&body="+ccd
        $.post('utilities.php',ddd,function(rr){
            $("#defaultModalPrimary").unblock();
             if(rr.response_code == 0)
                {
                    swal({
                        text:rr.response_message,
                        icon:"success"
                    }).then((rs)=>{
                        // getpage('blog_list.php','page');
                        do_filter();
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
                        // var ccd = ck_story.getData();
                        // ccd = ccd.replace(/nbsp;/g," ");
                        var dd = $("#form1").serialize();
                        // dd = dd + "&body="+ccd
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
                            getpage('banner_list.php','page');
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
<style>
    .label-info {
                background-color: #5bc0de;
            }
            .label {
                display: inline;
                padding: .2em .6em .3em;
                font-size: 75%;
                font-weight: 700;
                line-height: 1;
                color: #fff;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: .25em;
            }
            .bootstrap-tagsinput{
              width:100%;  
            }
            .bootstrap-tagsinput input {
                width:inherit;  
            }
</style>