<?php 
	# checks if record is selected
	if (isset($_REQUEST['id']))
	{
		# checks if selected record is an ID value
		if (ctype_digit($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];

			$page_title = "Product #$id Details";
		    include_once('../../includes/header_admin.php');

	    	validateAccess();

		    # display existing record
			$sql_data = "SELECT productID, status, catID, name, description,
				price, image, critical
				FROM products
				WHERE productID=$id";
			$result_data = $con->query($sql_data);

			# checks if record is not existing
			if (mysqli_num_rows($result_data) == 0)
			{
				header('location: index.php');
			}

			while ($row = mysqli_fetch_array($result_data))
			{
				$status = $row['status'];
				$cat = $row['catID'];
				$pname = $row['name'];
				$desc = $row['description'];
				$price = $row['price'];
				$image_existing = $row['image'];
				$crit = $row['critical'];
			}

			# displays list of categories
		    $sql_cat = "SELECT catID, name FROM categories ORDER BY name";
		    $result_cat = $con->query($sql_cat);

		    $list_cat = "";
			while ($row = mysqli_fetch_array($result_cat))
			{
				$catID = $row['catID'];
				$name = $row['name'];
				$selected = $catID == $cat ? "selected" : "";
				$list_cat .= "<option value='$catID' $selected>$name</option>";
			}

			# updates existing record
			if (isset($_POST['update']))
			{
				$status = mysqli_real_escape_string($con, $_POST['status']);
				$catID = mysqli_real_escape_string($con, $_POST['cat']);
				$name = mysqli_real_escape_string($con, $_POST['name']);
				$desc = mysqli_real_escape_string($con, $_POST['desc']);
				$price = mysqli_real_escape_string($con, $_POST['price']);
				$crit = mysqli_real_escape_string($con, $_POST['crit']);

				$upload = "../../images/products/"; # location where to upload the image
				$image = $_FILES["image"]["name"]; # gets the file from file upload
				$newImage = date('YmdHis-') . basename($image); # eg. 20170322051234-sample.jpg
				$file = $upload . $newImage;

				if (empty($image))
				{
					$sql_update = "UPDATE products SET status='$status',
						catID=$catID, name='$name', description='$desc', price=$price,
						critical=$crit, lastModified=NOW()
						WHERE productID=$id";
				}
				else
				{
					move_uploaded_file($_FILES["image"]["tmp_name"], $file);
					unlink($upload . $image_existing);

					$sql_update = "UPDATE products SET status='$status',
						catID=$catID, name='$name', description='$desc', price=$price,
						image='$newImage', critical=$crit, lastModified=NOW()
						WHERE productID=$id";

				}

				$con->query($sql_update) or die(mysqli_error($con));
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
<form method="POST" class="form-horizontal" enctype="multipart/form-data">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Status</label>
			<div class="col-lg-8">
				<select name="status" class="form-control" required>
					<option <?php if ($status == "Active") echo 'selected' ; ?>>Active</option>
					<option <?php if ($status == "Out of Stock") echo 'selected' ; ?>>Out of Stock</option>
				</select>
			</div>
		</div>
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
				<input name="name" type="text" class="form-control" value="<?php echo $pname; ?>" required />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Description</label>
			<div class="col-lg-8">
				<textarea name="desc" id="desc" rows="10" cols="80">
					<?php echo $desc; ?>
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
					class="form-control" value="<?php echo $price; ?>" required />
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="form-group">
			<label class="control-label col-lg-4">Image</label>
			<div class="col-lg-8">
				<div class="fileinput fileinput-new" data-provides="fileinput">
			  		<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
			    		<img src='<?php echo app_path . 'images/products/' . $image_existing ?>' alt="<?php echo $name ?>">
			  		</div>
			  		<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
				  	<div>
				    	<span class="btn btn-default btn-file">
				    		<span class="fileinput-new">Select image</span>
				    		<span class="fileinput-exists">Change</span>
				    		<input type="file" name="image">
				    	</span>
				    	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
				  	</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">Critical Level</label>
			<div class="col-lg-8">
				<input name="crit" type="number" min="1" max="100" class="form-control" value="<?php echo $crit; ?>" required />
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
?>