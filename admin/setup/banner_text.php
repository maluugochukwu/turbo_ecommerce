<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $id = $_REQUEST['id'];
    $title = $dbobject->db_query("SELECT * FROM home_page WHERE type = 'BANNER_TEXT' LIMIT 1");
    $subtitle = $dbobject->db_query("SELECT * FROM home_page WHERE type = 'BANNER_SUBTITLE' LIMIT 1");
}else
{
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
    <h4 class="modal-title" style="font-weight:bold">Banner Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Home.setBannerText">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <!-- <input type="hidden" name="type" value="WELCOME_NOTE"> -->
       <div class="row">
           
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label"> Banner Title</label>
                    <div id="title"></div>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Banner Subtitle</label>
                    <div id="content"></div>
                </div>
           </div>
       </div>
      
    
       <div class="server_message"></div>
        <button id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</button>
        
    </form>
</div>
<script src="https://cdn.ckeditor.com/ckeditor5/29.2.0/classic/ckeditor.js"></script>
<script>
    
</script>
<script>
    var content,introduction;
        

                var ck_story;
    $("document").ready(function(){
        ClassicEditor
        .create( document.querySelector( '#title' ) )
        .then((editor)=>{
            ck_story = editor
                    ck_story.setData("<?php echo $title[0]['value']; ?>");
        })
        .catch( error => {
            console.error( error );
        } );
        ClassicEditor
        .create( document.querySelector( '#content' ) )
        .then((editor)=>{
            content = editor
                    content.setData("<?php echo $subtitle[0]['value']; ?>");
        })
        .catch( error => {
            console.error( error );
        } );
    })            
    function saveRecord()
    {
        $("#defaultModalPrimary").block();
        var ddd = $("#form1").serialize();
        ddd = ddd + "&banner_text="+ck_story.getData()+ "&banner_subtitle="+content.getData()
        $.post('utilities.php',ddd,function(rr){
            $("#defaultModalPrimary").unblock();
             if(rr.response_code == 0)
                {
                    swal({
                        text:rr.response_message,
                        icon:"success"
                    }).then((rs)=>{
                        getpage('home_page_list.php','page');
                        $("#defaultModalPrimary").modal('hide');
                    })
                    
                }else{
                    $(".server_message").text(rr.response_message);
                }
        },'json')
    }
</script>
