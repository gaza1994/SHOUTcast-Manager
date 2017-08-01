<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: Manage Users
 * @website: http://scottishbordersdesign.co.uk/
*/
if (useraccess($_SESSION['username']) < "5") {
    echo "ACCESS DENIED - INCIDENT REPORTED";
    $event = " Attempted to access the restricted user management section.";
    addevent($_SESSION['username'], $event);
} else {
    $db = dbConnect();
    /////////////////////////////////////
    //
    // Handle the post if we are adding a new user
    //
    /////////////////////////////////////
    if (isset($_POST['newuser'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $email = $_POST['email'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $access = $_POST['access'];
        adduser($username, $password, $fname, $lname, $email, $access);
    }
    /////////////////////////////////////
    //
    // Handle the post if we are deleting a user
    //
    /////////////////////////////////////
    if (isset($_REQUEST['delete'])) {
        $id = $_REQUEST['id'];
        $username = $_REQUEST['username'];
        deluser($id, $username);
    }
    /////////////////////////////////////
    //
    // Login as User
    //
    /////////////////////////////////////
    if (isset($_REQUEST['loginas'])) {
        $id = $_REQUEST['id'];
        $username = $_REQUEST['username'];
        loginas($id, $username);
    }
    /////////////////////////////////////
    //
    // Handle the post if we are editing a user
    //
    /////////////////////////////////////
    if (isset($_POST['edituser'])) {
        $usrname = $_POST['username'];
        $password = $_POST['password'];
        if (!empty($password)) {
            $password = $_POST['password'];
        }
        $email = $_POST['email'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $id = $_POST['id'];
        $access = $_POST['access'];
        edituser($id, $usrname, $password, $fname, $lname, $email, $access);
    }
    /////////////////////////////////////
    //
    // Create our header table, we do this outside the query.
    //
    /////////////////////////////////////
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Registered Users</h3>
        <div class="box-tools">
            <div class="input-group">
                <input type="text" name="table_search" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search">
                <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tbody><tr>
                <th>ID</th>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>Action</th>
            </tr>
<?php /////////////////////////////////////
//
// Run our query and create alternating colors on the rows
//
/////////////////////////////////////
$i = "0";
$members = $db->get("members");

foreach ($members as $row) {
  $row = index_array($row);
  $bgcolor = ($i++ & 1) ? '#FFFFFF' : '#bcbcbc';
  echo "<tr>";
?>
        <td >
                <?echo $row[0];?>
                </td>
        <td>
                <?echo $row[1];?>
                </td>
        <td>
                <?echo $row[3];?>
                </td>
                <td>
                <?echo $row[4];?>
                </td>
        <td>
                <?echo $row[5];?>
                </td>
                <td>
                    <?php
    if ($row[1] == $_SESSION['username']) {
        echo "<a href=\"#\"><span class=\"badge bg-grey\">Login As</span></a> / ";
    } else { ?>
                <?echo "<a href=\"users.php?loginas=yes&id=$row[0]&username=$row[1]\"><span class=\"badge bg-orange\">Login As</span></a> / "; } ?>  
                <?echo "<a href=\"users.php?edit=yes&userid=$row[0]\"><span class=\"badge bg-light-blue\">Edit</span></a>";?>
         / <a href="users.php?delete=yes&id=<?echo $row[0];?>&username=<?echo $row[1];?>" onclick="javascript:return confirm('Are you sure you want to delete this user?')"><span class="badge bg-red">Delete</span></a>
        </td>
    </tr>
<?php }
?>
        </tbody></table>
    </div><!-- /.box-body -->
</div>
<?php if(isset($_REQUEST['edit'])) {
  $userid = $_REQUEST['userid'];
  $db->where("user_id", $userid);
  $members = $db->get("members");
  foreach ($members as $row) {
    $row = index_array($row);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Edit User</h3>
    </div><!-- /.box-header -->
    <!-- form start -->
    <form method="post" action="">
        <div class="box-body">
            <div class="form-group">
                <label for="">Username</label>
                <input type="text" class="form-control" id="" name="username" value="<?echo $row[1];?>" placeholder="<?echo $row[1];?>">
            </div>
            <div class="form-group" data-toggle="tooltip" title="Leave empty for no change">
                <label for="">Password</label>
                <input type="password" class="form-control" name="password" id="" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="">First Name</label>
                <input type="text" class="form-control" name="fname" id="" value="<?echo $row[3];?>" placeholder="<?echo $row[3];?>">
            </div>
            <div class="form-group">
                <label for="">Last Name</label>
                <input type="text" class="form-control" name="lname" id="" value="<?echo $row[4];?>" placeholder="<?echo $row[4];?>">
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" class="form-control" name="email" id="" value="<?echo $row[5];?>" placeholder="<?echo $row[5];?>">
            </div>
            <div class="form-group">
                <label>Access Level</label>
                <select class="form-control" name="access">
                    <option value="5" <?php if ($row[6] == "5") { echo "selected"; } ?>>Administrator</option>
                    <option value="4" <?php if ($row[6] == "4") { echo "selected"; } ?>>Technician</option>
                    <option value="3" <?php if ($row[6] == "3") { echo "selected"; } ?>>Helper</option>
                    <option value="2" <?php if ($row[6] == "2") { echo "selected"; } ?>>Read Only</option>
                    <option value="1" <?php if ($row[6] == "1") { echo "selected"; } ?>>User</option>
                    <option value="0" <?php if ($row[6] == "0") { echo "selected"; } ?>>Disabled</option>
                </select>
            </div>
        </div><!-- /.box-body -->
        <div class="box-footer">
            <input type="hidden" name="id" value="<?php echo $row['0'];?>">
            <input type="hidden" name="edituser" value="yes">
            <button type="submit" name="go" class="btn btn-primary">Update user</button>
        </div>
    </form>
</div>
<?php }
}else{
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Add New User</h3>
    </div><!-- /.box-header -->
    <!-- form start -->
    <form method="post" action="">
        <div class="box-body">
            <div class="form-group">
                <label for="">Username</label>
                <input type="text" class="form-control" id="" name="username" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="">Password</label>
                <input type="password" class="form-control" name="password" id="" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="">First Name</label>
                <input type="text" class="form-control" name="fname" id="" placeholder="First Name">
            </div>
            <div class="form-group">
                <label for="">Last Name</label>
                <input type="text" class="form-control" name="lname" id="" placeholder="Last Name">
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" class="form-control" name="email" id="" placeholder="Email">
            </div>
            <div class="form-group">
                <label>Access Level</label>
                <select class="form-control" name="access">
                    <option value="5">Administrator</option>
                    <option value="4">Technician</option>
                    <option value="3">Helper</option>
                    <option value="2">Read Only</option>
                    <option value="1">User</option>
                    <option value="0">Disabled</option>
                </select>
            </div>
        </div><!-- /.box-body -->
        <div class="box-footer">
            <input type="hidden" name="newuser" value="yes">
            <button type="submit" name="go" class="btn btn-primary">Add user</button>
        </div>
    </form>
</div>
<?php }
}
?>