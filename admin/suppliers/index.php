<?php 
	$page_title = "View Suppliers";
    include_once('../../includes/header_admin.php');

    validateAccess();

    # displays list of suppliers
    $sql_suppliers = "SELECT supplierID, companyName, address, status, addedOn, lastModified FROM suppliers";
    $result_suppliers = $con->query($sql_suppliers);

?>
<form method="POST" class="form-horizontal">
	<div class="col-lg-12">
		<table id="tblSuppliers" class="table table-hover">
			<thead>
				<th>#</th>
				<th>CompanyName</th>
				<th>Address</th>
				<th>Status</th>
				<th>Added On</th>
				<th>Last Modified</th>
				<th></th>
			</thead>
			<tbody>
				<?php
					while ($row = mysqli_fetch_array($result_suppliers))
					{
						$id = $row['supplierID'];
						$companyName = $row['companyName'];
						$address = $row['address'];
						$status = $row['status'];
						$added = $row['addedOn'];
						$modified = $row['lastModified'];

						echo "
							<tr>
								<td>$id</td>
								<td>$companyName</td>
								<td>$address</td>
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
			    $('#tblSuppliers').DataTable();
			});
		</script>
	</div>
</form>

<?php
	include_once('../../includes/footer.php');