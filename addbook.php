<?php 
	$page_title = "Add a Book Title";
    include_once('includes/header.php');

	validateAccess();

    # displays list of categories
    $sql_pub = "SELECT pubid, pubName FROM publishers ORDER BY pubName";
    $sql_author = "SELECT authorid, name FROM authors ORDER BY name";
    $result_pub = $con->query($sql_pub);
    $result_author = $con->query($sql_author);

    $list_pub = "";
    $list_author = "";
	while ($row = mysqli_fetch_array($result_pub))
	{
		$pubID = $row['pubID'];
		$pubName = $row['name'];
		$list_pub .= "<option value='$pubID'>$pubName</option>";
	}
	# add a product record
	if (isset($_POST['add']))
	{
		$pubID = mysqli_real_escape_string($con, $_POST['pub']);
		$authorID = mysqli_real_escape_string($con, $_POST['author']);
		$name = mysqli_real_escape_string($con, $_POST['name']);
		$price = mysqli_real_escape_string($con, $_POST['price']);
		$date = mysqli_real_escape_string($con, $_POST['date']);
		$note = mysqli_real_escape_string($con, $_POST['note']);
		

		move_uploaded_file($_FILES["image"]["tmp_name"], $file);

		$crit = mysqli_real_escape_string($con, $_POST['crit']);

		$sql_add = "INSERT INTO products VALUES ('', $pubID, '$authorID', '$name',
			'$price', '$date', $Notes,
			'Active', NOW(), NULL)";
		$con->query($sql_add) or die(mysqli_error($con));
		header('location: book.php');
	}

	while ($row = mysqli_fetch_array($result_author))
	{
		$authorID = $row['authorID'];
		$name = $row['name'];
		$list_author .= "<option value='$authorID'>$name</option>";
	}
	if (isset($_POST['add']))
	{
		$pubID = mysqli_real_escape_string($con, $_POST['pub']);
		$authorID = mysqli_real_escape_string($con, $_POST['author']);
		$name = mysqli_real_escape_string($con, $_POST['name']);
		$price = mysqli_real_escape_string($con, $_POST['price']);
		$date = mysqli_real_escape_string($con, $_POST['date']);
		$note = mysqli_real_escape_string($con, $_POST['note']);	
		

		move_uploaded_file($_FILES["image"]["tmp_name"], $file);

		$crit = mysqli_real_escape_string($con, $_POST['crit']);

	$sql_add = "INSERT INTO products VALUES ('', $pubID, '$authorID', '$name',
			'$price', '$date', $notes,
			'Active', NOW(), NULL)";
		$con->query($sql_add) or die(mysqli_error($con));
		header('location: book.php');
	}

?>
<form method="POST" class="form-horizontal" enctype="multipart/form-data">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Publisher</label>
			<div class="col-lg-8">
				<select name="pub" class="form-control" required>
					<option value="">Select one...</option>
					<?php echo $list_pub; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Author</label>
			<div class="col-lg-8">
				<select name="author" class="form-control" required>
					<option value="">Select one...</option>
					<?php echo $list_author; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Name</label>
			<div class="col-lg-8">
				<input name="name" type="text" class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Price</label>
			<div class="col-lg-8">
				<input name="price" type="number" min="1.00" max="10000.00" step="0.01"
					class="form-control" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Date</label>
			<div class="col-lg-8">
				<input name="name" type="text" class="form-control" value="mm/dd/yyyy" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Notes</label>
			<div class="col-lg-8">
				<input name="name" type="text" class="form-control" required />
			</div>
		</div>


				 <div class="form-group">
                        <div class="col-lg-offset-4 col-lg-8">
                            <button name="register" class="btn btn-outline-success btn-lg pull-right">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
			</div>
		</div>
	</div>
</form>

<?php
	include_once('includes/header.php');
?>