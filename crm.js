/***************************************************************
*  Project Name : Zinnfy
*  Created By : Ankush Sali
*  Created Date : 17-01-2019
*  Modification History :
*  Dated				Developer Name				Description
   17-01-2019		Ankush Sali						Used for Manage Customer Details
***************************************************************/

/*
Description : Customer Select Dropdown JS
Created By : Ankush Sali			Created Date : 17-01-2019
*/
(function(jQuery, $scope) {
	$scope.iconFormat = function (icon) {
		var originalOption = icon.element;
		if (!icon.id) { return icon.text; }
		return '<i class="' + jQuery(icon.element).data('icon') + '"></i>' + icon.text;
	};
})(jQuery, {});

var Select2Selects = function() {
	return {
		init: function() {
			jQuery('.Select_Customer_Detail').select2({
				placeholder: Error_please_select_customers,
				allowClear: true
			});
		}
	}
}();

/*
Description : Customer Details DataTable
Created By : Ankush Sali			Created Date : 17-01-2019
*/
jQuery(document).ready(function () {
	jQuery.validator.addMethod("lettersonly", function(value, element) {
		return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
	});
	jQuery("#mail_msg").jqte();
	var jqteStatus = true;
	jQuery(".status").click(function()
	{
		jqteStatus = jqteStatus ? false : true;
		jQuery("#mail_msg").jqte({"status" : jqteStatus})
	});
	Select2Selects.init();
	if (document.getElementById("New_Customer_Add")) {
		jQuery("#New_Customer_Add").validate();
		jQuery("#full_name").rules("add", {
			required: true, lettersonly: true,
			messages: {required:Error_please_enter_full_name,lettersonly:Error_please_enter_email}
		});
		jQuery("#cus_password").rules("add", {
			required: true, minlength: 8,	maxlength: 15,
			messages: {required:Error_please_enter_password,minlength:Error_please_enter_minimum_8_characters,maxlength:Error_please_enter_maximum_15_characters}
		});
		jQuery("#preferred_email").rules("add", {
			required: true, email: true,remote:{url : base_url + "CRM/Check_Exist_E_mail",type : "POST",async: true,},
			messages: {required:Error_please_enter_email,email:Error_please_enter_valid_email,remote:Error_email_already_exists}
		});
		jQuery("#phone").rules("add", {
			required: true, number: true, minlength: 5,	maxlength: 10,
			messages: {required:Error_please_enter_phone_number, number:Error_please_enter_only_digits, minlength:Error_please_enter_minimum_5_digit, maxlength: Error_please_enter_maximum_10_digit}
		});
		jQuery("#street_address").rules("add", {
			required: true, minlength: 5,	maxlength: 100,
			messages: {required:Error_please_enter_street_address, minlength:Error_please_enter_minimum_5_digit, maxlength: Error_please_enter_maximum_100_digit}
		});
		jQuery("#zip_code").rules("add", {
			required: true, minlength: 2,	maxlength: 8,
			messages: {required:Error_please_enter_zip_code_number, minlength:Error_please_enter_minimum_2_digit, maxlength: Error_please_enter_maximum_8_digit}
		});
		jQuery("#city").rules("add", {
			required: true, accept: "[a-zA-Z]", minlength: 3,	maxlength: 15,
			messages: {required:Error_Please_enter_city_name, accept: Error_please_enter_only_alphabets, minlength:Error_please_enter_minimum_3_digit, maxlength: Error_please_enter_maximum_15_digit}
		});
		jQuery("#state").rules("add", {
			required: true, accept: "[a-zA-Z]", minlength: 3,	maxlength: 15,
			messages: {required:Error_please_enter_state_name, accept: Error_please_enter_only_alphabets, minlength:Error_please_enter_minimum_3_digit, maxlength: Error_please_enter_maximum_15_digit}
		});
	}

	/* Apply daterangepicker */
	var start = moment().subtract(29, "days");
	var end = moment();
	jQuery(".Customer_Date_Range").daterangepicker({
		autoUpdateInput: false,
		ranges: {
			"Today": [moment(), moment()],
			"Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
			"Last 7 Days": [moment().subtract(6, "days"), moment()],
			"Last 30 Days": [moment().subtract(29, "days"), moment()],
			"This Month": [moment().startOf("month"), moment().endOf("month")],
			"Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
		}
	});

	jQuery(".Customer_Date_Range").on("apply.daterangepicker", function(ev, picker) {
		jQuery(this).val(picker.startDate.format(settings["zn_date_picker_date_format"]) + " ~ " + picker.endDate.format(settings["zn_date_picker_date_format"]));
	});

	jQuery(".Customer_Date_Range").on("cancel.daterangepicker", function(ev, picker) {
		jQuery(this).val("");
	});

	/*
Description : Payment Date Submit Click Event
Created By : Jay Maisuriya				Created Date : 29-01-2019
*/
jQuery(document).on('click', '.Customer_Date_Range_Submit', function () {
	var Range_Dates = jQuery(".Customer_Date_Range").val().split(" ~ ");
	if (document.getElementById("zf_Customers_Table")) {
		jQuery(".Customer_Date_Range_Clear").show();
		Customer_Datatable(Range_Dates[0],Range_Dates[1]);
	}
});

/*
Description : Payment Date Clear Click Event
Created By : Jay Maisuriya				Created Date : 29-01-2019
*/
jQuery(document).on('click', '.Customer_Date_Range_Clear', function () {
	if (document.getElementById("zf_Customers_Table")) {
		jQuery(".Customer_Date_Range").val('');
		jQuery(".Customer_Date_Range_Clear").hide();
		Customer_Datatable();
	}
});

if (document.getElementById("zf_Customers_Table")) {
	jQuery(".Customer_Date_Range_Clear").hide();
	Customer_Datatable();
}
	
	/*
	Description : Customer Booking Details DataTable
	Created By : Ankush Sali			Created Date : 17-01-2019
	*/
	jQuery(document).on("click",".Booking_Info",function() {
		jQuery(".zfa-loader-main").show();
    jQuery("#Booking_Info").modal("toggle");
		if (document.getElementById("zf_Booking_Info_Table")) {
			var email = jQuery(this).data("email");
			
			jQuery("#zf_Booking_Info_Table").DataTable({
				lengthMenu: [[10, 25, 50, 9999999999], [10, 25, 50, "All"]],
				bProcessing: true,
				bDestroy: true,
				responsive: true,
				language: {
						paginate: {
								previous: jQuery("<i>", { class: "fas fa-angle-left" }),
								next: jQuery("<i>", { class: "fas fa-angle-right" })
						}
				},
				serverSide: true,
				ajax:{
					url : base_url + "CRM/Booking_Info_Datatable",
					type: "POST",
					data:{"email":email},
					error: function(data){
						jQuery(".dataTables_processing").hide();
					}
				},
				"columns": [
					{ data: "title" },
					{ data: "booking_date" },
					{ data: null,
						render: function (data) {
							jQuery(".zfa-loader-main").hide();
							if(data["booking_status"] == "P"){
								return "Pending";
							}else if(data["booking_status"] == "C"){
								return "Confirm";
							}else if(data["booking_status"] == "R"){
								return "Reject";
							}else if(data["booking_status"] == "RS"){
								return "Re-shcedule";
							}else if(data["booking_status"] == "CC"){
								return "Cancel by Client";
							}else if(data["booking_status"] == "CS"){
								return "Cancel by Service Provider";
							}else if(data["booking_status"] == "CO"){
								return "Completed";
							}else{
								return "Mark as No Show";
							}
						}
					},
					{ data: "payment_method"},
					{ data: "booking_more_details"}
				],
			});
		}
	});
	
	/*
	Description : Customer Add Model Open
	Created By : Jay Maisuriya			Created Date : 30-01-2019
	*/
	jQuery(document).on("click",".zf_Add_New_Customer",function() {
		jQuery("#new_customer").modal("toggle");
	});
	
	/*
	Description : Customer Add Save Button Click
	Created By : Jay Maisuriya			Created Date : 30-01-2019
	*/
	jQuery(document).on("click","#zf_New_Add_Customer",function() {
		jQuery(".zfa-loader-main").show();
		
		if(jQuery('#New_Customer_Add').valid()){
			var preferred_email = jQuery("#preferred_email").val();
			var full_name = jQuery("#full_name").val();
			var cus_password = jQuery("#cus_password").val();
			var phone = jQuery("#phone").val();
			var street_address = jQuery("#street_address"). val();
			var zip_code = jQuery("#zip_code"). val();
			var city = jQuery("#city"). val();
			var state = jQuery("#state"). val();
			
			var formData = new FormData();
			formData.append("full_name", full_name);
			formData.append("cus_password", cus_password);
			formData.append("e_mail", preferred_email);
			formData.append("phone", phone);
			formData.append("address", street_address);
			formData.append("zip_code", zip_code);
			formData.append("city", city);
			formData.append("state", state);
			
			jQuery.ajax({
				url: base_url + "CRM/New_Customer",
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				success: function (data) {
					jQuery(".zfa-loader-main").hide();
					Swal.fire({
						type: "success",
						title: Error_added_successfully,
						showConfirmButton: false,
						timer: 1500
					});
					setTimeout(function(){ window.location.reload(); }, 1500);
				}
			});	
		}else{
			jQuery(".zfa-loader-main").hide();
		}
	});
	
	/*
	Description : Validation of Select Customer Enable/Disable Button
	Created By : Ankush Sali			Created Date : 20-02-2019
	*/
	jQuery('.Select_Customer_Detail').on('select change', function () {
		if (jQuery(this).val()!= '') {
			jQuery('#zn_Send_E-Mail_SMS').prop('disabled', false);	
		} else {
			jQuery('#zn_Send_E-Mail_SMS').prop('disabled', true);
		}
	});
	
	/*
	Description : Send Mail SMS Pop-Up JS
	Created By : Ankush Sali			Created Date : 09-02-2019
	*/
	jQuery(document).on("click","#zn_Send_E-Mail_SMS",function() {
    jQuery("#Send_E-Mail_SMS").modal("toggle");
	});
	
	/*
	Description : Attachement Button JS
	Created By : Ankush Sali			Created Date : 09-02-2019
	*/
	jQuery(document).on('click','#fake-file-button-browse',function(){
		jQuery('#mail_attch').click();
	});

	jQuery(document).on('change','#mail_attch',function(){
		jQuery('#fake-file-input-name').val(jQuery(this).val());
	});
	
	/*
	Description : Add Info for Send Mail to Customer JS
	Created By : Ankush Sali			Created Date : 09-02-2019
	*/
	jQuery(document).on("click","#zn_Send_E-Mail",function() {
		jQuery(".zfa-loader-main").show();
		var ids = "";
		var customer_ids = ""; 		 
		jQuery('.Select_Customer_Detail > option:selected').each(function() {
			ids += jQuery(this).val()+",";
			customer_ids = ids.slice(0, -1);		
		});

		var mail_sub = jQuery("#mail_sub").val();
		var mail_msg = jQuery("#mail_msg").val();
		var mail_attch = jQuery("#mail_attch").prop("files")[0];
		
		var formData = new FormData();
		
		formData.append("mail_sub", mail_sub);
		formData.append("mail_msg", mail_msg);
		formData.append("cust_ids", customer_ids);
		formData.append("mail_attch", mail_attch);
		
		jQuery("#zn_E-Mail_Form").validate();
		
		jQuery("#mail_sub").rules("add", {
			required: true,
			messages: {required:Error_please_enter_subject}
		});
		
		jQuery("#mail_msg").rules("add", {
			required: true,
			messages: {required:Error_please_enter_massage}
		});
		if(jQuery('#zn_E-Mail_Form').valid()){
			jQuery.ajax({
				url: base_url + "CRM/Send_Mail",
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				success: function (data) {
					if(data == "1"){
						Swal.fire({
							type: "success",
							title: Error_added_successfully,
							showConfirmButton: false,
							timer: 1500
						});
						setTimeout(function(){ window.location.reload(); }, 1500);
					}else{
						Swal.fire({
							type: "error",
							title: Error_oops,
							text: Error_something_went_wrong,
							showConfirmButton: false,
							timer: 1500
						});
					}
					jQuery(".zfa-loader-main").hide();
				}
			});	
		}else{
			setTimeout(function(){ window.location.reload(); }, 1500);
		}
	});
	
	/*
	Description : Add Info for Send SMS to Customer JS
	Created By : Ankush Sali			Created Date : 09-02-2019
	*/
	jQuery(document).on("click","#zn_Send_SMS",function() {
		jQuery(".zfa-loader-main").show();
		var ids = "";
		var customer_ids = ""; 		 
		jQuery('.Select_Customer_Detail > option:selected').each(function() {
			ids += jQuery(this).val()+",";
			customer_ids = ids.slice(0, -1);		
		});

		var sms_msg = jQuery("#sms_msg").val();

		var formData = new FormData();
		
		formData.append("sms_msg", sms_msg);
		formData.append("cust_ids", customer_ids);
		
		
    jQuery("#zn_SMS_Form").validate();
		
		jQuery("#sms_msg").rules("add", 
		{
			required: true,
			messages: {required:Error_please_enter_massage}
		});

		if(jQuery('#zn_SMS_Form').valid()){		
			jQuery.ajax({
				url: base_url + "CRM/Send_SMS",
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				success: function (data) {
					if(data == "1"){
						Swal.fire({
							type: "success",
							title: Error_added_successfully,
							showConfirmButton: false,
							timer: 1500
						});
						setTimeout(function(){ window.location.reload(); }, 1500);
					}else{
						Swal.fire({
							type: "error",
							title: Error_oops,
							text: Error_something_went_wrong,
							showConfirmButton: false,
							timer: 1500
						});
					}
					jQuery(".zfa-loader-main").hide();
				}
			});	
		}else{
			setTimeout(function(){ window.location.reload(); }, 1500);
		}
	});
	
	/*
	Description : All Mail SMS Pop-Up JS
	Created By : Ankush Sali			Created Date : 20-02-2019
	*/
	jQuery(document).on("click","#zn_All_E-Mail_SMS",function() {
    jQuery("#All_E-Mail_SMS").modal("toggle");
	});
	
	/*
	Description : All E-Mail DataTable JS
	Created By : Ankush Sali			Created Date : 20-02-2019
	*/
	if (document.getElementById("zf_Customers_E-Mails")) {
		jQuery("#zf_Customers_E-Mails").DataTable({
			lengthMenu: [[10, 25, 50, 9999999999], [10, 25, 50, "All"]],
			bProcessing: true,
			responsive: true,
			language: {
				paginate: {
					previous: jQuery("<i>", { class: "fas fa-angle-left" }),
					next: jQuery("<i>", { class: "fas fa-angle-right" })
				}
			},
			serverSide: true,
			ajax:{
				url : base_url + "CRM/E_Mails_Datatable",
				type: "POST",
				error: function(data){
					jQuery(".dataTables_processing").hide();
				}
			},
			columns: [
				{ data: "counter"},
				{ data: "mail_sub" },
				{ data: "mail_msg"},
				{ data: "mail_attch" },					
				{ data: "mail_dt"},
			],
		});
	}
	
	/*
	Description : All SMS DataTable JS
	Created By : Ankush Sali			Created Date : 20-02-2019
	*/
	if (document.getElementById("zf_Customers_SMS")) {
		jQuery("#zf_Customers_SMS").DataTable({
			lengthMenu: [[10, 25, 50, 9999999999], [10, 25, 50, "All"]],
			bProcessing: true,
			responsive: true,
			language: {
				paginate: {
					previous: jQuery("<i>", { class: "fas fa-angle-left" }),
					next: jQuery("<i>", { class: "fas fa-angle-right" })
				}
			},
			serverSide: true,
			ajax:{
				url : base_url + "CRM/SMS_Datatable",
				type: "POST",
				error: function(data){
					jQuery(".dataTables_processing").hide();
				}
			},
			columns: [
				{ data: "counter"},
				{ data: "sms_msg"},
				{ data: "sms_dt"},
			],
		});
	}
});

function Customer_Datatable(Start_Date = "",End_Date = "") {
	//if (document.getElementById("zf_Customers_Table")) {
		var Customer_Table = jQuery('#zf_Customers_Table').DataTable();
		Customer_Table.destroy();
		jQuery("#zf_Customers_Table").DataTable({
			lengthMenu: [[10, 25, 50, 9999999999], [10, 25, 50, "All"]],
			bProcessing: true,
			responsive: true,
			language: {
					paginate: {
							previous: jQuery("<i>", { class: "fas fa-angle-left" }),
							next: jQuery("<i>", { class: "fas fa-angle-right" })
					}
			},
			serverSide: true,
			ajax:{
					url : base_url + "CRM/Customer_Datatable",
					type: "POST",
					data: {"Start_Date": Start_Date, "End_Date": End_Date},
					dataSrc: function (json) {
						jQuery(".Select_Customer_Detail").empty();
						for ( var i=0, c_length=json.customer_details.length ; i<c_length ; i++ ) {
							jQuery(".Select_Customer_Detail").append("<option value="+json.customer_details[i]["ID"]+">"+json.customer_details[i]["Name"]+"</option>");
						}
						return json.data;
					},
					error: function(data){
						jQuery(".dataTables_processing").hide();
					}
				},
				columns: [
					{ data: null,
						render: function (data) {
							return data.full_name
						}
					},
					{ data: "e_mail" },
					{ data: "phone" },
					{ data: "address"},
					{ data: "city"},
					{ data: "state"},
					{ data: "zip_code"},
					{ data: null,
						render: function (data) {
							if(data.Total_Booking == 0) {
								var return_text =  '<a class="btn btn-primary disabled Booking_Info common-action" id="booking" data-email="'+data.e_mail+'" href="javascript:void(0);" data-toggle="modal"><i class="fa fa-calendar-alt"></i><span class="badge br-10">' +data.Total_Booking+ '</span></a>';
							}else{
								var return_text =  '<a class="btn btn-primary Booking_Info common-action" id="booking" data-email="'+data.e_mail+'" href="javascript:void(0);" data-toggle="modal"><i class="fa fa-calendar-alt"></i><span class="badge br-10">' +data.Total_Booking+ '</span></a>';
							}
							  return_text += '<button class="btn btn-md btn-primary zf_Edit_Customer common-action" data-Customer_id="'+data.ID+'"><i class="fa fa-edit"></i></button>';
							  return_text += '<button id="zf_Delete_Customer" class="btn btn-md btn-danger common-action" data-Customer_id="'+data.ID+'"><i class="fa fa-trash"></i></button>';
							return return_text;
						},
					},
				],
		});
 // }
}	
/*
Description : Edit Customer
Created By : Divyesh Patel			Created Date : 14-12-2019
*/
jQuery(document).on("click",".zf_Edit_Customer",function() {
	jQuery(".zfa-loader-main").show();
	var Customer_Id= jQuery(this).attr("data-customer_id");
	var formData = new FormData();
	formData.append("Customer_Id", Customer_Id);
	jQuery.ajax({
		url: base_url + "Customer/Get_One_Customer_Detail_For_Admin",
		type: "POST",
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		success: function (data) {
		 var Customer_Detail = jQuery.parseJSON(data);
		 jQuery("#Update_full_name").val(Customer_Detail.Full_Name);
		 jQuery("#Update_preferred_email").val(Customer_Detail.E_Mail);
		 jQuery("#Update_phone").val(Customer_Detail.Phone);
		 jQuery("#Update_street_address").val(Customer_Detail.Address);
		 jQuery("#Update_zip_code").val(Customer_Detail.Zip);
		 jQuery("#Update_city").val(Customer_Detail.City);
		 jQuery("#Update_state").val(Customer_Detail.State);
		 jQuery("#zf_Update_Customer").attr("data-customer_id",Customer_Detail.ID);
		 jQuery("#zf_Update_Customer_Password").attr("data-customer_id",Customer_Detail.ID);
		 jQuery("#update_customer").modal("toggle");
		 jQuery(".zfa-loader-main").hide();
		}
	});
});

/*
Description : Customer  Form Validate Function
Created By : Divyesh Patel			Created Date : 13-12-2019
*/
function Customer_data_Validate(){
	jQuery("#Customer_Update").validate();
	
	jQuery("#Update_full_name").rules("add", {
			required: true, lettersonly: true,
			messages: {required:Error_please_enter_full_name,lettersonly:Error_please_enter_email}
	});
	jQuery("#Update_preferred_email").rules("add", {
		required: true, email: true,
		messages: {required:Error_please_enter_email,email:Error_please_enter_valid_email}
	});
	jQuery("#Update_phone").rules("add", {
		required: true, number: true, minlength: 5,	maxlength: 10,
		messages: {required:Error_please_enter_phone_number, number:Error_please_enter_only_digits, minlength:Error_please_enter_minimum_5_digit, maxlength: Error_please_enter_maximum_10_digit}
	});
	jQuery("#Update_street_address").rules("add", {
		required: true, minlength: 5,	maxlength: 100,
		messages: {required:Error_please_enter_street_address, minlength:Error_please_enter_minimum_5_digit, maxlength: Error_please_enter_maximum_100_digit}
	});
	jQuery("#Update_zip_code").rules("add", {
		required: true, minlength: 2,	maxlength: 8,
		messages: {required:Error_please_enter_zip_code_number, minlength:Error_please_enter_minimum_2_digit, maxlength: Error_please_enter_maximum_8_digit}
	});
	jQuery("#Update_city").rules("add", {
		required: true, accept: "[a-zA-Z]", minlength: 3,	maxlength: 15,
		messages: {required:Error_Please_enter_city_name, accept: Error_please_enter_only_alphabets, minlength:Error_please_enter_minimum_3_digit, maxlength: Error_please_enter_maximum_15_digit}
	});
	jQuery("#Update_state").rules("add", {
		required: true, accept: "[a-zA-Z]", minlength: 3,	maxlength: 15,
		messages: {required:Error_please_enter_state_name, accept: Error_please_enter_only_alphabets, minlength:Error_please_enter_minimum_3_digit, maxlength: Error_please_enter_maximum_15_digit}
	});
}

/*
Description : Update Customer
Created By : Divyesh Patel			Created Date : 14-12-2019
*/
jQuery(document).on("click","#zf_Update_Customer",function() {
	jQuery(".zfa-loader-main").show();
		Customer_data_Validate();
	if(jQuery('#Customer_Update').valid()){
		var Customer_id = jQuery("#zf_Update_Customer").attr("data-customer_id");
	  var formData = new FormData();
		formData.append("ID", Customer_id);
		formData.append("full_name", jQuery("#Update_full_name").val());
		formData.append("e_mail", jQuery("#Update_preferred_email").val());
		formData.append("phone", jQuery("#Update_phone").val());
		formData.append("address", jQuery("#Update_street_address").val());
		formData.append("zip_code", jQuery("#Update_zip_code").val());
		formData.append("city", jQuery("#Update_city").val());
		formData.append("state", jQuery("#Update_state").val());
		
		jQuery.ajax({
			url: base_url + "Customer/Customer_Profile_Detail_Update_For_Admin",
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (data) {
				if(data == "1"){
					Swal.fire({
						type: "success",
						title: Error_added_successfully,
						showConfirmButton: false,
						timer: 1500
					});
					setTimeout(function(){ window.location.reload(); }, 1500);
				}else{
					Swal.fire({
						type: "error",
						title: Error_oops,
						text: Error_something_went_wrong,
						showConfirmButton: false,
						timer: 1500
					});
				}
				jQuery(".zfa-loader-main").hide();
			}
		});
	} else {
		jQuery(".zfa-loader-main").hide();
	}
	
});


/*
Description : Customer Password Form Validate Function
Created By :  Divyesh Patel			Created Date : 14-12-2019
*/
function Customer_Password_Validate(){
	jQuery("#Update_Customer_Password").validate();
	
	jQuery("#customer_old_password").rules("add",{
		required: true,
		messages: {required:Error_please_enter_old_password}
	});

	jQuery("#customer_new_password").rules("add",{
		required: true, minlength: 8,	maxlength: 15,
		messages: {required:Error_please_enter_password, minlength:Error_please_enter_minimum_8_digit, maxlength: Error_please_enter_maximum_15_characters,}
	});

	jQuery("#customer_confirm_password").rules("add",{
		equalTo: "#customer_new_password",
		messages: {equalTo:Error_your_password_not_match,}
	});
}

/*
Description : Customer Passwordr Change Old Password With New Password
Created By :  Divyesh Patel			Created Date : 14-12-2019
*/
jQuery(document).on("click","#zf_Update_Customer_Password",function() {
	jQuery(".zfa-loader-main").show();
	Customer_Password_Validate();
	if(jQuery('#Update_Customer_Password').valid()){
		var customer_id = jQuery("#zf_Update_Customer_Password").attr("data-customer_id");
		var formData = new FormData();
		formData.append("customer_old_password", jQuery("#customer_old_password").val());
		formData.append("cus_password", jQuery("#customer_new_password").val());
		formData.append("customer_id", customer_id);

		jQuery.ajax({
			url: base_url + "Customer/Change_Customer_Password_For_Admin",
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (data) {
				if(data == "1"){
					Swal.fire({
						type: "success",
						title: Error_updated_successfully,
						showConfirmButton: false,
						timer: 1500
					});
					jQuery("#customer_old_password").val("");
					jQuery("#customer_new_password").val("");
					jQuery("#customer_confirm_password").val("");
					setTimeout(function(){ window.location.reload(); }, 1500);
				}else{
					Swal.fire({
						type: "error",
						title: Error_oops,
						text: Error_old_password_not_match,
						showConfirmButton: false,
						timer: 1500
					});
				}
				jQuery(".zfa-loader-main").hide();
			}
		});
	}else{
		jQuery(".zfa-loader-main").hide();
	}
});


/*
Description :  Delete Customer Button Click Event
Created By :  Divyesh Patel			Created Date : 14-12-2019
*/
jQuery(document).on("click","#zf_Delete_Customer",function() {
	var formData = new FormData();
	formData.append("ID", jQuery(this).attr("data-customer_id"));
	Swal.fire({
		title: Error_are_you_sure,
		type: "warning",
		showCancelButton: true,
		confirmButtonText: Error_yes_delete_it,
		cancelButtonClass: "zf-swal-cancel",
		confirmButtonClass: "zf-swal-confirm",
		showLoaderOnConfirm: true,
	}).then((result) => {
		if (result.value) {
			jQuery(".zfa-loader-main").show();
			jQuery.ajax({
				url: base_url + "Customer/delete_customer",
				type: "POST",
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				success: function (data) {
					if(data == "1"){
						Swal.fire({
							type: "success",
							title: Error_deleted_successfully,
							showConfirmButton: false,
							timer: 1500
						});
						setTimeout(function(){ window.location.reload(); }, 1500);
					}else{
						Swal.fire({
							type: "error",
							title: Error_oops,
							text: Error_something_went_wrong,
							showConfirmButton: false,
							timer: 1500
						});
					}
					jQuery(".zfa-loader-main").hide();
				}
			});
		}
	});
});