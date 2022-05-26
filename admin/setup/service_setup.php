<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();





if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $blog_id = $_REQUEST['id'];
    $sql_blog = "SELECT * FROM services WHERE id = '$blog_id' LIMIT 1";
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
    <h4 class="modal-title" style="font-weight:bold">Services Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Services.saveService">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $blog_id; ?>">
       <div class="row">
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" value="<?php echo $blog[0]['title']; ?>" name="title" >
                </div>
           </div>
           
           
       </div>
       
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Content</label>
                    <div id="introduction"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Short Description</label>
                    <textarea name="description" id="description" class="form-control" cols="30" rows="10"><?php echo $blog[0]['description']; ?></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Icons</label>
                    <select name="icon" id="icon" onchange="showImage(this.value)" class="form-control">
                        <option value="">:: SELECT ICONS ::</option>
                        <option <?php echo ($blog[0]['icon'] =="images/icons/check-list.png")?"selected":""; ?> value="images/icons/check-list.png">Check List</option>
                        <option <?php echo ($blog[0]['icon'] =="images/icons/hiring.png")?"selected":""; ?> value="images/icons/hiring.png">Hiring</option>
                        <option <?php echo ($blog[0]['icon'] =="images/icons/presentation.png")?"selected":""; ?> value="images/icons/presentation.png">Presentation</option>
                        <option <?php echo ($blog[0]['icon'] =="images/icons/teamwork.png")?"selected":""; ?> value="images/icons/teamwork.png">Team Work</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6" >
                <img src="../<?php echo $blog[0]['icon'] ?>" id="icon_display" width="112" height="112" style="display:<?php echo ($operation == "edit")?"block":"none" ?>;" alt="">
            </div>
        </div>
        
        
      
        
       
       <div class="server_message"></div>
       <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
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
    function showImage(val)
    {
        $("#icon_display").attr("src","../"+val);
        $("#icon_display").show();
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
                    getpage('services_list.php','page');
                    $("#defaultModalPrimary").modal('hide');
                })
                
            }
            else{
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
    
    
    
    function saveRecord()
    {
        $("#save_facility").text("Loading......");
        var dd = $("#form1").serialize();
        dd = dd + "&content="+ck_story.getData()
        $.blockUI({message:"Saving Blog information. Kindly wait.."});
        $.post("utilities.php",dd,function(data){
            $.unblockUI();
            console.log(data);
                $("#save_facility").text("Save");
                    
            if(data.response_code == 0)
            {
                $('.server_message').css('color','green');
                $('.server_message').html(data.response_message);
                $("#defaultModalPrimary").modal('hide');
                do_filter();
            }else
            {
                
                $('.server_message').css('color','red');
                $('.server_message').html(data.response_message);
                
            }
        },'json')
    }
</script>