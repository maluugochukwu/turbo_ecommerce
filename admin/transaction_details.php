<?php
    include_once("libs/dbfunctions.php");
    $dbobject     = new dbobject();
    $id           = $_REQUEST['order_id'];
    $customer_id = $_REQUEST['customer_id'];
    $sql = "SELECT * FROM orderdetails WHERE customer_id = '$customer_id' AND order_id = '$id'";
    $result = $dbobject->db_query($sql);
?>
<style>
    b{
        color:#000
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold" align="center"> Transaction Breakdown</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 " style="background:#f5f9fc">
    <div class="tab">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" href="#tab-1" data-toggle="tab" role="tab"><i class="fa fa-shopping-cart"></i> Order Details</a></li>
            <li class="nav-item"><a class="nav-link" href="#tab-2" data-toggle="tab" role="tab"><i class="fa fa-user"></i> Customer Details</a></li>

        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-1" role="tabpanel">
               <div class="table-responsive">
                   <table id="order_summarys" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Order ID</th>
                                <th>Product Name</th>
<!--                                <th>Selling Price</th>-->
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Order Status</th>
                                <th>Shipping Status</th>
                                <th>Customer ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                                
                        </tbody>
                    </table>
               </div>
            </div>
            <div class="tab-pane" id="tab-2" role="tabpanel">
                <div class="row">
                    <div class="col-sm-12" id="photo_display">
                        <div class="card mb-3">
                            
                            <div class="card-body">
                                <table id="customer_tbl" class="table table-stripe">
                                    <thead>
                                        <tr role="row">
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Gender</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <?php
                                                $sql = "SELECT * FROM userdata_customer WHERE username = '$customer_id' LIMIT 1";
                                                $result = $dbobject->db_query($sql);
                                                foreach($result as $row)
                                                {
                                            ?>
                                                    <tr role="row" class="odd">
                                                        <th><?php echo $row['first_name']." ".$row['last_name']; ?></th>
                                                        <th><?php echo $row['email']; ?></th>
                                                        <th><?php echo $row['phone']; ?></th>
                                                        <th><?php echo $row['sex']; ?></th>
                                                    </tr>
                                            <?php
                                                }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
      var table;
  var editor;
  var op = "Orders.orderList";
  $(document).ready(function() {
    table = $("#order_summarys").DataTable({
      processing: true,
      columnDefs: [{
        orderable: false,
        targets: 0
      }],
      serverSide: true,
      paging: true,
      oLanguage: {
        sEmptyTable: "No record was found, please try another query"
      },

      ajax: {
        url: "utilities.php",
        type: "POST",
        data: function(d, l) {
          d.op = op;
          d.li = Math.random();
          d.order_id    = "<?php echo $id; ?>";
          d.customerid  = "<?php echo $customer_id; ?>";

//          d.end_date = $("#end_date").val();
        }
      }
    });
  });
    $(document).ready(function() {
    table = $("#customer_tbl").DataTable({
      processing: true,
      columnDefs: [{
        orderable: false,
        targets: 0
      }],
      serverSide: false,
      paging: true,
      oLanguage: {
        sEmptyTable: "No record was found, please try another query"
      },

    });
  });

  function do_filter() {
    table.draw();
  }
    
</script>