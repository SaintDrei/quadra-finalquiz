<?php 
	$page_title = "Add a Product";
    include_once('../../includes/header_admin.php');

	validateAccess();

    # displays list of categories
    $sql_cat = "SELECT catID, name FROM categories ORDER BY name";
    $result_cat = $con->query($sql_cat);

    $list_cat = "";
	while ($row = mysqli_fetch_array($result_cat))
	{
		$catID = $row['catID'];
		$name = $row['name'];
		$list_cat .= "<option value='$catID'>$name</option>";
	}

	# add a product record
	if (isset($_POST['add']))
	{
		$catID = mysqli_real_escape_string($con, $_POST['cat']);
		$name = mysqli_real_escape_string($con, $_POST['name']);
		$desc = mysqli_real_escape_string($con, $_POST['desc']);
		$price = mysqli_real_escape_string($con, $_POST['price']);
		
		$upload = "../../images/products/"; # location where to upload the image
		$image = $_FILES["image"]["name"]; # gets the file from file upload
		$newImage = date('YmdHis-') . basename($image); # eg. 20170322051234-sample.jpg
		$file = $upload . $newImage;

		move_uploaded_file($_FILES["image"]["tmp_name"], $file);

		$crit = mysqli_real_escape_string($con, $_POST['crit']);

		$sql_add = "INSERT INTO products VALUES ('', $catID, '$name', '$desc',
			'$price', '$newImage', 0,  $crit,
			'Active', NOW(), NULL)";
		$con->query($sql_add) or die(mysqli_error($con));
		header('location: index.php');
	}

?>
<form method="POST" class="form-horizontal" enctype="multipart/form-data">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Category</label>
			<div class="col-lg-8">
				<select name="cat" class="form-control" required>
					<option value="">Select one...</option>
					<?php echo $list_cat; ?>
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
			<label class="control-label col-lg-4">Description</label>
			<div class="col-lg-8">
				<textarea name="desc" id="desc" rows="10" cols="80">

	            </textarea>
	            <script>
	                CKEDITOR.replace( 'desc' );
	            </script>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Price</label>
			<div class="col-lg-8">
				<input name="price" type="number" min="1.00" max="10000.00" step="0.01"
					class="form-control" required />
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Image</label>
			<div class="col-lg-8">
				<div class="fileinput fileinput-new" data-provides="fileinput">
			  		<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
			    		<img src='<?php echo app_path; ?>images/placeholder.png' alt="...">
			  		</div>
			  		<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
				  	<div>
				    	<span class="btn btn-default btn-file">
				    		<span class="fileinput-new">Select image</span>
				    		<span class="fileinput-exists">Change</span>
				    		<input type="file" name="image" required>
				    	</span>
				    	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
				  	</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Critical Level</label>
			<div class="col-lg-8">
				<input name="crit" type="number" min="1" max="100" class="form-control" required />
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
?>