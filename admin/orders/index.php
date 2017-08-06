<?php
	$page_title = "View Orders";
	include_once('../../includes/header_admin.php');

	$sql_orders = "SELECT orderNo, orderDate,
			(SELECT COUNT(od.detailID) FROM orderdetails od
			WHERE od.orderNo = o.orderNo) AS totalQty,
			(SELECT SUM(od.Amount) FROM orderdetails od
			WHERE od.orderNo = o.orderNo) AS totalAmount,
			o.paymentMethod, o.approveDate, o.Status
			FROM orders o
			ORDER BY o.orderNo DESC";
	$result_orders = $con->query($sql_orders) or die(mysqli_error($con));
	$list_orders = "";
	while ($row = mysqli_fetch_array($result_orders))
	{
		$no = $row['orderNo'];
		$odate = $row['orderDate'];
		$qty = $row['totalQty'];
		$amount = $row['totalAmount'];
		$pay = $row['paymentMethod'];
		$adate = $row['approveDate'];
		$stat = $row['Status'];

		$list_orders .= "<tr>
							<td>$no</td>
							<td>$odate</td>
							<td>$qty</td>
							<td>$amount</td>
							<td>$pay</td>
							<td>$adate</td>
							<td>$stat</td>
							<td><a href='details.php?no=$no' class='btn btn-xs
								btn-info'><i class='fa fa-edit'></i></a>
							</td>
						</tr>";
	}
?>
<form class="form-horizontal">
	<div class="col-lg-12">
	<table class="table table-hover">
			<thead>
				<th> Order #</th>
				<th>Order Date</th>
				<th>Total Quantity</th>
				<th>Payment Method</th>
				<th>Approval Date</th>
				<th>Status</th>
				<th></th>
			</thead>
			<tbody>
				<?php echo $list_orders ?>
			</tbody>
		</table>
	</div>
</form>
<?php
	include_once('../../includes/footer.php');