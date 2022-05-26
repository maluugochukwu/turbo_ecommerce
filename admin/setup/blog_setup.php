<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();

$sql = "SELECT * FROM blog_category";
$blog_category = $dbobject->db_query($sql);



if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $blog_id = $_REQUEST['id'];
    $sql_blog = "SELECT * FROM blog WHERE id = '$blog_id' LIMIT 1";
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
 <!-- <script src="js/ckeditor.js"></script> -->
 <!-- <script src="https://cdn.ckeditor.com/ckeditor5/29.1.0/classic/ckeditor.js"></script> -->
 
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
    <h4 class="modal-title" style="font-weight:bold">Blog Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Blog.saveBlog">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $blog_id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['title'] ?>" name="title" >
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" id="category_id"  class="form-control">
                        <?php
                            if(count($blog_category) > 0)
                            {
                                echo "<option value=''>:: Select a category ::</option>";
                                foreach ($blog_category as $row) 
                                {
                                    $selected = ($row['id'] == $blog[0]['category_id'])?"selected":"";
                                    echo "<option $selected value='".$row['id']."'>".$row['name']."</option>";
                                }
                            }
                            else
                            {
                                echo "<option value=''>:: No category available ::</option>";
                            }
                        ?>
                    </select>
                </div>
           </div>
           
       </div>
       
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Blog Content</label>
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
                    <label for="">Blog Image</label>
                    <div id="extraupload"></div>
               </div>
           </div>
       </div>
       <?php
            }
       ?>
       <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="keywords">Keywords</label>
                    <input type="text" id="optval" name="keywords" value="<?php echo $blog[0]['keywords'] ?>" data-role="tagsinput"   />
                    <small class="text-muted">Separate choices with commas e.g., Small, Medium, Large,</small> 
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['description'] ?>" name="description" id="description">
                </div>
            </div>
       </div>
       <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label"><input type="checkbox" <?php echo ($blog[0]['allow_comment'] == 1)?"checked":""; ?> name="allow_comment"> Allow Comment</label>
                    <label class="form-label"><input type="checkbox" <?php echo ($blog[0]['auto_show_comment'] == 1)?"checked":""; ?> name="auto_show_comment"> Auto show comment</label>
                    
                </div>
            </div>
        </div>
        
       
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
        <script src="js/ckeditor_upload_adapter.js"></script>
<script>
    String.prototype.replaceAt = function(index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}
    var content,introduction;
        

                var ck_story;
    function MyCustomUploadAdapterPlugin( editor ) {
        editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
            return new MyUploadAdapter( loader );
        };
    }
    
    
    $("document").ready(function(){
        ClassicEditor
        .create( document.querySelector( '#introduction' ), {
        extraPlugins: [ MyCustomUploadAdapterPlugin ],
    } )
        .then((editor)=>{
            ck_story = editor
                    ck_story.setData("<?php echo addslashes($blog[0]['body']); ?>");
        })
        .catch( error => {
            console.error( error );
        } );
    })   
    $.fn.modal.Constructor.prototype._enforceFocus = function() {
                var $modalElement = this.$element;
                console.log($modalElement,"modalel")
                $(document).on('focusin.modal',function(e) {
                    console.log(e,"count log")
                    if ($modalElement.length > 0 && $modalElement[0] !== e.target && !$modalElement.has(e.target).length && $(e.target).parentsUntil('*[role="dialog"]').length === 0) {
                        $modalElement.focus();
                    }
                });
            };         
                            
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
        var ccd = ck_story.getData();
        // ccd = ccd.replace(/"/g,'" ');
        ccd = ccd.replace(/&nbsp;/g,"");
        ccd = ccd.replace(/[\n]/g,"");
        ccd = ccd.replace(/ <br>/g,"");
        ccd = ccd.replace(/<br>/g,"");
        // ccd = ccd.replace(/. /g,"");
        // ccd = ccd.replace(/[^a-zA-Z0-9 ?.<>/'"][nbsp;]/g, "");
        // console.log(ccd,"logger")
        $("#defaultModalPrimary").block();
        var ddd = $("#form1").serialize();
        ddd = ddd + "&body="+ccd
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
                        var ccd = ck_story.getData();
                        // str.replace(/[^a-zA-Z0-9 ][nbsp;]/g, "")
                        // ccd = ccd.replace(/[^a-zA-Z0-9 ?.<>/'"][nbsp;]/g, "");
                        ccd = ccd.replace(/&nbsp;/g,"");
                        ccd = ccd.replace(/[\n]/g,"");
                        ccd = ccd.replace(/ <br>/g,"");
                        ccd = ccd.replace(/<br>/g,"");
                        var dd = $("#form1").serialize();
                        dd = dd + "&body="+ccd
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
                            getpage('blog_list.php','page');
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
            .ck-labeled-field-view__input-wrapper,.ck-balloon-panel,.ck-input{z-index:99999999999 !important}
</style>