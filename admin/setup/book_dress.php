<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();
$special_roles = array("001","002");

$branch_filter = (in_array($_SESSION['role_id_sess'],$special_roles))?"":" AND region = '$_SESSION[region_sess]'";

$sql2 = "SELECT * FROM branch WHERE 1 = 1 $branch_filter";
$branches = $dbobject->db_query($sql2);

$sql3 = "SELECT * FROM package ";
$package = $dbobject->db_query($sql3);



$region_filter = (in_array($_SESSION['role_id_sess'],$special_roles))?"":" AND country_id = '$_SESSION[region_sess]'";

$sql3 = "SELECT * FROM region WHERE 1=1 $region_filter";
$region = $dbobject->db_query($sql3);

if(isset($_GET['op']))
{
//    $sql = "SELECT * FROM dress WHERE id = '$_REQUEST[id]'";
//    $dress = $dbobject->db_query($sql);
    $operation = "edit";
    
    $sql = "SELECT * FROM transaction_table WHERE transaction_id = '$_REQUEST[trans_id]'";
    $transaction = $dbobject->db_query($sql);
    
    $customer_email = $transaction[0]['source_acct'];
    $sql = "SELECT * FROM customers WHERE email = '$customer_email'";
    $customer = $dbobject->db_query($sql);
    
    $dress_id = $transaction[0]['dress_id'];
    $dress_name = $dbobject->getitemlabel('dress','id',$dress_id,'name');
    
    if($transaction[0]['is_payment_full'] == "no")
    {
        $sql = "SELECT amount_paid FROM installment_payment WHERE booking_id = '$_REQUEST[trans_id]' ORDER BY id ASC";
    $installment = $dbobject->db_query($sql);
         $amount_paid = $installment[0]['amount_paid'];
    }
//    $operation = "edit";
}
else
{
    $operation ="new";
}
?>
<style>
    .book_dress label{
          color:#000;
          font-weight: bold;
      }
</style>
<link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<script src="codebase/dhtmlxcalendar.js"></script>
<script>
    doOnLoad();
    var myCalendar;
function doOnLoad()
{
    myCalendar = new dhtmlXCalendarObject(["start_date2"]);
    myCalendar3 = new dhtmlXCalendarObject(["wedding_date"]);
    myCalendar2 = new dhtmlXCalendarObject(["end_date"]);
    myCalendar.setDate("<?php echo date("Y-m-d"); ?>");
//	myCalendar.setInsensitiveRange(null,"<?php //echo date("Y-m-d",strtotime("-1 days")); ?>");
	
    myCalendar.hideTime();
    var myEvent = myCalendar.attachEvent("onClick", function(rr){
            console.log(rr);
                myCalendar2.setInsensitiveRange(null,$("#start_date").val());
//                myCalendar2.setDate($("#start_date").val());
                $("#end_date").val($("#start_date").val());
                myCalendar2.setDate($("#start_date").val());
                $("#select_dress").empty()
                $("#select_dress").html("<option>Loading..</option>")
                $.post("utilities.php",{op:"Dress.getAvailableDress",pickup_date:$("#start_date2").val()},function(jj){
                    $("#select_dress").empty()
                    $("#select_dress").html(jj)
                })
        });
}
</script>
<style>
    label{
        color:#000;
        font-weight: 500
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold"><?php echo ($operation == "edit")?"Edit booked dress":"Book Dress"; ?></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 book_dress">
   
    <form action="" id="form12" onsubmit="return false">
        <input type="hidden" name="op" value="Dress.bookDress">
       <input type="hidden" name="operation" value="<?php echo $operation; ?>">
       <input type="hidden" name="trans_id" value="<?php echo $_REQUEST[trans_id]; ?>">
       
       <fieldset>
           <legend style="padding:3px; padding-right:30px;padding-left:15px; width:auto; border:1px solid #cacaca; color:green; font-size:18px">Customer Details</legend>
           <div class="row">
               <div class="col-sm-4">
                   <div class="form-group">
                        <label for="">First Name</label>
                        <input type="text" value="<?php echo $customer[0]['first_name']; ?>"  class="form-control" name="first_name" />
                    </div>
               </div>
               <div class="col-sm-4">
                    <div class="form-group">
                         <label for="">Last Name</label>
                        <input type="text" value="<?php echo $customer[0]['last_name']; ?>"   class="form-control" name="last_name" />
                    </div>
               </div>
               <div class="col-sm-4">
                    <div class="form-group">
                         <label for="">Phone</label>
                        <input type="text" value="<?php echo $customer[0]['phone']; ?>"   class="form-control" name="phone" />
                    </div>
               </div>
           </div>
           <div class="row">
               
               <div class="col-sm-4">
                    <div class="form-group">
                         <label for="">Email</label>
                        <input type="text" value="<?php echo $customer[0]['email']; ?>"   class="form-control" name="source_acct" />
                    </div>
               </div>
               
               <div class="col-sm-4">
                   <div class="form-group">
                        <label for="">Bank Name</label>
                        <select name="bank_name" id="bank_name" class="form-control">
                        <?php
                            $sql = "SELECT * FROM banks WHERE bank_type='commercial' order by bank_name asc";
                            $result = $dbobject->db_query($sql);
                            foreach($result as $row)
                            {
                                $selected = ($customer[0]['bank_name'] == $row['bank_code'])?"selected":"";
                                echo "<option $selected value='".$row['bank_code']."'>".$row['bank_name']."</option>";
                            }
                        ?>
                        </select>
                    </div>
               </div>
               <div class="col-sm-4">
                    <div class="form-group">
                         <label for="">Account Number</label>
                        <input type="number" min="0" id="account_no" name="account_no" onkeyup="fetchAccName(this.value)" class="form-control" value="<?php echo $customer[0]['account_no']; ?>" placeholder="" />
                    </div>
                    <input type="hidden" id="account_name" value="<?php echo $customer[0]['account_name']; ?>" name="account_name" />
                    <small id="acc_name" style="font-weight:bold"><?php echo $customer[0]['account_name']; ?></small>
               </div>
               
           </div>
            <div class="row">
               <div class="col-sm-6">
                    <div class="form-group">
                         <label for="">Address</label>
                         <textarea name="address" class="form-control"  cols="30" rows="3"><?php echo $customer[0]['address']; ?></textarea>
                        
                    </div>
               </div>
               <div class="col-sm-6">
                    <div class="form-group">
                         <label for="">Additional Request</label>
                         <textarea name="additional_request" class="form-control"  cols="30" rows="3"><?php echo $transaction[0]['additional_request']; ?></textarea>
                        
                    </div>
               </div>
           </div>
           <div class="row">
               <div class="col-sm-6">
                   <label for="phone_2">Phone 2</label><input type="text" value="<?php echo $customer[0]['phone_2'] ?>" name="phone_2" class="form-control">
               </div>
               <div class="col-sm-6">
                   <label for="payment_mode">Mode of payment</label>
                    <select id="payment_mode" class="form-control" name="payment_mode">
                        <option <?php echo ($transaction[0]['payment_mode'] == "TRANSFER")?"selected":""; ?> value="TRANSFER">TRANSFER</option>
                        <option <?php echo ($transaction[0]['payment_mode'] == "CARD")?"selected":""; ?> value="CARD">CARD</option>
                        <option <?php echo ($transaction[0]['payment_mode'] == "CASH")?"selected":""; ?> value="CASH">CASH</option>
                    </select>
               </div>
           </div>
           <br>
           <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                       <label for="phone_2">Payment Channel</label>
                       <select name="payment_channel" id="payment_channel" class="form-control">
                           <option <?php echo ($transaction[0]['payment_channel'] == "TRANSFER")?"selected":""; ?> value="STORE">STORE</option>
                           <option <?php echo ($transaction[0]['payment_channel'] == "INSTAGRAM")?"selected":""; ?>  value="INSTAGRAM">Instagram</option>
                            <option <?php echo ($transaction[0]['payment_channel'] == "FACEBOOK")?"selected":""; ?>  value="FACEBOOK">Facebook</option>
                            <option <?php echo ($transaction[0]['payment_channel'] == "TWITTER")?"selected":""; ?>  value="TWITTER">Twitter</option>
                            <option <?php echo ($transaction[0]['payment_channel'] == "WHATSAPP")?"selected":""; ?>  value="WHATSAPP">Whatsapp</option>
                       </select>
                  </div>
               </div>
               <div class="col-sm-6" id="online_platforms" >
                  <div class="form-group">
                    <label for="phone_2">Items<div><small><i style="color:light-blue">items stated here are free</i></small></div></label>
                  <input type="text" id="optval" name="items" value="<?php echo $transaction[0]['items']; ?>" data-role="tagsinput"   />
                    <small style="color:red">enter many items seperated by a comma</small>
                   </div>
               </div>
           </div>
           <br>
           <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                       <label for="caution_fee">Caution Fee</label>
                       <input type="text" id="caution_fee" value="<?php echo $transaction[0]['caution_fee']; ?>" name="caution_fee" class="form-control">
                  </div>
               </div>
           </div>
           <br>
           <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                       <label for="phone_2">Any extra items?</label>
                       <label for="extra_yes" onclick="is_extra_item('yes')"><input  name="is_extra_item" value="yes" <?php echo ($transaction[0]['is_extra_item'] == "yes")?"checked":"" ?>   id="extra_yes" type="radio">YES</label> &nbsp;
                       <label for="extra_no" onclick="is_extra_item('no')"><input  name="is_extra_item" value="no" <?php echo ($transaction[0]['is_extra_item'] == "no")?"checked":"" ?> id="extra_no" type="radio">NO</label>
                       
                  </div>
               </div>
           </div>
           <br>
           <div class="row" id="extra_item_div" style="<?php echo ($transaction[0]['is_extra_item'] == "yes")?"display:block":"display:none" ?>" >
               <div class="col-sm-6">
                  <div class="form-group">
                       <label for="extra_items">Extra Items</label>
                       <input type="text" id="extra_item" name="extra_item" value="<?php echo $transaction[0]['extra_item']; ?>" data-role="tagsinput"   />
                    <small style="color:red">enter many items seperated by a comma</small>
                  </div>
               </div>
               <div class="col-sm-6" id="online_platforms" >
                  <div class="form-group">
                    <label for="extra_item_price">Total price of extra items</label>
                  <input type="text" id="extra_item_price" name="extra_item_price" value="<?php echo $transaction[0]['extra_item_price']; ?>" class="form-control">
                   </div>
               </div>
           </div>
           
           <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                       <label for="phone_2">Is payment for dress in full?</label>
                       <label for="yess" onclick="is_payment_fulls('yes')"><input <?php echo ($operation == "edit")?"disabled":""; ?> name="is_payment_full" value="yes"  <?php echo ($operation == "edit")?(($transaction[0]['is_payment_full'] = "yes")?"checked":""):"checked" ?> id="yess" type="radio">YES</label> &nbsp;
                       
                       <label for="noo" onclick="is_payment_fulls('no')"><input <?php echo ($operation == "edit")?"disabled":""; ?> name="is_payment_full" <?php echo ($operation == "edit")?(($transaction[0]['is_payment_full'] = "no")?"checked":""):"" ?> value="no"  id="noo" type="radio">NO</label>
                       <div class="form-group" id="amount_paids" style="<?php echo ($operation == "edit")?(($transaction[0]['is_payment_full'] = "no")?"display:block":"display:none"):"display:none" ?>">
                           <label >Amount Paid</label>
                           <input value="<?php echo $amount_paid; ?>" type="text" <?php echo ($operation == "edit")?"readonly":""; ?> name="amount_paid" class="form-control" />
                       </div>
                  </div>
               </div>
               <div class="col-sm-6" >
                 
               </div>
           </div>
       </fieldset>
       <br>
       <fieldset>
           <legend style="padding:3px; padding-right:30px;padding-left:15px; width:auto; border:1px solid #cacaca; color:green; font-size:18px">Dress Information</legend>
           <div class="row">
               <div class="col-sm-3">
                   <div class="form-group">
                        <label for="">Pick up Date</label>
                        <input type="text" name="pickup_date" value="<?php echo $transaction[0]['pickup_date']; ?>"  class="form-control" id="start_date2" autocomplete="off" />
                    </div>
               </div>
               <div class="col-sm-3">
                   <label for="">Select Dress</label>
                   <select name="dress_id" id="select_dress" onchange="getGownPrice(this.value)"  class="form-control">
                           <?php
                            if($operation == "edit")
                            {
                                echo "<option value='".$dress_id."'>".$dress_name."</option>";
                            }
                            ?>
                   </select>
               </div>
               <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">Return Date</label>
                        <input type="text" name="return_date" value="<?php echo $transaction[0]['return_date']; ?>" class="form-control" id="end_date" autocomplete="off" />
                    </div>
               </div>
               <div class="col-sm-3">
                   <div class="form-group">
                        <label for="">Wedding Date</label>
                        <input type="text" name="wedding_date" value="<?php echo $transaction[0]['wedding_date']; ?>"  class="form-control" id="wedding_date" autocomplete="off" />
                    </div>
               </div>
           </div>
           <div class="row">
               
           </div>
           <div class="row">
               
               <?php
            //    if($_SESSION['role_id_sess'] == "002")
            //    {
               ?>
               <div class="col-sm-4">
                    <label for="">Region</label>
                    <select name="region"  id="region" onchange="getBranch(this.value)" class="form-control">
                    <option value="">:: SELECT A REGION ::</option>
                            <?php 
                            if($operation == "edit")
                            {
                                $country_name = $dbobject->getitemlabel("country","id",$transaction[0]['region'],"name");
                                echo "<option selected value='".$transaction[0]['region']."'>".$country_name."</option>";
                            }else{
                                foreach($region as $row)
                                {
                                    $country_name = $dbobject->getitemlabel("country","id",$row['country_id'],"name");
                                    // $selected = ($row['country_id'] == $dress[0]['region'])?"selected":"";
                                    echo "<option  value='".$row['country_id']."'>".$country_name."</option>";
                                }
                            }
                            
                            ?>
                    </select>
               </div>
               <?php
            //    }
               ?>
                <div class="col-sm-5">
                    <label for="">Branch</label>
                    <select  name="branch_id" class="form-control" id="branch_id">
                        <?php
                        if($_SESSION['role_id_sess'] == "002")
                        {
                            echo "<option value=''>:: FIRST SELECT A REGION ::</option>";
                        }
                        else
                        {
                            echo "<option value=''>:: Select a branch ::</option>";
                            foreach($branches as $row)
                            {
                                $selected = ($transaction[0]['branch_id'] == $row['id'])?"selected":"";
                                echo "<option $selected value='".$row['id']."'>".$row['name']."</option>";
                            }
                        }
                        ?>
                    </select>
               </div>
               
               <div class="col-sm-3">
<!--                   <label for="discount"><input type="checkbox" name="discount" id="discount"> Apply discount?</label>-->
<!--                  <input type="text" class="form-control" id="discount_price" name="discount_price" autocomplete="off" placeholder="Enter selling price"  />-->
                   <label for="discount"> Gown selling price</label>
                   <input type="text" value="<?php echo $transaction[0]['dress_amount']; ?>" class="form-control" id="dress_price" name="dress_price" autocomplete="off" placeholder=""  />
               </div>
           </div>
          
           <br>
<!--
           <div id="summary_div" style="border:dashed 1px #000; padding:10px;display:none">
              <div class="row">
                  <div class="col-sm-12" align="center">
                      <h1 id="dress_name_title"></h1>
                  </div>
              </div>
               <div class="row" >
                  <div class="col-sm-6">
                      &#8358; <span id="dress_price"></span> + &#8358; <span id="dress_caution_price"></span>
                      <br/>
                      <h2 style="font-weight:bold">Total: &#8358; <span id="dress_total"></span></h2>
                  </div>
                  <div class="col-sm-6">
                      <div><b style="color:#000">Current Status:</b> <span class="badge" id="status_message"></span></div>
                      <div><b style="color:#000">Return Date:</b> <span id="dress_return_date"></span> </div> 
                  </div>
              </div>
              <h3>Bonus</h3>
              <ol id="bonus" ></ol>
           </div>
-->
           
       </fieldset>
       
    
        <div class="server_message" style="font-weight:bold"></div>
        <br>
        <div class="row">
            <div class="col-sm-6">
                <button class="btn btn-success btn-block" id="save_facilitys" onclick="saveRecords()">Proceed</button>
            </div>
            <div class="col-sm-6">
                <button class="btn btn-danger btn-block" id="wer" onclick="javascript:$('#editing_product').modal('hide')" >Cancel</button>
            </div>
        </div>
          
    </form>
</div>
<link rel="stylesheet" href="css/bootstrap-tagsinput.css" />
<script src="js/bootstrap-tagsinput.js"></script>
<script src="js/sweet_alerts.js"></script>
<script src="js/jquery.uploadfile.min.js"></script>
        <link rel="stylesheet" href="css/uploadfile.css">
        <style>
            fieldset{
                display: block;
                margin-left: 2px;
                margin-right: 2px;
                padding-top: 0.35em;
                padding-bottom: 0.625em;
                padding-left: 0.75em;
                padding-right: 0.75em;
                border: 1px solid #ccc;
                
            }
        </style>
<script>
    function getBranch(val)
    {
        $.post("utilities.php",{op:"Dress.getBranch",region:val},function(ww){
            $("#branch_id").html(ww)
        })
    }
    function is_payment_fulls(val)
    {
        if($("#yess").is(':checked'))
            {
                $("#amount_paids").hide();
            }else if($("#noo").is(':checked'))
                {
                    $("#amount_paids").show();
                }
//        if(val == 'yes')
//            {
//                $("#amount_paids").hide();
//            }else{
//                $("#amount_paids").show();
//            }
    }
    function is_extra_item(val)
    {
        
        if($("#extra_yes").is(':checked'))
            {
                $("#extra_item_div").show();
            }else if($("#extra_no").is(':checked'))
                {
                    $("#extra_item_div").hide();
                }
        
    }
    function getGownPrice(el)
    {
        $.post('utilities.php',{op:'Dress.getDressAmountDetails',dress_id:el},function(rr){
            $("#dress_price").val(rr);
        })
    }
    $("#discount").click(()=>{
        if($("#discount").is(":checked"))
            {
                $("#discount_price").show();
            }
        else
            {
                $("#discount_price").hide();
            }
    })
    function display_info(val)
    {
        $("#summary_div").hide();
        $.post('utilities.php',{op:'Dress.getDress',dress_id:val},(rr)=>{
            console.log(rr);
//            var total = parseInt(rr.price,10) + parseInt(rr.caution_fee,10)
//            var total = 800.00 + 10.00
//            alert(parseInt(rr.price,10))
            $("#summary_div").slideDown();
            var readable_return_date = (rr.return_date != "")?new Date(rr.return_date).toDateString():"N/A";
            $("#dress_name_title").text(rr.name)
//            $("#dress_price").text(rr.price)
            $("#dress_caution_price").text(rr.caution_fee)
            $("#dress_total").text(rr.total)
            $("#status_message").text(rr.status.message)
            $("#dress_return_date").text(readable_return_date);
            var mme = "";
            rr.packages.forEach((items)=>{
                console.log(items);
                mme = mme + "<li style=''>"+items+"<li>";
            })
            $("#bonus").html(rr.packages_formatted);
            if(rr.status.code == 1)
                {
                    $("#status_message").addClass("badge-danger");
                    $("#status_message").removeClass("badge-success");
                }else
                    {
                        $("#status_message").addClass("badge-success");
                    $("#status_message").removeClass("badge-danger");
                    }
            
        },'json')
    }
    function fetchAccName(acc_no)
    {
        if($("#bank_name").val() == "")
        {
           alert("Kindly select a bank");
           $("#account_no").val("");
        }else{
            if(acc_no.length == 10)
            {
                var account  = acc_no;
                var bnk_code = $("#bank_name").val();
                $("#acc_name").text("Verifying account number....");
                $("#account_name").val("");
                $.post("utilities.php",{op:"Dress.getAccountName",account_no:account,bank_code:bnk_code},function(res){
                    $("#acc_name").text(res);
                    $("#account_name").val(res);
                });
            }else{
                $("#acc_name").text("Account Number must be 10 digits");
            }
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
                    allowedTypes:"jpg,png,jpeg",
                    maxFileSize:1000000,
                    autoSubmit:false,
                    returnType:"json",
                    onSubmit:function(files)
                    {
                        $.blockUI({message:"Saving Dress information. Kindly wait.."});
                    },
                    dynamicFormData: function()
                    {
                        
                        $("#save_facilitys").text("Loading......");
                        var dd = $("#settings_forms").serialize();
                        
                        return dd;
                    },
                    onSuccess:function(files,data,xhr,pd)
                    {
                        $.unblockUI();
                        console.log(data);
                         $("#save_facilitys").text("Save");
                               
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
    function change_channel(val)
    {
//        alert("fghj")
        if(val == "ONLINE")
            {
                $("#online_platforms").show();
            }
        else
            {
                $("#online_platforms").hide();
            }
    }
    function saveRecords()
    {
        var data  =  $("#form12").serialize();
        $.post('utilities.php',data,function(rr){
            if(rr.response_code == 0)
                {
                    swal({
                        text:rr.response_message,
                        icon:"success"
                    }).then((rs)=>{
                        printer(rr.data.id);
//                        getpage('booking_receipt.php?id'+rr.data.id,'page');
                        $("#editing_product").modal('hide');
                    })
                    
                }else{
                    swal({
                        text:rr.response_message,
                        icon:"error"
                    })
                }
        },'json')
    }
    function printer(id)
    {
        window.open("booking_receipt.php?g="+id, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=300,left=500,width=800,height=500,titlebar=no")
    }
</script>
<style>
    .ajax-upload-dragdrop, .ajax-file-upload-filename, .ajax-file-upload-statusbar
    {
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