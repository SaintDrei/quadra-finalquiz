<?php 
	$page_title = "Books Title";
    include_once('includes/header.php');

    if (isset($_POST['search']))
    {
    	$keyword = mysqli_real_escape_string($con, $_POST['keyword']);
    	header('location: book.php?s=' . $keyword);
    }
    if (isset($_REQUEST['p']))
    {
    	if (ctype_digit($_REQUEST['p']))
		{
			$pubid = $_REQUEST['p'];
			# displays list of products based from selected category
		    $sql_books = "SELECT t.titleID, p.pubID AS pubName, p.name,
		    	p.name, p.price, p.date, p.notes
		    	FROM title t 
		    	INNER JOIN publishers t ON p.pubID = c.pubID
		    	WHERE p.status!='Archived' AND p.pubID=$pubid";
		}
		else
		{
			header('location: book.php');
		}
    }
    else if (isset($_REQUEST['p']))
    {
    	$filter = "'%" . $_REQUEST['s'] . "%'";
    	# displays list of products based from keyword
	    $sql_books = "SELECT t.titleID, p.pubID AS pubName, p.name,
		    	p.name, p.price, p.date, p.notes
		    	FROM title t 
	    	INNER JOIN title t ON p.pubID = c.pubID
	    	WHERE p.status!='Archived' AND
	    	(c.name LIKE $filter OR
	    	p.name LIKE $filter OR
	    	p.notes LIKE $filter)";
    }
    else if (isset($_REQUEST['sort']))
    {
    	$sort = $_REQUEST['sort'];
    	$column = $sort == "name" ? "p.name" : 
    		$sort == "price" ? "p.price" : "p.productID";

    	# displays list of products sorted by name (A-Z) or price (min-max)
	    $sql_books = "SELECT p.productID, c.name AS catName, p.name,
	    	p.description, p.price, p.image, p.status, 
	    	p.addedOn, p.lastModified 
	    	FROM products p 
	    	INNER JOIN categories c ON p.catID = c.catID
	    	WHERE p.status!='Archived'
	    	ORDER BY $column";
    }
    else
    {
		# displays list of products
	    $sql_books = "SELECT p.productID, c.name AS catName, p.name,
	    	p.description, p.price, p.image, p.status, 
	    	p.addedOn, p.lastModified 
	    	FROM products p 
	    	INNER JOIN categories c ON p.catID = c.catID
	    	WHERE p.status!='Archived'";
    }
    

    $result_books = $con->query($sql_books);

    # displays list of categories
    $sql_pub = "SELECT c.pubID, c.name AS pubName,
    	(SELECT COUNT(pubID) FROM publishers
    	WHERE pubID = c.catID AND status!='Archived') AS totalCount
    	FROM titles t
    	ORDER BY c.name";

    $result_pub = $con->query($sql_pub);

?>
<form method="POST" class="form-horizontal">
	<div class="col-lg-3">
		<div class="input-group">
			<input name="keyword" class="form-control" placeholder="Keyword..." />
			<span class="input-group-btn">
				<button name="search" type="submit" class="btn">
					<i class="fa fa-search"></i>
				</button>
			</span>
		</div>
		<br/>
		<div class="list-group">
			<a href='products.php' class='list-group-item'>
				<span class='badge'><?php echo countData('products'); ?></span>
				All publishers
			</a>
			<?php
				while ($row = mysqli_fetch_array($result_pub))
				{
					$pid = $row['pubID'];
					$pubName = $row['pubName'];
					$total = $row['totalCount'];

					echo "
						<a href='addbook.php?c=$cid' class='list-group-item'>
							<span class='badge'>$total</span>
							$pubName
						</a>
					";
				}
			?>
		</div>
	</div>
	<div class="col-lg-9">
		<?php
			if (mysqli_num_rows($result_publishers) > 0)
			{
				while ($row = mysqli_fetch_array($result_publishers))
				{
					$pid = $row['productID'];
					$image = $row['image'];
					$name = $row['name'];
					$cat = $row['catName'];
					$price = $row['price'];

					echo "
						<a href='details.php?id=$pid' class='product'>
							<div class='col-lg-4'>
								<div class='thumbnail'>
									<div class='ratio' style=\"background-image: url('images/products/$image')\">
		                           	</div>
									<div class='caption'>
										<h3>$name</h3>
										<small>$cat</small><br/>
										P$price
										<hr/>
										<button data-pid='$pid' name='addtocart' class='btn btn-success btn-block cart'>
											<i class='fa fa-plus'></i> Add to Cart
										</button>
									</div>
								</div>
							</div>
						</a>
					";
				}
			}
			else
			{
				echo "
					<div class='col-lg-12'>
						<div class='thumbnail'>
							<br/>
							<br/>
							<h2 class='text-center'>No records found.</h2>
							<br/>
							<br/>
						</div>
					</div>
				";
			}
		?>
	</div>
</form>
<script>
	$('.cart').on('click', function(event){
		var pid = $(this).data('pid');
		var myURL = 'addtocart.php?p=' + pid + '&qty=1';

		$.ajax({
			url: myURL
		});
	});
</script>
<?php
	include_once('includes/footer.php');
?>