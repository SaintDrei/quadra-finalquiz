<?php 
	$page_title = "Add a Supplier";
    include_once('../../includes/header_admin.php');

    #validateAccess();
	
	# add a supplier record
	if (isset($_POST['add']))
	{
		$companyName = mysqli_real_escape_string($con, $_POST['companyName']);
		$address = mysqli_real_escape_string($con, $_POST['address']);
		$phone = mysqli_real_escape_string($con, $_POST['phone']);
		$mobile = mysqli_real_escape_string($con, $_POST['mobile']);

		$sql_add = "INSERT INTO suppliers VALUES ('', '$companyName', '$address', '$phone', '$mobile', 'Active', NOW(), NULL)";
		$con->query($sql_add) or die(mysqli_error($con));
		header('location: index.php');
	}

?>
<form method="POST" class="form-horizontal">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Company Name</label>
			<div class="col-lg-8">
				<input name="companyName" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Address</label>
			<div class="col-lg-8">
				<input name="address" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Landline</label>
			<div class="col-lg-8">
				<input name="phone" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Mobile</label>
			<div class="col-lg-8">
				<input name="mobile" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-offset-4 col-lg-8">
				<button name="add" type="submit" class="btn btn-success">
					Add
				</button>
			</div>
		</div>
	</div>
</form>

<?php
	include_once('../../includes/footer.php');