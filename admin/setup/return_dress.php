<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();




if(isset($_REQUEST['op']))
{
    $sql = "SELECT * FROM dress WHERE id = '$_REQUEST[id]'";
    $dress = $dbobject->db_query($sql);
    $operation = "edit";
}
else
{
    $operation ="new";
}
//var_dump($dress);
?>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Return (<?php echo $dress[0]['name']; ?>) Dress </h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
   
    <form action="" id="settings_forms" onsubmit="return false">
       <input type="hidden" name="op" value="Dress.returnDress">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="transaction_id" value="<?php echo $_REQUEST['trans_id']; ?>">
       <input type="hidden" name="dress_id" value="<?php echo $_REQUEST['id']; ?>">
       <div class="row">
           <div class="col-sm-12">
               <div class="form-group">
                    <label for="deductions">
                    Are there any deductions from the caution fee?
                    </label>
                    <div>
                        <label for="yes" ><input id="yes" onclick="ddr(this.value)" value="1" name="is_deduction" type="radio"> YES</label>
                        &nbsp; &nbsp;
                    <label for="no" ><input id="no" checked onclick="ddr(this.value)" value="0" name="is_deduction" type="radio"> NO</label>
                    </div>
                    
                </div>
           </div>
           
       </div>
<!--
       <div class="row">
           <div class="col-sm-12">
               <div class="form-group">
                    <label for="deductions">
                    Is <?php ?> 
                    </label>
                    <div>
                        <label for="yes" ><input id="yes" onclick="ddr(this.value)" value="1" name="is_deduction" type="radio"> YES</label>
                        &nbsp; &nbsp;
                    <label for="no" ><input id="no" checked onclick="ddr(this.value)" value="0" name="is_deduction" type="radio"> NO</label>
                    </div>
                    
                </div>
           </div>
           
       </div>
-->
       <div class="row" id="deduction_box" style="display:none">
           <div class="col-sm-6">
               <div class="form-group">
                    <label for="">Select a deduction</label>
                    <select name="deduction[name][]" id="" class="form-control">
                        <option value="damages">Damages</option>
                    </select>
                </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                    <label for="">Enter Deduction Amount</label>
                    <input type="text" name="deduction[amount][]" class="form-control">
                </div>
           </div>
       </div>
       
    
        <div class="server_message" style="font-weight:bold"></div>
      <a class="btn btn-success"  onclick="saveRecord()">SAVE</a>
    </form>
</div>

<link rel="stylesheet" href="css/bootstrap-tagsinput.css" />
<script src="js/bootstrap-tagsinput.js"></script>
<script src="js/jquery.uploadfile.min.js"></script>
<link rel="stylesheet" href="css/uploadfile.css">
<script>
    function ddr(el)
    {
//        var ss = $("#deductions").is(":checked");
        if(el == "1")
            {
                $("#deduction_box").show();
            }else{
                $("#deduction_box").hide();
            }
    }
    
    
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
                        $.blockUI({message:"Saving Dress information. Kindly wait.."});
                    },
                    dynamicFormData: function()
                    {
                        
                        $("#save_facility").text("Loading......");
                        var dd = $("#settings_forms").serialize();
                        
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
                            getpage('dress_list.php','page');
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
//        $.blockUI();
//         $.unblockUI();
         var data  =  $("#settings_forms").serialize();
        $.post('utilities.php',data,function(rr){
           
            if(rr.response_code == 0)
                {
                    swal({
                        text:rr.response_message,
                        icon:"success"
                    }).then((rs)=>{
                        getpage('transaction_list.php','page');
                        $("#defaultModalPrimary").modal('hide');
                    })
                    
                }else{
                    swal({
                        text:rr.response_message,
                        icon:"error"
                    })
                }
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
            }s
</style>