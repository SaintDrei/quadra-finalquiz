<?php
	$page_title = "Add Purchase Order";
	include_once('../../includes/header_admin.php');
	
	$sql_sup = "SELECT supplierID, companyName FROM suppliers ORDER BY companyName";
    $result_sup = $con->query($sql_sup);

	$list_sup = "";
	while ($row = mysqli_fetch_array($result_sup))
	{
		$supplierID = $row['supplierID'];
		$companyName = $row['companyName'];
		$list_sup .= "<option value='$supplierID'>$companyName</option>";
	}
	
	$sql_pro = "SELECT productID, name FROM products ORDER BY name";
    $result_pro = $con->query($sql_pro);

    $list_pro = "";
	while ($row = mysqli_fetch_array($result_pro))
	{
		$productID = $row['productID'];
		$name = $row['name'];
		$list_pro .= "<option value='$productID' name='pid'>$name</option>";
	}
	
	if (isset($_POST['add']))
	{
		
		$quantity = mysqli_real_escape_string($con, $_POST['quantity']);
		$pid = mysqli_real_escape_string($con, $_POST['product']);
		$sql_add = "INSERT INTO purchase_details VALUES ('', '', $pid, '$quantity')";
		$con->query($sql_add) or die(mysqli_error($con));
		header('location: add.php');
	}

	$sql_view = "SELECT p.productID, p.quantity, p.refNo, d.productID, d.name FROM purchase_details p INNER JOIN products d WHERE p.purchaseNo=0 and p.productID = d.productID";
	$result_view = $con->query($sql_view) or die(mysqli_error($con));
	$list_view = "";
	
	while ($row4 = mysqli_fetch_array($result_view))
	{	
	    $productID = $row4['productID'];
		$quantity = $row4['quantity'];
        $ref = $row4['refNo'];
		$pname = $row4['name'];
        $qtloc = $_SERVER['DOCUMENT_ROOT'] . '/lquiz/updateqty.php';
        
		$list_view .= "<tr>
							<td>$pname</td>
							
							<td>
                            
                            
                            <form method='get' action='./updateqty.php'>
                            <input type='number' class='form-control' value='$quantity' style='width:100px;' name='qty'/> 
                            </td>
                            <td>
                            <input type='number' value='$ref' hidden name='ref'>
                            <button class='update btn btn-success btn-xs' onclick='return confirm(\"Update Quantity?\");'>
									<i class='fa fa-refresh'></i>
								</button>
                                
                                <a href='delete.php?ref=$ref' class='btn btn-xs btn-danger' 
										onclick='return confirm(\"Delete Item?\");''>
										<i class='fa fa-trash'></i>
									</a>
                            </form>
				                
							</td>		
					   </tr>";
	}

	$sql_calc = "SELECT SUM(amount) FROM orderdetails
					WHERE orderNo=0 AND userID=1";
	$result_calc = $con->query($sql_calc) or die(mysqli_error($con));
	while ($row2 = mysqli_fetch_array($result_calc))
	{
		$total = $row2[0];
		$gross = $total * .88;
		$VAT = $total * .12;
	}

    $sql_totes = "SELECT SUM(quantity) as qty, purchaseNo from purchase_details where purchaseNo=0";
    $result_sum = $con->query($sql_totes) or die(mysqli_error($con));
    while($row55 = mysqli_fetch_array($result_sum)){
        $totally = $row55['qty'];
    }
	if (isset($_POST['edd']))
	{
		
        $supliar = $_POST['supplier'];
        $supliar = mysqli_real_escape_string($con, $_POST['supplier']);
        
		$sql_purchase = "INSERT INTO purchases VALUES ('', $supliar, NOW(), '', '', 'Pending')";
		$con->query($sql_purchase) or die(mysqli_error($con));
		
        
        $sql_max = "SELECT MAX(purchaseNo) AS max FROM purchase_details";
        $result_max = $con->query($sql_max) or die(mysqli_error($con));
        while($rowyerboat = mysqli_fetch_array($result_max))
        {
            $maxest = $rowyerboat['max'];
            $sql_epdate="UPDATE purchase_details SET purchaseNo = $maxest +1 WHERE purchaseNo=0";
                $con->query($sql_epdate) or die(mysqli_error($con));
        }
        header('location: index.php');
        

	}
?>

<form method="POST" class="form-horizontal" enctype="multipart/form-data">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Supplier</label>
			<div class="col-lg-8">
				<select name="supplier" class="form-control" required>
					<option value="">Select one...</option>
					<?php echo $list_sup; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Product</label>
			<div class="col-lg-8">
				<select name="product" class="form-control" required>
					<option value="">Select one...</option>
					<?php echo $list_pro; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Quantity</label>
			<div class="col-lg-8">
				<input name="quantity" type="number" min="1" max="10000"
					class="form-control" required />
			</div>
		</div>
	</div>
		<div class="col-lg-6">
			<table class="table table-hover">
				<thead>
					<th>Product</th>
					<th>Quantity</th>
					<th></th>
				</thead>
				<tbody>
					<?php echo $list_view; ?>
                   
				</tbody>
			</table>
            <hr>
            <span></span>
            Total items : <?php echo $totally; ?>
			
	</div>
    <div class="form-group">
		<div class="col-lg-offset-4 col-lg-8">
			<button name="add" type="submit" class="btn btn-success">
				Add <i class="fa fa-plus"></i>
			</button>
		</div>
	</div>
  <div class="form-group"> 
    <div class="col-sm-offset-2 col-sm-10 col-lg-offset-10 col-lg-2">
      <button type="submit" name="edd" class="btn btn-success">Submit <i class="fa fa-send"></i></button>
    </div>
  </div>
</form>
<?php
	include_once('../../includes/footer.php');
?>