<?php 
	# checks if record is selected
	if (isset($_REQUEST['id']))
	{
		# checks if selected record is an ID value
		if (ctype_digit($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];

			$page_title = "Supplier #$id Details";
		    include_once('../../includes/header_admin.php');

		    #validateAccess();

		    # display existing record
			$sql_data = "SELECT supplierID, status, companyName, address, phone, mobile FROM suppliers WHERE supplierID=$id";
			$result_data = $con->query($sql_data);

			# checks if record is not existing
			if (mysqli_num_rows($result_data) == 0)
			{
				header('location: index.php');
			}

			while ($row = mysqli_fetch_array($result_data))
			{
				$status = $row['status'];
				$companyName = $row['companyName'];
				$address = $row['address'];
				$phone = $row['phone'];
				$mobile = $row['mobile'];
			}

			# updates existing record
			if (isset($_POST['update']))
			{
				$status = mysqli_real_escape_string($con, $_POST['status']);
				$companyName = mysqli_real_escape_string($con, $_POST['companyName']);
				$address = mysqli_real_escape_string($con, $_POST['address']);
				$phone = mysqli_real_escape_string($con, $_POST['phone']);
				$mobile = mysqli_real_escape_string($con, $_POST['mobile']);

				$sql_update = "UPDATE suppliers SET companyName=$companyName, address='$address', 
					phone='$phone', mobile='$mobile', status='$status', lastModified=NOW() WHERE supplierID=$id";
				
				$result = $con->query($sql_update) or die(mysqli_error($con));
				header('location: index.php');
			}
		}
		else
		{
			header('location: index.php');
		}
	}
	else
	{
		header('location: index.php');
	}
?>
<form method="POST" class="form-horizontal">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Status</label>
			<div class="col-lg-8">
				<select name="status" class="form-control" required>
					<option <?php if ($status == "Active") echo 'selected' ; ?>>Active</option>
					<option <?php if ($status == "Inactive") echo 'selected' ; ?>>Inactive</option>
					<option <?php if ($status == "Blocked") echo 'selected' ; ?>>Blocked</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Company Name</label>
			<div class="col-lg-8">
				<input name="companyName" type="text" class="form-control" value="<?php echo $companyName; ?>" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Address</label>
			<div class="col-lg-8">
				<input name="address" type="text" class="form-control" value="<?php echo $address; ?>" required />
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Phone</label>
			<div class="col-lg-8">
				<input name="phone" type="text" class="form-control" value="<?php echo $phone; ?>" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Mobile</label>
			<div class="col-lg-8">
				<input name="mobile" type="text" class="form-control" value="<?php echo $mobile; ?>" required />
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-offset-4 col-lg-8">
				<button name="update" type="submit" class="btn btn-success">
					Update
				</button>
				<a href="index.php" class="btn btn-default">
					Back to View
				</a>
			</div>
		</div>
	</div>
</form>

<?php
	include_once('../../includes/footer.php');