<?php        
/***************************************************************
*  Project Name : Zinnfy
*  Created By : Ankush Sali
*  Created Date : 12-02-2019
*  Modification History :
*  Dated				Developer Name				Description
   12-02-2019		Jay Maisuriya					Used for Manage Business Details
	 23-07-2019		Jay Maisuriya					Add More Settings Email and SMS Templet At a Time of Create Business
	 23-07-2019		Jay Maisuriya					Email and SMS Templet At a Time of Create Business
	 24-07-2019		Jay Wankhede					Super Admin Profile Edit and update
	 05-08-2019		Jay Wankhede					Insert Business With Plan
***************************************************************/

defined("BASEPATH") OR exit("No direct script access allowed");

class Super_Admin extends CI_Controller {
	
	/* By Default Construct Function */
	function __construct() {
		parent::__construct();
		$this->load->model("Super_Admin_Model");
		$this->load->model("Plans_Model");
		$this->load->model("Payments_Model");
		$this->load->model("Setting_Model");
		$this->load->model("System_Model");
		$this->load->model("Labels_Model");
		$this->load->model("Mailchimp_Model");
	}
	
	public function index(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		
		$business_id = $this->session->userdata("business_id");
		$Today_Date = date("Y-m-d");
		
		$this->Pass_Data["view_page_path"] = "Super_Admin/Dashboard/index";
		$this->Pass_Data["view_page_title"] = "Super Admin";
		$this->Pass_Data["page_name"] = "Dashboard";
		$this->Pass_Data["Get_Count_All_Businesses"] = $this->System_Model->Get_Count_All_Businesses($business_id);		
		$this->Pass_Data["Get_Count_All_Customers"] = $this->System_Model->Get_Count_All_Customers($business_id);		
		$this->Pass_Data["Get_Count_Todays_All_Businesses"] = $this->System_Model->Get_Count_Todays_All_Businesses($business_id,$Today_Date);
		$this->Pass_Data["Get_Count_Todays_All_Customers"] = $this->System_Model->Get_Count_Todays_All_Customers($business_id,$Today_Date);
		
		$this->Pass_Data["Get_All_Activate_Plan"] = $this->System_Model->Get_All_Activate_Plan();
		
		$this->Pass_Data["Get_Count_All_Activate_Plan"] = count($this->Pass_Data["Get_All_Activate_Plan"]);
		$this->Pass_Data["Get_Count_Today_All_Activate_Plan"] = $this->System_Model->Get_Count_Today_All_Activate_Plan($Today_Date);

		$this->Pass_Data["Get_Count_Total_Earning"] = $this->System_Model->Get_Count_Total_Earning();
		$this->Pass_Data["Get_Count_Today_Earning"] = $this->System_Model->Get_Count_Today_Earning($Today_Date);
		
		$this->Pass_Data["zn_currency_symbol"] = $this->Setting_Model->Get_Option_Value("zn_currency_symbol",$business_id);
		
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}
	
	/*
	Description : Default My Business Details
	Created By : Ankush Sali			Created Date : 12-02-2019
	*/
	public function Businesses(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		
		$plans_All_Data = $this->Plans_Model->Get_All_Plans();
		$plans_Pass_Data = array();
		$this->Pass_Data["plans_All_Data"] = $plans_All_Data;
		
		$this->Pass_Data["view_page_path"] = "Super_Admin/My_Businesses/index";
		$this->Pass_Data["view_page_title"] = "My Businesses";
		$this->Pass_Data["page_name"] = "mybusiness";
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);

	}

	/*
	Description : Get All Business Details in DataTable
	Created By : Ankush Sali			Created Date : 12-02-2019
	*/
	public function Business_Datatable(){
		$Post_Data_Array = array();
		$Post_Data_Array = $this->input->post();
		
		$Pass_JSON_Array = array();
		$Total_Records = 0;
		
		$Serch_Value = $Post_Data_Array["search"]["value"];
		$Start = $Post_Data_Array["start"];
		$Length = $Post_Data_Array["length"];
		
		$business_id = $this->session->userdata("business_id");
		
		$All_Business_Detail_Result = $this->Super_Admin_Model->Get_All_Business_Details($business_id,$Start,$Length,$Serch_Value);
		$Total_Records = sizeof((array)$All_Business_Detail_Result);
		$counter = 1;
		
		foreach($All_Business_Detail_Result as $ABDR){
			$Business_Array = array();
			
			$Business_Array["counter"] = $counter++;
			$Business_Array["ID"] = $ABDR->ID;
			$Business_Array["user_email"] = $ABDR->user_email;
			$Business_Array["phone"] = $ABDR->phone;
			$Business_Array["full_name"] = $ABDR->full_name;
			$Business_Array["zip"] = $ABDR->zip;
			$Business_Array["address"] = $ABDR->address;
			$Business_Array["city"] = $ABDR->city;
			$Business_Array["state"] = $ABDR->state;
			$Business_Array["country"] = $ABDR->country;
			$Business_Array["notes"] = $ABDR->notes;
			$Business_Array["subdomain_name"] = $ABDR->subdomain_name;
			$Business_Array["status"] = $ABDR->status;
			$Business_Array["created_date"] = $ABDR->created_date;
			
			$Pass_JSON_Array[] = $Business_Array;
		}
		
		$json_data = array(
			"draw"            	=> intval($Post_Data_Array["draw"]),
			"recordsTotal"    	=> intval($Total_Records),  
			"recordsFiltered" 	=> intval($Total_Records),
			"data"            	=> $Pass_JSON_Array,
		);

		echo json_encode($json_data);		
	}
	
	/*
	Description : Change Business Status
	Created By : Ankush Sali			Created Date : 13-02-2019
	*/
	public function business_Status_Change(){
		$data_Update = array();
		$data_Update = $this->input->post();
		
		$business_id = $data_Update["business_id"];
		unset($data_Update["business_id"]);
		
		$query_Update_Result = $this->Super_Admin_Model->business_Status_Change($data_Update,$business_id);
		echo $query_Update_Result;
	}
	
	/*
	Description : Add New Business
	Created By : Ankush Sali			Created Date : 13-02-2019
	Edited By : Jay Maisuriya			Created Date : 23-07-2019
	*/
	public function insert_Business(){
		$data_Insert = array();
		$data_Insert = $this->input->post();
		
		$plan_ID = $data_Insert["plan_id"];
		unset($data_Insert["plan_id"]);

		/*mailchipmp subscription*/
			$mailchimp_status = $this->Setting_Model->Get_Option_Value("zn_mailchimp_status","1");
			$mailchimp_api_key = $this->Setting_Model->Get_Option_Value("zn_mailchimp_api_key","1");
			$mailchimp_list_id = $this->Setting_Model->Get_Option_Value("zn_mailchimp_list_id","1");
			if($mailchimp_status == "E" && $mailchimp_api_key !=="" && $mailchimp_list_id !==""){
				$this->Mailchimp_Model->Add_Subscriber($mailchimp_api_key, $mailchimp_list_id, $data_Insert["user_email"], $data_Insert["full_name"], "","");
			}
			/*end*/

		
		/* Sub Domain Code Start */
		$cpanel_user = 'zinnfeut';
		$cpanel_pass = '#mH_h87PB$';
		$cpanel_skin = 'paper_lantern';
		$cpanel_host = 'localhost';
		$dir = '/public_html/app';
		$subdomain = $data_Insert["subdomain_name"];
		$sock = fsockopen($cpanel_host, 2082);
		if(!$sock) { print('Socket error'); exit(); }
		$pass = base64_encode("$cpanel_user:$cpanel_pass");
		$in = "GET /frontend/$cpanel_skin/subdomain/doadddomain.html?rootdomain=$cpanel_host&domain=$subdomain&dir=$dir\r\n";
		$in .= "HTTP/1.0\r\n";
		$in .= "Host:$cpanel_host\r\n";
		$in .= "Authorization: Basic $pass\r\n";
		$in .= "\r\n";
		fputs($sock, $in);
		$sock_result = "";
		while (!feof($sock)) { $sock_result .= fgets ($sock, 128); }
		/* Sub Domain Code End */
		
		$data_Insert["status"] = "E";
		$data_Insert["role"] = "A";
		$data_Insert["created_date"] = date("Y-m-d h:i:s");
		$data_Insert["full_name"] = ucwords($data_Insert["full_name"]);
		$main_Domain = "zinnfy.com";
		$data_Insert["subdomain_name"] = $data_Insert["subdomain_name"].".".$main_Domain;
		$Orignal_Password = $data_Insert["user_password"];
		$data_Insert["user_password"] = hash("sha512", $data_Insert["user_password"] . config_item("encryption_key"));
		
		if($this->session->userdata("zf_register_email")){
			$data_Insert["user_email"] = $this->session->userdata("zf_register_email");
			$this->System_Model->Delete_All_Email_Token($data_Insert["user_email"]);
			$Session_Destroy_Array = array("zf_register_email");
			$this->session->unset_userdata($Session_Destroy_Array);
		}
		
		require_once(APPPATH."libraries/Stripe/init.php");
		try{
			\Stripe\Stripe::setApiKey($this->Setting_Model->Get_Option_Value("zn_stripe_secretkey","1"));
			
			$objcustomer = new \Stripe\Customer;
			$create_customer = $objcustomer::Create(array(
				"email"    => $data_Insert["user_email"],
				"description" => $data_Insert["user_email"]." This id name is ".$data_Insert["full_name"],
			));
			$data_Insert["stripe_customer_id"] = $create_customer->id;
		}catch (Exception $e) {
			$data_Insert["stripe_customer_id"] = "";
		}
		$New_Business_ID = $this->Super_Admin_Model->insert_Business($data_Insert);
		
		$data_Payment_Insert = array(
			"`business_id`" => $New_Business_ID,
			"`payment_method`" => "",
			"`transaction_id`" => "",
			"`payment_date`" => date("Y-m-d"),
			"`amount`" => 0,
			"`payment_status`" => "Complete",
			"`payment_description`" => "0.00 Charge for Business ID :- ".$New_Business_ID,
		);
		
		$Payment_ID = $this->Payments_Model->insert_Admin_Payments($data_Payment_Insert);
		
		$Get_One_Plan_Detail = $this->Plans_Model->Get_One_Plan_Detail($plan_ID);
		$plan_Days = $Get_One_Plan_Detail[0]->plan_days;

		$data_Business_Plan_Insert = array();
		$data_Business_Plan_Insert["`business_id`"] = $New_Business_ID;
		$data_Business_Plan_Insert["`plan_id`"] = $plan_ID;
		$data_Business_Plan_Insert["`payment_id`"] = $Payment_ID;
		$data_Business_Plan_Insert["`plan_start_date`"] = date("Y-m-d");
		$data_Business_Plan_Insert["`plan_end_date`"] = date("Y-m-d", strtotime("+". $plan_Days ." days"));
		$data_Business_Plan_Insert["`plan_status`"] = "E";
		$this->Plans_Model->insert_Business_Plan_Details($data_Business_Plan_Insert);
		
		require_once(APPPATH."libraries/class.phpmailer.php");
		$mail = new Zinnfy_phpmailer();
		$mail->Host = $this->config->item("sendgrid_Host");
		$mail->Username = $this->config->item("sendgrid_Username");
		$mail->Password = $this->config->item("sendgrid_Password");
		$mail->Port = $this->config->item("sendgrid_Port");
		$mail->SMTPSecure = $this->config->item("sendgrid_SMTPSecure");
		$mail->SMTPAuth = $this->config->item("sendgrid_SMTPAuth");
		$mail->CharSet = $this->config->item("sendgrid_CharSet");
		$mail->IsSMTP();
		$mail->SMTPDebug  = $this->config->item("sendgrid_SMTPDebug");
		$mail->IsHTML(true);
		

		$company_logo = base_url("assets/images/email_template_image/logo.png");
		$bg_image = base_url("assets/images/email_template_image/home-bg.png");
		$app_image = base_url("assets/images/email_template_image/app-img.jpg");
		$facebook_image = base_url("assets/images/email_template_image/facebook.png");
		$whatsapp_image = base_url("assets/images/email_template_image/whatsapp.png");
		$linkedin_image = base_url("assets/images/email_template_image/linkedin.png");
		$search_image = base_url("assets/images/email_template_image/search.png");
		
		$zn_LogIn_Page = base_url("/admin");
		$admin_email = "hello@zinnfy.com";
		$admin_name = "Zinnfy";
		
		$Template_Message =  '<!DOCTYPE>
		<html>
			<head>
				<title></title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1.0" />
				<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
			</head>
			<body style="margin:0; padding:0; bgcolor="#eaeced">
				<table style="min-width:320px; font-family: "Open Sans", sans-serif; color: #fff;" width="100%" cellspacing="0" cellpadding="0" bgcolor="#eaeced">
					
						<td class="wrapper" style="padding:0 10px;">
							
							<table data-module="module-4" data-thumb="thumbnails/04.png" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td data-bgcolor="bg-module" bgcolor="#eaeced">
										<table class="flexible" width="600" align="center" style="margin:0 auto;" cellpadding="0" cellspacing="0">
											<tr>
												<td data-bgcolor="bg-block" class="holder" style="" bgcolor="#f9f9f9">
													
													<table width="100%" cellpadding="0" cellspacing="0" style="background: url('.$bg_image.') no-repeat; background-size: cover; background-position: center; display: inline-block; width: 100%; position: relative;">

													<tbody style="left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6) !important; color: #fff;">
														<tr>
															<td class="img-flex" style="text-align:center;">
																<img src="'.$company_logo.'" style="vertical-align:top;padding: 20px 5px;" width="160" height="auto" alt="" />
															</td>
														</tr>
														<tr>
															<td data-color="title" data-size="size title" data-min="20" data-max="40" data-link-color="link title color" data-link-style="text-decoration:none; " class="title" align="center" style="font-size:35px;  padding:0 0 20px;letter-spacing: 1px;">
																Welcome!
															</td>
														</tr>
														<tr>
															<td data-color="title" data-size="size title" data-min="20" data-max="40" data-link-color="link title color" data-link-style="text-decoration:none; " class="title" align="center" style="font-size: 24px; font-weight: normal; padding:0 0 40px; letter-spacing: 1px;">
																We re thrilled to have you!
															</td>
														</tr>
														<tr>
															<td data-color="title" data-size="size title" data-min="20" data-max="40" data-link-color="link title color" data-link-style="text-decoration:none; " class="title" align="center" style="font-size: 20px; font-weight:600; padding:0 0 20px; letter-spacing: 1px;">
																Hello, '.$data_Insert["full_name"].' !  Welcome to the Zinnfy.
															</td>
														</tr>
														<tr>
															<td data-color="title" data-size="size title" data-min="20" data-max="40" data-link-color="link title color" data-link-style="text-decoration:none; " class="title" align="center" style="font-size:18px;  padding:0 0 20px; letter-spacing: 1px;">
																Your Account has been successfully created on Zinnfy.
															</td>
														</tr>


														<tr>
															<td data-color="title" data-size="size title" data-min="20" data-max="40" data-link-color="link title color" data-link-style="text-decoration:none; " class="title" align="left" style=" padding:0 0 20px; letter-spacing: 1px;">
																<h3 style="font-size: 24px; padding-top: 20px; padding-left: 20px;margin-bottom: 0;">Next Step to Proceed</h3>
																<hr style="width: 120px; float: left; margin-left: 20px;margin-top: 5px;" />
															</td>
														</tr>
														
														<tr>
															<td style="padding-bottom: 20px;">
																<a href="'.$zn_LogIn_Page.'" style="margin-left:20px; font-weight: bold; letter-spacing: 1px;outline:none;color:#68C2F8;text-decoration:none; font-size: 16px;">Click Here</a>
																
																<ul>
																	<li style="width: 50%; float: left; padding: 5px 0px; font-weight: 500; letter-spacing: 1px; margin-left: 0; font-size: 16px;  color: #fff;"> <a href="" style="color:#fff;text-decoration:none;cursor: pointer;">Add Services</a> </li>
																	<li style="width: 50%; float: left; padding: 5px 0px; font-weight: 500; letter-spacing: 1px; margin-left: 0; font-size: 16px;  color: #fff;"> <a href="" style="color:#fff;text-decoration:none;cursor: pointer;">Embed Code to your website</a> </li>
																	<li style="width: 50%; float: left; padding: 5px 0px; font-weight: 500; letter-spacing: 1px; margin-left: 0; font-size: 16px;  color: #fff;"> <a href="" style="color:#fff;text-decoration:none;cursor: pointer;">Your business settings</a> </li>
																	<li style="width: 50%; float: left; padding: 5px 0px; font-weight: 500; letter-spacing: 1px; margin-left: 0; font-size: 16px;  color: #fff;"> <a href="" style="color:#fff;text-decoration:none;cursor: pointer;">Manual Booking</a> </li>
																	<li style="width: 50%; float: left; padding: 5px 0px; font-weight: 500; letter-spacing: 1px; margin-left: 0;  font-size: 16px; color: #fff;"> <a href="" style="color:#fff;text-decoration:none; cursor: pointer;">Business Scheduling</a></li>
																</ul>
															</td>
														</tr>
														</tbody>
														</table>

														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td>
																	<a href="javascript:void(0)"><img src="'.$app_image.'" style="cursor: pointer;" width="100%" height="auto" alt="" /></a>
																</td>
															</tr>

															<tr style="background: #d9edff;">
																<td style="padding-top: 10px;">
																	<ul style="text-align:center; color:#000;">
																		<li class="w30" style="float: left; list-style: none; text-transform: uppercase; font-size: 14px; font-weight: bold; width: 30%; list-style: none;">Help Center</li>
																		<li class="w30" style="float: left; text-transform: uppercase; font-size: 14px; font-weight: bold; width: 30%;">Support 24/7</li>
																		<li class="w30" style="float: left; text-transform: uppercase; font-size: 14px; font-weight: bold; width: 30%;">Account</li>
																	
																		<li style="list-style: none; color: #7B7B7B; display: block; width: 100%; float: left; padding: 10px 0; font-weight: 600; letter-spacing: 1px; list-style: none;">support@zinnfy.com</li>
																	
																		<li style="list-style: none; display: block; width: 100%; float: left; margin-bottom: 10px;">
																			<a href="" style="padding-right: 5px;"><img src="'.$facebook_image.'" style="" width="20px" height="auto" alt="" /></a>
																			<a href="" style="padding-right: 5px;"><img src="'.$whatsapp_image.'" style="" width="20px" height="auto" alt="" /></a>
																			<a href="" style="padding-right: 5px;"><img src="'.$linkedin_image.'" style="" width="20px" height="auto" alt="" /></a>
																			<a href="" style="padding-right: 5px;"><img src="'.$search_image.'" style="" width="20px" height="auto" alt="" /></a>
																		</li>
																	</ul>

																</td>
															</tr>
														
													</table>
												</td>
											</tr>

										</table>
									</td>
								</tr>
							</table>
					
						</td>
					</tr>
					
				</table>
			</body>
		</html>';
		
		$mail->From = $admin_email;
		$mail->FromName = $admin_name;
		$mail->Sender = $admin_email;
		$mail->AddAddress($data_Insert["user_email"], ucwords($data_Insert["full_name"]));
		$mail->Subject = "Account Successfully Created at Zinnfy";
		$mail->Body = $Template_Message;
		if($mail->send()){}
		$mail->ClearAllRecipients();
		
		$Setting_Insert = array();
		$Setting_Insert[] = array("option_name" => "zn_company_name",
								  "option_value"=> "My Company Name",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_email",
								  "option_value"=> $data_Insert["user_email"],
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_address",
								  "option_value"=> "SUITE 5A-1204, 799 E DRAGRAM",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_city",
								  "option_value"=> "TUCSON",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_state",
								  "option_value"=> "AZ",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_phone",
								  "option_value"=> "00000000",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_phone_verify",
								  "option_value"=> "N",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_zip_code",
								  "option_value"=> "85001",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_country",
								  "option_value"=> "USA",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_logo",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_logo_display",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_address_display",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_currency_symbol_position",
								  "option_value"=> "$100",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_price_format_decimal_places",
								  "option_value"=> "2",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_tax_vat_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_tax_vat_type",
								  "option_value"=> "F",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_tax_vat_value",
								  "option_value"=> "10",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_partial_deposit_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_partial_type",
								  "option_value"=> "F",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_partial_deposit_amount",
								  "option_value"=> "10",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_partial_deposit_message",
								  "option_value"=> "You only need to pay a deposit to confirm your booking. The remaining amount needs to be paid on arrival.",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_allow_terms_and_conditions",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_terms_and_conditions_link",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_show_coupons_input_on_checkout",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_date_picker_date_format",
								  "option_value"=> "d-F-Y",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_pay_locally_status",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_paypal_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_paypal_api_username",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_paypal_api_password",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_paypal_api_signature",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_paypal_test_mode_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_stripe_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_stripe_secretkey",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_stripe_publishablekey",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_postalcode_status",
								  "option_value"=>"E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_postal_codes",
								  "option_value"=> "90001,90002,90003,90004,90005",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_cancelation_policy_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_cancel_policy_header",
								  "option_value"=> "Free cancellation before redemption",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_cancel_policy_textarea",
								  "option_value"=> "Full refund if cancelled within 24 hours of placing the order. If you cancel the order more than 24 hours, you can get a credit note for the amount paid. If cancelled in less than 24 hours before time of appointment/stay or in case of no-show, order will not be refunded.",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_special_offer",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_special_offer_text",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_show_time_duration",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_cart_scrollable",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_allow_privacy_policy",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_privacy_policy_link",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_front_desc_show",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_front_desc",
								  "option_value"=> "<div class=\"features\"><i class=\"icon ion-ios-locked-outline\"></i><h4 class=\"feature-tittle\">Safety</h4><p class=\"feature-text\">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p></div><div class=\"features\"><i class=\"icon ion-ribbon-a\"></i><h4 class=\"feature-tittle\">Best in Quality</h4><p class=\"feature-text\">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p></div><div class=\"features\"><i class=\"icon ion-ios-chatboxes-outline\"></i><h4 class=\"feature-tittle\">Communication</h4><p class=\"feature-text\">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p></div><div class=\"features\"><i class=\"icon ion-ios-alarm-outline\"></i><h4 class=\"feature-tittle\">Saves You Time</h4><p class=\"feature-text\">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p></div><div class=\"features\"><i class=\"icon ion-card\"></i><h4 class=\"feature-tittle\">Card Payment</h4><p class=\"feature-text\"> Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p></div>",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_currency_symbol",
								  "option_value"=> "$",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_country_code",
								  "option_value"=> "+1,us,United States: +1",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_language",
								  "option_value"=> "en",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_vc_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_p_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zf_bf_password",
								  "option_value"=> "8|15",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_bf_full_name",
								  "option_value"=> "E|E|4|20",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zf_bf_phone",
								  "option_value"=> "E|E|6|12",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_bf_street_address",
								  "option_value"=> "E|E|3|25",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_bf_city",
								  "option_value"=> "E|E|2|15",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_bf_state",
								  "option_value"=> "E|E|2|15",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_bf_zip_code",
								  "option_value"=> "E|E|4|8",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_bf_notes",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_front_language_selection_show",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_calculation_policy",
								  "option_value"=> "M",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_favicon_image",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_page_title",
								  "option_value"=> "Online Cleaning Appointment Booking",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_company_willwe_getin_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_hourly_base_price",
								  "option_value"=> "10",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_currency",
								  "option_value"=> "USD",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_cancelation_buffer_time",
								  "option_value"=> "120",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_reshedule_buffer_time",
								  "option_value"=> "120",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_thankyou_page_url",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_disable_period_already_booked",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_time_format",
								  "option_value"=> "12",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_time_morning_start",
								  "option_value"=> "09:00:00",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_time_morning_end",
								  "option_value"=> "12:00:00",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_time_afternoon_start",
								  "option_value"=> "13:00:00",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_time_afternoon_end",
								  "option_value"=> "16:00:00",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_time_evening_start",
								  "option_value"=> "17:00:00",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_time_evening_end",
								  "option_value"=> "20:00:00",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_recurrence_booking_status",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_admin_email_notification_status",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_client_email_notification_status",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_staff_email_notification_status",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_email_sender_name",
								  "option_value"=> $data_Insert["full_name"],
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_email_sender_address",
								  "option_value"=> $data_Insert["user_email"],
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_email_appointment_reminder_buffer",
								  "option_value"=> "60",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_admin_sms_notification_status",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_client_sms_notification_status",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_staff_sms_notification_status",
								  "option_value"=> "E",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_sms_credits",
								  "option_value"=> "10",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_google_calendar_access_token",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_google_calendar_timezone",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_google_calendar_expires_in",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_google_calendar_refresh_token",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_google_calendar_add_appointments",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_google_calendar_two_way_sync",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_mailchimp_status",
								  "option_value"=> "D",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_mailchimp_api_key",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Insert[] = array("option_name" => "zn_mailchimp_list_id",
								  "option_value"=> "",
								  "business_id"=>$New_Business_ID);
		$Setting_Data_Insert = $this->Super_Admin_Model->insert_Setting_Data($Setting_Insert);

		$Week_Insert = array();
		$Week_Insert[] = array("provider_id" => $New_Business_ID,
								"week_id"=>"1",
								"weekday_id"=>"1",
								"off_day"=>"N",
								"period_available"=>"M|A|E",
								"provider_schedule_type"=>"weekly");
		$Week_Insert[] = array("provider_id" => $New_Business_ID,
								"week_id"=>"1",
								"weekday_id"=>"2",
								"off_day"=>"N",
								"period_available"=>"M|A|E",
								"provider_schedule_type"=>"weekly");
		$Week_Insert[] = array("provider_id" => $New_Business_ID,
								"week_id"=>"1",
								"weekday_id"=>"3",
								"off_day"=>"N",
								"period_available"=>"M|A|E",
								"provider_schedule_type"=>"weekly");
		$Week_Insert[] = array("provider_id" => $New_Business_ID,
								"week_id"=>"1",
								"weekday_id"=>"4",
								"off_day"=>"N",
								"period_available"=>"M|A|E",
								"provider_schedule_type"=>"weekly");
		$Week_Insert[] = array("provider_id" => $New_Business_ID,
								"week_id"=>"1",
								"weekday_id"=>"5",
								"off_day"=>"N",
								"period_available"=>"M|A|E",
								"provider_schedule_type"=>"weekly");
		$Week_Insert[] = array("provider_id" => $New_Business_ID,
								"week_id"=>"1",
								"weekday_id"=>"6",
								"off_day"=>"N",
								"period_available"=>"M|A|E",
								"provider_schedule_type"=>"weekly");
		$Week_Insert[] = array("provider_id" => $New_Business_ID,
								"week_id"=>"1",
								"weekday_id"=>"7",
								"off_day"=>"N",
								"period_available"=>"M|A|E",
								"provider_schedule_type"=>"weekly");
		$Week_Data_Insert = $this->Super_Admin_Model->insert_Week_Data($Week_Insert);
		
		$Language_Insert[] = array(
									"language_code" => "en",
									"language_status" => "Y",
									"business_id" => $New_Business_ID,
									"labels_data" => "YTo0NTQ6e3M6MTU6Im5vX2FwcG9pbnRtZW50cyI7czoxNzoiTm8lMjBBcHBvaW50bWVudHMiO3M6NzoiaW52b2ljZSI7czo3OiJJbnZvaWNlIjtzOjEwOiJpbnZvaWNlX3RvIjtzOjEyOiJJTlZPSUNFJTIwVE8iO3M6MTI6Imludm9pY2VfZGF0ZSI7czoxNDoiSW52b2ljZSUyMERhdGUiO3M6MTQ6InBheW1lbnRfbWV0aG9kIjtzOjE2OiJQYXltZW50JTIwTWV0aG9kIjtzOjEyOiJzZXJ2aWNlX25hbWUiO3M6MTQ6IlNlcnZpY2UlMjBOYW1lIjtzOjM6InF0eSI7czozOiJxdHkiO3M6NToicHJpY2UiO3M6NToicHJpY2UiO3M6NzoiYWRkX29ucyI7czo3OiJBZGQtb25zIjtzOjk6InN1Yl90b3RhbCI7czoxMToiU3ViJTIwVG90YWwiO3M6MTk6InJlY2N1cmVuY2VfZGlzY291bnQiO3M6MjE6IlJlY2N1cmVuY2UlMjBEaXNjb3VudCI7czoxNToiY291cG9uX2Rpc2NvdW50IjtzOjE3OiJDb3Vwb24lMjBEaXNjb3VudCI7czozOiJ0YXgiO3M6MzoiVGF4IjtzOjU6InRvdGFsIjtzOjU6IlRvdGFsIjtzOjk6ImJvb2tlZF9vbiI7czoxMToiQm9va2VkJTIwT24iO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjExOiJEZXNjcmlwdGlvbiI7czo0OiJkYXRlIjtzOjQ6IkRhdGUiO3M6NjoiTGFibGVzIjtzOjY6IkxhYmxlcyI7czo2OiJFcnJvcnMiO3M6NjoiRXJyb3JzIjtzOjE1OiJsYW5ndWFnZV9zdGF0dXMiO3M6MTc6Imxhbmd1YWdlJTIwU3RhdHVzIjtzOjI1OiJzZXJ2aWNlX2FkZG9uX3ByaWNlX3J1bGVzIjtzOjMxOiJTZXJ2aWNlJTIwYWRkb24lMjBwcmljZSUyMHJ1bGVzIjtzOjEwOiJiYXNlX3ByaWNlIjtzOjEyOiJCYXNlJTIwUHJpY2UiO3M6ODoicXVhbnRpdHkiO3M6ODoiUXVhbnRpdHkiO3M6MjQ6InNlcnZpY2VfdW5pdF9wcmljZV9ydWxlcyI7czozMDoiU2VydmljZSUyMHVuaXQlMjBwcmljZSUyMHJ1bGVzIjtzOjE2OiJhbGxfYXBwb2ludG1lbnRzIjtzOjE4OiJBbGwlMjBBcHBvaW50bWVudHMiO3M6MTI6ImFsbF9ib29raW5ncyI7czoxNDoiQWxsJTIwQm9va2luZ3MiO3M6MjM6ImJvb2tfbWFudWFsX2FwcG9pbnRtZW50IjtzOjI3OiJCb29rJTIwTWFudWFsJTIwQXBwb2ludG1lbnQiO3M6MTQ6ImNob29zZV9zZXJ2aWNlIjtzOjE2OiJDaG9vc2UlMjBTZXJ2aWNlIjtzOjE2OiJjaG9vc2VfcmVjdXJyaW5nIjtzOjE4OiJDaG9vc2UlMjBSZWN1cnJpbmciO3M6NTg6InJlY3VycmluZ19kaXNjb3VudHNfYXBwbHlfZnJvbV90aGVfc2Vjb25kX2NsZWFuaW5nX29ud2FyZC4iO3M6NzI6IlJlY3VycmluZyUyMGRpc2NvdW50cyUyMGFwcGx5JTIwZnJvbSUyMHRoZSUyMHNlY29uZCUyMGNsZWFuaW5nJTIwb253YXJkLiI7czoyMjoiY2hvb3NlX2RhdGVfYW5kX3BlcmlvZCI7czoyODoiQ2hvb3NlJTIwRGF0ZSUyMGFuZCUyMFBlcmlvZCI7czoyNDoicGxlYXNlX3NlbGVjdF92YWxpZF9kYXRlIjtzOjMwOiJQbGVhc2UlMjBzZWxlY3QlMjB2YWxpZCUyMGRhdGUiO3M6MTA6ImRheV9wZXJpb2QiO3M6MTI6IkRheSUyMFBlcmlvZCI7czoyNDoicGxlYXNlX3NlbGVjdF9kYXlfcGVyaW9kIjtzOjMwOiJQbGVhc2UlMjBzZWxlY3QlMjBkYXklMjBwZXJpb2QiO3M6NDoibmV4dCI7czo0OiJOZXh0IjtzOjIwOiJjdXN0b21lcl9pbmZvcm1hdGlvbiI7czoyMjoiQ3VzdG9tZXIlMjBJbmZvcm1hdGlvbiI7czoxMzoiZXhpc3RpbmdfdXNlciI7czoxNToiRXhpc3RpbmclMjBVc2VyIjtzOjg6ImV4aXN0aW5nIjtzOjg6ImV4aXN0aW5nIjtzOjM6Im5ldyI7czozOiJuZXciO3M6ODoibmV3X3VzZXIiO3M6MTA6Ik5ldyUyMFVzZXIiO3M6MzA6InlvdV9hcmVfbWFraW5nX2FwcG9pbnRtZW50X2ZvciI7czozODoiWW91JTIwYXJlJTIwbWFraW5nJTIwYXBwb2ludG1lbnQlMjBmb3IiO3M6MTU6ImNoYW5nZV9jdXN0b21lciI7czoxNzoiQ2hhbmdlJTIwQ3VzdG9tZXIiO3M6MjA6InNlbGVjdF9leGlzdGluZ191c2VyIjtzOjI0OiJTZWxlY3QlMjBFeGlzdGluZyUyMFVzZXIiO3M6NToiZW1haWwiO3M6NToiRW1haWwiO3M6ODoicGFzc3dvcmQiO3M6ODoiUGFzc3dvcmQiO3M6OToiZnVsbF9uYW1lIjtzOjExOiJGdWxsJTIwTmFtZSI7czo1OiJwaG9uZSI7czo1OiJQaG9uZSI7czoxNDoic3RyZWV0X2FkZHJlc3MiO3M6MTY6IlN0cmVldCUyMEFkZHJlc3MiO3M6NDoiY2l0eSI7czo0OiJDaXR5IjtzOjU6InN0YXRlIjtzOjU6IlN0YXRlIjtzOjM6InppcCI7czozOiJaaXAiO3M6MTc6ImV4dHJhX2luZm9ybWF0aW9uIjtzOjE5OiJFeHRyYSUyMGluZm9ybWF0aW9uIjtzOjIyOiJzcGVjaWFsX3JlcXVlc3RzX25vdGVzIjtzOjM0OiJTcGVjaWFsJTIwcmVxdWVzdHMlMjAoJTIwTm90ZXMlMjApIjtzOjI4OiJkb195b3VfaGF2ZV9hX3ZhY3V1bV9jbGVhbmVyIjtzOjQxOiJEbyUyMHlvdSUyMGhhdmUlMjBhJTIwdmFjdXVtJTIwY2xlYW5lciUzRiI7czozOiJ5ZXMiO3M6MzoiWWVzIjtzOjE6InkiO3M6MToiWSI7czoyOiJubyI7czoyOiJObyI7czoxOiJuIjtzOjE6Ik4iO3M6MTk6ImRvX3lvdV9oYXZlX3BhcmtpbmciO3M6Mjg6IkRvJTIweW91JTIwaGF2ZSUyMHBhcmtpbmclM0YiO3M6MTg6Imhvd193aWxsX3dlX2dldF9pbiI7czoyOToiSG93JTIwd2lsbCUyMHdlJTIwZ2V0JTIwaW4lM0YiO3M6MTU6ImlfbGxfYmVfYXRfaG9tZSI7czoyMToiSSdsbCUyMGJlJTIwYXQlMjBob21lIjtzOjE0OiJwbGVhc2VfY2FsbF9tZSI7czoxODoiUGxlYXNlJTIwY2FsbCUyMG1lIjtzOjI3OiJ0aGVfa2V5X2lzX3dpdGhfdGhlX2Rvb3JtYW4iO3M6Mzc6IlRoZSUyMGtleSUyMGlzJTIwd2l0aCUyMHRoZSUyMGRvb3JtYW4iO3M6Njoib3RoZXJzIjtzOjY6Ik90aGVycyI7czozODoicGxlYXNlX3Byb3ZpZGVfYWRkaXRpb25hbF9pbnN0cnVjdGlvbnMiO3M6NDQ6IlBsZWFzZSUyMHByb3ZpZGUlMjBhZGRpdGlvbmFsJTIwaW5zdHJ1Y3Rpb25zIjtzOjQ6ImJhY2siO3M6NDoiQmFjayI7czoxNToiYm9va2luZ19zdW1tYXJ5IjtzOjE3OiJCb29raW5nJTIwU3VtbWFyeSI7czoxMDoiY2FydF9pdGVtcyI7czoxMjoiQ2FydCUyMEl0ZW1zIjtzOjEzOiJjYXJ0X2lzX2VtcHR5IjtzOjE3OiJDYXJ0JTIwaXMlMjBlbXB0eSI7czoxOToicmVjdXJyZW5jZV9kaXNjb3VudCI7czoyMToiUmVjdXJyZW5jZSUyMERpc2NvdW50IjtzOjM6InlheCI7czozOiJUYXgiO3M6MzoidmF0IjtzOjM6IlZhdCI7czozOiJnc3QiO3M6MzoiR3N0IjtzOjE1OiJwYXJ0aWFsX2RlcG9zaXQiO3M6MTc6IlBhcnRpYWwlMjBEZXBvc2l0IjtzOjE2OiJyZW1haW5pbmdfYW1vdW50IjtzOjE4OiJSZW1haW5pbmclMjBBbW91bnQiO3M6MTY6ImNvbXBsZXRlX2Jvb2tpbmciO3M6MTg6IkNvbXBsZXRlJTIwQm9va2luZyI7czo1OiJjbG9zZSI7czo1OiJDbG9zZSI7czoxOToiYmlsbGluZ19pbmZvcm1hdGlvbiI7czoyMToiQmlsbGluZyUyMEluZm9ybWF0aW9uIjtzOjExOiJ1cGRhdGVfY2FyZCI7czoxMzoiVXBkYXRlJTIwQ2FyZCI7czoxNjoicHVyY2hhc2VfY3JlZGl0cyI7czoxODoiUHVyY2hhc2UlMjBDcmVkaXRzIjtzOjIwOiJjcmVkaXRfb3JfZGViaXRfY2FyZCI7czoyNjoiQ3JlZGl0JTIwb3IlMjBEZWJpdCUyMENhcmQiO3M6ODoiYWN0aXZhdGUiO3M6ODoiQWN0aXZhdGUiO3M6MzU6InBsZWFzZV9hZGRfYXRsZWFzdF9vbmVfY2FyZF9kZXRhaWxzIjtzOjQ1OiJQbGVhc2UlMjBBZGQlMjBBdGxlYXN0JTIwT25lJTIwQ2FyZCUyMERldGFpbHMiO3M6MTI6InBheW1lbnRfZGF0ZSI7czoxNDoiUGF5bWVudCUyMERhdGUiO3M6NjoiYW1vdW50IjtzOjY6IkFtb3VudCI7czoxNDoicGF5bWVudF9zdGF0dXMiO3M6MTY6IlBheW1lbnQlMjBTdGF0dXMiO3M6NzoiYWN0aW9ucyI7czo3OiJBY3Rpb25zIjtzOjk6ImN1c3RvbWVycyI7czo5OiJDdXN0b21lcnMiO3M6MTM6Im5ld19jdXN0b21lcnMiO3M6MTU6Ik5ldyUyMEN1c3RvbWVycyI7czo0OiJuYW1lIjtzOjQ6Ik5hbWUiO3M6NjoiZV9tYWlsIjtzOjY6IkUtTWFpbCI7czo3OiJhZGRyZXNzIjtzOjc6IkFkZHJlc3MiO3M6ODoiemlwX2NvZGUiO3M6MTA6IlppcCUyMENvZGUiO3M6NjoiYWN0aW9uIjtzOjY6IkFjdGlvbiI7czoxMjoic2VuZF9tZXNzYWdlIjtzOjE0OiJTZW5kJTIwTWVzc2FnZSI7czoxMToiYWxsX21lc3NhZ2UiO3M6MTM6IkFsbCUyME1lc3NhZ2UiO3M6NzoibWVzc2FnZSI7czo3OiJNZXNzYWdlIjtzOjM6InNtcyI7czozOiJTTVMiO3M6Nzoic3ViamVjdCI7czo3OiJTdWJqZWN0IjtzOjEwOiJhdHRhY2htZW50IjtzOjEwOiJBdHRhY2htZW50IjtzOjEyOiJib29raW5nX2luZm8iO3M6MTQ6IkJvb2tpbmclMjBJbmZvIjtzOjE2OiJjbGVhbmluZ19zZXJ2aWNlIjtzOjE4OiJDbGVhbmluZyUyMFNlcnZpY2UiO3M6MTg6ImJvb2tpbmdfc2VydmVfZGF0ZSI7czoyMjoiQm9va2luZyUyMFNlcnZlJTIwRGF0ZSI7czoxNDoiYm9va2luZ19zdGF0dXMiO3M6MTY6IkJvb2tpbmclMjBTdGF0dXMiO3M6MTI6Im1vcmVfZGV0YWlscyI7czoxNDoiTW9yZSUyMERldGFpbHMiO3M6MTY6ImFkZF9uZXdfY3VzdG9tZXIiO3M6MjA6IkFkZCUyME5ldyUyMEN1c3RvbWVyIjtzOjE1OiJwcmVmZXJyZWRfZW1haWwiO3M6MTc6IlByZWZlcnJlZCUyMEVtYWlsIjtzOjk6ImRhc2hib2FyZCI7czo5OiJEYXNoYm9hcmQiO3M6MTQ6InRvdGFsX2Jvb2tpbmdzIjtzOjE2OiJUb3RhbCUyMEJvb2tpbmdzIjtzOjE1OiJ0b2RheXNfYm9va2luZ3MiO3M6MTg6IlRvZGF5J3MlMjBCb29raW5ncyI7czoxMzoidG90YWxfZWFybmluZyI7czoxNToiVG90YWwlMjBFYXJuaW5nIjtzOjE0OiJ0b2RheXNfZWFybmluZyI7czoxNzoiVG9kYXkncyUyMEVhcm5pbmciO3M6MTU6InRvdGFsX2N1c3RvbWVycyI7czoxNzoiVG90YWwlMjBDdXN0b21lcnMiO3M6MjA6InRvZGF5c19uZXdfY3VzdG9tZXJzIjtzOjI1OiJUb2RheSdzJTIwTmV3JTIwQ3VzdG9tZXJzIjtzOjE2OiJwZW5kaW5nX2Jvb2tpbmdzIjtzOjE4OiJQZW5kaW5nJTIwQm9va2luZ3MiO3M6MjM6InRvZGF5c19wZW5kaW5nX2Jvb2tpbmdzIjtzOjI4OiJUb2RheSdzJTIwUGVuZGluZyUyMEJvb2tpbmdzIjtzOjE3OiJ1cGNvbWluZ19ib29raW5ncyI7czoxOToiVXBjb21pbmclMjBCb29raW5ncyI7czo2OiJzdGF0dXMiO3M6NjoiU3RhdHVzIjtzOjg6ImN1c3RvbWVyIjtzOjg6IkN1c3RvbWVyIjtzOjc6InNlcnZpY2UiO3M6NzoiU2VydmljZSI7czo2OiJwZXJpb2QiO3M6NjoiUGVyaW9kIjtzOjQ4OiJwbGVhc2VfY29weV9hYm92ZV9jb2RlX2FuZF9wYXN0ZV9pbl95b3VyX3dlYnNpdGUiO3M6NjU6IlBsZWFzZSUyMENvcHklMjBhYm92ZSUyMGNvZGUlMjBhbmQlMjBwYXN0ZSUyMGluJTIweW91ciUyMHdlYnNpdGUuIjtzOjE5OiJleHBvcnRfeW91cl9kZXRhaWxzIjtzOjIzOiJFeHBvcnQlMjBZb3VyJTIwRGV0YWlscyI7czoxOToiYm9va2luZ19pbmZvcm1hdGlvbiI7czoyMToiQm9va2luZyUyMEluZm9ybWF0aW9uIjtzOjE5OiJzZXJ2aWNlX2luZm9ybWF0aW9uIjtzOjIxOiJTZXJ2aWNlJTIwSW5mb3JtYXRpb24iO3M6ODoib3JkZXJfaWQiO3M6MTA6Ik9yZGVyJTIwSWQiO3M6MTA6Im9yZGVyX2RhdGUiO3M6MTI6Ik9yZGVyJTIwRGF0ZSI7czoxMjoiYm9va2luZ19kYXRlIjtzOjE0OiJCb29raW5nJTIwRGF0ZSI7czo0OiJtb3JlIjtzOjQ6Ik1vcmUiO3M6NToidW5pdHMiO3M6NToiVW5pdHMiO3M6NjoiYWRkb25zIjtzOjY6IkFkZE9ucyI7czoxMzoic2VydmljZV90aXRsZSI7czoxNToiU2VydmljZSUyMFRpdGxlIjtzOjE0OiJzZXJ2aWNlX21ldGhvZCI7czoxNjoiU2VydmljZSUyME1ldGhvZCI7czoxMzoiYm9va2luZ191bml0cyI7czoxNToiQm9va2luZyUyMFVuaXRzIjtzOjEwOiJ1bml0X3RpdGxlIjtzOjI2OiJVbml0JTIwVGl0bGUlMjBJbmZvcm1hdGlvbiI7czoxMzoicXVhbnRpdHlfcmF0ZSI7czoxNToiUXVhbnRpdHklMjBSYXRlIjtzOjE0OiJib29raW5nX2FkZG9ucyI7czoxNjoiQm9va2luZyUyMEFkZE9ucyI7czoxMjoiYWRkb25zX3RpdGxlIjtzOjE0OiJBZGRPbnMlMjBUaXRsZSI7czo0OiJyYXRlIjtzOjQ6IlJhdGUiO3M6MTM6InNlcnZpY2VfdW5pdHMiO3M6MTU6IlNlcnZpY2UlMjBVbml0cyI7czoxMToidW5pdHNfdGl0bGUiO3M6MTM6IlVuaXRzJTIwVGl0bGUiO3M6OToibWF4X2xpbWl0IjtzOjExOiJNYXglMjBMaW1pdCI7czoxNDoib3B0aW9uYWxfbGFiZWwiO3M6MTY6Ik9wdGlvbmFsJTIwTGFiZWwiO3M6MTU6Im9wdGlvbmFsX3N5bWJvbCI7czoxNzoiT3B0aW9uYWwlMjBTeW1ib2wiO3M6MTQ6InNlcnZpY2VfYWRkb25zIjtzOjE2OiJTZXJ2aWNlJTIwQWRkb25zIjtzOjY6ImxhYmVscyI7czo2OiJMYWJlbHMiO3M6ODoibGFuZ3VhZ2UiO3M6ODoiTGFuZ3VhZ2UiO3M6MTU6InNlbGVjdF9sYW5ndWFnZSI7czoxNzoiU2VsZWN0JTIwTGFuZ3VhZ2UiO3M6ODoicGF5bWVudHMiO3M6ODoiUGF5bWVudHMiO3M6MTE6ImNsaWVudF9uYW1lIjtzOjEzOiJDbGllbnQlMjBOYW1lIjtzOjg6ImRpc2NvdW50IjtzOjg6IkRpc2NvdW50IjtzOjU6InRheGVzIjtzOjU6IlRheGVzIjtzOjEwOiJyZWN1cnJlbmNlIjtzOjEwOiJSZWN1cnJlbmNlIjtzOjEwOiJuZXRfYW1vdW50IjtzOjEyOiJOZXQlMjBBbW91bnQiO3M6MTQ6InByb2ZpbGVfdXBkYXRlIjtzOjE2OiJQcm9maWxlJTIwVXBkYXRlIjtzOjEyOiJ2ZXJpZnlfcGhvbmUiO3M6MTQ6IlZlcmlmeSUyMFBob25lIjtzOjM6Im90cCI7czozOiJPVFAiO3M6NzoiY291bnRyeSI7czo3OiJDb3VudHJ5IjtzOjE1OiJwcm9maWxlX1BpY3R1cmUiO3M6MTc6IlByb2ZpbGUlMjBQaWN0dXJlIjtzOjE1OiJwYXNzd29yZF9DaGFuZ2UiO3M6MTc6IlBhc3N3b3JkJTIwQ2hhbmdlIjtzOjEyOiJvbGRfUGFzc3dvcmQiO3M6MTQ6Ik9sZCUyMFBhc3N3b3JkIjtzOjEyOiJuZXdfUGFzc3dvcmQiO3M6MTQ6Ik5ldyUyMFBhc3N3b3JkIjtzOjE2OiJjb25maXJtX1Bhc3N3b3JkIjtzOjE4OiJDb25maXJtJTIwUGFzc3dvcmQiO3M6MTU6ImNoYW5nZV9QYXNzd29yZCI7czoxNzoiQ2hhbmdlJTIwUGFzc3dvcmQiO3M6OToic2NoZWR1bGVzIjtzOjk6IlNjaGVkdWxlcyI7czoxMzoic2xvdHNfdmlld19ieSI7czoyMDoiU2xvdHMlMjBWaWV3JTIwQnklM0YiO3M6MTI6ImF2YWlsYWJpbGl0eSI7czoxMjoiQXZhaWxhYmlsaXR5IjtzOjg6Im9mZl9kYXlzIjtzOjEwOiJPZmYlMjBEYXlzIjtzOjEzOiJzY2hlZHVsZV90eXBlIjtzOjE1OiJTY2hlZHVsZSUyMFR5cGUiO3M6NzoibW9ybmluZyI7czo3OiJNb3JuaW5nIjtzOjk6ImFmdGVybm9vbiI7czo5OiJBZnRlcm5vb24iO3M6NzoiZXZlbmluZyI7czo3OiJFdmVuaW5nIjtzOjE3OiJhZGRfc2VydmljZV9hZGRvbiI7czoyMToiQWRkJTIwU2VydmljZSUyMEFkZG9uIjtzOjU6InRpdGxlIjtzOjU6IlRpdGxlIjtzOjU6ImltYWdlIjtzOjU6IkltYWdlIjtzOjE3OiJkZWxldGVfdGhpc19pbWFnZSI7czoyNDoiRGVsZXRlJTIwVGhpcyUyMEltYWdlJTNGIjtzOjg6ImR1cmF0aW9uIjtzOjg6IkR1cmF0aW9uIjtzOjU6ImhvdXJzIjtzOjU6IkhvdXJzIjtzOjc6Im1pbnV0ZXMiO3M6NzoiTWludXRlcyI7czoxNjoiYWRkX3NlcnZpY2VfdW5pdCI7czoyMDoiQWRkJTIwU2VydmljZSUyMFVuaXQiO3M6MjA6Im9wdGlvbmFsX2xhYmVsX3RpdGxlIjtzOjI0OiJPcHRpb25hbCUyMExhYmVsJTIwVGl0bGUiO3M6MjA6Im9wdGlvbmFsX3VuaXRfc3ltYm9sIjtzOjI0OiJPcHRpb25hbCUyMFVuaXQlMjBTeW1ib2wiO3M6ODoic2VydmljZXMiO3M6ODoiU2VydmljZXMiO3M6MTE6ImFkZF9zZXJ2aWNlIjtzOjEzOiJBZGQlMjBTZXJ2aWNlIjtzOjc6Im1ldGhvZHMiO3M6NzoiTWV0aG9kcyI7czoxMjoicGF5bWVudF90eXBlIjtzOjE0OiJQYXltZW50JTIwVHlwZSI7czoxMjoiaG91cmx5X2Jhc2VkIjtzOjE0OiJIb3VybHklMjBCYXNlZCI7czoxMDoidW5pdF9iYXNlZCI7czoxMjoiVW5pdCUyMEJhc2VkIjtzOjE2OiJjYWxjdWxhdGlvbl90eXBlIjtzOjE4OiJDYWxjdWxhdGlvbiUyMFR5cGUiO3M6MTk6ImFwcGVhcmFuY2Vfc2V0dGluZ3MiO3M6MjE6IkFwcGVhcmFuY2UlMjBTZXR0aW5ncyI7czoxMDoicGFnZV90aXRsZSI7czoxMjoiUGFnZSUyMFRpdGxlIjtzOjI5OiJkaXNhYmxlX3BlcmlvZF9hbHJlYWR5X2Jvb2tlZCI7czozNToiRGlzYWJsZSUyMFBlcmlvZCUyMEFscmVhZHklMjBCb29rZWQiO3M6MTU6InNjcm9sbGFibGVfY2FydCI7czoxNzoiU2Nyb2xsYWJsZSUyMENhcnQiO3M6Mzc6InNob3dfdGltZV9kdXJhdGlvbl9vbl9ib29raW5nX3N1bW1lcnkiO3M6NDc6IlNob3clMjBUaW1lJTIwRHVyYXRpb24lMjBvbiUyMEJvb2tpbmclMjBTdW1tZXJ5IjtzOjIzOiJkYXRlX3BpY2tlcl9kYXRlX2Zvcm1hdCI7czoyNzoiRGF0ZS1QaWNrZXIlMjBEYXRlJTIwRm9ybWF0IjtzOjExOiJ0aW1lX2Zvcm1hdCI7czoxMzoiVGltZSUyMEZvcm1hdCI7czoxMzoiZmF2aWNvbl9pbWFnZSI7czoxNToiRmF2aWNvbiUyMEltYWdlIjtzOjE1OiJjbGlja190b191cGxvYWQiO3M6MTk6IkNsaWNrJTIwVG8lMjBVcGxvYWQiO3M6MTU6ImVfbWFpbF9zZXR0aW5ncyI7czoxNzoiRS1NYWlsJTIwU2V0dGluZ3MiO3M6MjU6ImFkbWluX2VtYWlsX25vdGlmaWNhdGlvbnMiO3M6Mjk6IkFkbWluJTIwRW1haWwlMjBOb3RpZmljYXRpb25zIjtzOjI2OiJjbGllbnRfZW1haWxfbm90aWZpY2F0aW9ucyI7czozMDoiQ2xpZW50JTIwRW1haWwlMjBOb3RpZmljYXRpb25zIjtzOjI0OiJzdGFmZl9lbWFpbF9ub3RpZmljYXRpb24iO3M6Mjg6IlN0YWZmJTIwRW1haWwlMjBOb3RpZmljYXRpb24iO3M6MTE6InNlbmRlcl9uYW1lIjtzOjEzOiJTZW5kZXIlMjBOYW1lIjtzOjEyOiJzZW5kZXJfZW1haWwiO3M6MTQ6IlNlbmRlciUyMEVtYWlsIjtzOjI3OiJhcHBvaW50bWVudF9yZW1pbmRlcl9idWZmZXIiO3M6MzE6IkFwcG9pbnRtZW50JTIwUmVtaW5kZXIlMjBCdWZmZXIiO3M6MjE6ImNsaWVudF9lbWFpbF90ZW1wbGF0ZSI7czoyNToiQ2xpZW50JTIwRW1haWwlMjB0ZW1wbGF0ZSI7czoyMDoiYWRtaW5fZW1haWxfdGVtcGxhdGUiO3M6MjQ6IkFkbWluJTIwRW1haWwlMjB0ZW1wbGF0ZSI7czoyMDoic3RhZmZfZW1haWxfdGVtcGxhdGUiO3M6MjQ6IlN0YWZmJTIwRW1haWwlMjB0ZW1wbGF0ZSI7czoxNjoiZGVmYXVsdF90ZW1wbGF0ZSI7czoxODoiRGVmYXVsdCUyMFRlbXBsYXRlIjtzOjE2OiJnZW5lcmFsX3NldHRpbmdzIjtzOjE4OiJHZW5lcmFsJTIwU2V0dGluZ3MiO3M6MTI6InBlcmlvZF90aW1lcyI7czoxNDoiUGVyaW9kJTIwVGltZXMiO3M6MTg6Im1vcm5pbmdfc3RhcnRfdGltZSI7czoyMjoiTW9ybmluZyUyMFN0YXJ0JTIwVGltZSI7czoyNjoicGxlYXNlX2VudGVyX3RpbWVfcHJvcGVybHkiO3M6MzI6IlBsZWFzZSUyMEVudGVyJTIwVGltZSUyMFByb3Blcmx5IjtzOjE2OiJtb3JuaW5nX2VuZF90aW1lIjtzOjIwOiJNb3JuaW5nJTIwRW5kJTIwVGltZSI7czoyMDoiYWZ0ZXJub29uX3N0YXJ0X3RpbWUiO3M6MjQ6IkFmdGVybm9vbiUyMFN0YXJ0JTIwVGltZSI7czoxODoiYWZ0ZXJub29uX2VuZF90aW1lIjtzOjIyOiJBZnRlcm5vb24lMjBFbmQlMjBUaW1lIjtzOjE4OiJldmVuaW5nX3N0YXJ0X3RpbWUiO3M6MjI6IkV2ZW5pbmclMjBTdGFydCUyMFRpbWUiO3M6MTY6ImV2ZW5pbmdfZW5kX3RpbWUiO3M6MjA6IkV2ZW5pbmclMjBFbmQlMjBUaW1lIjtzOjE3OiJob3VybHlfYmFzZV9wcmljZSI7czoyMToiSG91cmx5JTIwQmFzZSUyMFByaWNlIjtzOjExOiJwb3N0YWxfY29kZSI7czoxMzoiUG9zdGFsJTIwQ29kZSI7czoyNDoiY2FuY2VsbGF0aW9uX2J1ZmZlcl90aW1lIjtzOjI4OiJDYW5jZWxsYXRpb24lMjBCdWZmZXIlMjBUaW1lIjtzOjIxOiJyZXNoZWR1bGVfYnVmZmVyX3RpbWUiO3M6MjU6IlJlc2hlZHVsZSUyMEJ1ZmZlciUyMFRpbWUiO3M6ODoiY3VycmVuY3kiO3M6ODoiQ3VycmVuY3kiO3M6MTI6InByaWNlX2Zvcm1hdCI7czoxNDoiUHJpY2UlMjBGb3JtYXQiO3M6MjQ6ImN1cnJlbmN5X3N5bWJvbF9wb3NpdGlvbiI7czoyODoiQ3VycmVuY3klMjBTeW1ib2wlMjBQb3NpdGlvbiI7czoyNToiY2hhbmdlX2NhbGN1bGF0aW9uX3BvbGljeSI7czoyOToiQ2hhbmdlJTIwQ2FsY3VsYXRpb24lMjBQb2xpY3kiO3M6ODoibXVsdGlwbHkiO3M6ODoiTXVsdGlwbHkiO3M6NToiZXF1YWwiO3M6NToiRXF1YWwiO3M6MTA6InBlcmNlbnRhZ2UiO3M6MTA6IlBlcmNlbnRhZ2UiO3M6NDoiZmxhdCI7czo0OiJGbGF0IjtzOjE3OiJ0aGFua3lvdV9wYWdlX3VybCI7czoyMToiVGhhbmt5b3UlMjBQYWdlJTIwVVJMIjtzOjE5OiJjYW5jZWxsYXRpb25fcG9saWN5IjtzOjIxOiJDQU5DRUxMQVRJT04lMjBQT0xJQ1kiO3M6MjY6ImNhbmNlbGxhdGlvbl9wb2xpY3lfaGVhZGVyIjtzOjMwOiJDYW5jZWxsYXRpb24lMjBQb2xpY3klMjBIZWFkZXIiO3M6Mjc6ImNhbmNlbGxhdGlvbl9wb2xpY3lfbWVzc2FnZSI7czozMToiQ2FuY2VsbGF0aW9uJTIwUG9saWN5JTIwTWVzc2FnZSI7czoxNjoidGVybXNfY29uZGl0aW9ucyI7czoyNDoiVGVybXMlMjAlMjYlMjBDb25kaXRpb25zIjtzOjE0OiJwcml2YWN5X3BvbGljeSI7czoxNjoiUHJpdmFjeSUyMFBvbGljeSI7czoyNDoiYm9va2luZ19wYWdlX2luZm9ybWF0aW9uIjtzOjI4OiJCb29raW5nJTIwUGFnZSUyMEluZm9ybWF0aW9uIjtzOjIxOiJzcGVjaWFsX29mZmVyX21lc3NhZ2UiO3M6MjU6IlNwZWNpYWwlMjBPZmZlciUyME1lc3NhZ2UiO3M6MjQ6Imdvb2dsZV9jYWxlbmRhcl9zZXR0aW5ncyI7czoyODoiR29vZ2xlJTIwQ2FsZW5kYXIlMjBTZXR0aW5ncyI7czozNToiYWRkX2FwcG9pbnRtZW50c190b19nb29nbGVfY2FsZW5kZXIiO3M6NDM6IkFkZCUyMEFwcG9pbnRtZW50cyUyMFRvJTIwR29vZ2xlJTIwQ2FsZW5kZXIiO3M6MTI6InR3b193YXlfc3luYyI7czoxNjoiVHdvJTIwV2F5JTIwU3luYyI7czoxMDoiZGlzY29ubmVjdCI7czoxMDoiRGlzY29ubmVjdCI7czoxNzoibG9naW5fd2l0aF9nb29nbGUiO3M6MjE6IkxvZ2luJTIwd2l0aCUyMEdvb2dsZSI7czoyMToiY29tcGFueV9pbmZvX3NldHRpbmdzIjtzOjI1OiJDb21wYW55JTIwSW5mbyUyMFNldHRpbmdzIjtzOjEyOiJjb21wYW55X25hbWUiO3M6MTQ6IkNvbXBhbnklMjBOYW1lIjtzOjEyOiJjb3VudHJ5X2NvZGUiO3M6MTQ6IkNvdW50cnklMjBDb2RlIjtzOjEyOiJjb21wYW55X2xvZ28iO3M6MTQ6IkNvbXBhbnklMjBMb2dvIjtzOjQ1OiJtYW5hZ2VhYmxlX2Zvcm1fZmllbGRzX2Zvcl9mcm9udF9ib29raW5nX2Zvcm0iO3M6NTc6Ik1hbmFnZWFibGUlMjBGb3JtJTIwRmllbGRzJTIwRm9yJTIwRnJvbnQlMjBCb29raW5nJTIwRm9ybSI7czoxMDoiZmllbGRfbmFtZSI7czoxMjoiRmllbGQlMjBOYW1lIjtzOjg6InJlcXVpcmVkIjtzOjg6IlJlcXVpcmVkIjtzOjE0OiJtaW5pbXVtX2xlbmd0aCI7czoxNjoiTWluaW11bSUyMExlbmd0aCI7czoxNzoic2hvd19jb21wYW55X2xvZ28iO3M6MjE6IlNob3clMjBDb21wYW55JTIwTG9nbyI7czoyMDoic2hvd19jb21wYW55X2FkZHJlc3MiO3M6MjQ6IlNob3clMjBDb21wYW55JTIwQWRkcmVzcyI7czoyOToic2hvd19mcm9udF9sYW5ndWFnZV9zZWxlY3Rpb24iO3M6MzU6IlNob3clMjBGcm9udCUyMExhbmd1YWdlJTIwU2VsZWN0aW9uIjtzOjU6Im5vdGVzIjtzOjU6Ik5vdGVzIjtzOjE1OiJ2YWNjdW1lX2NsZWFuZXIiO3M6MTc6IlZhY2N1bWUlMjBDbGVhbmVyIjtzOjc6InBhcmtpbmciO3M6NzoiUGFya2luZyI7czoxODoiaG93X3dpbGxfd2VfZ2V0X0l0IjtzOjI2OiJIb3clMjB3aWxsJTIwd2UlMjBnZXQlMjBJdCI7czozMDoic2hvd19jb3Vwb25zX2lucHV0X29uX2NoZWNrb3V0IjtzOjM4OiJTaG93JTIwQ291cG9ucyUyMGlucHV0JTIwT24lMjBDaGVja291dCI7czoxNzoicGF5bWVudHNfc2V0dGluZ3MiO3M6MTk6IlBheW1lbnRzJTIwU2V0dGluZ3MiO3M6MTE6InBheV9sb2NhbGx5IjtzOjEzOiJQYXklMjBMb2NhbGx5IjtzOjY6InN0cmlwZSI7czo2OiJTdHJpcGUiO3M6MTE6InB1Ymxpc2hfa2V5IjtzOjEzOiJQdWJsaXNoJTIwS2V5IjtzOjEwOiJzZWNyZXRfa2V5IjtzOjEyOiJTZWNyZXQlMjBLZXkiO3M6NjoicGF5cGFsIjtzOjY6IlBheXBhbCI7czoxMjoiYXBpX3VzZXJuYW1lIjtzOjE0OiJBUEklMjBVc2VybmFtZSI7czoxMjoiYXBpX3Bhc3N3b3JkIjtzOjE0OiJBUEklMjBQYXNzd29yZCI7czo5OiJzaWduYXR1cmUiO3M6OToiU2lnbmF0dXJlIjtzOjk6InRlc3RfbW9kZSI7czoxMToiVGVzdCUyME1vZGUiO3M6MTg6InByb21vQ29kZV9zZXR0aW5ncyI7czoyMDoiUHJvbW9Db2RlJTIwU2V0dGluZ3MiO3M6MTM6ImFkZF9wcm9tb2NvZGUiO3M6MTU6IkFkZCUyMFByb21vY29kZSI7czoxMToiY291cG9uX2NvZGUiO3M6MTM6IkNvdXBvbiUyMENvZGUiO3M6MTE6ImNvdXBvbl90eXBlIjtzOjEzOiJDb3Vwb24lMjBUeXBlIjtzOjExOiJleHBpcnlfZGF0ZSI7czoxMzoiRXhwaXJ5JTIwRGF0ZSI7czoxMjoiY291cG9uX3ZhbHVlIjtzOjE0OiJDb3Vwb24lMjBWYWx1ZSI7czoxMToiY291cG9uX3VzZWQiO3M6MTM6IkNvdXBvbiUyMFVzZWQiO3M6MTI6ImNvdXBvbl9saW1pdCI7czoxNDoiQ291cG9uJTIwTGltaXQiO3M6MTk6InJlY3VycmVuY2Vfc2V0dGluZ3MiO3M6MjE6IlJlY3VycmVuY2UlMjBTZXR0aW5ncyI7czoxNDoiYWRkX3JlY3VycmVuY2UiO3M6MTY6IkFkZCUyMFJlY3VycmVuY2UiO3M6MTc6InJlY3VycmVuY2Vfc3RhdHVzIjtzOjE5OiJSZWN1cnJlbmNlJTIwU3RhdHVzIjtzOjE1OiJyZWN1cnJlbmNlX25hbWUiO3M6MTc6IlJlY3VycmVuY2UlMjBOYW1lIjtzOjE2OiJyZWN1cnJlbmNlX2xhYmVsIjtzOjE4OiJSZWN1cnJlbmNlJTIwTGFiZWwiO3M6MTU6InJlY3VycmVuY2VfZGF5cyI7czoxNzoiUmVjdXJyZW5jZSUyMERheXMiO3M6MjQ6InJlY3VycmVuY2VfZGlzY291bnRfdHlwZSI7czoyODoiUmVjdXJyZW5jZSUyMERpc2NvdW50JTIwVHlwZSI7czoyNToicmVjdXJyZW5jZV9kaXNjb3VudF92YWx1ZSI7czoyOToiUmVjdXJyZW5jZSUyMERpc2NvdW50JTIwVmFsdWUiO3M6NDoiZGF5cyI7czo0OiJEYXlzIjtzOjEzOiJkaXNjb3VudF90eXBlIjtzOjE1OiJEaXNjb3VudCUyMFR5cGUiO3M6MTQ6ImRpc2NvdW50X3ZhbHVlIjtzOjE2OiJEaXNjb3VudCUyMFZhbHVlIjtzOjEyOiJzbXNfc2V0dGluZ3MiO3M6MTQ6IlNNUyUyMFNldHRpbmdzIjtzOjIzOiJhZG1pbl9zbXNfbm90aWZpY2F0aW9ucyI7czoyNzoiQWRtaW4lMjBTTVMlMjBOb3RpZmljYXRpb25zIjtzOjI0OiJjbGllbnRfc21zX25vdGlmaWNhdGlvbnMiO3M6Mjg6IkNsaWVudCUyMFNNUyUyME5vdGlmaWNhdGlvbnMiO3M6MjI6InN0YWZmX3Ntc19ub3RpZmljYXRpb24iO3M6MjY6IlN0YWZmJTIwU01TJTIwTm90aWZpY2F0aW9uIjtzOjE2OiJ5b3VyX3Ntc19jcmVkaXRzIjtzOjIwOiJZb3VyJTIwU01TJTIwQ3JlZGl0cyI7czoyMjoicHVyY2hhc2Vfbm9fb2ZfY3JlZGl0cyI7czoyOToiUHVyY2hhc2UlMjBOby4lMjBvZiUyMENyZWRpdHMiO3M6MTY6InRvdGFsX3BheV9hbW91bnQiO3M6MjA6IlRvdGFsJTIwUGF5JTIwQW1vdW50IjtzOjEzOiJzdGFmZl9tZW1iZXJzIjtzOjE1OiJTdGFmZiUyME1lbWJlcnMiO3M6MTY6ImFkZF9zdGFmZl9tZW1iZXIiO3M6MjA6IkFkZCUyMFN0YWZmJTIwTWVtYmVyIjtzOjIwOiJhZGRfbmV3X3N0YWZmX21lbWJlciI7czoyNjoiQWRkJTIwTmV3JTIwU3RhZmYlMjBNZW1iZXIiO3M6MTg6ImFwcG9pbnRtZW50X2RldGFpbCI7czoyMDoiQXBwb2ludG1lbnQlMjBEZXRhaWwiO3M6MTM6ImN1c3RvbWVyX2luZm8iO3M6MTU6IkN1c3RvbWVyJTIwSW5mbyI7czo4OiJwaG9uZV9ubyI7czoxMDoiUGhvbmUlMjBObyI7czoxNToic3BlY2lhbF9yZXF1ZXN0IjtzOjE3OiJTcGVjaWFsJTIwUmVxdWVzdCI7czo2OiJyZWFzb24iO3M6NjoiUmVhc29uIjtzOjE0OiJ2YWN1dW1fY2xlYW5lciI7czoxNjoiVmFjdXVtJTIwQ2xlYW5lciI7czoxMjoiYXNzaWduX3N0YWZmIjtzOjE0OiJBc3NpZ24lMjBTdGFmZiI7czoxMjoic2VsZWN0X3N0YWZmIjtzOjE0OiJTZWxlY3QlMjBTdGFmZiI7czo4OiJjb21wbGV0ZSI7czo4OiJDb21wbGV0ZSI7czo3OiJjb25maXJtIjtzOjc6IkNvbmZpcm0iO3M6NjoicmVqZWN0IjtzOjY6IlJlamVjdCI7czo2OiJkZWxldGUiO3M6NjoiRGVsZXRlIjtzOjEzOiJldmVudHNfZGV0YWlsIjtzOjE1OiJFdmVudHMlMjBEZXRhaWwiO3M6MTE6ImV2ZW50X3RpdGxlIjtzOjEzOiJFdmVudCUyMFRpdGxlIjtzOjE3OiJldmVudF9kZXNjcmlwdGlvbiI7czoxOToiRXZlbnQlMjBEZXNjcmlwdGlvbiI7czoyMDoiZXZlbnRfc3RhcnRfZGF0ZVRpbWUiO3M6MjQ6IkV2ZW50JTIwU3RhcnQlMjBEYXRlVGltZSI7czoxODoiZXZlbnRfZW5kX2RhdGVUaW1lIjtzOjIyOiJFdmVudCUyMEVuZCUyMERhdGVUaW1lIjtzOjIxOiJldmVudF9jcmVhdGVfZGF0ZVRpbWUiO3M6MjU6IkV2ZW50JTIwQ3JlYXRlJTIwRGF0ZVRpbWUiO3M6MjI6ImV2ZW50X3VwZGF0ZWRfZGF0ZVRpbWUiO3M6MjY6IkV2ZW50JTIwVXBkYXRlZCUyMERhdGVUaW1lIjtzOjEwOiJjcm9wX2ltYWdlIjtzOjEyOiJDcm9wJTIwSW1hZ2UiO3M6MTM6ImNyb3BfYW5kX3NhdmUiO3M6MTc6IkNyb3AlMjAlMjYlMjBTYXZlIjtzOjY6Inppbm5meSI7czo2OiJaaW5uZnkiO3M6MTQ6Inppbm5meV9kb3RfY29tIjtzOjEwOiJaaW5uZnkuY29tIjtzOjEzOiJub3RpZmljYXRpb25zIjtzOjEzOiJOb3RpZmljYXRpb25zIjtzOjc6InByb2ZpbGUiO3M6NzoiUHJvZmlsZSI7czo3OiJsb2dfb3V0IjtzOjk6IkxvZyUyMG91dCI7czoxMjoiYXBwb2ludG1lbnRzIjtzOjEyOiJBcHBvaW50bWVudHMiO3M6MzoiY3JtIjtzOjM6IkNSTSI7czo4OiJzZXR0aW5ncyI7czo4OiJTZXR0aW5ncyI7czo3OiJjb21wYW55IjtzOjc6IkNvbXBhbnkiO3M6NzoiZ2VuZXJhbCI7czo3OiJHZW5lcmFsIjtzOjEwOiJhcHBlYXJhbmNlIjtzOjEwOiJBcHBlYXJhbmNlIjtzOjEwOiJwcm9tb19jb2RlIjtzOjEyOiJQcm9tbyUyMENvZGUiO3M6MjI6Im1hbmFnZWFibGVfZm9ybV9maWVsZHMiO3M6MjY6Ik1hbmFnZWFibGUlMjBGb3JtJTIwRmllbGRzIjtzOjE1OiJnb29nbGVfY2FsZW5kYXIiO3M6MTc6Ikdvb2dsZSUyMENhbGVuZGFyIjtzOjc6ImV4cG9ydHMiO3M6NzoiRXhwb3J0cyI7czoxMDoiZW1iZWRfY29kZSI7czoxMjoiRW1iZWQlMjBDb2RlIjtzOjc6ImJpbGxpbmciO3M6NzoiQmlsbGluZyI7czo3OiJzdWNjZXNzIjtzOjg6IlN1Y2Nlc3MhIjtzOjQ6ImluZm8iO3M6NToiSW5mbyEiO3M6Nzoid2FybmluZyI7czo4OiJXYXJuaW5nISI7czo2OiJkYW5nZXIiO3M6NzoiRGFuZ2VyISI7czo0Mjoid2hlcmVfd291bGRfeW91X2xpa2VfdXNfdG9fcHJvdmlkZV9zZXJ2aWNlIjtzOjU5OiJXaGVyZSUyMHdvdWxkJTIweW91JTIwbGlrZSUyMHVzJTIwdG8lMjBwcm92aWRlJTIwc2VydmljZSUzRiI7czo0NjoiaG93X29mdGVuX3dvdWxkX3lvdV9saWtlX3VzX3RvX3Byb3ZpZGVfc2VydmljZSI7czo2NToiSG93JTIwb2Z0ZW4lMjB3b3VsZCUyMHlvdSUyMGxpa2UlMjB1cyUyMHRvJTIwcHJvdmlkZSUyMHNlcnZpY2UlM0YiO3M6NTc6InJlY3VycmluZ19kaXNjb3VudHNfYXBwbHlfZnJvbV90aGVfc2Vjb25kX2NsZWFuaW5nX29ud2FyZCI7czo3MToiUmVjdXJyaW5nJTIwZGlzY291bnRzJTIwYXBwbHklMjBmcm9tJTIwdGhlJTIwc2Vjb25kJTIwY2xlYW5pbmclMjBvbndhcmQiO3M6MzA6IndoZW5fd291bGRfeW91X2xpa2VfdXNfdG9fY29tZSI7czo0NToiV2hlbiUyMHdvdWxkJTIweW91JTIwbGlrZSUyMHVzJTIwdG8lMjBjb21lJTNGIjtzOjYzOiJjaG9vc2VfZGF0ZV9hbmRfcGVyaW9kX3doaWNoX2lzX2NvbWZvcnRhYmxlX2Zvcl95b3VfZm9yX3NlcnZpY2UiO3M6ODQ6IkNob29zZSUyMGRhdGUlMjAlMjYlMjBwZXJpb2QlMjB3aGljaCUyMGlzJTIwY29tZm9ydGFibGUlMjBmb3IlMjB5b3UlMjBmb3IlMjBzZXJ2aWNlLiI7czo5OiJhYm91dF95b3UiO3M6MTE6IkFib3V0JTIweW91IjtzOjYzOiJ0aGlzX2luZm9ybWF0aW9uX3dpbGxfYmVfdXNlZF90b19jb250YWN0X3lvdV9hYm91dF95b3VyX3NlcnZpY2UiO3M6ODM6IlRoaXMlMjBpbmZvcm1hdGlvbiUyMHdpbGwlMjBiZSUyMHVzZWQlMjB0byUyMGNvbnRhY3QlMjB5b3UlMjBhYm91dCUyMHlvdXIlMjBzZXJ2aWNlIjtzOjk6ImVudGVyX290cCI7czoxMToiRW50ZXIlMjBPVFAiO3M6MTQ6IndoZXJlX3lvdV9saXZlIjtzOjIxOiJXaGVyZSUyMHlvdSUyMGxpdmUlM0YiO3M6MzI6IndoZXJlX3dvdWxkX3lvdV9saWtlX3VzX3RvX2NsZWFuIjtzOjQ3OiJXaGVyZSUyMHdvdWxkJTIweW91JTIwbGlrZSUyMHVzJTIwdG8lMjBjbGVhbiUzRiI7czo1MDoiaXRzX2Fib3V0X3lvdXJfc3BlY2lhbF9yZXF1ZXN0X3Byb3BlcnR5X2FjY2Vzc19ldGMiO3M6Njc6Ikl0cyUyMGFib3V0JTIweW91ciUyMHNwZWNpYWwlMjByZXF1ZXN0JTJDJTIwcHJvcGVydHklMjBhY2Nlc3MlMjBldGMiO3M6MjI6InNwZWNpYWxfcmVxdWVzdHNfTm90ZXMiO3M6MzQ6IlNwZWNpYWwlMjByZXF1ZXN0cyUyMCglMjBOb3RlcyUyMCkiO3M6Mzg6InNhdmVfZXh0cmFfb25feW91cl9hcHBvaW50bWVudF9ib29raW5nIjtzOjQ4OiJTYXZlJTIwZXh0cmElMjBvbiUyMHlvdXIlMjBhcHBvaW50bWVudCUyMGJvb2tpbmciO3M6MTY6ImhhdmVfYV9wcm9tb2NvZGUiO3M6MjM6IkhhdmUlMjBhJTIwcHJvbW9jb2RlJTNGIjtzOjU6ImFwcGx5IjtzOjU6IkFwcGx5IjtzOjE0OiJjb3Vwb25fYXBwbGllZCI7czoxNjoiQ291cG9uJTIwQXBwbGllZCI7czoyNDoicHJlZmVycmVkX3BheW1lbnRfbWV0aG9kIjtzOjI4OiJQcmVmZXJyZWQlMjBQYXltZW50JTIwTWV0aG9kIjtzOjQzOiJlbnRlcl95b3VyX2NhcmRfb3JfcGF5cGFsX2luZm9ybWF0aW9uX2JlbG93IjtzOjU5OiJFbnRlciUyMHlvdXIlMjBjYXJkJTIwb3IlMjBwYXlwYWwlMjBpbmZvcm1hdGlvbiUyMGJlbG93LiUyMCI7czo1MToieW91X3dpbGxfYmVfY2hhcmdlZF9hZnRlcl9zZXJ2aWNlX2hhc19iZWVuX3JlbmRlcmVkIjtzOjY3OiJZb3UlMjB3aWxsJTIwYmUlMjBjaGFyZ2VkJTIwYWZ0ZXIlMjBzZXJ2aWNlJTIwaGFzJTIwYmVlbiUyMHJlbmRlcmVkIjtzOjEyOiJjYXJkX3BheW1lbnQiO3M6MTI6IkNhcmQtUGF5bWVudCI7czoyMzoic2FmZV9hbmRfc2VjdXJlXzI1Nl9iaXQiO3M6Mjk6IlNhZmUlMjBhbmQlMjBzZWN1cmUlMjAyNTYtYml0IjtzOjIxOiJzc2xfZW5jcnlwdGVkX3BheW1lbnQiO3M6MjU6InNzbCUyMGVuY3J5cHRlZCUyMHBheW1lbnQiO3M6Mjg6ImlfaGF2ZV9yZWFkX2FuZF9hY2NlcHRlZF90aGUiO3M6Mzg6IkklMjBoYXZlJTIwcmVhZCUyMGFuZCUyMGFjY2VwdGVkJTIwdGhlIjtzOjMzOiJwbGVhc2VfYWNjZXB0X3Rlcm1zX2FuZF9jb25kaXRpb24iO3M6NDE6IlBsZWFzZSUyMEFjY2VwdCUyMFRlcm1zJTIwQW5kJTIwQ29uZGl0aW9uIjtzOjExOiJteV9ib29raW5ncyI7czoxMzoiTXklMjBCb29raW5ncyI7czoxNDoiYm9va2luZ19wZXJpb2QiO3M6MTY6IkJvb2tpbmclMjBQZXJpb2QiO3M6MTg6InJlc2NoZWR1bGVfYm9va2luZyI7czoyMDoiUmVzY2hlZHVsZSUyMEJvb2tpbmciO3M6MjA6InZpZXdfYm9va2luZ19kZXRhaWxzIjtzOjI3OiJWaWV3JTIwJTIwQm9va2luZyUyMERldGFpbHMiO3M6MTc6InJhdGluZ190b19ib29raW5nIjtzOjIxOiJSYXRpbmclMjB0byUyMEJvb2tpbmciO3M6MTc6InJhdGluZ19hbmRfcmV2aWV3IjtzOjIxOiJSYXRpbmclMjAlMjYlMjBSZXZpZXciO3M6MTQ6InRvdGFsX3BheW1lbnRzIjtzOjE2OiJUb3RhbCUyMFBheW1lbnRzIjtzOjE2OiJwZW5kaW5nX3BheW1lbnRzIjtzOjE4OiJQZW5kaW5nJTIwUGF5bWVudHMiO3M6MTg6InVwY29tbWluZ19ib29raW5ncyI7czoyMDoiVXBjb21taW5nJTIwQm9va2luZ3MiO3M6MTU6InByb2ZpbGVfcGljdHVyZSI7czoxNzoiUHJvZmlsZSUyMFBpY3R1cmUiO3M6MjI6InVwZGF0ZV9wcm9maWxlX3BpY3R1cmUiO3M6MjY6IlVwZGF0ZSUyMFByb2ZpbGUlMjBQaWN0dXJlIjtzOjMzOiJ0aGFua195b3VfZm9yX2Jvb2tpbmdfYXBwb2ludG1lbnQiO3M6NDI6IlRoYW5rJTIwWW91ISUyMEZvciUyMEJvb2tpbmclMjBBcHBvaW50bWVudCI7czo1NzoieW91X3dpbGxfYmVfbm90aWZpZWRfYnlfZW1haWxfd2l0aF9kZXRhaWxzX29mX2FwcG9pbnRtZW50IjtzOjc1OiJZb3UlMjB3aWxsJTIwYmUlMjBub3RpZmllZCUyMGJ5JTIwZW1haWwlMjB3aXRoJTIwZGV0YWlscyUyMG9mJTIwYXBwb2ludG1lbnQiO3M6MzI6InRoYW5rX3lvdV9mb3Jfc2lnbnVwX3dpdGhfemlubmZ5IjtzOjQzOiJUaGFuayUyMFlvdSElMjBGb3IlMjBTaWduVXAlMjB3aXRoJTIwWmlubmZ5IjtzOjYwOiJwbGVhc2VfY2hlY2tfeW91cl9pbmJveF90b19jb21wbGV0ZV90aGVfcmVnaXN0cmF0aW9uX3Byb2Nlc3MiO3M6NzY6IlBsZWFzZSUyMGNoZWNrJTIweW91ciUyMEluYm94JTIwdG8lMjBjb21wbGV0ZSUyMHRoZSUyMHJlZ2lzdHJhdGlvbiUyMHByb2Nlc3MiO3M6MTU6ImNoYW5nZV9wYXNzd29yZCI7czoxNzoiQ2hhbmdlJTIwUGFzc3dvcmQiO3M6MTA6ImNsaWNrX2hlcmUiO3M6MTI6IkNsaWNrJTIwSGVyZSI7czoyMzoiY3JlYXRlZF9hc19zdGFmZl9tZW1iZXIiO3M6Mjk6IkNyZWF0ZWQlMjBhcyUyMFN0YWZmJTIwTWVtYmVyIjtzOjQ1OiJ5b3VyX2FjY291bnRfaGFzX2JlZW5fc3VjY2Vzc2Z1bGx5X2NyZWF0ZWRfb24iO3M6NTc6IllvdXIlMjBhY2NvdW50JTIwaGFzJTIwYmVlbiUyMHN1Y2Nlc3NmdWxseSUyMGNyZWF0ZWQlMjBvbiI7czo0OToiYXNfYV9jdXN0b21lcl95b3VyX2FjY291bnRfZGV0YWlsc19hcmVfYXNfZm9sbG93cyI7czo2NjoiYXMlMjBhJTIwQ3VzdG9tZXIuJTIwWW91ciUyMGFjY291bnQlMjBkZXRhaWxzJTIwYXJlJTIwYXMlMjBmb2xsb3dzIjtzOjUzOiJhc19hX3N0YWZmX21lbWJlcl95b3VyX2FjY291bnRfZGV0YWlsc19hcmVfYXNfZm9sbG93cyI7czo3MjoiYXMlMjBhJTIwU3RhZmYlMjBNZW1iZXIuJTIwWW91ciUyMGFjY291bnQlMjBkZXRhaWxzJTIwYXJlJTIwYXMlMjBmb2xsb3dzIjtzOjIzOiJzaWduX2luX3RvX3lvdXJfYWNjb3VudCI7czo3NzoiVG8lMjBzaWduJTIwaW4lMjB0byUyMHlvdXIlMjBhY2NvdW50JTJDJTIwUGxlYXNlJTIwY2xpY2slMjBvbiUyMGJlbGxvdyUyMGxpbmsiO3M6OToidGhhbmtfeW91IjtzOjExOiJUaGFuayUyMHlvdSI7czo3OiJqYW51YXJ5IjtzOjc6IkphbnVhcnkiO3M6ODoiZmVicnVhcnkiO3M6ODoiRmVicnVhcnkiO3M6NToibWFyY2giO3M6NToiTWFyY2giO3M6NToiYXByaWwiO3M6NToiQXByaWwiO3M6MzoibWF5IjtzOjM6Ik1heSI7czo0OiJqdW5lIjtzOjQ6Ikp1bmUiO3M6NDoianVseSI7czo0OiJKdWx5IjtzOjY6ImF1Z3VzdCI7czo2OiJBdWd1c3QiO3M6OToic2VwdGVtYmVyIjtzOjk6IlNlcHRlbWJlciI7czo3OiJvY3RvYmVyIjtzOjc6Ik9jdG9iZXIiO3M6ODoibm92ZW1iZXIiO3M6ODoiTm92ZW1iZXIiO3M6ODoiZGVjZW1iZXIiO3M6ODoiRGVjZW1iZXIiO3M6MzoiamFuIjtzOjM6IkphbiI7czozOiJmZWIiO3M6MzoiRmViIjtzOjM6Im1hciI7czozOiJNYXIiO3M6MzoiYXByIjtzOjM6IkFwciI7czozOiJqdW4iO3M6MzoiSnVuIjtzOjM6Ikp1bCI7czozOiJKdWwiO3M6MzoiYXVnIjtzOjM6IkF1ZyI7czozOiJzZXAiO3M6MzoiU2VwIjtzOjM6Im9jdCI7czozOiJPY3QiO3M6Mzoibm92IjtzOjM6Ik5vdiI7czozOiJkZWMiO3M6MzoiRGVjIjtzOjY6Im1vbmRheSI7czo2OiJNb25kYXkiO3M6NzoidHVlc2RheSI7czo3OiJUdWVzZGF5IjtzOjk6IndlZG5lc2RheSI7czo5OiJXZWRuZXNkYXkiO3M6ODoidGh1cnNkYXkiO3M6ODoiVGh1cnNkYXkiO3M6NjoiZnJpZGF5IjtzOjY6IkZyaWRheSI7czo4OiJzYXR1cmRheSI7czo4OiJTYXR1cmRheSI7czo2OiJzdW5kYXkiO3M6NjoiU3VuZGF5IjtzOjM6Im1vbiI7czozOiJNb24iO3M6MzoidHVlIjtzOjM6IlR1ZSI7czozOiJ3ZWQiO3M6MzoiV2VkIjtzOjM6InRodSI7czozOiJUaHUiO3M6MzoiZnJpIjtzOjM6IkZyaSI7czozOiJzYXQiO3M6MzoiU2F0IjtzOjM6InN1biI7czozOiJTdW4iO3M6MjoiYW0iO3M6MjoiQU0iO3M6MjoicG0iO3M6MjoiUE0iO3M6NDoic2VuZCI7czo0OiJTZW5kIjtzOjQ6InNhdmUiO3M6NDoiU2F2ZSI7czo1OiJjbGVhciI7czo1OiJDbGVhciI7czoxNDoidHJhbnNhY3Rpb25faWQiO3M6MTY6IlRyYW5zYWN0aW9uJTIwSUQiO3M6MjI6InByb2ZpbGVfcGljdHVyZV91cGRhdGUiO3M6MjY6IlByb2ZpbGUlMjBQaWN0dXJlJTIwVXBkYXRlIjtzOjEzOiJzYXZlX3NldHRpbmdzIjtzOjE1OiJTYXZlJTIwU2V0dGluZ3MiO3M6MTI6InNhdmVfdGVtcGxldCI7czoxNDoiU2F2ZSUyMFRlbXBsZXQiO3M6MTk6ImNsaWVudF9zbXNfdGVtcGxhdGUiO3M6MjM6IkNsaWVudCUyMFNNUyUyMFRlbXBsYXRlIjtzOjE4OiJhZG1pbl9zbXNfdGVtcGxhdGUiO3M6MjI6IkFkbWluJTIwU01TJTIwVGVtcGxhdGUiO3M6MTg6InN0YWZmX3Ntc190ZW1wbGF0ZSI7czoyMjoiU3RhZmYlMjBTTVMlMjBUZW1wbGF0ZSI7czo1OiJsYWJlbCI7czo1OiJMYWJlbCI7czo1OiJ2YWx1ZSI7czo1OiJWYWx1ZSI7czo1OiJsaW1pdCI7czo1OiJMaW1pdCI7czoxNDoiZW5hYmxlX2Rpc2FibGUiO3M6MTY6IkVuYWJsZSUyRkRpc2FibGUiO3M6MTQ6Im1heGltdW1fbGVuZ3RoIjtzOjE2OiJNYXhpbXVtJTIwTGVuZ3RoIjtzOjE4OiJ6aXBfb3JfcG9zdGFsX2NvZGUiO3M6MjQ6IlppcCUyMG9yJTIwUG9zdGFsJTIwQ29kZSI7czo3OiJlbmFibGVkIjtzOjc6IkVuYWJsZWQiO3M6NjoidmVyaWZ5IjtzOjY6IlZlcmlmeSI7czoxNToiZGlzY291bnRfY291cG9uIjtzOjE3OiJEaXNjb3VudCUyMGNvdXBvbiI7czoxNzoiY29tcGxldGVfYm9va2luZ3MiO3M6MTk6IkNvbXBsZXRlJTIwQm9va2luZ3MiO3M6MjA6InBlcmlvZF9ub3RfYXZhaWxhYmxlIjtzOjI0OiJQZXJpb2QlMjBOb3QlMjBBdmFpbGFibGUiO3M6MTQ6ImJvb2tpbmdfcmV2aWV3IjtzOjE2OiJCb29raW5nJTIwUmV2aWV3IjtzOjY6ImNhbmNlbCI7czo2OiJDYW5jZWwiO3M6MTY6ImFsbF9ib29raW5nX2xpc3QiO3M6MjA6IkFsbCUyMEJvb2tpbmclMjBMaXN0IjtzOjY6InN1Ym1pdCI7czo2OiJTdWJtaXQiO3M6MTI6InVuaXRfYWRkX29ucyI7czoyMDoiVW5pdCUyMCUyRiUyMEFkZC1vbnMiO3M6ODoiZV9tYWlsX3MiO3M6ODoiRS1tYWlsJ3MiO3M6NToic21zX3MiO3M6NToiU01TJ3MiO30=",
									"errors_data" => "YToxMjg6e3M6NzoibW9ybmluZyI7czo3OiJNb3JuaW5nIjtzOjk6ImFmdGVybm9vbiI7czo5OiJBZnRlcm5vb24iO3M6NzoiZXZlbmluZyI7czo3OiJFdmVuaW5nIjtzOjEyOiJhcmVfeW91X3N1cmUiO3M6MTk6IkFyZSUyMHlvdSUyMHN1cmUlM0YiO3M6MTQ6Inllc19jb25maXJtX2l0IjtzOjIyOiJZZXMlMkMlMjBjb25maXJtJTIwaXQhIjtzOjE1OiJ5ZXNfY29tcGxldGVfaXQiO3M6MjM6IlllcyUyQyUyMGNvbXBsZXRlJTIwaXQhIjtzOjEzOiJ5ZXNfZGVsZXRlX2l0IjtzOjIwOiJZZXMlMkMlMjBkZWxldGUlMjBpdCI7czoxODoicmVqZWN0X2FwcG9pbnRtZW50IjtzOjIwOiJSZWplY3QlMjBBcHBvaW50bWVudCI7czoyMToiY2FuY2VsZWRfc3VjY2Vzc2Z1bGx5IjtzOjI0OiJDYW5jZWxlZCUyMFN1Y2Nlc3NmdWxseSEiO3M6NjoicmVhc29uIjtzOjY6IlJlYXNvbiI7czo2OiJzdWJtaXQiO3M6NjoiU3VibWl0IjtzOjU6ImVycm9yIjtzOjU6ImVycm9yIjtzOjQ6Im9vcHMiO3M6NzoiT29wcy4uLiI7czoyMDoic29tZXRoaW5nX3dlbnRfd3JvbmciO3M6MjU6IlNvbWV0aGluZyUyMHdlbnQlMjB3cm9uZyEiO3M6MjA6InlvdV9jYW5ub3RfZGVsZXRlX2l0IjtzOjI3OiJZb3UlMjBDYW5ub3QlMjBkZWxldGUlMjBpdCEiO3M6NzoicGVuZGluZyI7czo3OiJQZW5kaW5nIjtzOjc6ImNvbmZpcm0iO3M6NzoiQ29uZmlybSI7czo2OiJyZWplY3QiO3M6NjoiUmVqZWN0IjtzOjE5OiJyZXNjaGVkdWxlX2J5X2FkbWluIjtzOjIzOiJSZXNjaGVkdWxlJTIwQnklMjBBZG1pbiI7czoyMjoicmVzY2hlZHVsZV9ieV9jdXN0b21lciI7czoyNjoiUmVzY2hlZHVsZSUyMEJ5JTIwQ3VzdG9tZXIiO3M6MTY6ImNhbmNlbF9ieV9jbGllbnQiO3M6MjA6IkNhbmNlbCUyMEJ5JTIwQ2xpZW50IjtzOjI2OiJjYW5jZWxfYnlfc2VydmljZV9wcm92aWRlciI7czozMjoiQ2FuY2VsJTIwQnklMjBTZXJ2aWNlJTIwUHJvdmlkZXIiO3M6OToiY29tcGxldGVkIjtzOjk6IkNvbXBsZXRlZCI7czoxNToibWFya19hc19ub19zaG93IjtzOjIxOiJNYXJrJTIwQXMlMjBObyUyMFNob3ciO3M6MTQ6ImFscmVhZHlfYXNzaWduIjtzOjE2OiJBbHJlYWR5JTIwQXNzaWduIjtzOjE5OiJwbGVhc2Vfc2VsZWN0X3N0YWZmIjtzOjIzOiJQbGVhc2UlMjBTZWxlY3QlMjBTdGFmZiI7czoyMzoicGxlYXNlX2VudGVyX2NvbnRhY3Rfbm8iO3M6Mjk6IlBsZWFzZSUyMGVudGVyJTIwQ29udGFjdCUyME5vIjtzOjIzOiJwbGVhc2VfZW50ZXJfb25seV9kaWdpdCI7czoyOToiUGxlYXNlJTIwZW50ZXIlMjBPbmx5JTIwRGlnaXQiO3M6Nzoic3VjY2VzcyI7czo3OiJzdWNjZXNzIjtzOjMyOiJvbmx5X2pwZWdfYW5kX3BuZ19pbWFnZXNfYWxsb3dlZCI7czo0MjoiT25seSUyMGpwZWclMjBhbmQlMjBwbmclMjBpbWFnZXMlMjBhbGxvd2VkIjtzOjI5OiJtYXhpbXVtX2ZpbGVfdXBsb2FkX3NpemVfMl9tYiI7czozOToiTWF4aW11bSUyMGZpbGUlMjB1cGxvYWQlMjBzaXplJTIwMiUyMG1iIjtzOjI5OiJtaW5pbXVtX2ZpbGVfdXBsb2FkX3NpemVfMV9rYiI7czozOToiTWluaW11bSUyMGZpbGUlMjB1cGxvYWQlMjBzaXplJTIwMSUyMGtiIjtzOjI1OiJwbGVhc2VfZW50ZXJfb2xkX3Bhc3N3b3JkIjtzOjMxOiJQbGVhc2UlMjBFbnRlciUyME9sZCUyMFBhc3N3b3JkIjtzOjIxOiJwbGVhc2VfZW50ZXJfcGFzc3dvcmQiO3M6MjU6IlBsZWFzZSUyMEVudGVyJTIwcGFzc3dvcmQiO3M6MzM6InBsZWFzZV9lbnRlcl9taW5pbXVtXzhfY2hhcmFjdGVycyI7czo0MToiUGxlYXNlJTIwZW50ZXIlMjBtaW5pbXVtJTIwOCUyMGNoYXJhY3RlcnMiO3M6MzQ6InBsZWFzZV9lbnRlcl9tYXhpbXVtXzE1X2NoYXJhY3RlcnMiO3M6NDI6IlBsZWFzZSUyMGVudGVyJTIwbWF4aW11bSUyMDE1JTIwY2hhcmFjdGVycyI7czoyOToicGxlYXNlX2VudGVyX21heGltdW1fMTVfZGlnaXQiO3M6Mzc6IlBsZWFzZSUyMEVudGVyJTIwTWF4aW11bSUyMDE1JTIwRGlnaXQiO3M6MjM6InlvdXJfcGFzc3dvcmRfbm90X21hdGNoIjtzOjI5OiJZb3VyJTIwUGFzc3dvcmQlMjBOb3QlMjBNYXRjaCI7czoyMjoicGxlYXNlX2VudGVyX3ZhbGlkX290cCI7czoyODoiUGxlYXNlJTIwRW50ZXIlMjBWYWxpZCUyME9UUCI7czoxMDoicmVzZW5kX290cCI7czoxMjoiUmVzZW5kJTIwT1RQIjtzOjIyOiJyZXNjaGVkdWxlX2FwcG9pbnRtZW50IjtzOjI0OiJSZXNjaGVkdWxlJTIwQXBwb2ludG1lbnQiO3M6MTA6ImRheV9wZXJpb2QiO3M6MTI6IkRheSUyMFBlcmlvZCI7czoyNjoic2V0dGluZ3Nfc2F2ZV9zdWNjZXNzZnVsbHkiO3M6MzE6IlNldHRpbmdzJTIwU2F2ZSUyMFN1Y2Nlc3NmdWxseSEiO3M6Mjg6InlvdV9jYW5ub3RfYm9va19vbl9wYXN0X2RhdGUiO3M6Mzg6InlvdSUyMGNhbm5vdCUyMGJvb2slMjBvbiUyMHBhc3QlMjBkYXRlIjtzOjE1OiJzZWxlY3RfbGFuZ3VhZ2UiO3M6MTc6IlNlbGVjdCUyMExhbmd1YWdlIjtzOjIzOiJwbGVhc2Vfc2VsZWN0X2N1c3RvbWVycyI7czoyNzoiUGxlYXNlJTIwU2VsZWN0JTIwQ3VzdG9tZXJzIjtzOjIyOiJwbGVhc2VfZW50ZXJfZnVsbF9uYW1lIjtzOjI4OiJQbGVhc2UlMjBlbnRlciUyMGZ1bGwlMjBuYW1lIjtzOjI3OiJwbGVhc2VfZW50ZXJfb25seV9hbHBoYWJldHMiO3M6MzM6IlBsZWFzZSUyMGVudGVyJTIwb25seSUyMGFscGhhYmV0cyI7czoxODoicGxlYXNlX2VudGVyX2VtYWlsIjtzOjIyOiJQbGVhc2UlMjBlbnRlciUyMGVtYWlsIjtzOjI0OiJwbGVhc2VfZW50ZXJfdmFsaWRfZW1haWwiO3M6MzA6IlBsZWFzZSUyMGVudGVyJTIwdmFsaWQlMjBlbWFpbCI7czoyMDoiZW1haWxfYWxyZWFkeV9leGlzdHMiO3M6MjU6IkUtTWFpbCUyMGFscmVhZHklMjBleGlzdHMiO3M6MjU6InBsZWFzZV9lbnRlcl9waG9uZV9udW1iZXIiO3M6MzE6IlBsZWFzZSUyMGVudGVyJTIwcGhvbmUlMjBudW1iZXIiO3M6Mjg6InBsZWFzZV9lbnRlcl9taW5pbXVtXzVfZGlnaXQiO3M6MzY6InBsZWFzZSUyMEVudGVyJTIwTWluaW11bSUyMDUlMjBkaWdpdCI7czoyOToicGxlYXNlX2VudGVyX21heGltdW1fMTBfZGlnaXQiO3M6Mzc6InBsZWFzZSUyMEVudGVyJTIwbWF4aW11bSUyMDEwJTIwZGlnaXQiO3M6MzA6InBsZWFzZV9lbnRlcl9tYXhpbXVtXzEwMF9kaWdpdCI7czozODoicGxlYXNlJTIwRW50ZXIlMjBtYXhpbXVtJTIwMTAwJTIwZGlnaXQiO3M6Mjg6InBsZWFzZV9lbnRlcl9taW5pbXVtXzJfZGlnaXQiO3M6MzY6InBsZWFzZSUyMEVudGVyJTIwTWluaW11bSUyMDIlMjBkaWdpdCI7czoyODoicGxlYXNlX2VudGVyX21pbmltdW1fM19kaWdpdCI7czozNjoicGxlYXNlJTIwRW50ZXIlMjBNaW5pbXVtJTIwMyUyMGRpZ2l0IjtzOjI4OiJwbGVhc2VfZW50ZXJfbWluaW11bV84X2RpZ2l0IjtzOjM2OiJwbGVhc2UlMjBFbnRlciUyME1pbmltdW0lMjA4JTIwZGlnaXQiO3M6Mjg6InBsZWFzZV9lbnRlcl9tYXhpbXVtXzhfZGlnaXQiO3M6MzY6InBsZWFzZSUyMEVudGVyJTIwbWF4aW11bSUyMDglMjBkaWdpdCI7czoyOToicGxlYXNlX2VudGVyX21pbmltdW1fMTVfZGlnaXQiO3M6Mzc6InBsZWFzZSUyMEVudGVyJTIwTWluaW11bSUyMDE1JTIwZGlnaXQiO3M6Mjc6InBsZWFzZV9lbnRlcl9zdHJlZXRfYWRkcmVzcyI7czozMzoiUGxlYXNlJTIwRW50ZXIlMjBzdHJlZXQlMjBhZGRyZXNzIjtzOjI4OiJwbGVhc2VfZW50ZXJfemlwX2NvZGVfbnVtYmVyIjtzOjM2OiJQbGVhc2UlMjBlbnRlciUyMHppcCUyMGNvZGUlMjBudW1iZXIiO3M6MjI6IlBsZWFzZV9lbnRlcl9jaXR5X25hbWUiO3M6Mjg6IlBsZWFzZSUyMGVudGVyJTIwY2l0eSUyMG5hbWUiO3M6MjM6InBsZWFzZV9lbnRlcl9zdGF0ZV9uYW1lIjtzOjI5OiJQbGVhc2UlMjBlbnRlciUyMHN0YXRlJTIwbmFtZSI7czoyMDoicGxlYXNlX2VudGVyX3N1YmplY3QiO3M6MjQ6IlBsZWFzZSUyMGVudGVyJTIwc3ViamVjdCI7czoyMDoicGxlYXNlX2VudGVyX21hc3NhZ2UiO3M6MjQ6IlBsZWFzZSUyMGVudGVyJTIwbWFzc2FnZSI7czoyMDoiZW5hYmxlZF9zdWNjZXNzZnVsbHkiO3M6MjM6IkVuYWJsZWQlMjBTdWNjZXNzZnVsbHkhIjtzOjIxOiJkaXNhYmxlZF9zdWNjZXNzZnVsbHkiO3M6MjQ6IkRpc2FibGVkJTIwU3VjY2Vzc2Z1bGx5ISI7czoxOToiYm9va2luZ19pbmZvcm1hdGlvbiI7czoyMToiQm9va2luZyUyMEluZm9ybWF0aW9uIjtzOjIwOiJjdXN0b21lcl9pbmZvcm1hdGlvbiI7czoyMjoiQ3VzdG9tZXIlMjBJbmZvcm1hdGlvbiI7czoyMDoic2VydmljZXNfaW5mb3JtYXRpb24iO3M6MjI6IlNlcnZpY2VzJTIwSW5mb3JtYXRpb24iO3M6MjU6ImJvb2tpbmdfdW5pdHNfaW5mb3JtYXRpb24iO3M6Mjk6IkJvb2tpbmclMjBVbml0cyUyMEluZm9ybWF0aW9uIjtzOjI3OiJib29raW5nX2FkZF9vbnNfaW5mb3JtYXRpb24iO3M6MzE6IkJvb2tpbmclMjBBZGQtT25zJTIwSW5mb3JtYXRpb24iO3M6MTc6InVuaXRzX2luZm9ybWF0aW9uIjtzOjE5OiJVbml0cyUyMEluZm9ybWF0aW9uIjtzOjE5OiJhZGRfb25zX2luZm9ybWF0aW9uIjtzOjIxOiJBZGQtT25zJTIwSW5mb3JtYXRpb24iO3M6MjQ6ImxhYmVsc19zYXZlX3N1Y2Nlc3NmdWxseSI7czoyOToiTGFiZWxzJTIwU2F2ZSUyMFN1Y2Nlc3NmdWxseSEiO3M6NDM6ImF0bGVhc3Rfb25lX3BheW1lbnRfbWV0aG9kX3Nob3VsZF9iZV9lbmFibGUiO3M6NTY6IkF0LWxlYXN0JTIwT25lJTIwUGF5bWVudCUyME1ldGhvZCUyMFNob3VsZCUyMGJlJTIwRW5hYmxlIjtzOjMxOiJwbGVhc2VfZW50ZXJfc3RyaXBlX3B1Ymxpc2hfa2V5IjtzOjM5OiJQbGVhc2UlMjBlbnRlciUyMHN0cmlwZSUyMHB1Ymxpc2glMjBrZXkiO3M6MzA6InBsZWFzZV9lbnRlcl9zdHJpcGVfc2VjcmV0X2tleSI7czozODoiUGxlYXNlJTIwZW50ZXIlMjBzdHJpcGUlMjBzZWNyZXQlMjBrZXkiO3M6MzI6InBsZWFzZV9lbnRlcl9wYXlwYWxfYXBpX3VzZXJuYW1lIjtzOjQwOiJQbGVhc2UlMjBlbnRlciUyMHBheVBhbCUyMEFQSSUyMFVzZXJuYW1lIjtzOjMyOiJwbGVhc2VfZW50ZXJfcGF5cGFsX2FwaV9wYXNzd29yZCI7czo0MDoiUGxlYXNlJTIwZW50ZXIlMjBwYXlQYWwlMjBBUEklMjBQYXNzd29yZCI7czoyOToicGxlYXNlX2VudGVyX3BheXBhbF9zaWduYXR1cmUiO3M6MzU6IlBsZWFzZSUyMGVudGVyJTIwcGF5UGFsJTIwU2lnbmF0dXJlIjtzOjE3OiJwcm9tb19jb2RlX2VuYWJsZSI7czoyMToiUHJvbW8lMjBDb2RlJTIwRW5hYmxlIjtzOjE4OiJwcm9tb19jb2RlX2Rpc2FibGUiO3M6MjI6IlByb21vJTIwQ29kZSUyMERpc2FibGUiO3M6MjQ6InBsZWFzZV9lbnRlcl9jb3Vwb25fY29kZSI7czozMDoiUGxlYXNlJTIwZW50ZXIlMjBjb3Vwb24lMjBjb2RlIjtzOjI1OiJwbGVhc2VfZW50ZXJfY291cG9uX3ZhbHVlIjtzOjMxOiJQbGVhc2UlMjBlbnRlciUyMGNvdXBvbiUyMHZhbHVlIjtzOjI1OiJwbGVhc2VfZW50ZXJfbnVtZXJpY19vbmx5IjtzOjMxOiJQbGVhc2UlMjBlbnRlciUyMG51bWVyaWMlMjBvbmx5IjtzOjI1OiJwbGVhc2VfZW50ZXJfY291cG9uX2xpbWl0IjtzOjMxOiJQbGVhc2UlMjBlbnRlciUyMGNvdXBvbiUyMGxpbWl0IjtzOjIwOiJ1cGRhdGVkX3N1Y2Nlc3NmdWxseSI7czoyMjoiVXBkYXRlZCUyMFN1Y2Nlc3NmdWxseSI7czoyMDoiZGVsZXRlZF9zdWNjZXNzZnVsbHkiO3M6MjM6IkRlbGV0ZWQlMjBTdWNjZXNzZnVsbHkhIjtzOjI4OiJwbGVhc2VfZW50ZXJfcmVjdXJyZW5jZV9uYW1lIjtzOjM0OiJQbGVhc2UlMjBlbnRlciUyMHJlY3VycmVuY2UlMjBuYW1lIjtzOjI5OiJwbGVhc2VfZW50ZXJfcmVjdXJyZW5jZV9sYWJlbCI7czozNToiUGxlYXNlJTIwZW50ZXIlMjByZWN1cnJlbmNlJTIwbGFiZWwiO3M6Mjg6InBsZWFzZV9lbnRlcl9yZWN1cnJlbmNlX2RheXMiO3M6MzQ6IlBsZWFzZSUyMGVudGVyJTIwcmVjdXJyZW5jZSUyMGRheXMiO3M6MjU6InBsZWFzZV9lbnRlcl9vbmx5X251bWVyaWMiO3M6MzE6IlBsZWFzZSUyMGVudGVyJTIwb25seSUyMG51bWVyaWMiO3M6Mzg6InBsZWFzZV9lbnRlcl9yZWN1cnJlbmNlX2Rpc2NvdW50X3ZhbHVlIjtzOjQ2OiJQbGVhc2UlMjBlbnRlciUyMHJlY3VycmVuY2UlMjBkaXNjb3VudCUyMHZhbHVlIjtzOjI1OiJwbGVhc2VfZW50ZXJfcHJvcGVyX3ByaWNlIjtzOjMxOiJQbGVhc2UlMjBlbnRlciUyMFByb3BlciUyMFByaWNlIjtzOjIzOiJwbGVhc2VfZW50ZXJfdW5pdF90aXRsZSI7czoyOToiUGxlYXNlJTIwRW50ZXIlMjBVbml0JTIwVGl0bGUiO3M6MjM6InBsZWFzZV9lbnRlcl9iYXNlX3ByaWNlIjtzOjI5OiJQbGVhc2UlMjBFbnRlciUyMEJhc2UlMjBQcmljZSI7czozMDoicGxlYXNlX2VudGVyX3Byb3Blcl9iYXNlX3ByaWNlIjtzOjM4OiJQbGVhc2UlMjBFbnRlciUyMFByb3BlciUyMEJhc2UlMjBQcmljZSI7czoyNDoicGxlYXNlX2VudGVyX29ubHlfZGlnaXRzIjtzOjMwOiJQbGVhc2UlMjBFbnRlciUyME9ubHklMjBEaWdpdHMiO3M6MjI6InBsZWFzZV9lbnRlcl9tYXhfbGltaXQiO3M6Mjg6IlBsZWFzZSUyMEVudGVyJTIwTWF4JTIwTGltaXQiO3M6MTg6InBsZWFzZV9lbnRlcl9ob3VycyI7czoyMjoiUGxlYXNlJTIwRW50ZXIlMjBIb3VycyI7czoyMDoicGxlYXNlX2VudGVyX21pbnV0ZXMiO3M6MjQ6IlBsZWFzZSUyMEVudGVyJTIwTWludXRlcyI7czo0OToicGxlYXNlX2VudGVyX21pbmltdW1fNV9taW51dGVzX21heGltdW1fNTlfbWludXRlcyI7czo2MzoiUGxlYXNlJTIwRW50ZXIlMjBNaW5pbXVtJTIwNSUyME1pbnV0ZXMlMjBNYXhpbXVtJTIwNTklMjBtaW51dGVzIjtzOjIxOiJwbGVhc2VfZW50ZXJfcXVhbnRpdHkiO3M6MjU6IlBsZWFzZSUyMEVudGVyJTIwUXVhbnRpdHkiO3M6MjY6InBsZWFzZV9lbnRlcl9zZXJ2aWNlX3RpdGxlIjtzOjMyOiJQbGVhc2UlMjBlbnRlciUyMHNlcnZpY2UlMjB0aXRsZSI7czoxODoiYWRkZWRfc3VjY2Vzc2Z1bGx5IjtzOjIxOiJBZGRlZCUyMFN1Y2Nlc3NmdWxseSEiO3M6MjQ6InBsZWFzZV9lbnRlcl9hZGRvbl90aXRsZSI7czozMDoiUGxlYXNlJTIwRW50ZXIlMjBBZGRvbiUyMFRpdGxlIjtzOjIwOiJwbGVhc2Vfbm9fb2ZfY3JlZGl0cyI7czoyNzoiUGxlYXNlJTIwbm8uJTIwb2YlMjBDcmVkaXRzIjtzOjE3OiJwbGVhc2VfZW50ZXJfY2l0eSI7czoyMToiUGxlYXNlJTIwZW50ZXIlMjBjaXR5IjtzOjE4OiJwbGVhc2VfZW50ZXJfc3RhdGUiO3M6MjI6IlBsZWFzZSUyMGVudGVyJTIwc3RhdGUiO3M6MjE6InBsZWFzZV9lbnRlcl96aXBfY29kZSI7czoyNzoiUGxlYXNlJTIwZW50ZXIlMjB6aXAlMjBjb2RlIjtzOjI0OiJwbGVhc2VfcmluZ190aGVfZG9vcmJlbGwiO3M6ODY6IlBsZWFzZSUyMHByb3ZpZGUlMjBhZGRpdGlvbmFsJTIwaW5zdHJ1Y3Rpb25zLiUyMGUuZy4lMjBQbGVhc2UlMjByaW5nJTIwdGhlJTIwZG9vcmJlbGwuIjtzOjI4OiJjYWxsX21lX292ZXJfbXlfcGhvbmVfbnVtYmVyIjtzOjk0OiJQbGVhc2UlMjBwcm92aWRlJTIwYWRkaXRpb25hbCUyMGluc3RydWN0aW9ucy4lMjBlLmcuJTIwQ2FsbCUyMG1lJTIwb3ZlciUyMG15JTIwcGhvbmUlMjBudW1iZXIuIjtzOjQ2OiJrZXlfd2lsbF9iZV91bmRlcl90aGVfcG90X25leHRfdG9fdGhlX2JhY2tkb29yIjtzOjEyMDoiUGxlYXNlJTIwcHJvdmlkZSUyMGFkZGl0aW9uYWwlMjBpbnN0cnVjdGlvbnMuJTIwZS5nLiUyMEtleSUyMHdpbGwlMjBiZSUyMHVuZGVyJTIwdGhlJTIwcG90JTIwbmV4dCUyMHRvJTIwdGhlJTIwYmFja2Rvb3IuIjtzOjU5OiJwbGVhc2VfdXNlX3RoZV9zaWRlX2dhdGVfYW5kX3VzZV90aGVfc2xpZGluZ19kb29yc190b19lbnRlciI7czoxNzc6IlBsZWFzZSUyMHByb3ZpZGUlMjBhZGRpdGlvbmFsJTIwaW5zdHJ1Y3Rpb25zLiUyMGUuZy4lMjBQbGVhc2UlMjB1c2UlMjB0aGUlMjBzaWRlJTIwZ2F0ZSUyMGFuZCUyMHVzZSUyMHRoZSUyMHNsaWRpbmclMjBkb29ycyUyMHRvJTIwZW50ZXIlMkMlMjB0aGV5JTIwd2lsbCUyMGJlJTIwbGVmdCUyMHVubG9ja2VkLiI7czoyMzoicGxlYXNlX3NlbGVjdF9hX3NlcnZpY2UiO3M6Mjk6IlBsZWFzZSUyMHNlbGVjdCUyMGElMjBzZXJ2aWNlIjtzOjIwOiJwbGVhc2Vfc2VsZWN0X2FfdW5pdCI7czoyNjoiUGxlYXNlJTIwc2VsZWN0JTIwYSUyMHVuaXQiO3M6MjU6InBsZWFzZV9lbnRlcl92YWxpZF9udW1iZXIiO3M6MzE6IlBsZWFzZSUyMGVudGVyJTIwdmFsaWQlMjBudW1iZXIiO3M6MTQ6InJlYWNoX21heGxpbWl0IjtzOjE2OiJSZWFjaCUyME1heGxpbWl0IjtzOjE0OiJ0ZW1wbGV0X2VuYWJsZSI7czoxNjoiVGVtcGxldCUyMEVuYWJsZSI7czoxNToidGVtcGxldF9kaXNhYmxlIjtzOjE3OiJUZW1wbGV0JTIwRGlzYWJsZSI7czoyNToicGxlYXNlX2VudGVyX3ZhbGlkX2VfbWFpbCI7czozMToiUGxlYXNlJTIwRW50ZXIlMjBWYWxpZCUyMEUtTWFpbCI7czoyODoicGxlYXNlX2VudGVyX3ZhbGlkX3Byb21vY29kZSI7czozNDoiUGxlYXNlJTIwZW50ZXIlMjB2YWxpZCUyMHByb21vY29kZSI7czozMToieW91cl9wcm9tb2NvZGVfaGFzX2JlZW5fZXhwaXJlZCI7czozOToiWW91ciUyMFByb21vY29kZSUyMGhhcyUyMGJlZW4lMjBleHBpcmVkIjtzOjI4OiJwbGVhc2VfZW50ZXJfemlwX3Bvc3RhbF9jb2RlIjtzOjM2OiJQbGVhc2UlMjBFbnRlciUyMFppcCUyRlBvc3RhbCUyMENvZGUiO3M6NDI6Im91cl9zZXJ2aWNlX25vdF9hdmFpbGFibGVfYXRfeW91cl9sb2NhdGlvbiI7czo1NDoiT3VyJTIwc2VydmljZSUyMG5vdCUyMGF2YWlsYWJsZSUyMGF0JTIweW91ciUyMGxvY2F0aW9uIjtzOjIyOiJvbGRfcGFzc3dvcmRfbm90X21hdGNoIjtzOjI4OiJPbGQlMjBQYXNzd29yZCUyMG5vdCUyMG1hdGNoIjt9",
								);
		$Language_Insert_Insert = $this->Super_Admin_Model->insert_Language_Data($Language_Insert);

		$Recurrence_Discount_Insert = array();
		$Recurrence_Discount_Insert[] = array("recurrence_name" => "Once",
									"recurrence_label"=>"ZERO",
									"recurrence_days"=>"0",
									"recurrence_type"=>"P",
									"recurrence_rate"=>"0",
									"status"=>"E",
									"booking_count"=>"0",
									"business_id"=> $New_Business_ID);
		$Recurrence_Discount_Insert[] = array("recurrence_name" => "Weekly",
									"recurrence_label"=>"SAVE 15%",
									"recurrence_days"=>"7",
									"recurrence_type"=>"P",
									"recurrence_rate"=>"15",
									"status"=>"E",
									"booking_count"=>"14",
									"business_id"=> $New_Business_ID);
		$Recurrence_Discount_Insert[] = array("recurrence_name" => "Bi-Weekly",
									"recurrence_label"=>"SAVE 12.5%",
									"recurrence_days"=>"3",
									"recurrence_type"=>"P",
									"recurrence_rate"=>"12.5",
									"status"=>"E",
									"booking_count"=>"31",
									"business_id"=> $New_Business_ID);
		$Recurrence_Discount_Insert[] = array("recurrence_name" => "Monthly",
									"recurrence_label"=>"SAVE 10%",
									"recurrence_days"=>"30",
									"recurrence_type"=>"P",
									"recurrence_rate"=>"10",
									"status"=>"E",
									"booking_count"=>"4",
									"business_id"=> $New_Business_ID);
		$Recurrence_Discount_Data_Insert = $this->Super_Admin_Model->insert_Recurrence_Discount_Data($Recurrence_Discount_Insert);
		
		$E_Mail_Templet_Insert = array();
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Request",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2FkbWluX25hbWV9fSw8L2g2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij5Zb3UndmUgbmV3IGFwcG9pbnRtZW50IHdpdGgge3tjbGllbnRfbmFtZX19IHdpdGggZm9sbG93aW5nIGRldGFpbHM6IDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDtsaW5lLWhlaWdodDogMjJweDttYXJnaW46IDEwcHggMHB4IDE1cHg7ZmxvYXQ6IGxlZnQ7Ij5UaGlzIGFwcG9pbnRtZW50IGlzIGluIHBlbmRpbmcuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"P",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Request",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIHNldCBhIG5ldyBhcHBvaW50bWVudCB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+WW91ciBhcHBvaW50bWVudCBpcyB0ZW50YXRpdmUgYW5kIHlvdSB3aWxsIGJlIG5vdGlmaWVkIGFzIHdlIHdpbGwgY29uZmlybSB0aGlzIGJvb2tpbmcuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"P",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Request",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIG5ldyBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+VGhpcyBhcHBvaW50bWVudCBpcyBwZW5kaW5nLjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMHB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrO2JvcmRlci10b3A6IDFweCBzb2xpZCAjZTZlNmU2OyI+CgkJCQkJCQk8aDUgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTNweDttYXJnaW46IDBweCAwcHggNXB4OyI+VGhhbmsgeW91LDwvaDU+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTRweDtmb250LXdlaWdodDogNjAwO21hcmdpbjogMTBweCAwcHggMTVweDsiPnt7Y29tcGFueV9uYW1lfX08L2g2PgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJPC90Ym9keT4KCTwvdGFibGU+Cgk8L2Rpdj4KCTwvZGl2PgoJPC9ib2R5Pgo8L2h0bWw+",
									"email_template_status"=>"E",
									"email_template_type"=>"P",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Approved",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2FkbWluX25hbWV9fSw8L2g2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij5UaGUgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gd2l0aCBmb2xsb3dpbmcgZGV0YWlsczogPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8cCBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNXB4O2xpbmUtaGVpZ2h0OiAyMnB4O21hcmdpbjogMTBweCAwcHggMTVweDtmbG9hdDogbGVmdDsiPlRoaXMgYXBwb2ludG1lbnQgaGFzIGJlZW4gY29uZmlybWVkLjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMHB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrO2JvcmRlci10b3A6IDFweCBzb2xpZCAjZTZlNmU2OyI+CgkJCQkJCQk8aDUgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTNweDttYXJnaW46IDBweCAwcHggNXB4OyI+VGhhbmsgeW91LDwvaDU+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTRweDtmb250LXdlaWdodDogNjAwO21hcmdpbjogMTBweCAwcHggMTVweDsiPnt7Y29tcGFueV9uYW1lfX08L2g2PgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJPC90Ym9keT4KCTwvdGFibGU+Cgk8L2Rpdj4KCTwvZGl2PgoJPC9ib2R5Pgo8L2h0bWw+",
									"email_template_status"=>"E",
									"email_template_type"=>"C",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Approved",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIHNldCBhIG5ldyBhcHBvaW50bWVudCB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+WW91ciBhcHBvaW50bWVudCBoYXMgYmVlbiBjb25maXJtZWQuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"C",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Approved",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+VGhlIGFwcG9pbnRtZW50IHdpdGgge3tjbGllbnRfbmFtZX19IHdpdGggZm9sbG93aW5nIGRldGFpbHM6IDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDtsaW5lLWhlaWdodDogMjJweDttYXJnaW46IDEwcHggMHB4IDE1cHg7ZmxvYXQ6IGxlZnQ7Ij5UaGlzIGFwcG9pbnRtZW50IGhhcyBiZWVuIGNvbmZpcm1lZC48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"C",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rejected",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2FkbWluX25hbWV9fSw8L2g2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij5UaGUgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gd2l0aCBmb2xsb3dpbmcgZGV0YWlsczogPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8cCBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNXB4O2xpbmUtaGVpZ2h0OiAyMnB4O21hcmdpbjogMTBweCAwcHggMTVweDtmbG9hdDogbGVmdDsiPlRoaXMgYXBwb2ludG1lbnQgaGFzIGJlZW4gcmVqZWN0ZWQuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"R",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rejected",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIHNldCBhIG5ldyBhcHBvaW50bWVudCB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+WW91ciBhcHBvaW50bWVudCBoYXMgYmVlbiByZWplY3RlZC48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"R",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rejected",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+VGhlIGFwcG9pbnRtZW50IHdpdGgge3tjbGllbnRfbmFtZX19IHdpdGggZm9sbG93aW5nIGRldGFpbHM6IDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDtsaW5lLWhlaWdodDogMjJweDttYXJnaW46IDEwcHggMHB4IDE1cHg7ZmxvYXQ6IGxlZnQ7Ij5UaGlzIGFwcG9pbnRtZW50IGhhcyBiZWVuIHJlamVjdGVkLjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMHB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrO2JvcmRlci10b3A6IDFweCBzb2xpZCAjZTZlNmU2OyI+CgkJCQkJCQk8aDUgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTNweDttYXJnaW46IDBweCAwcHggNXB4OyI+VGhhbmsgeW91LDwvaDU+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTRweDtmb250LXdlaWdodDogNjAwO21hcmdpbjogMTBweCAwcHggMTVweDsiPnt7Y29tcGFueV9uYW1lfX08L2g2PgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJPC90Ym9keT4KCTwvdGFibGU+Cgk8L2Rpdj4KCTwvZGl2PgoJPC9ib2R5Pgo8L2h0bWw+",
									"email_template_status"=>"E",
									"email_template_type"=>"R",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rescheduled by You",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2FkbWluX25hbWV9fSw8L2g2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij5UaGUgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gd2l0aCBmb2xsb3dpbmcgZGV0YWlsczogPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8cCBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNXB4O2xpbmUtaGVpZ2h0OiAyMnB4O21hcmdpbjogMTBweCAwcHggMTVweDtmbG9hdDogbGVmdDsiPlRoaXMgYXBwb2ludG1lbnQgaGFzIGJlZW4gcmVzY2hlZHVsZWQuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"RSA",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rescheduled by Service Provider",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIHNldCBhIG5ldyBhcHBvaW50bWVudCB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+WW91ciBhcHBvaW50bWVudCBoYXMgYmVlbiByZXNjaGVkdWxlZC48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"RSA",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rescheduled by Service Provider",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+VGhlIGFwcG9pbnRtZW50IHdpdGgge3tjbGllbnRfbmFtZX19IHdpdGggZm9sbG93aW5nIGRldGFpbHM6IDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDtsaW5lLWhlaWdodDogMjJweDttYXJnaW46IDEwcHggMHB4IDE1cHg7ZmxvYXQ6IGxlZnQ7Ij5UaGlzIGFwcG9pbnRtZW50IGhhcyBiZWVuIHJlc2NoZWR1bGVkLjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMHB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrO2JvcmRlci10b3A6IDFweCBzb2xpZCAjZTZlNmU2OyI+CgkJCQkJCQk8aDUgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTNweDttYXJnaW46IDBweCAwcHggNXB4OyI+VGhhbmsgeW91LDwvaDU+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTRweDtmb250LXdlaWdodDogNjAwO21hcmdpbjogMTBweCAwcHggMTVweDsiPnt7Y29tcGFueV9uYW1lfX08L2g2PgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJPC90Ym9keT4KCTwvdGFibGU+Cgk8L2Rpdj4KCTwvZGl2PgoJPC9ib2R5Pgo8L2h0bWw+",
									"email_template_status"=>"E",
									"email_template_type"=>"RSA",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rescheduled by Customer",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2FkbWluX25hbWV9fSw8L2g2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij5UaGUgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gd2l0aCBmb2xsb3dpbmcgZGV0YWlsczogPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8cCBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNXB4O2xpbmUtaGVpZ2h0OiAyMnB4O21hcmdpbjogMTBweCAwcHggMTVweDtmbG9hdDogbGVmdDsiPlRoaXMgYXBwb2ludG1lbnQgaGFzIGJlZW4gcmVzY2hlZHVsZWQuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"RSC",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rescheduled by You",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIHNldCBhIG5ldyBhcHBvaW50bWVudCB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+WW91ciBhcHBvaW50bWVudCBoYXMgYmVlbiByZXNjaGVkdWxlZC48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"RSC",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Rescheduled by Customer",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+VGhlIGFwcG9pbnRtZW50IHdpdGgge3tjbGllbnRfbmFtZX19IHdpdGggZm9sbG93aW5nIGRldGFpbHM6IDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDtsaW5lLWhlaWdodDogMjJweDttYXJnaW46IDEwcHggMHB4IDE1cHg7ZmxvYXQ6IGxlZnQ7Ij5UaGlzIGFwcG9pbnRtZW50IGhhcyBiZWVuIHJlc2NoZWR1bGVkLjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMHB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrO2JvcmRlci10b3A6IDFweCBzb2xpZCAjZTZlNmU2OyI+CgkJCQkJCQk8aDUgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTNweDttYXJnaW46IDBweCAwcHggNXB4OyI+VGhhbmsgeW91LDwvaDU+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTRweDtmb250LXdlaWdodDogNjAwO21hcmdpbjogMTBweCAwcHggMTVweDsiPnt7Y29tcGFueV9uYW1lfX08L2g2PgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJPC90Ym9keT4KCTwvdGFibGU+Cgk8L2Rpdj4KCTwvZGl2PgoJPC9ib2R5Pgo8L2h0bWw+",
									"email_template_status"=>"E",
									"email_template_type"=>"RSC",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Cancelled By You",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2FkbWluX25hbWV9fSw8L2g2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij5UaGUgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gd2l0aCBmb2xsb3dpbmcgZGV0YWlsczogPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8cCBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNXB4O2xpbmUtaGVpZ2h0OiAyMnB4O21hcmdpbjogMTBweCAwcHggMTVweDtmbG9hdDogbGVmdDsiPlRoaXMgYXBwb2ludG1lbnQgaGFzIGJlZW4gY2FuY2VsbGVkLjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMHB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrO2JvcmRlci10b3A6IDFweCBzb2xpZCAjZTZlNmU2OyI+CgkJCQkJCQk8aDUgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTNweDttYXJnaW46IDBweCAwcHggNXB4OyI+VGhhbmsgeW91LDwvaDU+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTRweDtmb250LXdlaWdodDogNjAwO21hcmdpbjogMTBweCAwcHggMTVweDsiPnt7Y29tcGFueV9uYW1lfX08L2g2PgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJPC90Ym9keT4KCTwvdGFibGU+Cgk8L2Rpdj4KCTwvZGl2PgoJPC9ib2R5Pgo8L2h0bWw+",
									"email_template_status"=>"E",
									"email_template_type"=>"CS",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Cancelled By Service Provider",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIHNldCBhIG5ldyBhcHBvaW50bWVudCB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+WW91ciBhcHBvaW50bWVudCBoYXMgYmVlbiBjYW5jZWxsZWQuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"CS",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Cancelled By Service Provider",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+VGhlIGFwcG9pbnRtZW50IHdpdGgge3tjbGllbnRfbmFtZX19IHdpdGggZm9sbG93aW5nIGRldGFpbHM6IDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDtsaW5lLWhlaWdodDogMjJweDttYXJnaW46IDEwcHggMHB4IDE1cHg7ZmxvYXQ6IGxlZnQ7Ij5UaGlzIGFwcG9pbnRtZW50IGhhcyBiZWVuIGNhbmNlbGxlZC48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"CS",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Cancelled By Customer",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2FkbWluX25hbWV9fSw8L2g2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij5UaGUgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gd2l0aCBmb2xsb3dpbmcgZGV0YWlsczogPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8cCBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNXB4O2xpbmUtaGVpZ2h0OiAyMnB4O21hcmdpbjogMTBweCAwcHggMTVweDtmbG9hdDogbGVmdDsiPlRoaXMgYXBwb2ludG1lbnQgaGFzIGJlZW4gY2FuY2VsbGVkLjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMHB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrO2JvcmRlci10b3A6IDFweCBzb2xpZCAjZTZlNmU2OyI+CgkJCQkJCQk8aDUgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTNweDttYXJnaW46IDBweCAwcHggNXB4OyI+VGhhbmsgeW91LDwvaDU+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTRweDtmb250LXdlaWdodDogNjAwO21hcmdpbjogMTBweCAwcHggMTVweDsiPnt7Y29tcGFueV9uYW1lfX08L2g2PgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJPC90Ym9keT4KCTwvdGFibGU+Cgk8L2Rpdj4KCTwvZGl2PgoJPC9ib2R5Pgo8L2h0bWw+",
									"email_template_status"=>"E",
									"email_template_type"=>"CC",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Cancelled By You",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIHNldCBhIG5ldyBhcHBvaW50bWVudCB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+WW91ciBhcHBvaW50bWVudCBoYXMgYmVlbiBjYW5jZWxsZWQuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"CC",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Cancelled By Customer",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+VGhlIGFwcG9pbnRtZW50IHdpdGgge3tjbGllbnRfbmFtZX19IHdpdGggZm9sbG93aW5nIGRldGFpbHM6IDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDtsaW5lLWhlaWdodDogMjJweDttYXJnaW46IDEwcHggMHB4IDE1cHg7ZmxvYXQ6IGxlZnQ7Ij5UaGlzIGFwcG9pbnRtZW50IGhhcyBiZWVuIGNhbmNlbGxlZC48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"CC",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Completed",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2FkbWluX25hbWV9fSw8L2g2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij5UaGUgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gd2l0aCBmb2xsb3dpbmcgZGV0YWlsczogPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8cCBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNXB4O2xpbmUtaGVpZ2h0OiAyMnB4O21hcmdpbjogMTBweCAwcHggMTVweDtmbG9hdDogbGVmdDsiPlRoaXMgYXBwb2ludG1lbnQgaGFzIGJlZW4gY29tcGxldGVkLjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMHB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrO2JvcmRlci10b3A6IDFweCBzb2xpZCAjZTZlNmU2OyI+CgkJCQkJCQk8aDUgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTNweDttYXJnaW46IDBweCAwcHggNXB4OyI+VGhhbmsgeW91LDwvaDU+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTRweDtmb250LXdlaWdodDogNjAwO21hcmdpbjogMTBweCAwcHggMTVweDsiPnt7Y29tcGFueV9uYW1lfX08L2g2PgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJPC90Ym9keT4KCTwvdGFibGU+Cgk8L2Rpdj4KCTwvZGl2PgoJPC9ib2R5Pgo8L2h0bWw+",
									"email_template_status"=>"E",
									"email_template_type"=>"CO",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Completed",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+WW91J3ZlIHNldCBhIG5ldyBhcHBvaW50bWVudCB3aXRoIGZvbGxvd2luZyBkZXRhaWxzOiA8L2I+PC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0iZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5XaGVuIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Ym9va2luZ19kYXRlfX0gIHt7Ym9va2luZ19wZXJpb2R9fTwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkZvciA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3NlcnZpY2VfbmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlVuaXRzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7dW5pdHN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGQtb25zIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7YWRkb25zfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+TmFtZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2ZpcnN0bmFtZX19IHt7bGFzdG5hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5FbWFpbCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9lbWFpbH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBob25lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X3Bob25lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkcmVzcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9hZGRyZXNzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Tm90ZXMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tub3Rlc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlByaWNlIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cHJpY2V9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QYXltZW50IDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7cGF5bWVudF9tZXRob2R9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bGluZS1oZWlnaHQ6IDIycHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4O2Zsb2F0OiBsZWZ0OyI+WW91ciBhcHBvaW50bWVudCBoYXMgYmVlbiBjb21wbGV0ZWQuPC9wPgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"CO",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Completed",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+VGhlIGFwcG9pbnRtZW50IHdpdGgge3tjbGllbnRfbmFtZX19IHdpdGggZm9sbG93aW5nIGRldGFpbHM6IDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPHAgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDtsaW5lLWhlaWdodDogMjJweDttYXJnaW46IDEwcHggMHB4IDE1cHg7ZmxvYXQ6IGxlZnQ7Ij5UaGlzIGFwcG9pbnRtZW50IGhhcyBiZWVuIGNvbXBsZXRlZC48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"CO",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Reminder",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPXBhZGRpbmc6IDI1cHggMzBweDtiYWNrZ3JvdW5kOiAjZmZmO2Zsb2F0OiBsZWZ0O2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCTxkaXYgc3R5bGU9ImJvcmRlci1ib3R0b206IDFweCBzb2xpZCAjZTZlNmU2O2Zsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8aDYgc3R5bGU9ImNvbG9yOiAjNjA2MDYwO2ZvbnQtc2l6ZTogMTVweDttYXJnaW46IDEwcHggMHB4IDEwcHg7Zm9udC13ZWlnaHQ6IDYwMDsiPkhpIHt7YWRtaW5fbmFtZX19LDwvaDY+CgkJCQkJCQk8cCBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNXB4O21hcmdpbjogMTBweCAwcHggMTVweDsiPldlIGp1c3Qgd2FudGVkIHRvIHJlbWluZCB5b3UgdGhhdCB5b3UgaGF2ZSBhcHBvaW50bWVudCB3aXRoICB7e2NsaWVudF9uYW1lfX0gaXMgc2NoZWR1bGVkIGluIHt7YXBwX3JlbWFpbl90aW1lfX0gaG91cnMuIDwvYj48L3A+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPldoZW4gOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tib29raW5nX2RhdGV9fSAge3tib29raW5nX3BlcmlvZH19PC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+Rm9yIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7c2VydmljZV9uYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+VW5pdHMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3t1bml0c319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZC1vbnMgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3thZGRvbnN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5OYW1lIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Zmlyc3RuYW1lfX0ge3tsYXN0bmFtZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkVtYWlsIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2VtYWlsfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGhvbmUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfcGhvbmV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5BZGRyZXNzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7Y2xpZW50X2FkZHJlc3N9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Ob3RlcyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e25vdGVzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UHJpY2UgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twcmljZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPlBheW1lbnQgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3twYXltZW50X21ldGhvZH19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQk8L2Rpdj4KCQkJCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTBweCAwcHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ym9yZGVyLXRvcDogMXB4IHNvbGlkICNlNmU2ZTY7Ij4KCQkJCQkJCTxoNSBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxM3B4O21hcmdpbjogMHB4IDBweCA1cHg7Ij5UaGFuayB5b3UsPC9oNT4KCQkJCQkJCTxoNiBzdHlsZT0iY29sb3I6ICM2MDYwNjA7Zm9udC1zaXplOiAxNHB4O2ZvbnQtd2VpZ2h0OiA2MDA7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+e3tjb21wYW55X25hbWV9fTwvaDY+CgkJCQkJCTwvZGl2PgoJCQkJCTwvZGl2PgoJCQkJPC90ZD4KCQkJPC90cj4KCQk8L3Rib2R5PgoJPC90YWJsZT4KCTwvZGl2PgoJPC9kaXY+Cgk8L2JvZHk+CjwvaHRtbD4=",
									"email_template_status"=>"E",
									"email_template_type"=>"RM",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Reminder",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e2NsaWVudF9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+V2UganVzdCB3YW50ZWQgdG8gcmVtaW5kIHlvdSB0aGF0IHlvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2FkbWluX25hbWV9fSBpcyBzY2hlZHVsZWQgaW4ge3thcHBfcmVtYWluX3RpbWV9fSBob3Vycy4gPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"RM",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Insert[] = array("email_subject" => "Appointment Reminder",
									"email_message"=>"",
									"default_message"=>"PGh0bWw+Cgk8aGVhZD4KCTxtZXRhIG5hbWU9InZpZXdwb3J0IiBjb250ZW50PSJ3aWR0aD1kZXZpY2Utd2lkdGgsIGluaXRpYWwtc2NhbGU9MS4wIi8+Cgk8bWV0YSBodHRwLWVxdWl2PSJDb250ZW50LVR5cGUiIGNvbnRlbnQ9InRleHQvaHRtbDsgY2hhcnNldD1VVEYtOCIgLz4KCTx0aXRsZT5BcHBvaW50bWVudCBFbWFpbDwvdGl0bGU+Cgk8L2hlYWQ+Cgk8Ym9keT4KCTxkaXYgc3R5bGU9Im1hcmdpbjogMDtwYWRkaW5nOiAwO2ZvbnQtZmFtaWx5OiAnSGVsdmV0aWNhIE5ldWUnLCAnSGVsdmV0aWNhJywgSGVsdmV0aWNhLCBBcmlhbCwgc2Fucy1zZXJpZjtmb250LXNpemU6IDEwMCU7bGluZS1oZWlnaHQ6IDEuNjtib3gtc2l6aW5nOiBib3JkZXItYm94OyI+Cgk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50O21heC13aWR0aDogNjAwcHggIWltcG9ydGFudDttYXJnaW46IDAgYXV0byAhaW1wb3J0YW50O2NsZWFyOiBib3RoICFpbXBvcnRhbnQ7Ij4KCTx0YWJsZSBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2MyYzJjMjt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDttYXJnaW46IDMwcHggMHB4Oy13ZWJraXQtYm9yZGVyLXJhZGl1czogNXB4Oy1tb3otYm9yZGVyLXJhZGl1czogNXB4Oy1vLWJvcmRlci1yYWRpdXM6IDVweDtib3JkZXItcmFkaXVzOiA1cHg7Ij4KCQk8dGJvZHk+CgkJCTx0ciBzdHlsZT0iYm9yZGVyLWJvdHRvbTogMXB4IHNvbGlkICNlNmU2ZTY7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDEwMCU7ZGlzcGxheTogYmxvY2s7Ij4KCQkJCTx0ZCBzdHlsZT0id2lkdGg6IDEwMCU7dmVydGljYWwtYWxpZ246IHRvcDtmbG9hdDogbGVmdDsiPgoJCQkJCTxkaXYgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7ZmxvYXQ6IGxlZnQ7cGFkZGluZzozNXB4IDE1cHg7d2lkdGg6IDkzJTtjbGVhcjogbGVmdDsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJ3aWR0aDogYXV0bztoZWlnaHQ6IDgwcHg7dmVydGljYWwtYWxpZ246IHRvcDttYXJnaW46IDBweCBhdXRvO3RleHQtYWxpZ246IGNlbnRlcjsiPgoJCQkJCQkJPGltZyBzdHlsZT0id2lkdGg6IGF1dG87ZGlzcGxheTogaW5saW5lLWJsb2NrO2hlaWdodDogMTAwJTsiIHNyYz0ie3tjb21wYW55X2xvZ299fSIgYWx0PSJ7e2NvbXBhbnlfbG9nb319IiAvPgoJCQkJCQk8L2Rpdj4KCQkJCQk8L2Rpdj4KCQkJCTwvdGQ+CgkJCTwvdHI+CgkJCTx0cj4KCQkJCTx0ZD4KCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAyNXB4IDMwcHg7YmFja2dyb3VuZDogI2ZmZjtmbG9hdDogbGVmdDtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQk8ZGl2IHN0eWxlPSJib3JkZXItYm90dG9tOiAxcHggc29saWQgI2U2ZTZlNjtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jazsiPgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxMHB4O2ZvbnQtd2VpZ2h0OiA2MDA7Ij5IaSB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sPC9oNj4KCQkJCQkJCTxwIHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE1cHg7bWFyZ2luOiAxMHB4IDBweCAxNXB4OyI+V2UganVzdCB3YW50ZWQgdG8gcmVtaW5kIHlvdSB0aGF0IHlvdSBoYXZlIGFwcG9pbnRtZW50IHdpdGggIHt7Y2xpZW50X25hbWV9fSBpcyBzY2hlZHVsZWQgaW4ge3thcHBfcmVtYWluX3RpbWV9fSBob3Vycy4gPC9iPjwvcD4KCQkJCQkJPC9kaXY+CgkJCQkJCTxkaXYgc3R5bGU9ImZsb2F0OiBsZWZ0O3dpZHRoOiAxMDAlO2Rpc3BsYXk6IGJsb2NrOyI+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+V2hlbiA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2Jvb2tpbmdfZGF0ZX19ICB7e2Jvb2tpbmdfcGVyaW9kfX08L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Gb3IgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tzZXJ2aWNlX25hbWV9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5Vbml0cyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3VuaXRzfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+QWRkLW9ucyA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2FkZG9uc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5hbWUgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tmaXJzdG5hbWV9fSB7e2xhc3RuYW1lfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+RW1haWwgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfZW1haWx9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QaG9uZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e2NsaWVudF9waG9uZX19IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPkFkZHJlc3MgOiA8L2xhYmVsPgoJCQkJCQkJCTxzcGFuIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Zm9udC13ZWlnaHQ6IDQwMDtjb2xvcjogIzYwNjA2MDtsaW5lLWhlaWdodDogMjVweDtmbG9hdDogbGVmdDt3aWR0aDogNzAlOyI+e3tjbGllbnRfYWRkcmVzc319IDwvc3Bhbj4KCQkJCQkJCTwvZGl2PgoJCQkJCQkJPGRpdiBzdHlsZT0iZGlzcGxheTogaW5saW5lLWJsb2NrO3dpZHRoOiAxMDAlO2Zsb2F0OiBsZWZ0OyI+CgkJCQkJCQkJPGxhYmVsIHN0eWxlPSJmb250LXNpemU6IDE1cHg7Y29sb3I6ICM5OTk5OTk7cGFkZGluZy1yaWdodDogNXB4O3dpZHRoOiAyNSU7d2hpdGUtc3BhY2U6IG5vd3JhcDtmbG9hdDogbGVmdDtsaW5lLWhlaWdodDogMjVweDsiPk5vdGVzIDogPC9sYWJlbD4KCQkJCQkJCQk8c3BhbiBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2ZvbnQtd2VpZ2h0OiA0MDA7Y29sb3I6ICM2MDYwNjA7bGluZS1oZWlnaHQ6IDI1cHg7ZmxvYXQ6IGxlZnQ7d2lkdGg6IDcwJTsiPnt7bm90ZXN9fSA8L3NwYW4+CgkJCQkJCQk8L2Rpdj4KCQkJCQkJCTxkaXYgc3R5bGU9ImRpc3BsYXk6IGlubGluZS1ibG9jazt3aWR0aDogMTAwJTtmbG9hdDogbGVmdDsiPgoJCQkJCQkJCTxsYWJlbCBzdHlsZT0iZm9udC1zaXplOiAxNXB4O2NvbG9yOiAjOTk5OTk5O3BhZGRpbmctcmlnaHQ6IDVweDt3aWR0aDogMjUlO3doaXRlLXNwYWNlOiBub3dyYXA7ZmxvYXQ6IGxlZnQ7bGluZS1oZWlnaHQ6IDI1cHg7Ij5QcmljZSA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3ByaWNlfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCQk8ZGl2IHN0eWxlPSJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7d2lkdGg6IDEwMCU7ZmxvYXQ6IGxlZnQ7Ij4KCQkJCQkJCQk8bGFiZWwgc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtjb2xvcjogIzk5OTk5OTtwYWRkaW5nLXJpZ2h0OiA1cHg7d2lkdGg6IDI1JTt3aGl0ZS1zcGFjZTogbm93cmFwO2Zsb2F0OiBsZWZ0O2xpbmUtaGVpZ2h0OiAyNXB4OyI+UGF5bWVudCA6IDwvbGFiZWw+CgkJCQkJCQkJPHNwYW4gc3R5bGU9ImZvbnQtc2l6ZTogMTVweDtmb250LXdlaWdodDogNDAwO2NvbG9yOiAjNjA2MDYwO2xpbmUtaGVpZ2h0OiAyNXB4O2Zsb2F0OiBsZWZ0O3dpZHRoOiA3MCU7Ij57e3BheW1lbnRfbWV0aG9kfX0gPC9zcGFuPgoJCQkJCQkJPC9kaXY+CgkJCQkJCTwvZGl2PgoJCQkJCQk8ZGl2IHN0eWxlPSJwYWRkaW5nOiAxMHB4IDBweDtmbG9hdDogbGVmdDt3aWR0aDogMTAwJTtkaXNwbGF5OiBibG9jaztib3JkZXItdG9wOiAxcHggc29saWQgI2U2ZTZlNjsiPgoJCQkJCQkJPGg1IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDEzcHg7bWFyZ2luOiAwcHggMHB4IDVweDsiPlRoYW5rIHlvdSw8L2g1PgoJCQkJCQkJPGg2IHN0eWxlPSJjb2xvcjogIzYwNjA2MDtmb250LXNpemU6IDE0cHg7Zm9udC13ZWlnaHQ6IDYwMDttYXJnaW46IDEwcHggMHB4IDE1cHg7Ij57e2NvbXBhbnlfbmFtZX19PC9oNj4KCQkJCQkJPC9kaXY+CgkJCQkJPC9kaXY+CgkJCQk8L3RkPgoJCQk8L3RyPgoJCTwvdGJvZHk+Cgk8L3RhYmxlPgoJPC9kaXY+Cgk8L2Rpdj4KCTwvYm9keT4KPC9odG1sPg==",
									"email_template_status"=>"E",
									"email_template_type"=>"RM",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$E_Mail_Templet_Data_Insert = $this->Super_Admin_Model->insert_E_Mail_Templet_Data($E_Mail_Templet_Insert);
		
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Request",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91IGhhdmUgYW4gYXBwb2ludG1lbnQgb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fQ==",
									"sms_template_status"=>"E",
									"sms_template_type"=>"P",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Request",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdSBoYXZlIGFuIGFwcG9pbnRtZW50IG9uIHt7Ym9va2luZ19kYXRlfX0gZm9yIHt7c2VydmljZV9uYW1lfX0=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"P",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Request",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdSBoYXZlIGFuIGFwcG9pbnRtZW50IG9uIHt7Ym9va2luZ19kYXRlfX0gZm9yIHt7c2VydmljZV9uYW1lfX0u",
									"sms_template_status"=>"E",
									"sms_template_type"=>"P",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Approved",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91ciBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSBvbiB7e2Jvb2tpbmdfZGF0ZX19IGZvciB7e3NlcnZpY2VfbmFtZX19IGhhcyBiZWVuIGNvbmZpcm1lZC4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"C",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Approved",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiBjb25maXJtZWQu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"C",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Approved",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiBjb25maXJtZWQu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"C",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rejected",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91ciBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSBvbiB7e2Jvb2tpbmdfZGF0ZX19IGZvciB7e3NlcnZpY2VfbmFtZX19IGhhcyBiZWVuIHJlamVjdGVkLg==",
									"sms_template_status"=>"E",
									"sms_template_type"=>"R",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rejected",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiByZWplY3RlZC4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"R",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rejected",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiByZWplY3RlZC4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"R",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rescheduled by You",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91ciBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSBvbiB7e2Jvb2tpbmdfZGF0ZX19IGZvciB7e3NlcnZpY2VfbmFtZX19IGhhcyBiZWVuIHJlc2NoZWR1bGUgYnkgeW91Lg==",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RSA",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rescheduled by Service Provider",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiByZXNjaGVkdWxlZCBieSBzZXJ2aWNlIHByb3ZpZGVyLg==",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RSA",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rescheduled by Service Provider",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiByZXNjaGVkdWxlIGJ5IHNlcnZpY2UgcHJvdmlkZXIu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RSA",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rescheduled by Customer",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91ciBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSBvbiB7e2Jvb2tpbmdfZGF0ZX19IGZvciB7e3NlcnZpY2VfbmFtZX19IGhhcyBiZWVuIHJlc2NoZWR1bGUgYnkgY3VzdG9tZXIu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RSC",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rescheduled by You",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiByZXNjaGVkdWxlZCBieSB5b3Uu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RSC",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Rescheduled by Customer",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiByZXNjaGVkdWxlIGJ5IGN1c3RvbWVyLg==",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RSC",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Cancelled By You",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91ciBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSBvbiB7e2Jvb2tpbmdfZGF0ZX19IGZvciB7e3NlcnZpY2VfbmFtZX19IGhhcyBiZWVuIGNhbmNlbGxlZCBieSB5b3Uu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CS",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Cancelled By Service Provider",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiBjYW5jZWxsZWQgYnkgc2VydmljZSBwcm92aWRlci4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CS",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Cancelled By Service Provider",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiBjYW5jZWxsZWQgYnkgc2VydmljZSBwcm92aWRlci4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CS",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Cancelled By Customer",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91ciBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSBvbiB7e2Jvb2tpbmdfZGF0ZX19IGZvciB7e3NlcnZpY2VfbmFtZX19IGhhcyBiZWVuIGNhbmNlbGxlZCBieSBjdXN0b21lci4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CC",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Cancelled By You",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiBjYW5jZWxsZWQgYnkgeW91Lg==",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CC",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Cancelled By Customer",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiBjYW5jZWxsZWQgYnkgY3VzdG9tZXIu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CC",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Completed",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91ciBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSBvbiB7e2Jvb2tpbmdfZGF0ZX19IGZvciB7e3NlcnZpY2VfbmFtZX19IGhhcyBiZWVuIGNvbXBsZXRlZC4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CO",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Completed",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiBjb21wbGV0ZWQu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CO",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Completed",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gb24ge3tib29raW5nX2RhdGV9fSBmb3Ige3tzZXJ2aWNlX25hbWV9fSBoYXMgYmVlbiBjb21wbGV0ZWQu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"CO",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Reminder",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2FkbWluX25hbWV9fSwKWW91ciBhcHBvaW50bWVudCB3aXRoIHt7Y2xpZW50X25hbWV9fSBpcyBzY2hlZHVsZWQgaW4ge3thcHBfcmVtYWluX3RpbWV9fSBob3Vycy4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RM",
									"user_type"=>"A",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Reminder",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e2NsaWVudF9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2FkbWluX25hbWV9fSBpcyBzY2hlZHVsZWQgaW4ge3thcHBfcmVtYWluX3RpbWV9fSBob3Vycy4=",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RM",
									"user_type"=>"C",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Insert[] = array("sms_subject" => "Appointment Reminder",
									"sms_message"=>"",
									"default_message"=>"RGVhciB7e3N0YWZmX3BlcnNvbl9uYW1lfX0sCllvdXIgYXBwb2ludG1lbnQgd2l0aCB7e2NsaWVudF9uYW1lfX0gaXMgc2NoZWR1bGVkIGluIHt7YXBwX3JlbWFpbl90aW1lfX0gaG91cnMu",
									"sms_template_status"=>"E",
									"sms_template_type"=>"RM",
									"user_type"=>"S",
									"business_id"=> $New_Business_ID);
		$SMS_Templet_Date_Insert = $this->Super_Admin_Model->insert_SMS_Templet_Data($SMS_Templet_Insert);
		
		echo $SMS_Templet_Date_Insert;
	}
	
	/*
	Description : Delete Business
	Created By : Ankush Sali			Created Date : 14-02-2019
	*/
	public function delete_Business(){
		$data_Delete = array();
		$data_Delete = $this->input->post();

		$data_business_delete = array("business_id"=>$data_Delete["ID"]);
		$data_provider_delete = array("provider_id"=>$data_Delete["ID"]);

		$query_Delete_Result = $this->Super_Admin_Model->delete_Business($data_business_delete,$data_provider_delete);

		echo $query_Delete_Result;
	}
	
	/*
	Description : Get E-Mail for Existing Validation
	Created By : Ankush Sali			Created Date : 06-03-2019
	Edit By : Jay Maisuriya			Created Date : 01-11-2019
	*/
	public function get_E_Mail_For_Validation(){
		$Post_Data = $this->input->post();
		$Get_Exist_Bool_Val = $this->System_Model->Get_All_Exist_E_Mails($Post_Data["business_e_mail"]);
		if ($Get_Exist_Bool_Val) { 
			echo "false"; 
		} else {
			echo "true";
		} 
	}
	
	/*
	Description : Get Domain for Existing Validation
	Created By : Jay Maisuriya			Created Date : 01-11-2019
	*/
	public function get_Domain_For_Validation(){
		$Post_Data = $this->input->post();
		$Post_Domain_Name = $Post_Data["business_domain"].".zinnfy.com";
		$Get_Exist_Bool_Val = $this->System_Model->Get_All_Exist_Domains($Post_Domain_Name);
		$All_Domains = array("check.zinnfy.com","checking.zinnfy.com","demo.zinnfy.com","admin.zinnfy.com","test.zinnfy.com","testing.zinnfy.com","xyz.zinnfy.com","abc.zinnfy.com","dummy.zinnfy.com","example.zinnfy.com");
		if ($Get_Exist_Bool_Val) {
			echo "false";
		} else {
			if (in_array($Post_Domain_Name, $All_Domains)){
				echo "false";
			} else {
				echo "true";
			}
		} 
	}
	
	/*
	Description : Super Admin Settings Page
	Created By : Jay Maisuriya   	Created Date : 02-08-2019
	*/
	public function Settings(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		
		$business_id = $this->session->userdata("business_id");
		$settings_Array = array(
			"zn_currency" => $this->Setting_Model->Get_Option_Value("zn_currency",$business_id),
			"zn_stripe_publishablekey" => $this->Setting_Model->Get_Option_Value("zn_stripe_publishablekey",$business_id),
			"zn_stripe_secretkey" => $this->Setting_Model->Get_Option_Value("zn_stripe_secretkey",$business_id),
			"zn_one_sms_price" => $this->Setting_Model->Get_Option_Value("zn_one_sms_price",$business_id),
			"zn_one_staff_member_price" => $this->Setting_Model->Get_Option_Value("zn_one_staff_member_price",$business_id),
		);
		
		$this->Pass_Data["Super_Admin_Settings"] = $settings_Array;
		$this->Pass_Data["view_page_path"] = "Super_Admin/Settings/index";
		$this->Pass_Data["view_page_title"] = "Super Admin";
		$this->Pass_Data["page_name"] = "settings";
		
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}
	
	/*
	Description : Super Admin Plans Page
	Created By : Jay Maisuriya   	Created Date : 04-08-2019
	*/
	public function Plans(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		
		$business_id = $this->session->userdata("business_id");
		
		$this->Pass_Data["view_page_path"] = "Super_Admin/Plans/index";
		$this->Pass_Data["view_page_title"] = "Super Admin";
		$this->Pass_Data["page_name"] = "plans";
		
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}
	
	/*
	Description : Insert Plan Function
	Created By : Jay Maisuriya			Created Date : 04-08-2019
	*/
	public function insert_Plans(){
		$data_Insert = array();
		$data_Insert = $this->input->post();
		$business_id = $this->session->userdata("business_id");
		$stripe_product_id = "";
		$stripe_plan_id = "";
		
		if($data_Insert["plan_days"] != "0"){
			require_once(APPPATH."libraries/Stripe/init.php");
			
			try{
				\Stripe\Stripe::setApiKey($this->Setting_Model->Get_Option_Value("zn_stripe_secretkey",$business_id));
				
				$objproduct = new \Stripe\Product;
				$one_product_create = $objproduct::Create(array(
					"name" => $data_Insert["plan_name"],
					"type" => "service",
					"statement_descriptor" => substr($data_Insert["plan_description"],0,22),
				));
				$stripe_product_id = $one_product_create->id;
				
				$objplan = new \Stripe\Plan;
				$one_plan_create = $objplan::Create(array(
					"amount" => ((double)$data_Insert["plan_rate"] * 100),
					"interval" => "day",
					"product" => $stripe_product_id,
					"currency" => $this->Setting_Model->Get_Option_Value("zn_currency",$business_id),
					"interval_count" => $data_Insert["plan_days"],
					"nickname" => $data_Insert["plan_name"]." For ".$data_Insert["plan_days"]." Days"
				));
				$stripe_plan_id = $one_plan_create->id;
			}catch (Exception $e) {
				$error = $e->getMessage();				
				echo "Message Is - ".$error;
				die();
			}
			$data_Insert["stripe_product_id"] = $stripe_product_id;
			$data_Insert["stripe_plan_id"] = $stripe_plan_id;
			$data_Insert["status"] = "D";
			$query_Insert_Result = $this->Plans_Model->insert_Plans($data_Insert);
			echo $query_Insert_Result;
		}else{
			echo "1";
		}
	}
	
	/*
	Description : Get All Plans Details in DataTable
	Created By : Jay Maisuriya			Created Date : 04-08-2019
	*/
	public function Plans_Datatable(){
		$Post_Data_Array = array();
		$Post_Data_Array = $this->input->post();
		
		$Pass_JSON_Array = array();
		$Total_Records = 0;
		
		$Serch_Value = $Post_Data_Array["search"]["value"];
		$Start = $Post_Data_Array["start"];
		$Length = $Post_Data_Array["length"];
		
		$All_Plans_Detail_Result = $this->Plans_Model->Get_All_Plans_Details($Start,$Length,$Serch_Value);
		$Total_Records = sizeof((array)$All_Plans_Detail_Result);
		$counter = 1;
		
		foreach($All_Plans_Detail_Result as $APDR){
			$Plans_Array = array();
			
			$Plans_Array["ID"] = $APDR->ID;
			$Plans_Array["plan_name"] = $APDR->plan_name;
			$Plans_Array["plan_description"] = $APDR->plan_description;
			$Plans_Array["plan_days"] = $APDR->plan_days;
			$Plans_Array["plan_rate"] = $APDR->plan_rate;
			$Plans_Array["status"] = $APDR->status;

			$All_Plans_Detail_Result = $this->Plans_Model->Check_Plan_Id_Exist($APDR->ID);
			if($All_Plans_Detail_Result){
				$Plans_Array["delete_status"] = "no";
			}else{
				$Plans_Array["delete_status"] = "yes";
			}
			
			
			$Pass_JSON_Array[] = $Plans_Array;
		}
		
		$json_data = array(
			"draw"            	=> intval($Post_Data_Array["draw"]),
			"recordsTotal"    	=> intval($Total_Records),  
			"recordsFiltered" 	=> intval($Total_Records),
			"data"            	=> $Pass_JSON_Array,
		);

		echo json_encode($json_data);		
	}
	
	/*
	Description : Change Plan Status (Checkbox)
	Created By : Jay Maisuriya			Created Date : 04-08-2019
	*/
	public function plan_Status_Change(){
		$data_Update = array();
		$data_Update = $this->input->post();
		
		$plan_id = $data_Update["plan_id"];
		unset($data_Update["plan_id"]);
		
		$query_Update_Result = $this->Plans_Model->plan_Status_Change($data_Update,$plan_id);
		echo $query_Update_Result;
		
	}
	
	/*
	Description : Get One Plan Detail
	Created By : Jay Maisuriya			Created Date : 04-08-2019
	*/
	public function Get_One_Plan_Detail(){
		$plan_ID = $this->input->post("plan_ID");
		$Get_One_Plan_Detail_Result = $this->Plans_Model->Get_One_Plan_Detail($plan_ID);
		$Pass_JSON_Array = array();
		$Pass_JSON_Array["ID"] = $Get_One_Plan_Detail_Result[0]->ID;
		$Pass_JSON_Array["plan_name"] = ucwords($Get_One_Plan_Detail_Result[0]->plan_name);
		$Pass_JSON_Array["plan_description"] = $Get_One_Plan_Detail_Result[0]->plan_description;
		$Pass_JSON_Array["plan_days"] = $Get_One_Plan_Detail_Result[0]->plan_days;
		$Pass_JSON_Array["plan_rate"] = $Get_One_Plan_Detail_Result[0]->plan_rate;
		echo json_encode($Pass_JSON_Array);
	}
	
	/*
	Description : Update Service Function
	Created By : Jay Maisuriya			Created Date : 04-02-2019
	*/
	public function update_Plan(){
		$data_Update = array();
		$data_Update = $this->input->post();
		$business_id = $this->session->userdata("business_id");
		$stripe_product_id = "";
		$stripe_plan_id = "";
		
		if($data_Update["plan_days"] != "0"){
			require_once(APPPATH."libraries/Stripe/init.php");
			
			try{
				\Stripe\Stripe::setApiKey($this->Setting_Model->Get_Option_Value("zn_stripe_secretkey",$business_id));
				
				$plan_id = $data_Update["ID"];
				unset($data_Update["ID"]);
				if($plan_id != "1"){
					$Get_One_Plan_Detail = $this->Plans_Model->Get_One_Plan_Detail($plan_id);
					if($Get_One_Plan_Detail[0]->stripe_product_id == ""){
						$objproduct = new \Stripe\Product;
						$one_product_create = $objproduct::Create(array(
							"name" => $data_Update["plan_name"],
							"type" => "service",
							"statement_descriptor" => substr($data_Update["plan_description"],0,22),
						));
						$stripe_product_id = $one_product_create->id;
						
						$objplan = new \Stripe\Plan;
						$one_plan_create = $objplan::Create(array(
							"amount" => ((double)$data_Update["plan_rate"] * 100),
							"interval" => "day",
							"product" => $stripe_product_id,
							"currency" => $this->Setting_Model->Get_Option_Value("zn_currency",$business_id),
							"interval_count" => $data_Update["plan_days"],
							"nickname" => $data_Update["plan_name"]." For ".$data_Update["plan_days"]." Days"
						));
						$stripe_plan_id = $one_plan_create->id;
						$data_Update["stripe_product_id"] = $stripe_product_id;
						$data_Update["stripe_plan_id"] = $stripe_plan_id;
					}else{
						$stripe_product_id = $Get_One_Plan_Detail[0]->stripe_product_id;
						if($Get_One_Plan_Detail[0]->plan_days != $data_Update["plan_days"] || $Get_One_Plan_Detail[0]->plan_rate != $data_Update["plan_rate"]){
							$objplan = new \Stripe\Plan;
							$one_plan_create = $objplan::Create(array(
								"amount" => ((double)$data_Update["plan_rate"] * 100),
								"interval" => "day",
								"product" => $stripe_product_id,
								"currency" => $this->Setting_Model->Get_Option_Value("zn_currency",$business_id),
								"interval_count" => $data_Update["plan_days"],
								"nickname" => $data_Update["plan_name"]." For ".$data_Update["plan_days"]." Days"
							));
							$stripe_plan_id = $one_plan_create->id;
							$data_Update["stripe_product_id"] = $stripe_product_id;
							$data_Update["stripe_plan_id"] = $stripe_plan_id;
						}
					}
				}
				
				$query_Update_Result = $this->Plans_Model->update_Plan($data_Update,$plan_id);
				echo $query_Update_Result;
			}catch (Exception $e) {
				$error = $e->getMessage();				
				echo "Message Is - ".$error;
				die();
			}
		}else{
			echo "1";
		}
	}
	
	/*
	Description : Delete Plan Function
	Created By : Jay Maisuriya			Created Date : 04-08-2019
	*/
	public function delete_Plan(){
		$data_Delete = array();
		$data_Delete = $this->input->post();
		$query_Delete_Result = $this->Plans_Model->delete_Plan($data_Delete);
		echo $query_Delete_Result;
	}
	
	/*
	Description : Super Admin Profile Edit Page
	Created By : Jay Wankhede   	Created Date : 24-07-2019
	*/
	public function Profile(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		$id = $this->session->userdata("ID");

		$Super_Admin_Profile_Edit = $this->System_Model->One_Profile_Details($id);
		$Super_Admin_Detail_Array = array();
		
		$this->Pass_Data["view_page_path"] = "Super_Admin/Profile/index";
		$this->Pass_Data["view_page_title"] = "Super Admin";
		$this->Pass_Data["page_name"] = "Super Admin Profile";

		$Super_Admin_Detail_Array["Full_Name"] = $Super_Admin_Profile_Edit[0]->full_name;
		$Super_Admin_Detail_Array["Phone"] = $Super_Admin_Profile_Edit[0]->phone;
		$Super_Admin_Detail_Array["Phone_verify"] = $Super_Admin_Profile_Edit[0]->phone_verify;
		$Super_Admin_Detail_Array["Address"] = $Super_Admin_Profile_Edit[0]->address;
		$Super_Admin_Detail_Array["Zip"] = $Super_Admin_Profile_Edit[0]->zip;
		$Super_Admin_Detail_Array["City"] = $Super_Admin_Profile_Edit[0]->city;
		$Super_Admin_Detail_Array["State"] = $Super_Admin_Profile_Edit[0]->state;
		$Super_Admin_Detail_Array["Country"] = $Super_Admin_Profile_Edit[0]->country;
		$Super_Admin_Detail_Array["Profile_Image"] = $Super_Admin_Profile_Edit[0]->image;
		
		$this->Pass_Data["Super_Admin_Detail_Array"] = $Super_Admin_Detail_Array;

		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}
	
	/*
	Description : Super Admin Profile Update
	Created By : Jay Wankhede   	Created Date : 24-07-2019
	*/
	public function Super_Admin_Profile_Update(){
		$data_Update = array();
		$data_Update = $this->input->post();
		$id = $this->session->userdata("ID");
		if(isset($data_Update["phone"])){
	        $Super_Admin_Profile_Detail = $this->System_Model->One_Profile_Details($id);
			$Super_Admin_Phonenumber = $Super_Admin_Profile_Detail[0]->phone;
    	    if($data_Update["phone"] !== $Super_Admin_Phonenumber){
    		    $data_Update["phone_verify"] = "N";
    	    }
		}
		$query_Update_Result = $this->System_Model->One_Profile_Update($data_Update,$id);
		if(isset($data_Update["full_name"])){
			$this->session->set_userdata("zf_full_name", $data_Update["full_name"]);
		}else if(isset($data_Update["image"])){
			$this->session->set_userdata("zf_image", $data_Update["image"]);
			$this->Other_Users_Images_Delete();
		}
		echo $query_Update_Result;
	}
	
	/*
	Description : Unused Users Images Delete
	Created By : Jay Wankhede   	Created Date : 24-07-2019
	*/
	public function Other_Users_Images_Delete(){
		$Super_Admin_Used_Images = $this->Setting_Model->Get_All_Used_User_Images();
		$Customer_Used_Images = $this->Setting_Model->Get_Customer_Used_User_Images();
		$Image_Array = array_merge($Super_Admin_Used_Images,$Customer_Used_Images);
		$this->load->helper("directory");
		$Super_Admin_Image_Array = directory_map("./assets/images/user_images/");
		foreach($Super_Admin_Image_Array as $SAIA){
			if(!(in_array($SAIA,$Image_Array))){
				$Delete_File_Name = "./assets/images/user_images/".$SAIA;
				@unlink($Delete_File_Name);
			}
		}
	}
	
	/*
	Description : Change Super Admin Password
	Created By : Jay Wankhede			Created Date : 26-07-2019
	*/
	public function Change_Super_Admin_Password(){
		$id = $this->session->userdata("ID");

		$data_Update = $this->input->post();
		$Super_Admin_Old_Password = hash("sha512", $data_Update["Super_Admin_Old_Password"] . config_item("encryption_key"));
		$data_Update["user_password"] = hash("sha512",  $data_Update["user_password"] . config_item("encryption_key"));
		
		$Profile_Details_Result = $this->System_Model->One_Profile_Details($id);
		if($Profile_Details_Result[0]->user_password != $Super_Admin_Old_Password){
			echo "old_not_match";
		}else{
			unset($data_Update["Super_Admin_Old_Password"]);
			$query_Update_Result = $this->System_Model->One_Profile_Update($data_Update,$id);
			echo $query_Update_Result;
		}
	}
	
	/*
	Description : Display Payments On Super Admin Site
	Created By : Jay Wankhede				Created Date : 05-08-2019
	*/
	public function Payment(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		$this->Pass_Data["view_page_path"] = "Super_Admin/Payment/index";
		$this->Pass_Data["view_page_title"] = "Payments";
		$this->Pass_Data["page_name"] = "payment";
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}
	
	/*
	Description : Business Payments Datatable
	Created By : Jay Wankhede				Created Date : 05-08-2019
	Edit By : Jay Maisuriya				Created Date : 23-08-2019
	*/
	public function Payment_Datatable(){
		$Post_Data_Array = array();
		$Post_Data_Array = $this->input->post();

		$Pass_JSON_Array = array();
		$Total_Records = 0;

		$Serch_Value = $Post_Data_Array["search"]["value"];
		$Start = $Post_Data_Array["start"];
		$Length = $Post_Data_Array["length"];
		$Start_Date = $Post_Data_Array["Start_Date"];
		$End_Date = $Post_Data_Array["End_Date"];

		if($Start_Date != "" && $End_Date != ""){
			$Start_Date = date("Y-m-d",strtotime($Start_Date));
			$End_Date = date("Y-m-d",strtotime($End_Date));
		}
		$business_id = $this->session->userdata("business_id");
		$All_Payment_Detail_Result = $this->Payments_Model->Get_All_Business_Payment_Detail($Start,$Length,$Serch_Value,$Start_Date,$End_Date);
		$Total_Records = sizeof((array)$All_Payment_Detail_Result);
		
		$counter = 1;
		foreach($All_Payment_Detail_Result as $APDR){
			$Payment_Array = array();
			$Payment_Array["ID"] = $counter;
			$Payment_Array["plan_id"] = $APDR->ID;
			$Payment_Array["full_name"] = $APDR->full_name;
			$Payment_Array["payment_method"] = $APDR->payment_method;
			$Payment_Array["transaction_id"] = $APDR->transaction_id;
			$Payment_Array["payment_date"] = date("d-F-Y",strtotime($APDR->payment_date));
			$Payment_Array["amount"] = $APDR->amount;
			$Payment_Array["payment_status"] = $APDR->payment_status;
			$Payment_Array["business_id"] = $APDR->business_id;
			$Pass_JSON_Array[] = $Payment_Array;
			$counter++;
		}

		$json_data = array(
			"draw" => intval($Post_Data_Array["draw"]),
			"recordsTotal" => intval($Total_Records), 
			"recordsFiltered" => intval($Total_Records),
			"data" => $Pass_JSON_Array,
		);
		echo json_encode($json_data);
	}

	/*
	Description : Zinnfy Verify Phone
	Created By : Divyesh	Patel		Created Date : 07-11-2019
	*/
	public function Verify_Phone(){
		$ID = $this->session->userdata("ID");
		$Post_Data = $this->input->post();
		$Phone_number = str_replace("+", "", $Post_Data["phone"]);
		$Random_OTP = substr( str_shuffle("0123456789012345678901234567890123456789"), 0, 4 );
		
		$status = false;
		if(isset($Post_Data["resend_otp"])){
			$Phone_OTP_Result = $this->System_Model->Get_Phone_OTP_if_Available($ID,$Phone_number,"SA");
			if(!empty($Phone_OTP_Result)){
				$Random_OTP = $Phone_OTP_Result[0]->random_OTP;
				$status = true;
			}
		}

		if($status == false){
			$Data_Insert_Array = array(
				"user_id" => $ID,
				"role" => "SA",
				"phone" => $Phone_number,
				"random_OTP" => $Random_OTP,
			);
			$query_Insert_Result = $this->System_Model->insert_Phone_OTP($Data_Insert_Array);
			if($query_Insert_Result > 0){
					$status = true;
			}
		}
		$json_Pass_Array["status"] = $status;
		echo json_encode($json_Pass_Array);
		
	}	

	/*
	Description : Zinnfy Verify Phone
	Created By : Divyesh	Patel		Created Date : 07-11-2019
	*/
	public function Verify_Phone_OTP(){
		$ID = $this->session->userdata("ID");
		$Post_Data = $this->input->post();
		$Phone_number = str_replace("+", "", $Post_Data["phone"]);
		$OTP =  $Post_Data["OTP"];

		$status = false;
		$verify_Phone_OTP_Result = $this->System_Model->verify_Phone_OTP($ID,"SA",$Phone_number,$OTP);
		if($verify_Phone_OTP_Result > 0){
			$status = true;
			$this->System_Model->Phone_Verify_Status($ID,"SA");
		}
		$json_Pass_Array["status"] = $status;
		echo json_encode($json_Pass_Array);
	}

	/*
	Description : Display Labels On Super Admin Site
	Created By : Divyesh Patel				Created Date : 26-11-2019
	*/
	public function labels(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		$this->Pass_Data["view_page_path"] = "Super_Admin/Labels/index";
		$this->Pass_Data["view_page_title"] = "labels";
		$this->Pass_Data["page_name"] = "labels";
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}

	/*
	Description : language View Function
	Created By : Divyesh Patel				Created Date : 26-11-2019
	*/
	public function Country_Labels(){
		$Post_Data = array();
		$Post_Data = $this->input->post();
		if(!empty($Post_Data) && !empty($Post_Data["language_code"])){
			$business_id = $this->session->userdata("business_id");
			$Language_Data = $this->System_Model->Get_Language_Data($Post_Data["language_code"], $business_id);
			if($Language_Data){
				$language_status = $Language_Data[0]->language_status;
				$language_data = unserialize(base64_decode($Language_Data[0]->labels_data));
				$language_errors_data = unserialize(base64_decode($Language_Data[0]->errors_data));
			} else {
				$Language_Data = $this->System_Model->Get_Language_Data("en", $business_id);
				$language_status = $Language_Data[0]->language_status;
				$language_data = unserialize(base64_decode($Language_Data[0]->labels_data));
				$language_errors_data = unserialize(base64_decode($Language_Data[0]->errors_data));
			}
			?>
				<section class="section">
					<div class="container">
						<div class="row">
							<div class="container">
								<nav class="nav nav-tabs nav-justified" id="tab-default">
								<a class="nav-item nav-link active col-lg-4 col-md-4 col-sm-4 col-4" id="labels_tab" data-toggle="tab" href="#zf_Labels_View"><h6 class="mb-0">Lables</h6></a>
								<a class="nav-item nav-link col-lg-4 col-md-4 col-sm-4 col-4" id="errors_tab" data-toggle="tab" href="#zf_Errors_Labels"><h6 class="mb-0">Errors</h6></a>
								<div class="col-lg-3 col-md-3 col-sm-3 col-3 float-left mt-5">
									<input class="tgl tgl-light" id="zn_language_status" type="checkbox" <?php       if($language_status == "Y"){echo "checked";} ?> />
									<label class="tgl-btn tgl-btn-success mar-0" for="zn_language_status"></label>
								</div>
								<div class="col-2 col-sm-2 col-md-2 col-lg-2 float-left">
									<button id="zf_labels_Save" data-language_code="<?php     echo $Post_Data["language_code"];?>" data-business_id="<?php     echo $business_id;?>"  class="btn btn-md btn-success float-right"><i class="fa fa-save"></i> Save Settings</button>
								</div>
								</nav>
							</div>
						</div>
					</div>
				</section>

				<div class="tab-content" id="tab-default-content">
					<div class="container tab-pane fade show active" id="zf_Labels_View">
						<div class="content">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mx-auto">
									<div id="accordion">
										<form id="language_data_form">
										<?php
											foreach ($language_data as $language_data_key => $language_data_value){
												?>
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left mt-5">
														<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 float-left mt-5">
															<label class="control-label"><?php echo     str_replace("_"," ",$language_data_key);?></label>
														</div>
														<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 float-left mt-5">
															<input type="text"  name="<?php echo     $language_data_key;?>" class="form-control"  value="<?php echo     urldecode($language_data_value);?>" />
														</div>
													</div>
												<?php
											}
										?>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="container tab-pane fade" id="zf_Errors_Labels">
						<div class="content">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mx-auto">
									<div id="accordion">
										<form id="language_errors_data_form">
										<?php
											foreach ($language_errors_data as $language_errors_data_key => $language_errors_data_value){
												?>
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left mt-5">
														<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 float-left mt-5">
															<label class="control-label"><?php echo     str_replace("_"," ",$language_errors_data_key);?></label>
														</div>
														<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 float-left mt-5">
															<input type="text"  name="<?php echo     $language_errors_data_key;?>" class="form-control"  value="<?php echo     urldecode($language_errors_data_value);?>" />
														</div>
													</div>
												<?php
											}
										?>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
		}
	}

	/*
	Description : Update labels Function
	Created By : Divyesh Patel				Created Date : 26-11-2019
	*/
	public function Country_Labels_Update(){
		$Post_Data = array();
		$Post_Data = $this->input->post();
		$language_code = $Post_Data["language_code"];
		$business_id = $Post_Data["business_id"];
		$language_status = $Post_Data["language_status"];

		$language_data = explode("&", $Post_Data["language_data"]);
		$language_errors_data = explode("&", $Post_Data["language_errors_data"]);

		$Data_language_data_Array=array();
		foreach ($language_data as  $language_data_value) {
			$labels_data = explode("=", $language_data_value);
			$Data_language_data_Array[$labels_data[0]] = $labels_data[1];
		}

		$Data_Labels_Error_Array=array();
		foreach ($language_errors_data as  $language_errors_data_value) {
			$labels_error = explode("=", $language_errors_data_value);
			$Data_Labels_Error_Array[$labels_error[0]] = $labels_error[1];
		}

		$labels_serialize = serialize($Data_language_data_Array);
		$labels_bese64 = base64_encode($labels_serialize);

		$errors_serialize = serialize($Data_Labels_Error_Array);
		$errors_bese64 = base64_encode($errors_serialize);

		$Data_Update = array(
			"language_status" => $language_status,
			"labels_data" => $labels_bese64,
			"errors_data" => $errors_bese64,
		); 

		$result = $this->System_Model->Country_Labels_Update($Data_Update, $language_code, $business_id);
		echo $result;
	}

	/*
	Description :Mailchimp View Funcation
	Created By : Divyesh Patel	Created Date : 29-01-2020
	*/
	public function Mailchimp(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}

		$business_id = $this->session->userdata("ID");


		$mailchimp_status = $this->Setting_Model->Get_Option_Value("zn_mailchimp_status",$business_id);
		$mailchimp_api_key = $this->Setting_Model->Get_Option_Value("zn_mailchimp_api_key",$business_id);
		$mailchimp_list_id = $this->Setting_Model->Get_Option_Value("zn_mailchimp_list_id",$business_id);
		
		$this->Pass_Data["Mailchimp_Data"] = array(
			'zn_mailchimp_status' => $mailchimp_status, 
			'zn_mailchimp_api_key' => $mailchimp_api_key, 
			'zn_mailchimp_list_id' => $mailchimp_list_id, 
		);
		
		$Mail_chimp_list_data = $this->Mailchimp_Model->Get_all_list($mailchimp_api_key);

		$this->Pass_Data["Mailchimp_List_Data"] = $Mail_chimp_list_data;
		$this->Pass_Data["view_page_path"] = "Super_Admin/mailchimp/index";
		$this->Pass_Data["view_page_title"] = "Mailchimp";
		$this->Pass_Data["page_name"] = "Mailchimp";
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}

	/*
	Description :Feedback View Funcation
	Created By : Divyesh Patel	Created Date : 06-02-2020
	*/
	public function Feedback(){
		$post_data = $this->input->post();
		$feed_feedback_type = $post_data["feed_feedback_type"];
		$feed_feedback = $post_data["feed_feedback"];
		$feed_username = $post_data["feed_username"];
		$feed_email = $post_data["feed_email"];

		require_once(APPPATH."libraries/class.phpmailer.php");
		$mail = new Zinnfy_phpmailer();
		$mail->Host = $this->config->item("sendgrid_Host");
		$mail->Username = $this->config->item("sendgrid_Username");
		$mail->Password = $this->config->item("sendgrid_Password");
		$mail->Port = $this->config->item("sendgrid_Port");
		$mail->SMTPSecure = $this->config->item("sendgrid_SMTPSecure");
		$mail->SMTPAuth = $this->config->item("sendgrid_SMTPAuth");
		$mail->CharSet = $this->config->item("sendgrid_CharSet");
		$mail->IsSMTP();
		$mail->SMTPDebug  = $this->config->item("sendgrid_SMTPDebug");
		$mail->IsHTML(true);
		
		$admin_name = "Zinnfy";
		$admin_email = "hello@zinnfy.com";
		$company_logo = base_url("assets/images/logo-gradient.png");
		
		$Template_Message =  '<html>
			<head>
				<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>Feedback</title>
			</head>
			<body>
				<div style="margin: 0;padding: 0;font-family: Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif;font-size: 100%;line-height: 1.6;box-sizing: border-box;">
					<div style="display: block !important;max-width: 600px !important;margin: 0 auto !important;clear: both !important;">
						<table style="border: 1px solid #c2c2c2;width: 100%;float: left;margin: 30px 0px;-webkit-border-radius: 5px;-moz-border-radius: 5px;-o-border-radius: 5px;border-radius: 5px;">
							<tbody>
								<tr style="border-bottom: 1px solid #e6e6e6;float: left;width: 100%;display: block;">
									<td style="width: 100%;vertical-align: top;float: left;">
										<div style="vertical-align: top;float: left;padding:35px 15px;width: 93%;clear: left;">
											<div style="width: auto;height: 80px;vertical-align: top;margin: 0px auto;text-align: center;">
												<img src="'.$company_logo.'" style="width: auto;display: inline-block;height: 100%;" />
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div style="padding: 25px 30px;background: #fff;float: left;display: block;">
											<div style="border-bottom: 1px solid #e6e6e6;float: left;width: 100%;display: block;">
												<p style="color: #606060;font-size: 15px;margin: 10px 0px 15px;"><b>Feedback Type:</b>  '.$feed_feedback_type.'</p>
												<p style="color: #606060;font-size: 15px;margin: 10px 0px 15px;"><b>Feeddback :</b>  '.$feed_feedback.' </p>
												<p style="color: #606060;font-size: 15px;margin: 10px 0px 15px;"><b>Name :</b>  '.$feed_username.' </p>
												<p style="color: #606060;font-size: 15px;margin: 10px 0px 15px;"><b>Email :</b> '.$feed_email.' </p>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</body>
    </html>';
		
		$mail->From = $admin_email;
		$mail->FromName = $admin_name;
		$mail->Sender = $admin_email;
		$mail->AddAddress($admin_email, "Zinnfy User Feedback");
		$mail->Subject = "Zinnfy User Feedback";
		$mail->Body = $Template_Message;
		if($mail->send()){
			$date = date("Y-m-d H:i:s");
			$FeedBack_Data_Array = array(
				"feedback_type" => $feed_feedback_type,
				"feedback" => $feed_feedback,
				"feedback_username" => $feed_username,
				"feedback_email" => $feed_email,
				"date" => $date,
			);
			echo $this->Super_Admin_Model->Save_Feedback($FeedBack_Data_Array);
		}
		$mail->ClearAllRecipients();
	}

	/*
	Description : Subscribe newsletter Details
	Created By : Divyesh Patel			Created Date :06-02-2020
	*/
	public function Subscribe_newsletter(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		
		$this->Pass_Data["view_page_path"] = "Super_Admin/Subscribe_newsletter/index";
		$this->Pass_Data["view_page_title"] = "Subscribe newsletter";
		$this->Pass_Data["page_name"] = "Subscribe newsletter";
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}

	/*
	Description : Subscribe newsletter Data
	Created By : Divyesh Patel			Created Date :06-02-2020
	*/
	public function Subscribe_Newsletter_Datatable(){
		$Post_Data_Array = array();
		$Post_Data_Array = $this->input->post();
		
		$Pass_JSON_Array = array();
		$Total_Records = 0;
		
		$Serch_Value = $Post_Data_Array["search"]["value"];
		$Start = $Post_Data_Array["start"];
		$Length = $Post_Data_Array["length"];
		
		$Subscribe_Newsletter_Result = $this->Super_Admin_Model->Subscribe_Newsletter_Datatable($Start,$Length,$Serch_Value);
		$Total_Records = sizeof((array)$Subscribe_Newsletter_Result);
		$counter = 1;
		
		foreach($Subscribe_Newsletter_Result as $Subscribe){
			$Subscribe_Newsletter_Array = array();
			
			$Subscribe_Newsletter_Array["counter"] = $counter++;
			$Subscribe_Newsletter_Array["ID"] = $Subscribe->ID;
			$Subscribe_Newsletter_Array["subscribe_email"] = $Subscribe->subscribe_email;
			$Subscribe_Newsletter_Array["newsletter_type"] = $Subscribe->newsletter_type;
			$Subscribe_Newsletter_Array["subscribe_by"] = $Subscribe->subscribe_by;
			$Subscribe_Newsletter_Array["mail_send_status"] = $Subscribe->mail_send_status;
			$Subscribe_Newsletter_Array["date"] = date("Y-m-d",strtotime($Subscribe->date));
			
			$Pass_JSON_Array[] = $Subscribe_Newsletter_Array;
		}
		
		$json_data = array(
			"draw"            	=> intval($Post_Data_Array["draw"]),
			"recordsTotal"    	=> intval($Total_Records),  
			"recordsFiltered" 	=> intval($Total_Records),
			"data"            	=> $Pass_JSON_Array,
		);

		echo json_encode($json_data);		
	}

	/*
	Description : Feedback Details
	Created By : Divyesh Patel			Created Date :06-02-2020
	*/
	public function Feed_back(){
		if(!$this->session->userdata("zf_log_in") || $this->session->userdata("zf_role") != "SA"){
			redirect(base_url("Admin"));
		}
		
		$this->Pass_Data["view_page_path"] = "Super_Admin/Feedback/index";
		$this->Pass_Data["view_page_title"] = "Feedback";
		$this->Pass_Data["page_name"] = "Feedback";
		$this->load->view("Super_Admin/Super_Admin_Main_Layout", $this->Pass_Data);
	}

	/*
	Description : Feedback Data
	Created By : Divyesh Patel			Created Date :06-02-2020
	*/
	public function Feedback_Datatable(){
		$Post_Data_Array = array();
		$Post_Data_Array = $this->input->post();
		
		$Pass_JSON_Array = array();
		$Total_Records = 0;
		
		$Serch_Value = $Post_Data_Array["search"]["value"];
		$Start = $Post_Data_Array["start"];
		$Length = $Post_Data_Array["length"];
		
		$Feedback_Result = $this->Super_Admin_Model->Feedback_Table_data($Start,$Length,$Serch_Value);
		$Total_Records = sizeof((array)$Feedback_Result);
		$counter = 1;
		
		foreach($Feedback_Result as $Feedback){
			$Feedback_Array = array();
			
			$Feedback_Array["counter"] = $counter++;
			$Feedback_Array["ID"] = $Feedback->ID;
			$Feedback_Array["feedback_type"] = $Feedback->feedback_type;
			$Feedback_Array["feedback"] = $Feedback->feedback;
			$Feedback_Array["feedback_username"] = $Feedback->feedback_username;
			$Feedback_Array["feedback_email"] = $Feedback->feedback_email;
			$Feedback_Array["date"] = date("Y-m-d",strtotime($Feedback->date));
			
			$Pass_JSON_Array[] = $Feedback_Array;
		}
		
		$json_data = array(
			"draw"            	=> intval($Post_Data_Array["draw"]),
			"recordsTotal"    	=> intval($Total_Records),  
			"recordsFiltered" 	=> intval($Total_Records),
			"data"            	=> $Pass_JSON_Array,
		);

		echo json_encode($json_data);		
	}

	/*
	Description : Subscribe news letter
	Created By : Divyesh Patel			Created Date :07-02-2020
	*/
	public function subscribe_news_letter()
	{
		$post_data = $this->input->post();
		$check_subscribe = $this->Super_Admin_Model->check_subscribe_news_letter($post_data["subscribe_email"]);
		if($check_subscribe){
			echo "exist";
		} else {
			$date = date("Y-m-d H:i:s");
			$subscribe_Array = array(
				"subscribe_email" => $post_data["subscribe_email"],
				"newsletter_type" => "introduction_newsletter",
				"subscribe_by" => "manual",
				"mail_send_status" => "N",
				"date" => $date,
			);

			/*mailchipmp subscription*/
			$mailchimp_status = $this->Setting_Model->Get_Option_Value("zn_mailchimp_status","1");
			$mailchimp_api_key = $this->Setting_Model->Get_Option_Value("zn_mailchimp_api_key","1");
			$mailchimp_list_id = $this->Setting_Model->Get_Option_Value("zn_mailchimp_list_id","1");
			if($mailchimp_status == "E" && $mailchimp_api_key !=="" && $mailchimp_list_id !==""){
				$this->Mailchimp_Model->Add_Subscriber($mailchimp_api_key, $mailchimp_list_id, $post_data["subscribe_email"], "", "","");
			}
			/*end*/
	    echo $this->Super_Admin_Model->subscribe_news_letter($subscribe_Array);
		}
	}

	/*
	Description : Uplod Newsletter CSV
	Created By : Divyesh Patel			Created Date :07-02-2020
	*/
	public function Uplod_Newsletter_CSV()
	{
	 $post_data = $this->input->post();
	 $newsletter_type = $post_data["newsletter_type"];
	 $fileName = $_FILES["csv_file"]["tmp_name"];
	 if ($_FILES["csv_file"]["size"] > 0) {
	   $file = fopen($fileName, "r");
	   $flag = true;
     while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
       if ($flag) {$flag = false;continue;}
       $check = $this->Super_Admin_Model->check_subscribe_news_letter($column[1]);
       if($check){
       	
       } else {
       	 $date = date("Y-m-d H:i:s");
       	 $Data_Insert_Array = array(
       	 	"subscribe_email" => $column[1],
       	 	"newsletter_type" => $newsletter_type,
       	 	"subscribe_by" => "import",
       	 	"mail_send_status" => "N",
       	 	"date" => $date,
       	 );
       	 $this->Super_Admin_Model->subscribe_news_letter($Data_Insert_Array);
       }
     }
     echo "1";
	 }
	}

	public function Send_Newsletter_Mail()
	{
		$post_data = $this->input->post();
	  $bulk_send = $post_data["bulk_send"];
	  $send_newsletter_type = $post_data["send_newsletter_type"];
	  $Subscriber_email_data = $this->Super_Admin_Model->Get_News_letter_data_by_range($bulk_send, $send_newsletter_type);
	 	if(!empty($Subscriber_email_data)){
	 		require_once(APPPATH."libraries/class.phpmailer.php");
			$super_admin_name = "Zinnfy";
			$super_admin_email = "hello@zinnfy.com";
			$Image_Path = base_url("/assets/images/subscriber_image/");
			if($send_newsletter_type == "introduction_newsletter"){
	 			$Template_Message =  '<html>
					<head>
					</head>
					  <body style="background-color: #fafafa;">
							<div style="background-color: #fff; width: 600px; margin: 0 auto; padding: 0px;"class="">
								<div class="top-img">
									<a href="http://bit.ly/31NNBVt">	<img src="'.$Image_Path.'zinnfy-marketing.jpg" style="width:100%;"> </a>
								</div>
								<div class="download-center">
									<h3 style="text-align:center; font-size: 28px;margin-bottom: 10px;"> Download Mobile App: </h3>
								</div>
								<div style="padding-bottom: 20px; text-align: center;"class="download-img">
									<a href="javascript:void(0)"> <img src="'.$Image_Path.'unnamed-1.png"> </a>
									<a href="javascript:void(0)"> <img src="'.$Image_Path.'unnamed-2.png"> </a>
								</div>
								<div style="text-align:center;"class="get-started">
									<a style="font-size: 18px;padding: 18px;background-color: #009fc7;border-radius: 4px;color: #fff;text-decoration: none;font-weight: 600; margin-top:20px;display: inline-block;" href="http://bit.ly/31NNBVt"> Get Started  Free </a>
									<div class="follow">
										<h4 style="font-size: 26px; margin-top: 40px; margin-bottom:10px;"> Follow Us On</h4>
										<div style="display: inline-flex;justify-content: center;">
										<a href="https://www.facebook.com/Zinnfy-102931237932233/"><img src="'.$Image_Path.'i-1.png" alt="Facebook" style="display:inherit;border:0;height:auto;outline:none;text-decoration:none;margin-right: 20px;cursor: pointer;" height="24" width="24" class="CToWUd"></a>
										<a href="https://www.instagram.com/zinnfy_online_booking_software/"><img src="'.$Image_Path.'i-2.png" alt="Instagram" style="display:inherit;border:0;height:auto;outline:none;text-decoration:none;margin-right: 20px;cursor: pointer;" height="24" width="24" class="CToWUd"></a>
										<a href="https://www.linkedin.com/company/zinnfy/"><img src="'.$Image_Path.'i-3.png" alt="LinkedIn" style="display:inherit;border:0;height:auto;outline:none;text-decoration:none;margin-right: 20px;cursor: pointer;" height="24" width="24" class="CToWUd"></a>
										<a href="https://www.youtube.com/channel/UCWPJDGIJ_oR1-eXroEd5C7w/featured"><img src="'.$Image_Path.'i-4.png" alt="YouTube" style="display:inherit;border:0;height:auto;outline:none;text-decoration:none;margin-right: 0px;cursor: pointer;" height="24" width="24" class="CToWUd"></a>
										</div>
										<h4 style="font-size: 28px; margin-top: 40px; margin-bottom: 10px;">For Any Further Assistance:</h4>
											<div style="text-align: center;display: grid;">
												<a style="font-size: 18px;color: #278bd1;margin-bottom: 5px;width: 100%;float: left;"href=""> support@zinnfy.com </a>
												<a style="font-size: 18px;color: #278bd1;margin-bottom: 5px;width: 100%;float: left;"href="https://www.zinnfy.com/support_ticket"> Click here for Support Ticket </a>
												<span style="font-size: 18px;font-style: italic;margin-bottom: 5px;width: 100%;float: left;"> Copyright © 2020 Zinnfy, All rights reserved.</span>
												<span style="font-size: 18px;margin-bottom: 5px;width: 100%;float: left;"> Want to change how you receive these emails?</span>
												<span style="font-size: 18px; margin-bottom: 5px;width: 100%;float: left;"> You can <a style="color: #278bd1;" href="javascript:void(0)"> update your preferences </a> or <a style="color: #278bd1;" href="javascript:void(0)"> unsubscribe from this list. </a></span>
											</div>
									</div>
								</div>
							</div>
					</body>	
				</html>';
	 			$Mail_Subject =  'test';
	 		} /*elseif ($send_newsletter_type == "email_2") {
	 			$Template_Message =  '';
	 			$Mail_Subject =  'test';
	 		} elseif ($send_newsletter_type == "email_3") {
	 			$Template_Message =  '';
	 			$Mail_Subject =  'test';
	 		}*/
			
			foreach ($Subscriber_email_data as $Subscriber_email_data) {
				$send_subsciber_mail = $Subscriber_email_data->subscribe_email;
				$mail = new Zinnfy_phpmailer();
				$mail->Host = $this->config->item("sendgrid_Host");
				$mail->Username = $this->config->item("sendgrid_Username");
				$mail->Password = $this->config->item("sendgrid_Password");
				$mail->Port = $this->config->item("sendgrid_Port");
				$mail->SMTPSecure = $this->config->item("sendgrid_SMTPSecure");
				$mail->SMTPAuth = $this->config->item("sendgrid_SMTPAuth");
				$mail->CharSet = $this->config->item("sendgrid_CharSet");
				$mail->IsSMTP();
				$mail->SMTPDebug  = $this->config->item("sendgrid_SMTPDebug");
				$mail->IsHTML(true);
				
				$mail->From = $super_admin_email;
				$mail->FromName = $super_admin_name;
				$mail->Sender = $super_admin_email;
				$mail->AddAddress($send_subsciber_mail, $Mail_Subject);
				$mail->Subject = $Mail_Subject;
				$mail->Body = $Template_Message;
				if($mail->send()){
					$this->Super_Admin_Model->subscribe_news_letter_status_update($send_subsciber_mail,$send_newsletter_type);
				}
				$mail->ClearAllRecipients();
			}
			echo "1";
	 	}
	}
}
?>