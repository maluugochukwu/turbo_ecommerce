<?php
session_start();
require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable("./");
$dotenv->load();

require_once('libs/dbfunctions.php');
if(!isset($_SESSION['username_sess']))
{
    header('location: logout.php');
}
if(!isset($_COOKIE['tour_state']))
{
    $tour_state = "yes";
    setcookie('tour_state',$tour_state,time()+(86400 * 300),'/');
    
}else
{
    $tour_state = $_COOKIE['tour_state'];
}

require_once('class/menu.php');
$menu = new Menu();
$menu_list = $menu->generateMenu($_SESSION['role_id_sess']);
$menu_list = $menu_list['data'];
// var_dump($_SESSION);
$dbobject = new dbobject();
$sql = "SELECT bank_name,account_no,account_name FROM userdata WHERE username = '$_SESSION[username_sess]' LIMIT 1 ";
$user_det = $dbobject->db_query($sql);

?>
<!DOCTYPE html>
<html lang="en" >


<!-- Mirrored from appstack.bootlab.io/dashboard-default.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 26 Jul 2019 15:56:51 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Portal">
	<meta name="author" content="<?php echo $_ENV['APPLICATION_NAME'] ?>">

	<title><?php echo $_ENV['APPLICATION_NAME'] ?></title>
<!--	<title>200 Store</title>-->

    <link rel="preconnect" href="http://fonts.gstatic.com/" crossorigin>
<!--    <link rel="icon" href="https://www.store200.com/assets/images/logo-green-black-text.png" sizes="32x32" />-->
    <link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
    <link rel="stylesheet" href="css/owl.carousel.css" />
    <link rel="stylesheet" href="css/owl.theme.css" />
    <link rel="stylesheet" href="css/introjs.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/enjoyhint.css" />
	<!-- PICK ONE OF THE STYLES BELOW -->
	<!-- <link href="css/classic.css" rel="stylesheet"> -->
	<!-- <link href="css/corporate.css" rel="stylesheet"> -->
	<!-- <link href="css/modern.css" rel="stylesheet"> -->
	<link href="css/zoomove.min.css" rel="stylesheet">

	<!-- BEGIN SETTINGS -->
	<!-- You can remove this after picking a style -->
	<style>
		body {
			opacity: 0;
		}
	</style>
	<script src="js/settings.js"></script>
	<!-- END SETTINGS -->
<!-- Global site tag (gtag.js) - Google Analytics -->

    <script src="js/app.js"></script>
    <script src="js/jquery.blockUI.js"></script>
	<script src="js/parsely.js"></script>
	
	<script src="js/sweet_alerts.js"></script>
	<script src="js/main.js"></script>
	<script src="codebase/dhtmlxcalendar.js"></script>
	
</head>

<body >
	<div class="wrapper" >
		<nav  class="sidebar" <?php if($_SESSION['role_id_sess'] == "003" && ($user_det[0]['account_no'] == "00000000000" || $user_det[0]['bank_name'] == "00")){ echo "style='display:none'"; } ?> >
			<div class="sidebar-content ">
				<a class="sidebar-brand" target="_blank" href="#">
                 <div align="center"><img src="img/log.jpg" style="max-width: 40%" alt="<?php echo $_ENV['APPLICATION_NAME'] ?> Logo"></div>
                 <h4 align="center"><?php echo $_ENV['APPLICATION_NAME']  ; ?></h4>
                </a>
                
				<ul class="sidebar-nav">
					<li class="sidebar-item">
                        <button style="background:#fff" class="btn btn-outline-success btn-block d-inline-block d-sm-none" disabled="">
                            <?php echo $_SESSION['firstname_sess'].' '.$_SESSION['lastname_sess']; ?>
                        </button>
                        <a style="margin-top:0" href="javascript:window.location='logout.php'" class="d-inline-block d-sm-none btn btn-danger btn-block">
                            Logout
                        </a>
						<a href="home.php" data-toggle="" class="sidebar-link collapsed dashboard">
              				 <span class="align-middle">Dashboard</span>
            			</a>
						
                        <?php
                        foreach($menu_list as $row)
                        {
                        ?>
                            <a  href="#k<?php echo $row['menu_id']; ?>" id="<?php echo $row['menu_id']; ?>" data-toggle="collapse" class="sidebar-link collapsed">
                                <i class="align-middle <?php echo $row['icon']; ?>" ></i> <span class="align-middle"><?php echo $row['menu_name']; ?></span>
                            </a>
                            <?php
                                if($row['has_sub_menu'] == true)
                                {
                                    echo '<ul id="k'.$row['menu_id'].'"   class="sidebar-dropdown list-unstyled collapse" >';
                                    foreach($row['sub_menu'] as $row2)
                                    {
                                        if($row2['menu_id'] == "2dss5")
                                        {
//                                            if($_SESSION['role_id_sess'] == 001 || $_SESSION['church_type_id_sess'] == 1)
//                                            {
                                       
                            ?>
                                            <li class="sidebar-item" id="<?php echo $row2['menu_id']; ?>"><a class="sidebar-link"  ><?php echo $row2['name']; ?></a>
                                            </li>
                            <?php
//                                            }
                                        }
                                        else
                                        {
                                      
                            ?>
                                            <li class="sidebar-item" id="<?php echo $row2['menu_id']; ?>" >
                                                <a class="sidebar-link" href="javascript:getpage('<?php echo $row2['menu_url']; ?>','page')">
                                                    <?php echo $row2['name']; ?>
                                                </a>
                                            </li>
                            <?php
                                        }
                                    }
                                    echo '</ul>';
                                }
                            ?>
                        <?php
                        }
                        ?>
                    </li>
                    
					
				</ul>

				<div class="sidebar-bottom d-none d-lg-block">
					<div class="media">
						<img class="rounded-circle mr-3" src="<?php echo $_SESSION['photo_path_sess']; ?>" alt="<?php echo $_SESSION['firstname_sess'].' '.$_SESSION['lastname_sess']; ?>" width="40" height="40">
						<div class="media-body">
							<h5 class="mb-1"><?php echo $_SESSION['firstname_sess'].' '.$_SESSION['lastname_sess']; ?></h5>
							
                            <div>
                                <button class="btn btn-danger btn-block" onclick="window.location='logout.php'">Logout</button>
                            </div>
						</div>
					</div>
				</div>

			</div>
		</nav>

		<div class="main" >
			<nav class="navbar navbar-expand navbar-light bg-white">
				<a class="sidebar-toggle d-flex mr-2">
                    <i class="hamburger align-self-center"></i>
                </a>
                <a href="javascript:void(0)" class="d-flex mr-2" >
                    Your Role: &nbsp; <span style="font-weight:bold; color:#000"><?php  echo $dbobject->getitemlabel('role','role_id',$_SESSION['role_id_sess'],'role_name');?></span>
                </a>&nbsp; &nbsp; | &nbsp; &nbsp; 
                <div class="custom-control custom-switch">
                    <input type="checkbox" <?php echo ($tour_state == "yes")?"checked":""; ?> class="custom-control-input" id="customSwitch1">
                    <label class="custom-control-label" id="tour_label" for="customSwitch1"><?php echo ($tour_state == "yes")?"Turn off tour":"Turn on tour"; ?></label>
                </div>
                <?php
                if($_SESSION['role_id_sess'] != '001' && $_SESSION['role_id_sess'] != '002')
                {
                ?>
                &nbsp; &nbsp; | &nbsp; &nbsp; 
                <a href="javascript:void(0)" class="d-flex mr-2" >
                    Region: &nbsp; <span style="font-weight:bold; color:#000"><?php  echo $dbobject->getitemlabel('country','id',$_SESSION['region_sess'],'name');?></span>
                </a>
                <?php
                }
                ?>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav ml-auto">
						
					
						<li class="nav-item dropdown">
                        <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings align-middle"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        </a>

							<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-toggle="dropdown" >
                <img src="<?php echo $_SESSION['photo_path_sess'] ?>" class="avatar img-fluid rounded-circle mr-1" alt="<?php echo $_SESSION['firstname_sess'].' '.$_SESSION['lastname_sess']; ?>" /> <span class="text-dark"><?php echo $_SESSION['firstname_sess'].' '.$_SESSION['lastname_sess']; ?></span>
              </a>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="javascript:getpage('profile.php','page')"><i class="align-middle mr-1" data-feather="user"></i> Profile</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="logout.php">Sign out</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>

			<main class="content" id="page">
				<div class="container-fluid p-0">
					<div class="row">
						<div class="col-12 col-lg-6">
							<div class="card" >
								<div class="card-header">
									<h5 class="card-title new_btn">Area Chart</h5>
									<h6 class="card-subtitle text-muted">Total Sales Count and Total Revenue comparison.</h6>
								</div>
								<div class="card-body">
									<div class="chart">
										<canvas id="apexcharts-area"></canvas>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12 col-lg-6">
                            <div class="card flex-fill w-100">
								<div class="card-header">
									
									<h5 class="card-title">Bar Chart</h5>
									<h6 class="card-subtitle text-muted">Transaction chart for last year and this year.</h6>
								</div>
								<div class="card-body">
									<div class="chart">
										<canvas id="chartjs-bar"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="containers" style="width:100%; height:850px"></div>
                        </div>
                    </div>
					<div class="row">
<!--
                   <div class="col-12 col-lg-6">
                        <div class="card" data-intro='Area Chart show line graphs of total sales count and total revenue'>
                            <div class="card-header">
                                <h5 class="card-title">Merchant Pie Chart (Top Selling)</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart chart-sm">
                                    <canvas id="chartjs-pie"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
-->
                    <div class="col-sm-12 " style="padding-top:50px">
                        
							<div class="card flex-fill">
								<div class="card-header">
									
									<h5 class="card-title mb-0" onclick='doEnjoy()'>5 Recent Sales</h5>
								</div>
                            <div id="datatables-dashboard-projects_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6"></div>
                                    <div class="col-sm-12 col-md-6"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                       <div class="table-responsive" >
                                        <table id="datatables-dashboard-projects" class="table table-striped my-0 dataTable no-footer" role="grid" aria-describedby="datatables-dashboard-projects_info">
                                            <thead>
                                                <tr role="row">
                                                    <th>Branch Name</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
//                                                $merchant_id = $_SESSION['merchant_details']['merchant_id'];
//                                                $filter = ($_SESSION['role_id_sess'] == 001)?"":" AND source_acct = '$merchant_id'";
                                                $filter = "";
                                                $sql = "SELECT * FROM transaction_table WHERE 1 = 1 $filter ORDER BY created desc LIMIT 5";
                                                $result = $dbobject->db_query($sql);
                                                if(count($result) > 0)
                                                {
                                                    foreach($result as $row)
                                                    {
                                            ?>
                                                        <tr>
                                                            <td><?php echo $dbobject->getitemlabel("branch","id",$row['branch_id'],"name"); ?></td>
                                                            <td><?php echo "&#x20a6; ".number_format($row['transaction_amount'],2); ?></td>
                                                            <td><?php echo date("F jS, Y", strtotime($row['created'])); ?></td>
                                                        </tr>
                                            <?php
                                                    }
                                                }else
                                                {
                                                    echo "<tr><td>No record found</td></tr>";
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
			</main>

			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-left">
							<ul class="list-inline">
								
								<li class="list-inline-item">
									<a class="text-muted" href="#">Help Center</a>
								</li>
								
							</ul>
						</div>
						<div class="col-6 text-right">
							<p class="mb-0">
								&copy; <?php echo date('Y') ."-". (date('Y') + 3); ?>  <a target="_blank" href="#" class="text-muted"><?php echo $_ENV['APPLICATION_NAME'] ?></a>
							</p>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>
	<style>
        .dhtmlxcalendar_material 
        {
            z-index: 99999 !important;
        }
    </style>
<link rel="stylesheet" href="codebase/dhtmlxcalendar.css" />
<link rel="stylesheet" href="css/bootstrap-tour-standalone.min.js" />
<script src="codebase/dhtmlxcalendar.js"></script>
<script src="js/owl.carousel.js"></script>
	<script src="js/cart.js"></script>
	<script src="js/bootstrap-tour-standalone.min.js"></script>
	
	<script>
        
        
	</script>
	<script>
        
$(function() {
    
//            $.post('utilities.php',{op:'Dashboard.topFiveMerchant'},function(dd){
//                console.log('record from dashbordee ',dd);
////                $("#tfive").html(dd.topfive);
//                new Chart(document.getElementById("chartjs-pie"),dd.pie);
//            },'json');
    
                
			// Bar chart
            $.post('utilities.php',{op:'Dashboard.transactionHistoryPreviousNow'},function(dd){
                console.log('record from dashbord ',dd);
                new Chart(document.getElementById("chartjs-bar"),dd)
            },'json')
//			new Chart(document.getElementById("chartjs-bar"), );transactionHistoryPreviousNow
		});
	</script>
	<script>
        $.post('utilities.php',{op:'Dashboard.transactionCountSales'},function(dd){
                console.log('record from dashbord ',dd);
                new Chart(document.getElementById("apexcharts-area"),dd)
            },'json')

	</script>

<div class="modal fade" id="defaultModalPrimary" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modal_div">
            <div class="modal-header">
                <h5 class="modal-title">Default modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body m-3">
                <p class="mb-0">Use Bootstrap’s JavaScript modal plugin to add dialogs to your site for lightboxes, user notifications, or completely custom content.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
   <div class="modal fade" id="editing_product" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal_div2">
            <div class="modal-header">
                <h5 class="modal-title">Default modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body m-3">
                <p class="mb-0">Use Bootstrap’s JavaScript modal plugin to add dialogs to your site for lightboxes, user notifications, or completely custom content.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
</body>
<script src="js/highmaps.js"></script>
<script src="js/exporting.js"></script>
<script src="js/ng-all.js"></script>

 <script src="js/intro.min.js"></script>
<script src="js/kinetic.js"></script>
<script src="js/enjoyhint.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<style>
    
</style>
<script>
    var tour_state             = "<?php echo $tour_state; ?>";
    function detectMob() {
    var frew = ( ( window.innerWidth <= 800 ) || ( window.innerHeight <= 600 ) );
    if(frew)
    {
        swal({
            title:"Notice",
            icon:"info",
            text:"To use the tour guide, kindly view this page from a wider screen"
        })
        // alert("To use the tour guide, kindly view this page from a wider screen")
    }
    return frew;
  }
  tour_state = (detectMob())?"no":tour_state;
    var menuStates = {
        'branch':
            {
                isSaved:0,
                step:
                    {
                        default:[
                            {
                                'click #branch_btn' : 'Click this button to start creating your branch',
                            }
                        ],
                        alt:[
                            {
                                'next .table-responsive' : 'You can find the record for the branch here. Click the next button to continue',
                            },
                            {
                                'click #006' : 'Next step is to create your Users/Employees. Now click on the "User Setup" button',
                            }
                        ],
                        
                    }
            },
        'branchSetup':
            {
                isSaved:0,
                step:
                    {
                        default:[
                            {
                                'click #modal_div' : 'Fill the form correctly and click the save button',
                            }
                        ],
                        alt:[],
                    }
            },
        'user':
            {
                isSaved:0,
                step:
                    {
                        default:[
                            {
                                'click #create_user':'Click on this button to create an employee'
                            }
                        ],
                        alt:[
                            {
                                'next .table-responsive' : 'You can find the record for the employee here. Click the next button to continue',
                            },
                            {
                                'click #016' : 'Click the Activities menu.',
                            },
                            {
                                'click #24' : 'First Let\'s create packages that will be used for the dress. ',
                            }
                        ]
                    }
            },
        'dashboard':
            {
                isSaved:0,
                step:
                    {
                        default:[
                            {
                                'next .navbar' : '<div  align="center"><img class="rounded-circle" src="img/avatars/avatar-5.jpg" width="200" height="200" /><div>Hello there, my name is Jane.<br/> I will be your guide. Click the "Next" button below to continue</div></div>',

                            },
                            {
                                'click #004' : 'Click the "System Setup" button to reveal other menus',

                            },
                            {
                                'click #25' : 'We first need to create a branch. click the "Branch List" button',
                            }
                        ],
                        alt:[]
                    }
            },
        'package':
            {
                isSaved:0,
                step:
                    {
                        default:[
                            {
                                'click #package_btn':'Click on this button to create a package'
                            }
                        ],
                        alt:[
                            {
                                'next .table-responsive' : 'You can find the record for the pacakage here. Click the next button to continue',
                            },
                            {
                                'click #19' : 'Now let\'s create the dress. Click the button to continue',
                            }
                        ]
                    }
            },
    }
    var never_guess_var_branch = 0;
    function runTour(menu)
    {
        
        if(tour_state == "yes")
        {
            var enjoyhint_instance = new EnjoyHint({});
            var steps = (menu.isSaved == "0")?menu.step.default:menu.step.alt;
//            console.log(steps);
            enjoyhint_instance.set(steps);
            enjoyhint_instance.run();
        }
    }

    
    runTour(menuStates.dashboard);
    
    var data;
    
    
   $("#customSwitch1").click(()=>{
       var conf = confirm("Your page will refresh. Do you want to continue?")
       if(conf)
       {
           if($("#customSwitch1").is(":checked"))
           {
               var state = "yes";
               var oo = new Date();
               oo.setTime(oo.getTime() + (300 *24*60*60*1000));
               var expires = oo.toUTCString();
               document.cookie = "tour_state="+state+";"+expires+";path=/";
               tour_state = state;

           }
           else
           {
               var state = "no";
               var oo = new Date();
               oo.setTime(oo.getTime() + (300 *24*60*60*1000));
               var expires = oo.toUTCString();
               document.cookie = "tour_state="+state+";"+expires+";path=/";
               tour_state = state;
           }
           window.location = "home.php";
        }else{
            if($("#customSwitch1").is(":checked"))
                {
                    $("#customSwitch1").prop('checked',false);
                }else{
                    $("#customSwitch1").prop('checked',true);
                }
            
        }
   })

    async function ht()
    {
        await $.post('utilities.php',{op:'Dashboard.stateHeatMap'},function(dd){
             },'json').then((ee)=>{

            data = ee.data


        });
        return data
    }
    
   ht().then((ee)=>{ console.log(JSON.stringify(ee))
    Highcharts.mapChart('containers', {
    chart: {
        map: 'countries/ng/ng-all'
    },

    title: {
        text: 'Sales count across Nigeria'
    },

    subtitle: {
        text: 'Source map: <a href="#">My Blog</a>'
    },

    mapNavigation: {
        enabled: true,
        buttonOptions: {
            verticalAlign: 'bottom'
        }
    },

    colorAxis: {
        min: 0
    },

    series: [{
        data: ee,
        name: 'Sales Count',
        states: {
            hover: {
                color: '#BADA55'
            }
        },
        dataLabels: {
            enabled: true,
            format: '{point.name}'
        }
    }]
});
                    })

//                     jQuery(document).ready(function($) {

// if (window.history && window.history.pushState) {

//   window.history.pushState('forward', null, './home.php');
//   window.history.pushState('forward', null, './home.php');

//   $(window).on('popstate', function() {
//     alert('Back button was pressed.');
//   });

// }
// });
//    $(document).ready(function(){
//        introJs().start();
//    })
    
    //initialize instance
//    var enjoyhint_instance = new EnjoyHint({});
//   
//    //simple config. 
//    //Only one step - highlighting(with description) "New" button 
//    //hide EnjoyHint after a click on the button.
//    var enjoyhint_script_steps = [
//        {
//            'click #004' : 'Click the "System Setup" button to reveal other menus',
//        },
//        {
//            'click #25' : 'Click the "Branch List" button to start creating your branch',
//           
//            'onBeforeStart':function(){
//                getpage('branch_list.php','page')
//              }
//            
//        },
//        {
//            'click #branch_btn' : 'The "Create New Branch" button will pop up an interface to input details for the branch',
//            
////            'onBeforeStart':function(){
////                $("#branch_btn").click()
////            }
//        },
//        {
//            'click #modal_div' : 'Fill the form correctly and click on the save button',
////            'trigger':'skip'
////            'timeout':3000
//        }
//    ];
//
//    //set script config
//    enjoyhint_instance.set(enjoyhint_script_steps);
//
//    //run Enjoyhint script
//    enjoyhint_instance.run();
</script>

<!-- Mirrored from appstack.bootlab.io/dashboard-default.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 26 Jul 2019 15:57:08 GMT -->
</html>