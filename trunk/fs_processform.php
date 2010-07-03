<?php

/**
 * This file processes all the admin forms
 */


// variable for form outcome
$message = array(
	'output'=>false,
	'type'=>'success',
	'message'=>'',
	'action'=>isset($_GET['action']) ? $_GET['action'] : '',
	'showform'=>true
);

// form action variable
$action = isset($_GET['action']) ? $_GET['action'] : '';