<?php
if(isset($_POST) && $_POST['url'] != "")
{
	$link = trim($_POST['url']);
	require_once 'insta.php';
	$insta = new Insta($link);
	$insta->start();
	$return = $insta->get_errors_info_warnings();
	/*
	$_SESSION['return'] = $return;
	if(count($return['errors']) > 0)
	{
		header("Location: index.php");
		die();
	}
	*/
}
?>