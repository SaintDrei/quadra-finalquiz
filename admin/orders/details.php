<?php
    if (isset($_REQUEST['no']))
    {
        $orderNo = $_REQUEST['no'];
 
        $page_title = "Order #$orderNo Details";
        include_once('../../includes/header_admin.php');
 
        $sql_order = "SELECT od.DetailID, od.ProductID, p.Image,
            p.Name, c.Name AS Category, p.Price, od.Quantity,
            od.Amount FROM orderdetails od
            INNER JOIN products p ON od.productID = p.productID
            INNER JOIN categories c ON p.catID = c.CatID
            WHERE od.orderNo=$orderNo";
       
        $result_order = $con->query($sql_order) or die(mysqli_error($con));
 
        $list_order = "";
        while ($row = mysqli_fetch_array($result_order))
        {
            $did = $row['DetailID'];
            $pid = $row['ProductID'];
            $image = $row['Image'];
            $pname = $row['Name'];
            $cat = $row['Category'];
            $price = number_format($row['Price'], 2, '.', ',');
            $qty = $row['Quantity'];
            $amount = number_format($row['Amount'], 2, '.', ',');
 
            $list_order .=  "<tr>
                                <td><img src='../../images/products/$image' width='150' /></td>
                                <td><h3>$pname</h3>
                                    <small><em>$cat</em></small>
                                </td>
                                <td>P$price</td>
                                <td>$qty<td/>
                                <td>P$amount</td>
                            </tr>";
        }
 
        $sql_summary = "SELECT o.status, o.orderDate, o.paymentMethod,
            o.approveDate,
            (SELECT SUM(od.amount) FROM orderdetails od
            WHERE od.orderNo = o.orderNo) AS totalAmount
            FROM orders o
            WHERE o.orderNo=$orderNo";
        $result_summary = $con->query($sql_summary) or die(mysqli_error($con));
        while ($row2 = mysqli_fetch_array($result_summary))
        {
            $status = $row2['status'];
            $odate = $row2['orderDate'];
            $adate = $row2['approveDate'];
            $payment = $row2['paymentMethod'];
            $total = $row2['totalAmount'];
            $gross = $total * .88;
            $VAT = $total * .12;
        }
 
        $sql_user = "SELECT u.firstName, u.lastName, u.email,
            u.street, u.municipality, c.name AS cityName, u.landline,
            u.mobile FROM orderdetails od
            INNER JOIN users u ON od.userID = u.userID
            INNER JOIN cities c ON u.cityID = c.cityID
            WHERE od.orderNo=$orderNo";
 
        $result_user = $con->query($sql_user) or die(mysqli_error($con));
        while ($row3 = mysqli_fetch_array($result_user))
        {
            $firstName = $row3['firstName'];
            $lastName = $row3['lastName'];
            $emailAdd = $row3['email'];
            $street = $row3['street'];
            $municipality = $row3['municipality'];
            $cityName = $row3['cityName'];
            $landline = $row3['landline'];
            $mobile = $row3['mobile'];
        }
    }
    else
    {
        header('location: index.php');
    }

    if(isset($_POST['approve']))
    {
        $sql_approve = "UPDATE orders SET approveDate=NOW(), status='Approved' WHERE orderNo=$orderNo";
        $query_approve = $con->query($sql_approve) or die(mysqli_error($con));

        header('location: index.php');
    }
    else if (isset($_POST['deliver']))
    {
        $sql_items = "SELECT productID, quantity FROM orderdetails
                WHERE orderNo=$orderNo";
        $result_items = $con->query($sql_items) or die(mysqli_error($con));
        while ($row4 = mysqli_fetch_array($result_items))
        {
            $productID = $row4['productID'];
            $quantity = $row4['quantity'];

            updateInventory($con, $productID, $quantity);

            logMovement($con, $orderNo, 'OUT', $productID, $quantity);
        }

        $sql_orders = "UPDATE orders SET status='Completed'
            WHERE orderNo = $orderNo";
        $con->query($sql_orders) or die(mysqli_error($con));

        $sql_delivery = "UPDATE deliveries SET status='Completed',
            deliverDate = NOW()
            WHERE orderNo=$orderNo";

        $con->query($sql_delivery) or die(mysqli_error($con));

        header('location: index.php');
    }
   
?>
    <form class="form-horizontal" method="POST">
    <div class="col-lg-8">
       
            <table class="table table-hover">
                <thead>
                    <th colspan="2">Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                </thead>
                <tbody>
                    <?php echo $list_order; ?>
                </tbody>
            </table>
            <hr/>
            <h2>Billing and Delivery Details</h2>
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="control-label col-lg-4">First Name</label>
                    <div class="col-lg-8">
                        <input name="fn" type="text" class="form-control"
                        value="<?php echo $firstName ?>" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-4">Last Name</label>
                    <div class="col-lg-8">
                        <input name="ln" type="text" class="form-control"
                        value="<?php echo $lastName ?>" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-4">Email Address</label>
                    <div class="col-lg-8">
                        <input name="email" type="email" class="form-control"
                        value="<?php echo $emailAdd ?>" disabled />
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="control-label col-lg-4">Street</label>
                    <div class="col-lg-8">
                        <input name="st" type="text" class="form-control"
                        value="<?php echo $street ?>" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-4">Municipality</label>
                    <div class="col-lg-8">
                        <input name="muni" type="text" class="form-control"
                        value="<?php echo $municipality ?>" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-4">City</label>
                    <div class="col-lg-8">
                        <input name="muni" type="text" class="form-control"
                        value="<?php echo $cityName ?>" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-4">Landline</label>
                    <div class="col-lg-8">
                        <input name="phone" type="text" class="form-control"
                        value="<?php echo $landline ?>" disabled />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-4">Mobile</label>
                    <div class="col-lg-8">
                        <input name="mobile" type="text" class="form-control"
                        value="<?php echo $mobile ?>" disabled />
                    </div>
                </div>
            </div>
    </div>
    <div class="col-lg-4">
        <div class="well">
            <h3 class="text-center">Order Summary</h3>
            <table class="table table-hover">
                <tr>
                    <td>Status</td>
                    <td align='right'><?php echo $status; ?></td>
                </tr>
                <tr>
                    <td>Order Date</td>
                    <td align='right'><?php echo $odate; ?></td>
                </tr>
                <tr>
                    <td>Payment Method</td>
                    <td align='right'><?php echo $payment; ?></td>
                </tr>
                <tr>
                    <td>Approval Date</td>
                    <td align='right'><?php echo $adate; ?></td>
                </tr>
                <tr>
                    <td>Gross Amount</td>
                    <td align='right'>P<?php echo number_format($gross, 2, '.', ','); ?></td>
                </tr>
                <tr>
                    <td>VAT</td>
                    <td align='right'><?php echo number_format($VAT, 2, '.', ','); ?></td>
                </tr>
                <tr>
                    <td><b>Total Amount</b></td>
                    <td align='right'><b>P<?php echo number_format($total, 2, '.', ','); ?></b></td>
                </tr>
            </table>
            <hr/>
            <button name='approve' class='btn btn-success btn-block btn-lg'
                onclick='return confirm("Approve order?");'
                <?php echo$status=='Approved' ? 'disabled' : '' ; ?>>
                Approve
            </button>
            <button name='deliver' class='btn btn-success btn-block btn-lg'
                onclick='return confirm("Deliver order?");'
                <?php echo$status=='Pending' ? 'disabled' : '' ; ?>>
                Deliver Items
            </button>
        </div>
    </div>
    </form>
    <div class='row'></div>
 
<?php
    include_once('../../includes/footer.php');
?> 