<?php     
/***************************************************************
*  Project Name : Zinnfy
*  Created By : Ankush Sali
*  Created Date : 12-02-2019
*  Modification History :
*  Dated				Developer Name				Description
   12-02-2019		Ankush Sali						Used for Manage Business Details
	 23-07-2019		Jay Maisuriya					Email and SMS Templet Insert Functions
***************************************************************/
defined("BASEPATH") OR exit("No direct script access allowed");

class Super_Admin_Model extends CI_Model {
	
	/* By Default Construct Function */
	function __construct() {
		parent::__construct();
	}
	
	/*
	Description : Get All Business Details
	Created By : Ankush Sali			Created Date : 12-02-2019
	*/
	public function Get_All_Businesses(){
		$this->db->select("`ID`,`full_name`");
		$this->db->from("`zn_user`");
		$this->db->where("`role`", "A");
		$query_result = $this->db->get();
		return $query_result->result();
	}
	
	/*
	Description : Get All Business Details
	Created By : Ankush Sali			Created Date : 12-02-2019
	Created By : Jay Maisuriya			Created Date : 23-07-2019
	*/
	public function Get_All_Business_Details($business_id,$Start,$Length,$Serch_Value){
		$this->db->select("`ID`,`user_email`,`phone`,`full_name`,`zip`,`address`,`city`,`state`,`country`,`notes`,`subdomain_name`,`status`,`created_date`,`business_id`");
		$this->db->from("`zn_user`");
		$this->db->where("`role`", "A");
		if($Serch_Value != ""){
			$this->db->group_start();
			$this->db->like("`user_email`", $Serch_Value);
			$this->db->or_like("`phone`", $Serch_Value);
			$this->db->or_like("`full_name`", $Serch_Value);
			$this->db->or_like("`zip`", $Serch_Value);
			$this->db->or_like("`address`", $Serch_Value);
			$this->db->or_like("`city`", $Serch_Value);
			$this->db->or_like("`state`", $Serch_Value);
			$this->db->or_like("`country`", $Serch_Value);
			$this->db->or_like("`notes`", $Serch_Value);
			$this->db->or_like("`subdomain_name`", $Serch_Value);
			$this->db->group_end();
		}
		$this->db->order_by("`ID` DESC");
		$this->db->limit($Length, $Start);
		$query_result = $this->db->get();
		return $query_result->result();
	}
	
	/*
	Description : Change Business Status
	Created By : Ankush Sali			Created Date : 13-02-2019
	*/
	public function business_Status_Change($data_Update,$business_id){
		$this->db->where("`ID`",$business_id);
		$result = $this->db->update("`zn_user`",$data_Update);
		return $result;
	}
	
	/*
	Description : Add New Business
	Created By : Ankush Sali			Created Date : 13-02-2019
	*/
	public function insert_Business($data_Insert){
		$this->db->insert("`zn_user`",$data_Insert);
		$insert_id = $this->db->insert_id();
		
		$data_Update = array("`business_id`" => $insert_id);
		$this->db->where("`ID`",$insert_id);
		$result = $this->db->update("`zn_user`",$data_Update);
		
		return $insert_id;
		return $result;
	}
	
	/*
	Description : Delete Business
	Created By : Ankush Sali			Created Date : 14-02-2019
	*/
	public function delete_Business($data_business_delete,$data_provider_delete){
		$this->db->select("`ID`");
		$this->db->from("`zn_user`");
		$this->db->where("`business_id`",$data_business_delete["business_id"]);
		$this->db->where("`role`","SM");
		$query_result = $this->db->get()->result();
		
		foreach($query_result as $qr){
			$SM_business_id_Array = array();
			$SM_business_id_Array["business_id"] = $qr->ID;
			$SM_provider_id_Array = array();
			$SM_provider_id_Array["provider_id"] = $qr->ID;
			$this->db->delete("`zn_user`",$SM_business_id_Array);
			$this->db->delete("`zn_week_days_available`",$SM_provider_id_Array);
		}
		
		$this->db->select("`ID`");
		$this->db->from("`zn_service`");
		$this->db->where("`business_id`",$data_business_delete["business_id"]);
		$Service_Result = $this->db->get()->result();
		
		if(!empty($Service_Result)){
			foreach($Service_Result as $SR){
				/* Delete Service Unit Pricing Rules */
				$this->db->select("`ID`");
				$this->db->from("`zn_services_units`");
				$this->db->where("`services_id`",$SR->ID);
				$Service_Units_Result = $this->db->get()->result();
				
				if(!empty($Service_Units_Result)){
					foreach($Service_Units_Result as $SUR){
						$Service_Unit_ID_Array = array();
						$Service_Unit_ID_Array["`unit_id`"] = $SUR->ID;
						$this->db->delete("`zn_services_units_price_rules`",$Service_Unit_ID_Array);
					}
				}
				
				/* Delete Service Addon Pricing Rules */
				$this->db->select("`ID`");
				$this->db->from("`zn_services_addons`");
				$this->db->where("`services_id`",$SR->ID);
				$Service_Addons_Result = $this->db->get()->result();
				
				if(!empty($Service_Addons_Result)){
					foreach($Service_Addons_Result as $SAR){
						$Service_Addon_ID_Array = array();
						$Service_Addon_ID_Array["`addon_id`"] = $SAR->ID;
						$this->db->delete("`zn_services_addons_price_rules`",$Service_Addon_ID_Array);
					}
				}
				
				$Service_ID_Array = array();
				$Service_ID_Array["`services_id`"] = $SR->ID;
				$this->db->delete("`zn_services_units`",$Service_ID_Array);
				$this->db->delete("`zn_services_addons`",$Service_ID_Array);
			}
		}
		
		$this->db->delete("`zn_api_tokens`",$data_business_delete);
		$this->db->delete("`zn_booking_addons`",$data_business_delete);
		$this->db->delete("`zn_booking_info`",$data_business_delete);
		$this->db->delete("`zn_booking_units`",$data_business_delete);
		$this->db->delete("`zn_business_payments`",$data_business_delete);
		$this->db->delete("`zn_business_plans_details`",$data_business_delete);
		$this->db->delete("`zn_coupons`",$data_business_delete);
		$this->db->delete("`zn_customer`",$data_business_delete);
		$this->db->delete("`zn_email_sms`",$data_business_delete);
		$this->db->delete("`zn_email_templates`",$data_business_delete);
		$this->db->delete("`zn_email_user`",$data_business_delete);
		$this->db->delete("`zn_language`",$data_business_delete);
		$this->db->delete("`zn_off_days`",$data_provider_delete);
		$this->db->delete("`zn_order_client_info`",$data_business_delete);
		$this->db->delete("`zn_payments`",$data_business_delete);
		$this->db->delete("`zn_rating_review`",$data_business_delete);
		$this->db->delete("`zn_recurrence_discount`",$data_business_delete);
		$this->db->delete("`zn_recurring_status`",$data_business_delete);
		$this->db->delete("`zn_service`",$data_business_delete);
		$this->db->delete("`zn_settings`",$data_business_delete);
		$this->db->delete("`zn_sms_templates`",$data_business_delete);
		$this->db->delete("`zn_sms_user`",$data_business_delete);
		$this->db->delete("`zn_staff_commission`",$data_business_delete);
		$this->db->delete("`zn_staff_google_calendar_settings`",$data_business_delete);
		$this->db->delete("`zn_user`",$data_business_delete);
		$result = $this->db->delete("`zn_week_days_available`",$data_provider_delete);
		return $result;
	}
	
	/*
	Description : Insert Setting Data When Admin Created
	Created By :  Ajay Prajapati			Created Date : 22-02-2019
	*/
	public function insert_Setting_Data($Setting_Insert){
		$result = $this->db->insert_batch("`zn_settings`",$Setting_Insert);
		return $result;
	}

	/*
	Description : Insert Week days Data When Admin Created
	Created By :  Ajay Prajapati			Created Date : 22-02-2019
	*/
	public function insert_Week_Data($Week_Insert){
		$result = $this->db->insert_batch("`zn_week_days_available`",$Week_Insert);
		return $result;
	}
	
	/*
	Description : Insert Language Data When Admin Created
	Created By :  Jay Maisuriya			Created Date : 01-12-2019
	*/
	public function insert_Language_Data($Language_Insert){
		$result = $this->db->insert_batch("`zn_language`",$Language_Insert);
		return $result;
	}
	
	/*
	Description : Insert Recurrence Discount Data When Admin Created
	Created By :  Jay Maisuriya			Created Date : 24-07-2019
	*/
	public function insert_Recurrence_Discount_Data($Recurrence_Discount_Insert){
		$result = $this->db->insert_batch("`zn_recurrence_discount`",$Recurrence_Discount_Insert);
		return $result;
	}
	
	/*
	Description : Insert E-Mail Templet When Admin Created
	Created By :  Jay Maisuriya			Created Date : 23-07-2019
	*/
	public function insert_E_Mail_Templet_Data($E_Mail_Templet_Insert){
		$result = $this->db->insert_batch("`zn_email_templates`",$E_Mail_Templet_Insert);
		return $result;
	}
	
	/*
	Description : Insert SMS Templet When Admin Created
	Created By :  Jay Maisuriya			Created Date : 23-07-2019
	*/
	public function insert_SMS_Templet_Data($SMS_Templet_Insert){
		$result = $this->db->insert_batch("`zn_sms_templates`",$SMS_Templet_Insert);
		return $result;
	}

	/*
	Description : Add Feed back
	Created By : Divyesh Patel			Created Date : 06-02-2020
	*/
	public function Save_Feedback($data_Insert){
	  $result =	$this->db->insert("`zn_front_feedback`",$data_Insert);
		return $result;
	}

	/*
	Description : Get Subscribe_Newsletter
	Created By : Divyesh Patel			Created Date : 06-02-2020
	*/
	public function Subscribe_Newsletter_Datatable($Start,$Length,$Serch_Value){
		$this->db->select("`ID`,`subscribe_email`,`newsletter_type`,`mail_send_status`,`subscribe_by`,`date`");
		$this->db->from("`zn_subscribe_newsletter`");
		if($Serch_Value != ""){
			$this->db->group_start();
			$this->db->like("`subscribe_email`", $Serch_Value);
			$this->db->or_like("`newsletter_type`", $Serch_Value);
			$this->db->or_like("`subscribe_by`", $Serch_Value);
			$this->db->group_end();
		}
		$this->db->order_by("`ID` DESC");
		$this->db->limit($Length, $Start);
		$query_result = $this->db->get();
		return $query_result->result();
	}

	/*
	Description : Get Subscribe_Newsletter
	Created By : Divyesh Patel			Created Date : 06-02-2020
	*/
	public function Feedback_Table_data($Start,$Length,$Serch_Value){
		$this->db->select("`ID`,`feedback_type`,`feedback`,`feedback_username`,`feedback_email`,`date`");
		$this->db->from("`zn_front_feedback`");
		if($Serch_Value != ""){
			$this->db->group_start();
			$this->db->like("`feedback_type`", $Serch_Value);
			$this->db->or_like("`feedback_username`", $Serch_Value);
			$this->db->or_like("`feedback_email`", $Serch_Value);
			$this->db->group_end();
		}
		$this->db->order_by("`ID` DESC");
		$this->db->limit($Length, $Start);
		$query_result = $this->db->get();
		return $query_result->result();
	}

	/*
	Description : check_subscribe_news_letter
	Created By : Divyesh Patel			Created Date : 06-02-2020
	*/
	public function check_subscribe_news_letter($subscribe_email)
	{
		$this->db->select("`subscribe_email`");
		$this->db->from("`zn_subscribe_newsletter`");
		$this->db->where("`subscribe_email`",$subscribe_email);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return true;
		} else {
			return false;
		}
	}	

	/*
	Description : save subscribe_news_letter
	Created By : Divyesh Patel			Created Date : 06-02-2020
	*/
	public function subscribe_news_letter($data_Insert)
	{
		$result =	$this->db->insert("`zn_subscribe_newsletter`",$data_Insert);
		return $result;
	}

	/*
	Description : Get Newsletter data
	Created By : Divyesh Patel			Created Date : 11-02-2020
	*/
	public function Get_News_letter_data_by_range($bulk_send, $send_newsletter_type)
	{
		$this->db->select("`subscribe_email`");
		$this->db->from("`zn_subscribe_newsletter`");
		$this->db->where("`newsletter_type`",$send_newsletter_type);
		$this->db->where("`mail_send_status`","N");
		$this->db->order_by("`ID` ASC");
		$this->db->limit($bulk_send);
		$query_result = $this->db->get();
		return $query_result->result();	
	}

	public function subscribe_news_letter_status_update($send_Subscribe_Mail,$send_newsletter_type)
	{
		$data_Update = array("`mail_send_status`" => "Y");
		$this->db->where("`subscribe_email`",$send_Subscribe_Mail);
		$this->db->where("`newsletter_type`",$send_newsletter_type);
		$result = $this->db->update("`zn_subscribe_newsletter`",$data_Update);
	}

}