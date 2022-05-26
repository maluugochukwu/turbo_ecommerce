<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $operation = 'edit';
    $id = $_REQUEST['id'];
    $sql_contact = "SELECT * FROM home_page WHERE id = '$id' LIMIT 1";
    $contact = $dbobject->db_query($sql_contact);
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
    <h4 class="modal-title" style="font-weight:bold">Home Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Home.saveHome">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="id" value="<?php echo $id; ?>">
       <!-- <input type="hidden" name="type" value="WELCOME_NOTE"> -->
       <div class="row">
           
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label"> Content</label>
                    <div id="introduction"></div>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" id="type" class="form-control">
                        <option value="WELCOME_NOTE">WELCOME NOTE</option>
                        <option value="WELCOME_TITLE">WELCOME TITLE</option>
                    </select>
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
        .create( document.querySelector( '#introduction' ) )
        .then((editor)=>{
            ck_story = editor
                    ck_story.setData("<?php echo $contact[0]['value'] ; ?>");
        })
        .catch( error => {
            console.error( error );
        } );
    })            
    function saveRecord()
    {
        $("#defaultModalPrimary").block();
        var ddd = $("#form1").serialize();
        var ccd = ck_story.getData();
        ccd = ccd.replace(/"/g,'" ');
        ccd = ccd.replace(/&nbsp;/g,"");
        ddd = ddd + "&value="+ccd
        
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
