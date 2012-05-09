<?php

class Application_Form_Register extends Zend_Form
{

    public function init()
    {
     	// ID field. Hidden.
		$user_id = new Zend_Form_Element_Hidden("user_id");
		
		// Username
		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Username:')
			->setRequired(true)
			->addValidator('NotEmpty');
			
		// Firstname
		$firstname = new Zend_Form_Element_Text('firstname');
		$firstname->setLabel('Firstname:')
			->setRequired(true)
			->addValidator('NotEmpty');
			
		// Surname
		$surname = new Zend_Form_Element_Text('surname');
		$surname->setLabel('Surname:')
			->setRequired(true)
			->addValidator('NotEmpty');
			
		// Email
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email:')
			->setRequired(true)
			->addValidator('NotEmpty');
			
		// Level
		$level = new Zend_Form_Element_Select('level');
		$level->setLabel('Level:');
		$level->addMultiOption(1,'Basic');
		if(Application_Model_User::isLoggedInUserAdmin()){
			$level->addMultiOption(9,'Editor');
			$level->addMultiOption(10,'Administrator');
		}
		$validator_level= new Zend_Validate_InArray(array(1,9,10));
		$validator_level->setMessage("You must select a level");
		$level->addValidator($validator_level);
		
		// Set password
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Set password:');
		
		// Set password again
		$password_again = new Zend_Form_Element_Password('password_again');
		$password_again->setLabel('Password again:');
		
		// Radio for enabled/disabled
		$enabled = new Zend_Form_Element_Radio('enabled');
		$enabled->setLabel("Enabled?");
		$enabled->addMultiOption(0,"Disabled");
		$enabled->addMultiOption(1,"Enabled");
		
		// Save button
		$save_button = new Zend_Form_Element_Submit("save");
		$save_button->setValue("save_button");
		$save_button->setLabel("Save");
		
		// Assemble the form
		$this->addElements(array(
		//	$user_id,
			$username,
			$firstname,
			$surname,
			$email,
			//$level,
			//$enabled,
			$password,
			$password_again,
			$save_button
		));
    }


}

