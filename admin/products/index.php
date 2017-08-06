<?php 
	$page_title = "View Products";
    include_once('../../includes/header_admin.php');

    validateAccess();
    
    # displays list of products
    $sql_products = "SELECT p.productID, c.name AS catName, p.name,
    	p.description, p.price, p.image, p.status, 
    	p.addedOn, p.lastModified 
    	FROM products p 
    	INNER JOIN categories c ON p.catID = c.catID
    	WHERE p.status!='Archived'";

    $result_products = $con->query($sql_products);

?>
<form method="POST" class="form-horizontal">
	<div class="col-lg-12">
		<table id="tblUsers" class="table table-hover">
			<thead>
				<th>#</th>
				<th>Category</th>
				<th>Name</th>
				<th>Image</th>
				<th>Price</th>
				<th>Status</th>
				<th>Added On</th>
				<th>Last Modified</th>
				<th></th>
			</thead>
			<tbody>
				<?php
					while ($row = mysqli_fetch_array($result_products))
					{
						$id = $row['productID'];
						$cat = $row['catName'];
						$name = $row['name'];
						$image = $row['image'];
						$price = $row['price'];
						$status = $row['status'];
						$added = $row['addedOn'];
						$modified = $row['lastModified'];

						echo "
							<tr>
								<td>$id</td>
								<td>$cat</td>
								<td>$name</td>
								<td>
									<img src='../../images/products/$image' width='200' />
								</td>
								<td>$price</td>
								<td>$status</td>
								<td>$added</td>
								<td>$modified</td>
								<td>
									<a href='details.php?id=$id' class='btn btn-xs btn-info'>
										<i class='fa fa-edit'></i>
									</a>
									<a href='delete.php?id=$id' class='btn btn-xs btn-danger' 
										onclick='return confirm(\"Archived record?\");''>
										<i class='fa fa-trash'></i>
									</a>
								</td>
							</tr>
						";
					}

				?>
			</tbody>
		</table>
		<script>
			$(document).ready(function(){
			    $('#tblUsers').DataTable();
			});
		</script>
	</div>
</form>

<?php
	include_once('../../includes/footer.php');
?>