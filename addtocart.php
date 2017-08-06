<?php
	if(isset($_REQUEST['p'])&&
		isset($_REQUEST['qty']))
{
	include "function.php";
	include "config.php";

	$productID = $_REQUEST['p'];
	$quantity = $_REQUEST['qty'];
	addToCart($con, $productID, $quantity);
}
?>