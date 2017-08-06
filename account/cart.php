<?php
	$page_title = "My Cart";
	include_once('../includes/header.php');

	$sql_cart = "SELECT od.DetailID, od.ProductID, p.Image,
		p.Name, c.Name AS Category, p.Price, od.Quantity,
		od.Amount FROM orderdetails od
		INNER JOIN products p ON od.productID = p.productID
		INNER JOIN categories c ON p.catID = c.catID
		WHERE od.orderNo=0 AND od.userID=1";
	$result_cart = $con->query($sql_cart) or die(mysqli_error($con));
	$list_cart = "";
	while ($row = mysqli_fetch_array($result_cart))
	{	
		$did = $row['DetailID'];
		$pid = $row['ProductID'];
		$image = $row['Image'];
		$pname = $row['Name'];
		$cat = $row['Category'];
		$price = $row['Price'];
		$qty = $row['Quantity'];
		$amount = $row['Amount'];

		$list_cart .= "<tr>
							<td><img src='../images/products/$image'
								width='150' /></td>
							<td><h3>$pname</h3>
								<small><em>$cat</em></small>
							</td>
							<td>p$price</td>
							<td>
								<input type='number' min='1' max='100' 
									class='form-control' value='$qty' required 
									/>
							</td>
							<td>P$amount</td>
							<td>
								<button class='update btn btn-success btn-xs'>
									<i class='fa fa-refresh'></i>
								</button>
								<button class='delete btn btn-danger btn-xs'>
									<i class='fa fa-trash'></i>
								</button>
							</td>		
					   </tr>";
	}

	$sql_compute = "SELECT SUM(amount) FROM orderdetails
					WHERE orderNo=0 AND userID=1";
	$result_compute = $con->query($sql_compute) or die(mysqli_error($con));
	while ($row2 = mysqli_fetch_array($result_compute))
	{
		$total = $row2[0];
		$gross = $total * .88;
		$VAT = $total * .12;
	}
?>
	<div class="col-lg-8">
		<form class="form-horizontal" method="POST">
			<table class="table table-hover">
				<thead>
					<th colspan="2">Item</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Amount</th>
				</thead>
				<tbody>
					<?php echo $list_cart; ?>	
				</tbody>
			</table>
		</form>	
	</div>
	<div class="col-lg-4">
		<div class="well">
		<h3 class="text-center">Order Summary</h3>
		<table class="table table-hover">
			<tr>
				<td>Gross Amount</td>
				<td align='right'>P<?php echo number_format($gross, 2,'.', ',' 
					) ; ?></td>
			</tr>
			<tr>
				<td>VAT</td>
				<td align='right'><?php echo number_format($VAT, 2,'.', ',' 
					); ?></td>	
			</tr>
			<tr>
				<td><b>Total Amount</b></td>
				<td align='right'><b>P<?php echo number_format($total, 2,'.', ',' 
					); ?></b></td>
			</tr>
		</table>
		<hr/>
		<a href='checkout.php' class='btn btn-success btn-lg btn-block'>
		<i class='fa fa-money'></i> Checkout
		</a>
	</div>
<?php
	include_once('../includes/footer.php');

?>