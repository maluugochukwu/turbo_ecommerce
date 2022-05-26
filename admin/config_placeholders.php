<?php
include_once("libs/dbfunctions.php");
$dbobject = new dbobject();

$template_id = $_GET['temp_id'];
$sql = "SELECT * FROM template_placeholder WHERE template_id = '$template_id' ";
$template_placeholder = $dbobject->db_query($sql);




    
$sql_template = "SELECT title FROM templates WHERE id = '$template_id' LIMIT 1";
$template = $dbobject->db_query($sql_template);

?>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Placeholder Configuration for <?php echo $template[0]['title']; ?> template</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
   <div class="server_message"></div>
    <form action="" id="settings_form" onsubmit="return false">
        <input type="hidden" name="op" value="Template.configPlaceholder">
        <input type="hidden" name="template_id" value="<?php echo $template_id; ?>">
        <?php
        if(count($template_placeholder) > 0)
        {
            foreach($template_placeholder as $row)
            {
        ?>
       <div class="row">
           <div class="col-sm-2">
               <div class="form-group">
                    <label for=""><?php echo $row['marker']; ?><input type="hidden" name="marker[]" value="<?php echo $row['marker']; ?>" /></label>
                </div>
           </div>
           <div class="col-sm-2">
               <div class="form-group">
                    
                    <input type="text" class="form-control" name="label[]" value="<?php echo $row['label']; ?>" />
                </div>
           </div>
           
           <div class="col-sm-3">
               <div class="form-group">
                    <input class="form-control" name="extra_info[]" placeholder="Add extra information" value="<?php echo  $row['extra_info'];  ?>" />
                </div>
           </div>
           <div class="col-sm-2 oga" >
                <div class="form-group">
                    <select name="data_type[]" onchange="showlistfield('<?php echo $row[marker] ?>',this)" class="form-control">
                        <option value="TEXT" <?php echo ($row['data_type'] == "TEXT")?"selected":""; ?> >TEXT</option>
                        <option value="NUMBER" <?php echo ($row['data_type'] == "NUMBER")?"selected":""; ?> >NUMBER</option>
                        <option value="DATE" <?php echo ($row['data_type'] == "DATE")?"selected":""; ?> >DATE</option>
                        <option value="LIST" <?php echo ($row['data_type'] == "LIST")?"selected":""; ?> >LIST</option>
                    </select>
                </div>
           </div>
           <div class="col-sm-3" style="<?php echo ($row['data_type']=="LIST")?"display: show":"display: none"; ?> ">
                <div class="form-group">
                <input type="text"  name="list_values[]" value="<?php echo $row['list_values']; ?>" data-role="tagsinput"   />
                    <small style="color:blue">seperate items with a comma</small>
                </div>
           </div>
       </div>
       
       <?php
            }
        }else{
            echo "<h3>There are no placeholders for this template</h3>";
        }
       ?>
       
       <div id="err"></div>
      <button class="btn btn-success" id="save_facility" onclick="saveRecord()">SAVE</button>
    </form>
</div>
<link rel="stylesheet" href="css/bootstrap-tagsinput.css" />
<script src="js/bootstrap-tagsinput.js"></script>
<script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
<script>
    function showlistfield(id,el)
    {
        if(el.value == "LIST")
        {
            var ss = $(el).parentsUntil(".oga")[0]
            $(ss).parent().next().show();
        }else
        {
            var ss = $(el).parentsUntil(".oga")[0]
            $(ss).parent().next().hide();
        }
        
    }
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
    
   
    function saveRecord()
    {
        var dd = $("#settings_form").serialize();
        // console.log(dd);
        $.post("utilities.php",dd,function(fr){
            console.log(fr)
            $("#err").css('color','green')
            $("#err").html(fr.response_message)
            getpage('template_list.php','page');
        },'json')
    }
</script>
<style>
    .ajax-upload-dragdrop, .ajax-file-upload-filename, .ajax-file-upload-statusbar{
                width: auto !important;
            }
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