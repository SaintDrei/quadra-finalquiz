<?php
    $page_title = "Administrator Login";
    include_once('../includes/header_admin.php');
 
    if (isset($_POST['login']))
    {
        session_start();
 
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $pw = hash('sha256', mysqli_real_escape_string($con, $_POST['pw']));
 
        $sql_login = "SELECT userID, typeID FROM users
           WHERE email='$email' AND password='$pw' AND status!='Archived'";
        $result_login = $con->query($sql_login) or die(mysqli_error($con));
 
        if (mysqli_num_rows($result_login) > 0)
        {
            while ($row = mysqli_fetch_array($result_login))
            {
                $_SESSION['userid'] = $row['userID'];
                $_SESSION['typeid'] = $row['typeID'];
            }
            header('location: index.php');
        }
 
    }
 
?>
<form method="POST" class="form-horizontal">
    <div class="col-lg-6">
        <?php
            if (isset($_POST['login']))
            {
                if (mysqli_num_rows($result_login) == 0)
                {
                    echo "
                       <div class='alert alert-danger'>
                           Incorrect email or password.
                       </div>
                   ";
                }
 
            }
        ?>
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
        <div class="form-group">
            <div class="col-lg-offset-4 col-lg-8">
                <button name="login" type="submit" class="btn btn-success">
                    Login
                </button>
            </div>
        </div>
    </div>
</form>
<?php
    include_once('../includes/footer.php');
?>