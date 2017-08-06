<?php 
	$page_title = "View Users";
    include_once('../../includes/header_admin.php');

    #validateAccess();

    # displays list of users
    $sql_users = "SELECT u.userID, t.userType, u.firstName, u.lastName,
		u.email, u.status, u.addedOn, u.lastModified
		FROM users u
		INNER JOIN types t ON u.typeID = t.typeID
		WHERE u.Status!='Archived'";
    $result_users = $con->query($sql_users);

?>
<form method="POST" class="form-horizontal">
	<div class="col-lg-12">
		<table id="tblUsers" class="table table-hover">
			<thead>
				<th>#</th>
				<th>User Type</th>
				<th>Name</th>
				<th>Email</th>
				<th>Status</th>
				<th>Added On</th>
				<th>Last Modified</th>
				<th></th>
			</thead>
			<tbody>
				<?php
					while ($row = mysqli_fetch_array($result_users))
					{
						$id = $row['userID'];
						$type = $row['userType'];
						$fn = $row['firstName'];
						$ln = $row['lastName'];
						$email = $row['email'];
						$status = $row['status'];
						$added = $row['addedOn'];
						$modified = $row['lastModified'];

						echo "
							<tr>
								<td>$id</td>
								<td>$type</td>
								<td>$ln, $fn</td>
								<td>$email</td>
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