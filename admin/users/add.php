<?php 
	$page_title = "Add a User";
    include_once('../../includes/header_admin.php');

    #validateAccess();
    
    # displays list of user types
    $sql_types = "SELECT typeID, userType FROM types ORDER BY userType";
    $result_types = $con->query($sql_types);

    $list_types = "";
	while ($row = mysqli_fetch_array($result_types))
	{
		$typeID = $row['typeID'];
		$userType = $row['userType'];
		$list_types .= "<option value='$typeID'>$userType</option>";
	}

	# displays list of cities
    $sql_cities = "SELECT cityID, name FROM cities ORDER BY name";
    $result_cities = $con->query($sql_cities);

    $list_cities = "";
	while ($row = mysqli_fetch_array($result_cities))
	{
		$cityID = $row['cityID'];
		$name = $row['name'];
		$list_cities .= "<option value='$cityID'>$name</option>";
	}

	# add a user record
	if (isset($_POST['add']))
	{
		$typeID = mysqli_real_escape_string($con, $_POST['type']);
		$fn = mysqli_real_escape_string($con, $_POST['fn']);
		$ln = mysqli_real_escape_string($con, $_POST['ln']);
		$email = mysqli_real_escape_string($con, $_POST['email']);
		$pw = hash('sha256', mysqli_real_escape_string($con, $_POST['pw']));
		$st = mysqli_real_escape_string($con, $_POST['st']);
		$muni = mysqli_real_escape_string($con, $_POST['muni']);
		$cityID = mysqli_real_escape_string($con, $_POST['cities']);
		$phone = mysqli_real_escape_string($con, $_POST['phone']);
		$mobile = mysqli_real_escape_string($con, $_POST['mobile']);

		$sql_add = "INSERT INTO users VALUES ('', $typeID, '$fn', '$ln',
			'$email', '$pw', '$st', '$muni', $cityID, '$phone', '$mobile',
			'Active', NOW(), NULL)";
		$con->query($sql_add) or die(mysqli_error($con));
		header('location: index.php');
	}

?>
<form method="POST" class="form-horizontal">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">User Type</label>
			<div class="col-lg-8">
				<select name="type" class="form-control" required>
					<option value="">Select one...</option>
					<?php echo $list_types; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">First Name</label>
			<div class="col-lg-8">
				<input name="fn" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Last Name</label>
			<div class="col-lg-8">
				<input name="ln" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Email Address</label>
			<div class="col-lg-8">
				<input name="email" type="email" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Password</label>
			<div class="col-lg-8">
				<input name="pw" type="password" class="form-control" required />
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Street</label>
			<div class="col-lg-8">
				<input name="st" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Municipality</label>
			<div class="col-lg-8">
				<input name="muni" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">City</label>
			<div class="col-lg-8">
				<select name="cities" class="form-control" required>
					<option value="">Select one...</option>
					<?php echo $list_cities; ?>
				</select>
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