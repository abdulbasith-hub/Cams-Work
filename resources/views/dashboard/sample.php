
<!DOCTYPE html>
<html lang="en">
<head>








<!-- Below Code Added By Angad to Prevent Browser Back and Desable Offline Mode--> 
<!-- 
<script type="text/javascript">

function preventBack(){
	window.history.forward();
	}
setTimeout("preventBack()", 0);
window.onunload=function(){null;};

//Below Function for No-Cache 
	function burstCache() {
		if (!window.navigator.onLine) {
			document.body.innerHTML = 'Loading...';
			window.location = '/audit/';
		}
	}
</script> -->




<script>
if (!navigator.onLine) {
	 document.body.innerHTML = 'Loading...';
	 window.location = 'logout.htm';
	}
</script>	
<!-- 
<body onload="burstCache()" onpageshow="if (event.persisted) preventBack();">
	
</body>
-->


<!-- Basic Page Needs
  ================================================== -->
<meta charset="utf-8">

<title>AuditOnline-2</title>
<meta content="AuditOnline" name="description">
<meta content="AuditOnline" name="keywords">
<!-- Mobile Specific Metas
  ================================================== -->
<meta content="width=device-width, initial-scale=1.0" name="viewport">

<!-- Favicons
  ================================================== -->
<link rel="apple-touch-icon" sizes="57x57"
	href="/resources/homePage/0/images/favicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60"
	href="/resources/homePage/0/images/favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72"
	href="/resources/homePage/0/images/favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76"
	href="/resources/homePage/0/images/favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114"
	href="/resources/homePage/0/images/favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120"
	href="/resources/homePage/0/images/favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144"
	href="/resources/homePage/0/images/favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152"
	href="/resources/homePage/0/images/favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180"
	href="/resources/homePage/0/images/favicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"
	href="/resources/homePage/0/images/favicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32"
	href="/resources/homePage/0/images/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96"
	href="/resources/homePage/0/images/favicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16"
	href="/resources/homePage/0/images/favicon/favicon-16x16.png">
<link rel="manifest"
	href="/resources/homePage/0/images/favicon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage"
	content="/resources/homePage/0/images/favicon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">

<!-- Vendor CSS Files 
  ================================================== -->
<link
	href="/resources/homePage/0/vendor/bootstrap/css/bootstrap.min.css"
	rel="stylesheet">
<link
	href="/resources/homePage/0/vendor/icofont/icofont.min.css"
	rel="stylesheet">
<link
	href="/resources/homePage/0/vendor/boxicons/css/boxicons.min.css"
	rel="stylesheet">
<link
	href="/resources/homePage/0/vendor/venobox/venobox.css"
	rel="stylesheet">
<link
	href="/resources/homePage/0/vendor/owl.carousel/assets/owl.carousel.min.css"
	rel="stylesheet">
<link
	href="/resources/homePage/0/vendor/aos/aos.css"
	rel="stylesheet">
<link rel="stylesheet"
	href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Gugi&display=swap"
	rel="stylesheet">
<!-- <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700&display=swap" rel="stylesheet"> -->
<link
	href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap"
	rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Viga&display=swap"
	rel="stylesheet">
<!-- Custom CSS File 
  ================================================== -->
  <!-- Bhashini Plugin Start -->
<script src="https://translation-plugin.bhashini.co.in/v2/website_translation_utility.js" data-pos-x="5" data-pos-y="10" referer="www.auditonline.gov.in" referrerpolicy="strict-origin-when-cross-origin"></script>
  <!-- Bhashini Plugin End -->

<script type="text/javascript"
	src="/resources/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript"
	src="/resources/js/popper.js"></script>
<link
	href="/resources/homePage/0/css/style.css"
	rel="stylesheet">
<script src="/js/sha256.js"></script>
<script type='text/javascript' src='js/login.js'></script>
<script type="text/javascript">
	
	$(document).ready(function() {
		  $('.read-more').click(function() {
		    $(this).toggleClass('expanded');
		  });
		});

	
	
		var soltId = 'bdb630ebb56fad3ab707a0448f42436d';
		var popupStr='null';

	
</script>
<style type="text/css">
#read-more {
	max-height: 110px; /* Adjust the desired height */
	overflow: hidden;
}

.mrgin {
	margin-top: 20px;
}

.error {
	display: none;
	color: red;
}

.errormsg {
	color: red;
}

.divDisplay {
	display: none;
	color: red;
}

.tab {
	overflow: hidden;
	/* background-color: #f1f1f1; */
	background-color: #ccc;
	border: solid gray;
}

.tab button {
	background-color: inherit;
	float: left;
	border: none;
	/* outline: 0.1px dashed gray; */
	cursor: pointer;
	padding: 10px;
	transition: 0.3s;
	font-size: 17px;
}

.tab button:hover {
	background-color: #ddd;
}

.tab button.active {
	/* background-color: #ccc; */
	background-color: #979797;
	color: white;
}

.tabcontent {
	display: none;
	padding: 6px 12px;
	border: 1px solid #ccc;
	border-top: none;
}

table, table td {
	border: 1px solid #cccccc;
}

td, th {
	text-align: center;
	vertical-align: middle;
}

.field-name-field-quick-links {
	border-right: 1px dashed #3699da;
	border-bottom: 1px dashed #3699da;
	border-left: 1px dashed #3699da;
	border-top: 1px dashed #3699da;
}

.demo{
border:2px solid #3699da;

}

.demo1{
border-right: 2px solid #3699da;
	border-bottom: 2px solid #3699da;
	
	border-top: 2px solid #3699da;
}

.dottedline{
border-top: 2px  dashed #3699da;
margin-top:30px;
}

.read-more-button {
  animation: blink 1s infinite alternate;
  color: yellow;
}

.read-more-button:hover{

 

color: yellow;
					  
			
}

@keyframes blink {
  0% {
    opacity: 2;
 
 
 
  
 

  
  }
  100% {
    opacity: 0.5;
  }
}

.show-hide{
					
			  
													 
 

color: yellow;
			 
					
		
		   
			
																
			  
}
 .col-divided {
      position: relative;
      height: 50px;
      border: 1px solid #000; /* Just for visualization */
    }

    .col-divided::before {
      content: "";
      position: absolute;
      top: 0;
      left: 50%;
      width: 1px;
      border-left: 2px dotted #ece9e9; /* Set the border to dotted */
      height: 100%;
    }
    .field field-name-field-quick-link-copy field-type-text-long field-label-hidden dottedline{
    
    margin-bottom:20px;
    }
    
    
    .upperdiv{
    margin-bottom:10px;
    }
@media (max-width: 425px) {
            /* Styles for h5 */
            .uptobutton {
                font-size: 17px; 
              /* Adjust the font size as needed */
            }
        }

		   
					 
 

							 
					
			  
				  
									  
  
 
</style>
</head>
<input type="hidden" id="action" value="welcome.do" />
<body onload="document.getElementById('defaultOpen').click();"
	data-ng-app="centerLevelModule"
	data-ng-controller="centerLevelController">
	<!-- ======= Top Bar ======= -->
	<section id="topbar" class="d-lg-block">
		<div class="container">
			<div class="row">
				<div class="col-12 col-md-6 col-lg-6 order-2 order-md-1">
					<a href="https://india.gov.in" class="btn text-white"
						target="_blank" title="External Link - Government of India">Government of India
					</a> <a href="https://panchayat.gov.in" target="_blank"
						class="btn font-orange"
						title="External Link - Ministry of Panchayati Raj">Ministry of Panchayati Raj </a>
				</div>
				<div
					class="col-12 col-md-6 col-lg-6  order-1 order-md-2 accessibility-sec text-left text-lg-right">

					<div class="btn-group">
						<div class="dropdown">
							<button class="btn text-white btn-sm dropdown-toggle"
								type="button" id="dropdownMenuButton" data-toggle="dropdown"
								aria-haspopup="true" aria-expanded="false">Languages</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								
									<a class="dropdown-item"
										href="javascript:changeLanguage('en')">
										English
									</a>
								
									<a class="dropdown-item"
										href="javascript:changeLanguage('hi')">
										Hindi
									</a>
								
							</div>
						</div>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-sm text-white decrease">A-</button>
						<button type="button" class="btn btn-sm text-white reset">A</button>
						<button type="button" class="btn btn-sm text-white increase">A+</button>

					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="top-header">
		<!-- ======= Header ======= -->
		<header id="header" style="background: #ffffff; height: 77px;">
			<div class="container-fluid">
				<div class="row">
					<div class="col-12 col-md-6">
						<div class="logo mr-auto">
							<h1>
								<a href="#"> <img
									src="/resources/homePage/0/images/emblem.png" title="Emblem"
									alt="Emblem" class="img-fluid"> <span
									class="px-3 position-relative">AuditOnline</span>
								</a> <img class="img-fluid float-right g-20"
									src="/resources/homePage/0/images/g20-logo.png" title="G20"
									alt="G20 Logo">
							</h1>
						</div>
					</div>

					<div class="col-12 col-md-6">
						<nav class="nav-menu navbar-expand-lg d-none d-lg-block mt-lg-4">
							<ul class="float-lg-right">
								<li class="nav-item active"><a
									class="nav-link font-weight-bold" href="welcome.do"> HOME</a></li>
								<li class="nav-item"><a class="nav-link font-weight-bold"
									href="#about">About Us</a></li>
								<li class="nav-item"><a class="nav-link font-weight-bold"
									href="#kpis">DashBoard</a></li>
								<li class="nav-item"><a class="nav-link font-weight-bold"
									href="#features">FEATURES</a></li>
								<li class="nav-item"><a class="nav-link font-weight-bold"
									href="#hero">DOWNLOAD</a></li>
								<li class="nav-item"><a class="nav-link font-weight-bold"
									data-target="#faqmodal" data-toggle="modal" href="#">FAQs</a></li>
								<li class="nav-item"><a class="nav-link font-weight-bold"
									href="#reports">Reports</a></li>

								<li class="nav-item"><a href="#contact"
									class="nav-link login-btn shadow font-weight-bold text-white"
									data-toggle="modal" data-target="#LoginModal"> <i
										class="fa fa-user-circle text-white mr-2"></i> Login</a></li>
							</ul>
						</nav>
						<!-- .nav-menu -->
					</div>
				</div>
			</div>
		</header>
	</section>


	<section class="mainhead pb-3">
		<div class="container" id="about">
			<div class="row">
				<div class="col-12 col-lg-6 order-2 order-lg-1">
					<div class="aboutAudit mt-5 mb-2">
						<h1
							class="font-weight-bold font-gradient font-gugi animate__animated animate__pulse">
							Facilitating Audit In  Government
						</h1>
						<p
							class="text-justify text-white pt-4 font-weight-bold letter-spacing-1"
							id="read-more">
							<strong>AuditOnline</strong>
							is a configurable platform enabling Government entities to facilitate their internal and external audits and to comply with the Comptroller and Auditor General of India&rsquo;s (CAG) defined standards and guidelines. It aids effective auditing by ensuring tracking &amp; monitoring of end-to-end auditing process including follow-up of audit observations, audit paras and action taken on audit paras It has significantly simplified scheme-based audit of Panchayats&#39; accounts at all three levels (District, Block, and Village). Envisaged to record details as per the state-specific audit manuals, along with the capabilities to serve the purpose of maintaining past audit records of the auditee with an associated list of the auditors assigned and the audit team involved in the act while setting out as an impeccable tool for Audit, improvising transparency &amp; accountability And since auditing is a critical business requirement hence AuditOnline&rsquo;s architectural aptness may be utilized to facilitate Urban Local Bodies (ULB) and Line department audits with a little alteration
						</p>
						<a href="#" title="Read More" data-toggle="modal"
							data-target="#aboutAudit" class="font-green">Read more<i
							class="fa fa-long-arrow-right"></i></a>

						<!-- <button class="btn btn-success btn-rounded-1 btn-sm"><i class="fa fa-arrow-circle-right"></i> Request for Demo</button> -->
					</div>
				</div>

				<div class="col-12 col-lg-6 order-1 order-lg-2">
					<div id="carouselExampleIndicators" class="carousel slide my-1 p-2"
						data-ride="carousel">

						<div class="carousel-inner">
							<div class="carousel-item active text-center">
								<img class="img-fluid mx-auto" alt="First slide"
									src="/resources/homePage/0/images/sliders/slider-1.png"
									style="max-height: 450px; margin: 0 auto">
							</div>
							<div class="carousel-item text-center">
								<img class="img-fluid mx-auto" alt="Second slide"
									src="/resources/homePage/0/images/sliders/slider-2.png"
									style="max-height: 450px; margin: 0 auto">
							</div>
							<div class="carousel-item text-center">
								<img class="img-fluid mx-auto" alt="Third slide"
									src="/resources/homePage/0/images/sliders/slider-3.png"
									style="max-height: 450px; margin: 0 auto">
							</div>
						</div>

						<a class="carousel-control-prev" href="#carouselExampleIndicators"
							role="button" data-slide="prev"> <span
							class="carousel-control-prev-icon" aria-hidden="true"></span> <span
							class="sr-only">Previous</span>
						</a> <a class="carousel-control-next"
							href="#carouselExampleIndicators" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">Next</span>
						</a>

					</div>

				</div>

			</div>

			<div class="row">

				<div class="table-responsive">
					<div class="kpi-sec conversion-auto px-2 px-md-0">
						<div class="col-12 text-right p-0 font-weight-500"
							style="margin-bottom: -5px;">
							<label for="year" class="control-label text-white">Select Period</label> <select
								id="year" data-ng-model="year"
								class="form-control form-control-sm border-0 ng-valid ng-not-empty ng-dirty ng-valid-parse ng-touched"
								htmlescape="true" data-ng-change="yearChange(year)"
								style="display: inline-block; width: auto;">
								
									
									
										<option value="2019-2020">2019-2020</option>
									
								
									
									
										<option value="2020-2021">2020-2021</option>
									
								
									
									
										<option value="2021-2022">2021-2022</option>
									
								
									
									
										<option value="2022-2023">2022-2023</option>
									
								
									
										<option value="2023-2024" selected="selected">2023-2024</option>
									
									
								
							</select>
						</div>

						<div id="wrapper-div"></div>
						<div class="wrapper" id="kpis">

							<div
								class="col-12 col-md-3 p-2 p-md-3  field field-name-field-quick-links field-type-field-collection field-label-hidden">
								<div class="kpi-value">
																		<div class="zpdiv kpi-value-style">
										<a href="dpYearBookClosedetails.htm?year={{yearBook}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="dpTotalYearBookCount"></span>
											ZPs</a>
									</div>
									<div class="bpdiv kpi-value-style">
										<a href="bpYearBookClosedetails.htm?year={{yearBook}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="bpTotalYearBookCount"></span>
											BPs</a>
									</div>
									<div class="gpdiv  kpi-value-style">
										<a href="yearBookClosedetails.htm?year={{yearBook}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="totalYearBookCount"></span>
											GPs</a>
									</div>

								</div>

								<div class="contentfcid">
									<i class="fa fa-book fa-lg center-block bg-icon"></i>
									<div
										class="field field-name-field-quick-link-title field-type-text field-label-hidden">
										Year Book Closed
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden">
										<h2 class="counter animate__animated animate__bounceIn"><span data-ng-bind="totalYearBook"></span></h2>

 										<a href="yearBookNotClosedDetails.htm?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25" target="_blank"
										title="Year Book Not Closed" target="_parent" class="text-success">Year Book Not Closed <i
								   
										class="fa fa-long-arrow-right fa-1x"></i></a>
									</div>
									<!--  <a href="yearBookClosedetails.htm?year={{yearBook}}" target="_blank" title="No. of Gram Panchayats with Year Book Closed Details" target="_parent">Details <i class="fa fa-long-arrow-right fa-1x"></i></a> -->
								</div>
							</div>
							<div
								class="col-12 col-md-3 p-2 p-md-3  field field-name-field-quick-links field-type-field-collection field-label-hidden">

								<div class="contentfcid">
									<i class="fa fa-users fa-lg center-block bg-icon"></i>
									<div
										class="field field-name-field-quick-link-title field-type-text field-label-hidden">
										Enlisted Auditors
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden">
										<h2 class="counter animate__animated animate__bounceIn"><span data-ng-bind="auditorCount"></span></h2>
									</div>
									<a href="auditordetails.htm?year={{year}}" target="_blank"
										title="No. of Registered Auditors" target="_parent">Details <i
										class="fa fa-long-arrow-right fa-1x"></i></a>
								</div>
							</div>
							<div
								class="col-12 col-md-3 p-2 p-md-3  field field-name-field-quick-links field-type-field-collection field-label-hidden">
								<div class="kpi-value">
									<div class="zpdiv kpi-value-style">
										<a href="districtAuditeeRegistered.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="dpAuditeeRegisterCount"></span>
											ZPs</a>
										<!-- Loader -->
										<!-- <div class="spinner-border spinner-border-sm" role="status">
  										<span class="sr-only">Loading...</span>
								</div> -->
									</div>
									<div class="bpdiv kpi-value-style">
										<a href="blockAuditeeRegistered.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="bpAuditeeRegisterCount"></span>
											BPs</a>
									</div>
									<div class="gpdiv  kpi-value-style">
										<a href="auditeeRegistered.htm?year={{year}}" target="_parent"
											title="Click here for Details"><span data-ng-bind="auditeeRegisterCount"></span>
											GPs</a>
									</div>
									<!-- <div class="gpdiv  kpi-value-style">
										<a target="_parent" title="Click here for Details">{{ldReportCount}}
											Ldept</a>
									</div> -->
								</div>

								<div class="contentfcid">
									<i class="fa fa-users fa-lg center-block bg-icon"></i>
									<div
										class="field field-name-field-quick-link-title field-type-text field-label-hidden">
										Enlisted Auditees
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden">
										<h2 class="counter animate__animated animate__bounceIn"><span data-ng-bind="totalAuditeeRegisterCount"></span></h2>
									</div>
									<!-- <a href="auditeeRegistered.htm?year={{year}}" target="_blank" title="No. of Registered Auditees" target="_parent">Details <i class="fa fa-long-arrow-right fa-1x"></i></a> -->
								</div>
							</div>
							<div
								class="col-12 col-md-3 p-2 p-md-3  field field-name-field-quick-links field-type-field-collection field-label-hidden">
								<div class="kpi-value">
									<div class="zpdiv kpi-value-style">
										<a href="districtAuditeePlandetails.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="dpAuditeeCount"></span>
											ZPs</a>
									</div>
									<div class="bpdiv kpi-value-style">
										<a href="blockAuditeePlandetails.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="bpAuditeeCount"></span>
											BPs</a>
									</div>
									<div class="gpdiv  kpi-value-style">
										<a href="auditeePlandetails.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="bpAuditeeCount"></span>
											GPs</a>
									</div>
									<!-- <div class="gpdiv  kpi-value-style">
										<a target="_parent" title="Click here for Details">{{ldAuditeeCount}}
											Ldept</a>
									</div> -->

								</div>

								<div class="contentfcid">
									<i class="fa fa-file fa-lg center-block bg-icon"></i>
									<div
										class="field field-name-field-quick-link-title field-type-text field-label-hidden">
										Audit Plans Prepared
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden">
										<h2 class="counter animate__animated animate__bounceIn"><span data-ng-bind="totalAuditeeCount"></span></h2>
									</div>
									<!--  <a href="auditeePlandetails.htm?year={{year}}" target="_blank" title="No. of Registered Auditees" target="_parent">Details <i class="fa fa-long-arrow-right fa-1x"></i></a> -->
								</div>
							</div>
							<div
								class="col-12 col-md-3 p-2 p-md-3  field field-name-field-quick-links field-type-field-collection field-label-hidden">

								<div class="kpi-value">
									<div class="zpdiv kpi-value-style">
										<a href="districtObservationCountReport.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="dpObservationCount"></span>
											ZPs</a>
									</div>
									<div class="bpdiv kpi-value-style">
										<a href="blockObservationCountReport.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="bpObservationCount"></span>
											BPs</a>
									</div>
									<div class="gpdiv  kpi-value-style">
										<a href="observationCountReport.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="observationCount"></span>
											GPs</a>
									</div>
									<!-- <div class="gpdiv  kpi-value-style">
										<a target="_parent" title="Click here for Details">{{ldObsCount}}
											Ldept</a>
									</div> -->
								</div>
								<div class="contentfcid">
									<i class="fa fa-database fa-lg center-block bg-icon"></i>
									<div
										class="col-md-10 field field-name-field-quick-link-title field-type-text field-label-hidden">
										No. of Observations Recorded
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden">
										<h2 class="counter animate__animated animate__bounceIn"><span data-ng-bind="totalObservationCount"></span></h2>
									</div>
									<!-- <a href="observationCountReport.htm?year={{year}}" target="_blank" title="No. of Registered Auditees" target="_parent">Details <i class="fa fa-long-arrow-right fa-1x"></i></a> -->
								</div>
							</div>
							<div
								class="col-12 col-md-3 p-2 p-md-3  field field-name-field-quick-links field-type-field-collection field-label-hidden">
								<div class="kpi-value">
									<div class="zpdiv kpi-value-style">
										<a href="dpWithAuditReportGeneratedReport.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="dpWithReportsGeneratedCount"></span>
											ZPs</a>
									</div>
									<div class="bpdiv kpi-value-style">
										<a href="bpWithAuditReportGeneratedReport.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="bpWithReportsGeneratedCount"></span>
											BPs</a>
									</div>
									<div class="gpdiv  kpi-value-style">
										<a href="gpWithAuditReportGeneratedReport.htm?year={{year}}"
											target="_parent" title="Click here for Details"><span data-ng-bind="gpWithReportsGeneratedCount"></span>
											GPs</a>
									</div>
									<!-- <div class="gpdiv  kpi-value-style">
										<a target="_parent" title="Click here for Details">{{ldPlanCreated}}
											Ldept</a>
									</div> -->
								</div>

								<div class="contentfcid">
									<i class="fa fa-file fa-lg center-block bg-icon"></i>
									<div
										class="field field-name-field-quick-link-title field-type-text field-label-hidden">
										No. of Audit Report Generated
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden">
										<h2 class="counter animate__animated animate__bounceIn"><span data-ng-bind="totalReportsGeneratedCount"></span></h2>
									</div>
									<!-- <a href="gpWithAuditReportGeneratedReport.htm?year={{year}}" target="_blank" title="No. of Registered Auditees" target="_parent">Details <i class="fa fa-long-arrow-right fa-1x"></i></a> -->
								</div>
							</div>
<!-- #################################### settlement Report And Action Taken Count Report start ################################################################################### -->
							<div
								class="col-12 col-md-3 p-2 p-md-3  field field-name-field-quick-links field-type-field-collection field-label-hidden">
								<div class="kpi-value" data-ng-if="year >= '2022-2023'">

									<div class="zpdiv kpi-value-style">
										<a href="settlementReport.htm?year={{year}}&level=Z"
											target="_parent" title="Click here for Details"><span data-ng-bind="dpWiseSettlementCount"></span> ZPs
										</a>
									</div>
									<div class="bpdiv kpi-value-style">
										<a href="settlementReport.htm?year={{year}}&level=B"
											target="_parent" title="Click here for Details"><span data-ng-bind="bpWiseSettlementCount"></span>
											BPs</a>
									</div>
									<div class="gpdiv  kpi-value-style">
										<a href="settlementReport.htm?year={{year}}&level=G"
											target="_parent" title="Click here for Details"><span data-ng-bind="gpWiseSettlementCount"></span>
											GPs</a>
									</div>


								</div>
								<div class="kpi-value" data-ng-if="year < '2022-2023'">
					
										<div class="zpdiv kpi-value-style">
											<a href="#"
												target="_parent" title="Details 2022-2023 onwards">0
												ZPs</a>
										</div>
										<div class="bpdiv kpi-value-style">
											<a href="#"
												target="_parent" title="Details 2022-2023 onwards">0
												BPs</a>
										</div>
										<div class="gpdiv  kpi-value-style">
											<a href="#"
												target="_parent" title="Details 2022-2023 onwards">0
												GPs</a>
										</div>
									
								
								</div>

								<div class="contentfcid">
									<i class="fa fa-file fa-lg center-block bg-icon"></i>
									<div
										class="field field-name-field-quick-link-title field-type-text field-label-hidden">
														   
										No.of Settlement Report Generated<img src="resources/image/new_red.gif">
												 
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden"
										data-ng-if="year >= '2022-2023'">
										<h2 class="counter animate__animated animate__bounceIn"><span data-ng-bind="totalSettlementCount"></span></h2>
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden"
										data-ng-if="year < '2022-2023'">
										<h2 class="counter animate__animated animate__bounceIn">0</h2>
									</div>
									<!-- <a href="gpWithAuditReportGeneratedReport.htm?year={{year}}" target="_blank" title="No. of Registered Auditees" target="_parent">Details <i class="fa fa-long-arrow-right fa-1x"></i></a> -->
								</div>
							</div>
							<div
								class="col-12 col-md-3 p-2 p-md-3  field field-name-field-quick-links field-type-field-collection field-label-hidden">
								<div class="kpi-value" data-ng-if="year >= '2022-2023'">
									<div class="zpdiv kpi-value-style">
										<a
											href="actionTakenSettlementLevelWise.htm?year={{year}}&level=Z"
											target="_parent" title="Click here for Details"><span data-ng-bind="dpActionTakenCount"></span>
											ZPs</a>
									</div>
									<div class="bpdiv kpi-value-style">
										<a
											href="actionTakenSettlementLevelWise.htm?year={{year}}&level=B"
											target="_parent" title="Click here for Details"><span data-ng-bind="bpActionTakenCount"></span>
											BPs</a>
									</div>
									<div class="gpdiv  kpi-value-style">
										<a
											href="actionTakenSettlementLevelWise.htm?year={{year}}&level=G"
											target="_parent" title="Click here for Details"><span data-ng-bind="gpActionTakenCount"></span>
											GPs</a>
									</div>
								</div>
								<div class="kpi-value" data-ng-if="year < '2022-2023'">
									<div class="zpdiv kpi-value-style">
										<a href="#"
											target="_parent" title="Details 2022-2023 onwards">0 
											ZPs</a>
									</div>
									<div class="bpdiv kpi-value-style">
										<a href="#"
											target="_parent" title="Details 2022-2023 onwards">0 
											BPs</a>
									</div>
									<div class="gpdiv  kpi-value-style">
										<a href="#"
											target="_parent" title="Details 2022-2023 onwards">0 
											GPs</a>
									</div>
								</div>

								<div class="contentfcid">
									<i class="fa fa-file fa-lg center-block bg-icon"></i>
									<div
										class="field field-name-field-quick-link-title field-type-text field-label-hidden">
										Action Taken Report<img src="resources/image/new_red.gif">
												 
									</div>
									<div
										class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden"
										data-ng-if="year >= '2022-2023'">
										<h2 class="counter animate__animated animate__bounceIn"><span data-ng-bind="totalActionTakenCount"></span></h2>

										<a href="actionTakenReport.htm" target="_blank"
										title="ATR Status Report" target="_parent" class="text-success">ATR Status <i
										class="fa fa-long-arrow-right fa-1x"></i></a>
									</div>
			 
									<div class="field field-name-field-quick-link-copy field-type-text-long field-label-hidden" data-ng-if="year < '2022-2023'">
										  
										<h2 class="counter animate__animated animate__bounceIn">0</h2>
									</div>
									<!-- <a href="gpWithAuditReportGeneratedReport.htm?year={{year}}" target="_blank" title="No. of Registered Auditees" target="_parent">Details <i class="fa fa-long-arrow-right fa-1x"></i></a> -->
								</div>
							</div>
<!-- ################################### settlement Report And Action Taken Count Report end ####################################################################################-->
						</div>
						<br>
						<div></div>
				  


					</div>


				</div>



			</div>
		</div>
	</section>
	<!-- ======= Services Section ======= -->
 <main id="main">
	<section id="configuration" class="py-1 pt-2">
		<div class="container">
			
			
			
			
		</div>
		<br>








		<div class="container">

			<div class="work-process-list animate__animated animate__slideInLeft">
				<div class="row p-md-0">
					<div class="col-12 p-md-0">
						<h4
							class="py-2 mb-0 font-weight-bold font-blue font-gugi d-flex align-items-center heading-color-blue animate__animated animate__slideInLeft">
							Easy Steps to Configure AuditOnline
						</h4>
					</div>
				</div>
				<ul class="row px-md-0 pb-0 mb-0 p-0 pr-5 pr-md-0">
					<li class="col-12 col-md-2 p-1"><span class="font-weight-500"
						style="background-color: #7ab435;"> Define Configuration by specifying Auditor and Auditee and Plan Period (Internal/External) <a class="fa fa-long-arrow-right"
							style="border: 6px solid #8fda36;">1</a></span></li>

					<li class="col-12 col-md-2 p-1"><span class="font-weight-500"
						style="background-color: #b48735;"> Define Process Flow by specifying all tasks and its Mapping <a class="fa fa-long-arrow-right"
							style="border: 6px solid #daa436;">2</a></span></li>

					<li class="col-12 col-md-2 p-1"><span class="font-weight-500"
						style="background-color: #bd5876;"> Define Category/Sub-Category and Dynamic Form (Case Sheet/Fact Sheet) <a class="fa fa-long-arrow-right"
							style="border: 6px solid #ffa2be;">3</a></span></li>

					<li class="col-12 col-md-2 p-1"><span class="font-weight-500"
						style="background-color: #1e9da7;"> Define Report Template for generation of Audit Report <a class="fa fa-long-arrow-right"
							style="border: 6px solid #36ceda;">4</a></span></li>

					<li class="col-12 col-md-2 p-1"><span class="font-weight-500"
						style="background-color: #cdb628;"> Constitute Audit team and Assign team to Auditee <a class="fa fa-long-arrow-right"
							style="border: 6px solid #ffdd0f;">5</a></span></li>

					<li class="col-12 col-md-2 p-3 mt-md-1 text-center">

						<p class="heading-color-blue font-weight-bold"
							style="font-size: 18px">
							Ready to Use
						</p> <img
						src="/resources/homePage/0/images/check.png"
						class="img-fluid title-img"
						style="max-width: 50%; margin: 0 auto;">
					</li>
				</ul>


			</div>
			
		</div>
	</section>
	<section id="features" class="py-4">
		<div class="container">

			<div class="row">
				<div class="col-12 ">
					<h4
						class="pt-1 font-weight-bold font-blue font-gugi text-white text-shadow mb-lg-4">
						Featutres of Audit Online Ensuring Transparency Accountability 
						<i class="fa fa-long-arrow-right"></i>

					</h4>

				</div>
			</div>
			<div class="row row-bg custom-col">
				<div class="col-lg-3 col-md-6 mt-2"
					style="background-color: rgb(127 255 212/ 59%); box-shadow: 2px 6px 9px 1px #0000004f;">
					<div class="icon-box">
						<div class="icon">
							<i class="fa fa-cog fa-2x"></i>
						</div>
						<h6 class="title">
							<a href="#">Fully Configurable</a>
						</h6>
						<p class="description mb-1">
							Fully Configurable Desc Configurable for both Internal/External Audit of PRIs, ULB and Line Department.
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mt-2 p-2"
					style="background-color: #6495ed8f; box-shadow: 2px 6px 9px 1px #0000004f;">
					<div class="icon-box">
						<div class="icon">
							<i class="fa fa-sitemap fa-2x" aria-hidden="true"></i>
						</div>
						<h6 class="title">
							<a href="#">Workflow Enabled</a>
						</h6>
						<p class="description mb-1">
							Allows any type of work flow to be configured. Seamlessly maps the processes of Auditor/ Auditee and between Auditor and Auditee.
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mt-2 p-2"
					style="background-color: #d21e76a1; box-shadow: 2px 6px 9px 1px #0000004f;">
					<div class="icon-box">
						<div class="icon">
							<i class="fa fa-random fa-2x"></i>
						</div>
						<h6 class="title">
							<a href="#">Categories/Sub Categories</a>
						</h6>
						<p class="description mb-1">
							Enables qualitative tags for audit observations into categories/sub categories
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mt-2 p-2"
					style="background-color: #ff8f02a8; box-shadow: 2px 6px 9px 1px #0000004f;">
					<div class="icon-box">
						<div class="icon">
							<i class="fa fa-file-text fa-2x"></i>
						</div>
						<h6 class="title">
							<a href="#">Dynamic Form Designer</a>
						</h6>
						<p class="description mb-1">
							Dynamic Form Designer allows designing the forms for Case Record and Fact Sheet easily and dynamically keying in the formulas and necessary fields with required validations.
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mt-2 p-2"
					style="background-color: #c2b546c4; box-shadow: 2px 6px 9px 1px #0000004f;">
					<div class="icon-box">
						<div class="icon">
							<i class="fa fa-tachometer fa-2x"></i>
						</div>
						<h6 class="title">
							<a href="#">Customizable Reports</a>
						</h6>
						<p class="description mb-1">
							Allows generation and download of various graphical reports in PDF and Excel formats for easy analysis and monitoring based on customizable Report Templates.
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mt-2 p-2"
					style="background-color: #99cf54bf; box-shadow: 2px 6px 9px 1px #0000004f;">
					<div class="icon-box">
						<div class="icon">
							<i class="fa fa-exclamation-circle fa-2x"></i>
						</div>
						<h6 class="title">
							<a href="#">Notification Designer</a>
						</h6>
						<p class="description mb-1">
							Get notified for everything you receive in AuditOnline via Email/SMS/ System based alerts by configuring the notifications at various trigger points.
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mt-2 p-2"
					style="background-color: #90e4ffa8; box-shadow: 2px 6px 9px 1px #0000004f;">
					<div class="icon-box">
						<div class="icon">
							<i class="fa fa-clock-o fa-2x"></i>
						</div>
						<h6 class="title">
							<a href="#">Audit Team and Audit Schedule</a>
						</h6>
						<p class="description mb-1">
							Enables easy constitution and managing of audit teams and preparing the audit schedule by assigning team to an Auditee.
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mt-2 p-2"
					style="background-color: #798ef7a1; box-shadow: 2px 6px 9px 1px #0000004f;">
					<div class="icon-box">
						<div class="icon">
							<i class="fa fa-bullhorn fa-2x"></i>
						</div>
						<h6 class="title">
							<a href="#">Easy Communication and Follow-up</a>
						</h6>
						<p class="description mb-1">
							Captures complete process of the audit and enables reply and follow up instantaneously and amenable for analysis and monitoring.
						</p>
					</div>
				</div>
			</div>

		</div>
	</section>
	<!-- End Services Section --> <!-- ======= Hero Section ======= -->
	<section id="hero"
		class="d-flex flex-column justify-content-center align-items-center">
		<div class="container">
			<div class="row mb-5 pt-4">
				<div class="col-lg-4 col-md-6 col-sm-12">
					<h5
						class="pt-1 font-weight-bold font-blue font-gugi text-white text-shadow mb-lg-4">
						On Boarded Institution/Users
					</h5>
					<ul class="list-style-none m-0 p-0">
						<li class="my-2"><i
							class="fa fa-check-square text-warning shadow fa-1x mr-2"></i> <h8
								class="text-white"> Ministry of Rural Development</h8></li>
						<li class="my-2"><i
							class="fa fa-check-square text-warning shadow fa-1x mr-2"></i> <h8
								class="text-white"> RLB-Gram, Block, District and equivalent Panchayat</h8></li>

					</ul>
				</div>

				<div class="col-lg-4 col-md-6 col-sm-12">

					<h5
						class="pt-1 font-weight-bold font-blue font-gugi text-white text-shadow mb-lg-4">
						Citizen Section
					</h5>
					<ul class="list-style-none m-0 p-0">
						<li class="my-2"><i
							class="fa fa-check-square text-warning shadow fa-1x mr-2"></i> <a
							class="text-white"
							href="assignPanchWithAuditSchedule1.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">Audit Scheduling</a>
						</li>
						<li class="my-2"><i
							class="fa fa-check-square text-warning shadow fa-1x mr-2"></i> <a
							class="text-white"
							href="auditTeamDetailsReport.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">Constituted Audit Teams</a></li>
						<li class="my-2"><i
							class="fa fa-check-square text-warning shadow fa-1x mr-2"></i> <a
							class="text-white"
							href="observationAnalysis.htm?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">Analysis of Observations</a>
						</li>
						
						
						<li class="my-2"><i
							class="fa fa-check-square text-warning shadow fa-1x mr-2"></i> <a
							class="text-white"
							href="centreLevelReport.htm?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">Year Book Closed And Audit Reports Generated (Consolidated)</a></li>

					</ul>
				</div>

				<div class="col-lg-4 col-md-6 col-sm-12">
					<h5
						class="pt-1 font-weight-bold font-blue font-gugi text-white text-shadow mb-lg-4">
						Supporting Documents
					</h5>
					<ul class="list-style-none m-0 p-0">




						<li class="my-2"><i
							class="fa fa-file-pdf-o text-warning shadow fa-1x mr-2"></i> <a
							class="text-white"
							href="AuditIntroductionPPT.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">AuditOnline Introduction (0.53 MB)</a></li>
						<li class="my-2"><i
							class="fa fa-file-pdf-o text-warning shadow fa-1x mr-2"></i> <a
							class="text-white"
							href="AuditDetailedPPT.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">AuditOnline Detailed Presentation
								(3.55 MB)</a></li>
						<li class="my-2"><i
							class="fa fa-file-pdf-o text-warning shadow fa-1x mr-2"></i> <a
							class="text-white"
							href="priaaaUserManual.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">User Manual (7.6 MB)</a></li>
						<li class="my-2"><i
							class="fa fa-file-video-o text-warning shadow fa-1x mr-2"></i> <a
							class="text-white" href="downloadTrainingVideo.htm">Training Videos</a></li>
						<!-- <div class="col-lg-4 col-md-6 col-sm-12" style="display:block">
									<ul> -->
						<li class="my-2"><i
							class="fa fa-file-pdf-o text-warning shadow fa-1x mr-2"></i> <a
							class="text-white" href="downloadSupportingDocument.htm">SOPs (4 MB)</a></li>
							  
						<li class="my-2"><button class="read-more-button btn btn-link"
								id="showMore" onclick="return showMore();">Read more</button></li>
					</ul>
					<div id="hiddenLink" style="display: none">
						<ul class="list-style-none m-0 p-0">
							<li class="my-2"><i
								class="fa fa-file-excel-o text-warning shadow fa-1x mr-2"></i> <a
								class="text-white"
								href="ProcessFlowAndMappingTemplate.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">Process Flow And Mapping Template
									(3.55 MB)</a><img src="resources/image/new_red.gif"></li>

							<li class="my-2"><i
								class="fa fa-file-pdf-o text-warning shadow fa-1x mr-2"></i> <a
								class="text-white"
								href="RequestRequisitionModuleSOP.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">Request Requisition Module SOP (7.6 MB)</a><img src="resources/image/new_red.gif"></li>
											   
							<li class="my-2"><i
								class="fa fa-file-pdf-o text-warning shadow fa-1x mr-2"></i> <a
								class="text-white"
								href="SOPForTheATRFunctionality.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">SOP For The ATR Functionality (7.6 MB)</a><img src="resources/image/new_red.gif"></li>
											   

							<li class="my-2"><i
								class="fa fa-file-pdf-o text-warning shadow fa-1x mr-2"></i> <a
								class="text-white"
								href="TransferModuleDocument.do?OWASP_CSRFTOKEN=Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25">Transfer Module Document (7.6 MB)</a><img src="resources/image/new_red.gif"></li>
											   

							   
							<li class="my-2"><button class="read-more-button btn btn-link"
									id="showLess" onclick="return showLess();">Show Less</button></li>

						</ul>
						<!-- </ul></div> -->
					</div>
				</div>

			</div>

		</div>
		</div>
		</div>
	</section>
	<!-- End Hero --> </main>
	<!-- End #main -->

	<!-- ======= Footer ======= -->
	<footer id="footer">
		<div class="bg-dark-voilet">
			<div class="container-fluid">
				<div class="row">
					<div class="ad_images text-center mx-auto">
						<ul>
							<li><a href="http://www.panchayat.gov.in/"
								title="Panchayat Raj( External Site that opens in a new window)"
								target="_blank"> <img
									src="/resources/homePage/0/images/panchayati-raj.png"
									class="img-fluid" alt="panchayat Raj"></a></li>
							<li><a href="http://www.digitalindia.gov.in/"
								title="Digital India( External Site that opens in a new window)"
								target="_blank"> <img
									src="/resources/homePage/0/images/digital-india-logo.png"
									width="115" height="39" alt="digital india"></a></li>
							<li><a href="http://data.gov.in/"
								title="Data Portal (External Site that opens in a new window)"
								target="_blank"> <img
									src="/resources/homePage/0/images/data-gov-logo.png"
									width="140" height="40" alt="data"></a></li>
							<li><a href="http://india.gov.in/"
								title="NPI (External Site that opens in a new window)"
								target="_blank"> <img
									src="/resources/homePage/0/images/india-gov-logo.png"
									width="61" height="40" alt="NPI"></a></li>
							<li><a href="http://deity.gov.in/"
								title="DeitY (External Site that opens in a new window)"
								target="_blank"> <img
									src="/resources/homePage/0/images/Deity-logo.png"
									alt="DeitY"></a></li>
							<li><a href="http://pmindia.gov.in/"
								title="PM INDIA (External Site that opens in a new window)"
								target="_blank"> <img
									src="/resources/homePage/0/images/pm-india-logo.png"
									alt="pm india"></a></li>
							<li><a href="http://www.nic.in/"
								title="NIC (External Site that opens in a new window)"
								target="_blank"> <img
									src="/resources/homePage/0/images/niclogo8.png"
									alt="nic"></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<hr class="my-0" />
		<div class="container-fluid d-lg-flex py-2 bg-dark-voilet pb-4">
			<div class="col-xl-12 mr-lg-auto text-center text-lg-center">
				<ul
					class="navbar navbar-expand mb-1 text-center justify-content-center mx-auto">
					<li class="nav-item"><a class="nav-link text-white" href=""
						data-toggle="modal" data-target="#myModal2"> Privacy Policy</a></li>
					<li class="nav-item"><a class="nav-link text-white" href=""
						data-toggle="modal" data-target="#myModal5"> Web Policy</a></li>
					<li class="nav-item"><a class="nav-link text-white" href=""
						data-toggle="modal" data-target="#myModal1">Terms And Conditions</a></li>
					<li class="nav-item"><a class="nav-link text-white" href=""
						data-toggle="modal" data-target="#myModal3">Copyright</a></li>
					<li class="nav-item"><a class="nav-link text-white" href=""
						data-toggle="modal" data-target="#myModal4">Contact Us</a></li>
				</ul>
				<div class="copyright text-white">
					Content on this website is owned, updated and managed by the
					<a class="text-white" href="https://panchayat.gov.in"
						target="_blank">Ministry of Panchayati Raj</a>
					,
					Government of India
				</div>
				<div class="credits  text-white">
					Designed and Developed by
					<br> <a class="text-white" href="https://www.nic.in"
						target="_blank">National Informatics Centre</a>
				</div>
			</div>
		</div>
	</footer>
	<!-- End Footer -->

	<a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>



	<div class="modal" tabindex="-1" role="dialog" id="faqmodal">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						Frequently Asked Questions
					</h5>
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body faq">
					<div class="faq-list">
						<ul>
							<li><i class="bx bx-help-circle icon-help"></i> <a
								data-toggle="collapse" class="collapsed" href="#faq-list-1">What is the meaning of Audit Configuration?<i
									class="bx bx-chevron-down icon-show"></i><i
									class="bx bx-chevron-up icon-close"></i></a>
								<div id="faq-list-1" class="collapse " data-parent=".faq-list">
									<p>
										Audit Configuration marks the start of configuring the audit process in the online system. Audit Configuration specifies the Nature of Audit being performed such as Financial Audit, Scheme Audit etc, Plan Period i.e. Internal/External, the corresponding Auditor as well as Auditee Department, Observation Type (Receipt/Expenditure etc) and the Audit Stages which will be part of the Audit.
									</p>
								</div></li>
							<li><i class="bx bx-help-circle icon-help"></i> <a
								data-toggle="collapse" href="#faq-list-2" class="collapsed">What do you mean by Category And Subcategory? <i
									class="bx bx-chevron-down icon-show"></i><i
									class="bx bx-chevron-up icon-close"></i></a>
								<div id="faq-list-2" class="collapse" data-parent=".faq-list">
									<p>
										Category(s) and Sub Category(s) facilitate the Auditor in categorizing its observations. These are defined based on each Configuration. It is defined by the Department Admin of the Auditor and later used by the team of Auditors while recording the observations. Admin can specify as many category(s) as required.
									</p>
								</div></li>
							<li><i class="bx bx-help-circle icon-help"></i> <a
								data-toggle="collapse" href="#faq-list-3" class="collapsed">What is case record/Sheet? <i
									class="bx bx-chevron-down icon-show"></i><i
									class="bx bx-chevron-up icon-close"></i></a>
								<div id="faq-list-3" class="collapse" data-parent=".faq-list">
									<p>
										Case sheet basically include certain basic details about the file audited on which Auditor records the defaulters information before recording the observations. Case Sheet is defined Configuration wise and any number and type of fields can be included as part of Case Sheet. For instance, in Property Registration Audit, Auditor scrutinizes the file of Mr. Anuj Gupta. While auditing that file, there would be certain details related to that file like Registrant Name, Property No etc. which the auditor would record before recording the observations against that file.
									</p>
								</div></li>
							<li><i class="bx bx-help-circle icon-help"></i> <a
								data-toggle="collapse" href="#faq-list-4" class="collapsed">What is Fact sheet? <i
									class="bx bx-chevron-down icon-show"></i><i
									class="bx bx-chevron-up icon-close"></i></a>
								<div id="faq-list-4" class="collapse" data-parent=".faq-list">
									<p>
										Fact sheet basically include the fields on which Auditor records facts/figures for the observation raised as part of the Audit. Fact Sheet is usually defined against each category and sub category. For instance, the category is Taxes on Sales/VAT and Sub category is Short levy of tax due to turnover escaped assessment, the face sheet form could include fields like total turnover and Amount of Short levy, total amount etc.
									</p>
								</div></li>
							<li><i class="bx bx-help-circle icon-help"></i> <a
								data-toggle="collapse" href="#faq-list-5" class="collapsed">What do you mean by Process Flow?<i
									class="bx bx-chevron-down icon-show"></i><i
									class="bx bx-chevron-up icon-close"></i></a>
								<div id="faq-list-5" class="collapse" data-parent=".faq-list">
									<p>
										Process Flow is used to define the tasks which will be performed during the Audit process. While defining the process flow tasks, various details such as Task Name, Task For (Auditor/Auditee), Audit Stage to which the tasks belongs to, Task Type, Department Level and the Designation who will be performing the task will be defined. Tasks are defined configuration wise. Once the task(s) are defined, they are mapped, to create the flow for routing the audit process among various tasks. The similar process flow would be followed for each office location belonging to the Configuration.
									</p>
								</div></li>
							<li><i class="bx bx-help-circle icon-help"></i> <a
								data-toggle="collapse" href="#faq-list-6" class="collapsed">What do you mean by last audited detail?<i class="bx bx-chevron-down icon-show"></i><i
									class="bx bx-chevron-up icon-close"></i></a>
								<div id="faq-list-6" class="collapse" data-parent=".faq-list">
									<p>
										Last Audit Details facilitates specifying the details such as From-To Date, Supporting documents if any of the last Audit held for the particular Auditee, if the details are available. If the details are not available, then the user can simply specify that the details are not available. The Last Audit details are required to be specified only for the first time. However, for the next audit, the current audit details will become the Last Audit details.
									</p>
								</div></li>
							<li><i class="bx bx-help-circle icon-help"></i> <a
								data-toggle="collapse" href="#faq-list-7" class="collapsed">What do you mean by current audited detail?<i class="bx bx-chevron-down icon-show"></i><i
									class="bx bx-chevron-up icon-close"></i></a>
								<div id="faq-list-7" class="collapse" data-parent=".faq-list">
									<p>
										Current Audit Details facilitates specifying the details such as Current Audit From- To Date, Audit Schedule Start and End Date and assign a Audit team to the Auditee.Based on these schedules dates, the audit team would visit the respective Auditee office and perform an audit.
									</p>
								</div></li>
							<li><i class="bx bx-help-circle icon-help"></i> <a
								data-toggle="collapse" href="#faq-list-8" class="collapsed">What is the report template?<i class="bx bx-chevron-down icon-show"></i><i
									class="bx bx-chevron-up icon-close"></i></a>
								<div id="faq-list-8" class="collapse" data-parent=".faq-list">
									<p>
										There are various kind of reports that are generated during the Audit process such Audit Enquiry Report, Local Audit Report, Draft Note , Draft Para etc. Report Template is used to define a template for Auditor or Auditee specifying the format in which the particular report will be generated. While defining the Report template, details such as Configuration Name, Report Template for (Auditor/Auditee), Audit Stage, Whether Report Chapterization is Required, Annexure etc. is specified. The report once generated will be printed as per the defined template.
									</p>
								</div></li>
						</ul>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">
						Close
					</button>
				</div>
			</div>

		</div>
	</div>

	<div class="modal fade" id="myModal2" aria-hidden="true"
		style="display: none;">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						Privacy Policy
					</h5>
				</div>
				<div class="modal-body">
					<div class="row footer-row-padding footer-modal">
						<div class="col-sm-12">
							<h4>
								<i class="icon-trophy"></i>
								Introduction
							</h4>
							<p>
								Thanks for visiting website of AuditOnline, and reviewing our privacy policy. We collect no personal information, like names or addresses, when you visit our website. If you choose to provide that information to us, it is only used to fulfill your request for information. We do collect some technical information when you visit to make your visit seamless. The section below explains how we handle and collect technical information when you visit our website. Information Collected and Stored Automatically
							</p>
							<br>
							<h4>
								<i class="icon-key"></i>
								Summarizing our use of site visitors information, this collected data is used
								:
							</h4>
							<ul>
								<li>To ensure our site is relevant to your needs</li>
								<li>To help us create and publish content most relevant to you</li>
								<li>To allow you access to limited-entry areas of our site as appropriate</li>
							</ul>
							<br>
							<h4>
								<i class="icon-ribbon"></i>
								Storage, Control and Security
							</h4>
							<p>
								Data collected by auditonline.gov.in is stored in secure servers located in Delhi. Auditonline.gov.in has security procedures in place to protect the loss, misuse or alteration of information under its control. These security measures include the necessary protection to prevent, as far as possible, access to auditonline.gov.in databases to parties other than auditonline.gov.in. The utmost care is maintained to ensure your personal information is not at risk.
								<br> <br>
								By using this web site, you consent to collection, use and storage of your personal information by us in the manner described in this privacy statement and elsewhere on the web site. We reserve the right to occasionally update this privacy statement. The last updated date at the top of the privacy statement indicates the date of the document last revision.
							</p>
							<br>
							<h4>
								<i class="icon-envelope"></i>
								Contact Us
							</h4>
							<p>
								auditonline.gov.in welcomes your comments regarding this privacy statement. We also invite you to contact us, in case you have questions about this privacy statement.
							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger btn-empty-dark"
						data-dismiss="modal">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal1" role="dialog">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						Terms And Conditions
					</h5>
				</div>
				<div class="modal-body">
					<div class="row footer-row-padding footer-modal">
						<div class="col-sm-12">
							<p>
								This website is designed, developed and maintained by National Informatics Centre, Government of India.
							</p>
							<br>
							<p>
								Though all efforts have been made to ensure the accuracy and currency of the content on this website, the same should not be constructed as a statement of law or used for any legal purposes. In case of any ambiguity or doubts, users are advised to verify/check with the State Departments and/or other source(s), and to obtain appropriate professional advice. Under no circumstances will this Department/Ministry be liable for any expense, loss or damage including, without limitation, indirect or consequential loss or damage, or any expense, loss or damage whatsoever arising from use, or loss of use, of data, arising out of or in connection with the use of this website.
							</p>
							<br>
							<p>
								These terms and conditions shall be governed by and constructed in accordance with the Indian Laws. Any dispute arising under the terms and conditions shall be subject to the jurisdiction of the courts of India.
							</p>
							<br>
							<p>
								The information posted on this website could include hypertext links or pointers to information created and maintained by non-Government/private organizations. Ministry of Panchayati Raj is providing these links and pointers solely for your information and convenience. When you select a link to an outside website, you are leaving this website and are subject to the privacy and security policies of the owners/sponsors of the outside website.
							</p>
							<br>
							<p>
								Ministry of Panchayati Raj does not guarantee the availability of such linked pages at all times
							</p>
							<br>
							<p>
								Ministry of Panchayati Raj cannot authorize the use of copyrighted materials contained in linked websites. Users are advised to request such authorization from the owner of the linked website.
							</p>
							<br>
							<p>
								Ministry of Panchayati Raj does not guarantee that linked websites comply with Indian Government Websites Guidelines.
							</p>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger btn-empty-dark"
						data-dismiss="modal">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal3" role="dialog">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						Copyright Policy
					</h5>
				</div>
				<div class="modal-body">
					<div class="row footer-row-padding footer-modal">
						<div class="col-sm-12">
							<p>
								Material featured on this site may be reproduced free of charge in any format or media without requiring specific permission. This is subject to the material being reproduced accurately and not being used in a derogatory manner or in a misleading context. Where the material is being published or issued to others, the source must be prominently acknowledged. However, the permission to reproduce this material doesnt extend to any material on this site, which is explicitly identified as being the copyright of a third party. Authorization to reproduce such material must be obtained from the copyright holders concerned.
							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger btn-empty-dark"
						data-dismiss="modal">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="myModal4" role="dialog">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						Contact Us
					</h5>
				</div>
				<div class="modal-body">
					<div class="row footer-row-padding footer-modal">
						<div class="col-sm-12">
							<h4>
								Web Information Manager
							</h4>
							<p>
								Ministry of Panchayati Raj
							</p>
							<p>
								Government of India
							</p>
							<p>
								11th Floor J.P. Building
								,
							</p>
							<p>
								Kasturba Gandhi Marg, Connaught Place
								,
							</p>
							<p>
								New Delhi
								- 110001
							</p>
							<p id="supportemail">
								Support Email
								<a href="mailto:support-auditonline@nic.in?subject=Support"
									class="text-primary">: <span class="font-weight-bold">support[hyphen]auditonline[at]nic[dot]in</span></a>
							</p>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger btn-empty-dark"
						data-dismiss="modal">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>






	<div class="modal fade" id="myModal5" aria-hidden="true"
		style="display: none;">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						Web Policy
					</h5>
				</div>
				<div class="modal-body">
					<div class="row footer-row-padding footer-modal">
						<div class="col-sm-12">



		  
													  
							<p>The following terms and conditions will apply if you wish to use the Internet for availing a service. Please go through the conditions carefully and if you accept them, you may register yourself and transact on the site. On using this site for service delivery, you are deemed to have agreed to the terms and conditions set forth below. If You do not agree with all these terms and conditions, you must not transact on this Website.</p>
		   
		  
													   
							<p>If a user violates the terms and conditions Government (service owner) reserves the right to deactivate all such user registration and cancel any or all services requested without any notice. Garbage / Junk values in profile may lead to Deactivation.</p>
		   
		  
							<p>Operationalization of this agreement is subject to existing laws and legal processes of the respective Government, and nothing contained in this agreement is in derogation of Governments right to comply with law enforcement requests or requirements relating to your use of this Web Site or information provided to or gathered by this site with respect to such use. You agree that Government may provide details of your use of the Web Site to regulators or police or to any other third party, or in order to resolve disputes or complaints which relate to the Web Site, at Governments complete discretion.</p>
		   
		  
													   
							<p>Kasturba Gandhi Marg, Connaught Place,</p>
		   
		  
															   
							<p>This agreement is made between: respective service owner department of the respective government who has configured the serviceGovernment (Us) and The User (You), the individual, whose details are set out in the Portal User Creation page.</p>
		   
							<br>
		   
							<h4>Payment Option</h4>
			
		  
															
							<p>The list of payment options available are internet banking /debit card payment / credit card payment from banks that are listed when selecting each of the above options. Apart from the fee chargeable to Government against each service, bank / payment gateway transaction charges will be applicable extra. In case of a failed transaction the user shall have no right to claim the amount. The loss on this account shall not be borne either by Government or by the Banks /Payment Gateways.</p>
		   
							<br>
		   
							<h4>UserCreation</h4>
			
		  
							<p>You are responsible for maintaining the confidentiality of the password and account, and are fully responsible for all activities that occur under your password or account.</p>
		   
							<br>
		   
							<h4>Complaints Procedure</h4>
			
		  
							<p>You can reach us on the contact details given in the Contacts option given in the login page.</p>
		   
							<br>
		   
							<h4>General Obligations</h4>
			
		  
															  
							<p>You shall access web site only for lawful purposes and you shall be responsible for complying with all applicable laws, statutes and regulations in connection with the use of Government web site. This Website is for your personal or commercial use by approved kiosks. You shall not modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative works from, transfer or sell any information, or services obtained from this Website. You shall not create a hypertext link to the Website or frame the Website, except with the express advance written permission of the Government.</p>
		   
							<br>
		   
							<h4>Information Provided</h4>
			
		  
					   
															   
							<p>The information you provide in the Registration page must be complete and accurate. Government 00. reserves the right at all times to disclose any information as deems necessary to satisfy any applicable law, regulation, legal process.</p>
		   
							<br>
		   
							<h5>Termination</h5>
			
		  
												   
							<p>We may at any time at our sole discretion and without giving any reason or any prior notice terminate or temporarily suspend your access to all or any part of the web site.</p>
		   
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger btn-empty-dark"
						data-dismiss="modal">
						Close
					</button>
				</div>
			</div>

		</div>
	</div>















	<!-- Sign-In Modal Box-->
	<div class="modal fade" id="aboutAudit" role="dialog">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="AboutAuditOnline">
						About AuditOnline
					</h5>
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p class="text-justify">
						<strong>AuditOnline</strong>
						is a configurable platform enabling Government entities to facilitate their internal and external audits and to comply with the Comptroller and Auditor General of India&rsquo;s (CAG) defined standards and guidelines. It aids effective auditing by ensuring tracking &amp; monitoring of end-to-end auditing process including follow-up of audit observations, audit paras and action taken on audit paras It has significantly simplified scheme-based audit of Panchayats&#39; accounts at all three levels (District, Block, and Village). Envisaged to record details as per the state-specific audit manuals, along with the capabilities to serve the purpose of maintaining past audit records of the auditee with an associated list of the auditors assigned and the audit team involved in the act while setting out as an impeccable tool for Audit, improvising transparency &amp; accountability And since auditing is a critical business requirement hence AuditOnline&rsquo;s architectural aptness may be utilized to facilitate Urban Local Bodies (ULB) and Line department audits with a little alteration
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="LoginModal" role="dialog">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">
						Login
					</h5>
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="">
						<form id="loginForm" role="form" class="form" accept-charset="UTF-8" action="login.htm" method="post">
							<div class="form-group">
								<input type="hidden" name="OWASP_CSRFTOKEN"
									value="Z2TN-IYJM-TD96-FTLK-KB9H-CCS3-69OK-YU25" />
								<input id="hPwd" name="hPwd" type="hidden" value=""/>
								<label class="" for="username">Username</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fa fa-user"></i>
										</div>
									</div>
									<input id="userName" name="userName" onkeypress="validateOnFocus(&#39;userName&#39;);" placeholder="Username" class="form-control" type="text" value="" maxlength="100" autocomplete="off"/>
									<div class="invalid-feedback" id="userName_error">
										Error.Required
									</div>
									<div class="invalid-feedback" id="invalid_error">
										Error.InvalidLoginId
									</div>
									<div class="invalid-feedback">
										Enter Valid Username
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="" for="pwd">Password</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fa fa-lock"></i>
										</div>
									</div>
									<input id="userPassword" name="password" placeholder="Password" class="form-control" onfocus="validateOnFocus(&#39;userPassword&#39;);" type="password" value="" maxlength="100" autocomplete="off"/>
									<div class="invalid-feedback" id="userPassword_error">
										Error.Required
									</div>
									<div class="invalid-feedback" id="invalid_password_error">
										Invalid Login Id Password
									</div>
									<div class="invalid-feedback">
										Enter Password
									</div>
								</div>
							</div>
							<div class="">
								<img alt="Captcha" id="img_Capatcha" src="captchaImage"
									width="150" height="35"> <a href="#"
									onclick="javascript:refreshCaptcha()"
									class="btn btn-default btn-social-icon pointer"><i
									class="fa fa-refresh fa-spin fa-2x"></i> <!--<i class="icofont-refresh"></i>--></a>
							</div>
							<div class="form-group">
								<label for="captcha">CAPTCHA </label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fa fa-hand-o-right"></i>
										</div>
									</div>
									<input id="captchaAnswer" name="captchaAnswer" placeholder="CAPTCHA Answer" class="form-control" required="required" type="text" value="" maxlength="6" autocomplete="off"/>
									<div class="invalid-feedback" id="captchaAnswer_error">
										Error.Required
									</div>
									<div class="invalid-feedback" id="invalid_captchaAnswer_error">
										Captcha Wrong
									</div>
									<div class="invalid-feedback">
										Please fill out this field.
									</div>
								</div>
							</div>


							<div class="text-center">
								<button type="button" class="btn btn-primary mt-2"
									id="submitButton" onclick="validateUser(this)">
									<i class="fa fa-sign-in"></i>
									Login
								</button>

								<button type="button" class="btn btn-info mt-2"
									id="forget_password" onclick="callForgetPassword();">
									<i class="fa fa-key"></i>
									Forget Password
								</button>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>
	</div>
		
				
	<script>
	
/* 	function hideDataDiv(){
		
	
        var	dataDiv = document.getElementById("dataDiv");
   	 var dataDiv1 = document.getElementById("dataDiv1");
   	 var toggleButton = document.getElementById("toggleButton");
	 var saveButton = document.getElementById("saveButton");
	 
	$("#toggleButton").show();
		 saveButton.style.display="none"

		dataDiv.style.display="none";
		dataDiv1.style.display="block"
			$("#toggleButton1").hide();
		$("#saveButton1").show();
	}
	
	function hideDataDiv1(){
		
		 var	dataDiv = document.getElementById("dataDiv");
	   	 var dataDiv1 = document.getElementById("dataDiv1");
	   	 var toggleButton1 = document.getElementById("toggleButton1");
		 var saveButton1 = document.getElementById("saveButton1");
		 $("#toggleButton1").show();
		 saveButton1.style.display="none"

	   	dataDiv.style.display="block";
		dataDiv1.style.display="none"
			$("#toggleButton").hide();
		$("#saveButton").show();
		
	}
	
	function toggle(){
		
		$("#toggleButton").hide();
		$("#saveButton").show();
		$("#dataDiv").hide();
		$("#dataDiv1").hide();
	}
	
function toggle1(){
		
		$("#toggleButton1").hide();
		$("#saveButton1").show();
		$("#dataDiv").hide();
		$("#dataDiv1").hide();
	} */
	
	
	
	</script>
	<script type="text/javascript">
		/* function openTab(evt, Heading) {

		 var i, tabcontent, tablinks;
		 tabcontent = document.getElementsByClassName("tabcontent");
		 for (i = 0; i < tabcontent.length; i++) {
		 tabcontent[i].style.display = "none";
		 }
		 tablinks = document.getElementsByClassName("tablinks");
		 for (i = 0; i < tablinks.length; i++) {
		 tablinks[i].className = tablinks[i].className
		 .replace(" active", "");
		 }
		 document.getElementById(Heading).style.display = "block";
		 evt.currentTarget.className += " active";
		
		 } */

		/* function nextTab(id){
		 document.getElementById(id).click();
		 }
		 */
		$(document).ready(function() {
			$("#supportemail").attr("data-toggle", "tooltip");
			$("#supportemail").attr("data-placement", "top");
			$("#supportemail").attr("title", "support-auditonline@nic.in");
			document.getElementById('defaultOpen').click();
									 
		});
	</script>
	<script>
		function showMore() {
		
			document.getElementById("hiddenLink").style.display = "block"
			document.getElementById("showMore").style.display = "none"

		}

		function showLess() {

			document.getElementById("hiddenLink").style.display = "none"
			document.getElementById("showMore").style.display = "block"

		}
	</script>
</body>

</html>

<!-- Vendor JS Files -->
<script
	src="/resources/homePage/0/vendor/jquery/jquery.min.js"></script>
<script
	src="/resources/homePage/0/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script
	src="/resources/homePage/0/vendor/jquery.easing/jquery.easing.min.js"></script>
<script
	src="/resources/homePage/0/vendor/jquery-sticky/jquery.sticky.js"></script>
<script
	src="/resources/homePage/0/js/main.js"></script>
<script type='text/javascript'
	src='/priaaadwr/engine.js'></script>
<script type='text/javascript'
	src="/priaaadwr/util.js">
	
</script>
<script type='text/javascript'
	src='/priaaadwr/interface/defaultAdapter.js'></script>
<script type="text/javascript"
	src="/resources/reportComponents/angular.min.js"></script>
<script type="text/javascript"
	src="/resources/reportComponents/angular-route.min.js"></script>
<script type="text/javascript"
	src="/resources/reportComponents/echarts.min.js"></script>
<script type="text/javascript"
	src="/resources/reportComponents/component/centerLevel/centerLevel.controller.js"></script>
<script type="text/javascript"
	src="/resources/reportComponents/component/centerLevel/centerLevel.model.js"></script>
<script type="text/javascript"
	src="/resources/reportComponents/component/centerLevel/centerLevel.service.js"></script>
<script
	src="/resources/homePage/0/vendor/aos/aos.js"></script>
<link
	href="/resources/homePage/0/vendor/aos/aos.css"
	rel="stylesheet">
<script
	src="/resources/homePage/0/vendor/bootstrap/js/bs4pop.js"></script>
<link rel="stylesheet"
	href="/resources/homePage/0/vendor/bootstrap/css/bs4pop.css">
</script>
<!-- Notification js -->

<!-- Notification js -->













