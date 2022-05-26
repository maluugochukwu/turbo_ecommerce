<?php
include_once("libs/dbfunctions.php");
//var_dump($_SESSION);
$merchant_id = $_REQUEST['merchant_id'];
$sql = "SELECT * FROM notification WHERE merchant_id = '$merchant_id'";
$dbobject = new dbobject();
$result = $dbobject->db_query($sql);
?>
   <div class="card">
    <div class="card-header">
        <h5 class="card-title">Merchant Notification List</h5>
<!--        <h6 class="card-subtitle text-muted">The report contains orders that have not been attended to on time in the system.</h6>-->
    </div>
    <div class="card-body">
      
        <div id="datatables-basic_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
<!--
            <div class="row">
                <div class="col-sm-3">
                    <label for="">Create Role</label>
                </div>
            </div>
-->
            <div class="row">
                <div class="col-sm-12 table-responsive">
                    <table id="page_list" class="table table-striped " >
                        <thead>
                            <tr role="row">
                                <th>S/N</th>
                                <th>Contact Details</th>
                                <th>Type</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(count($result) > 0)
                            {
                                foreach($result as $row)
                                {
                                    echo "<tr>
                                            <td></td>
                                            <td>".$row['address']."</td>
                                            <td>".$row['type']."</td>
                                            <td>".$row['purpose']."</td>
                                         </tr>";
                                }
                            }else
                            {
                                echo "<tr><td colspan='4' align='center'>No notification found for this merchant</td></tr>";
                            }
                            
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<script src="../js/sweet_alerts.js"></script>-->
<!--<script src="../js/jquery.blockUI.js"></script>-->
