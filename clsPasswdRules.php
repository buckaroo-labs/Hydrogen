<?php

//This class enforces rules for password length and conplexity.

//NOTE: I honestly can't remember whether I wrote this or downloaded it :-(
//It sort of looks like my work and I can't find matching text on Google,
//So I will hope that if I have stolen it, I can be pardoned.

//Assumptions:
//(1) Passwords will never be stored as unencrypted text
//		nor returned to the user in any form (lost passwords cannot
//		be retrieved; they must be reset for security reasons).
//(2) All password input can be HTML encoded after being checked, so
//		any and all non-alphanumeric characters are allowed
//NOTE: HTML encoding changes the length of the password string!


//USAGE: 
/*
$myPR=new PasswordRules(false);
$myPR->addRule("min",12,"/\S/","character");
$pw_result=$myPR->checkPassword($_POST['pwd']);



*/

class PasswordRules{

	protected $_rules;

	function ruleText($row) {
		$retval='';
		//check: rule type=minimum
		if ($this->_rules[$row][1]=="min") {
				$showplural = ($this->_rules[$row][2] > 1) ? "s" : "";
				$retval="Password must include at least " . $this->_rules[$row][2] . " ". $this->_rules[$row][4] . $showplural . '.';
		}
		if ($this->_rules[$row][1]=="max") {
				$showplural = ($this->_rules[$row][2] > 1) ? "s" : "";
				$retval="Password must not include more than " . $this->_rules[$row][2] . " ". $this->_rules[$row][4] . $showplural . '.';		}
		return$retval;
	}

	public function __construct($useDefaults=true) {
	//modify this function to turn rules on/off or to change their criteria
		if ($useDefaults) {
		$this->_rules = array(
			//active (1/0), type, value, search pattern, description, satisfied (true/false)
			array(1, 'min', 8,	'/\S/',					'character', 			false),
			array(1, 'min', 1,	'/[A-Z]/',				'upper case letter', 	false),
			array(1, 'min', 1,	'/[a-z]/',				'lower case letter', 	false),
			array(1, 'min', 1,	'/\d/',					'number', 				false),
			array(1, 'min', 1,	'/[^a-zA-Z0-9]/',		'non-alphanumeric character', false),
			array(1, 'max', 99,	'/\S/',					'character', 			false)
			);
		}
	}
	
	public function addRule($type,$value,$pattern,$description) {
		$index=count($this->_rules);
		if ($type=="min") $this->_rules[$index]=array(1,'min',$value,$pattern,$description,false);
		if ($type=="max") $this->_rules[$index]=array(1,'max',$value,$pattern,$description,false);
		return 1;
	}
	

	public function filterPassword($password) {
		//This function has no connection to what goes on in the rest of the class!
		//It is provided as an example only
		$encoded = filter_var($input,FILTER_SANITIZE_ENCODED);
		return $encoded;
	}

	public function checkPassword($password) {
		//checks an UNFILTERED password
		//return true or false
		$errors = false;
		//loop through each rule, setting initial value of satisfied=false;
		for ($row = 0; $row < count($this->_rules); $row++) {
		$this->_rules[$row][5]=false;
		//check: rule is active
			if ($this->_rules[$row][0]) {
				//check: rule type=minimum
				if ($this->_rules[$row][1]=='min') {
					//match the pattern to the password value
					$found= preg_match_all($this->_rules[$row][3],$password);
					//compare number of matches to the minimum
					if ($found < $this->_rules[$row][2]) {
						$errors=true;
					} else {$this->_rules[$row][5]=true; }
				}
				if ($this->_rules[$row][1]=='max') {
					//match the pattern to the password value
					$found= preg_match_all($this->_rules[$row][3],$password);
					//compare number of matches to the maximum
					if ($found > $this->_rules[$row][2]) {
						$errors=true;
					} else {$this->_rules[$row][5]=true; }
				}
			}
		}
		return $errors ? false : true;
	}

	public function showRules() {
		//return all rules as an array of HTML strings to be presented to user
		//usage: echo (implode('<br>',myPRinstance.showRules()));
		$html = array();
		//loop through each rule
		for ($row = 0; $row < count($this->_rules); $row++) {
			//check: rule is active
			if ($this->_rules[$row][0]) {
				//format the text according to whether the rule is satisfied
				//$css_class = ($this->_rules[$row][5]) ? 'passwordrule_satisfied' : 'passwordrule_unsatisfied';
				$font_color = ($this->_rules[$row][5]) ? '#000000' : 'FF0000';
				//append the html to the array
				//$html[$row]='<div class="' . $css_class . '">' .  $this->ruleText($row) . '</div>';
				$html[$row]='<font color="' . $font_color . '">' .  $this->ruleText($row) . '</font>';
			}
		}
		return $html;
	}

}

?>