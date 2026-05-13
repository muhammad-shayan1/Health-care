<?php
/**
 * cities.php — Manage Cities Page
 * Healthcare Admin Panel — UI Only
 */
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit();
}
include("..//connect.php");
//delete city row
if (isset($_POST["btn-delete-row"])) {
    $get_city_id = $_POST['city_id'];
    $delete_city = "delete  from cities where city_id='$get_city_id'";
    mysqli_query($connect, $delete_city);
    header("location:cities.php");
} else {
    echo "";
}

//edit city row 
if (isset($_POST['btn-edit-row'])) {
    $get_city_id = $_POST['city_id'];
    $update_city_name = $_POST['update_city_name'];
    $update_city_Status = $_POST['update_city_status'];
    $update_city = "UPDATE cities
SET city_name = '$update_city_name',
    city_status = '$update_city_Status'
WHERE city_id = '$get_city_id'";
    $update_query = mysqli_query($connect, $update_city);
        header("location:cities.php");
} else {
    echo '';
}
$pageTitle = 'Manage Cities';
include('includes/header.php');
include('includes/sidebar.php');
?>
<?php
// adding city
       $select_doctor = "SELECT city_name FROM cities";
    $select_check = mysqli_query($connect, $select_doctor);
    $city_name_check = [];
    while ($row_checking = mysqli_fetch_assoc($select_check)) {
        $city_name_check[] = $row_checking['city_name'];
    }
if (isset($_POST['save_city_btn'])) {
    $city_name = $_POST["city_name"];
    $city_status = $_POST["select_status"];
    if ($city_name === '') {
        $city_table_message = "First Select City.";
    } elseif (in_array($city_name, $city_name_check)) {
            $city_table_message = "city is already there";
        }
     elseif ($city_status === '') {
        $city_table_message = "Select City Status.";
    } else {
        $insert_city = "insert into cities (city_name,city_status) values('$city_name','$city_status')";
        $insert_city_query = mysqli_query($connect, $insert_city);
        $city_table_message = "City Added Successfully!";
    }
} else {
    echo '';

}



?>
<div class="page-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h4><i class="bi bi-geo-alt-fill me-2 text-primary"></i>Manage Cities</h4>
            <ul class="breadcrumb-custom">
                <li><a href="dashboard.php">Home</a></li>
                <li>Manage Cities</li>
            </ul>
        </div>
    </div>


    <div class="form-wrapper">
        <form method="POST" action="#" class="city-form">
            <h1>Add New City</h1>
            <input type="text" class="form-control" placeholder="add new city" name="city_name" > 

            <select name="select_status" class="city-select">
                <option value="" disabled>select city</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit" name="save_city_btn" class="city-btn">
                <i class="bi bi-check-circle-fill"></i> Save City
            </button>

            <?php if (!empty($city_table_message)) {
                ?>
                <div class="city-message">
                    <h5><?= $city_table_message ?></h5>
                </div>
                <?php
            }
            ?>
        </form>
    </div>


    <!-- Cities Table -->
    <div class="section-card page-fade-in stagger-2">
        <div class="section-card-header">
            <h5><i class="bi bi-table"></i> Cities List
                <span class="info-chip ms-2">
                    <?php
              $result2 = $connect->query("SELECT COUNT(*) AS total FROM cities");
              $row2 = $result2->fetch_assoc();
              $totalcities = $row2['total'];
              echo $totalcities;
              ?>
                </span>
            </h5>
        </div>
        <div class="section-card-body table-responsive-custom">
            <table class="admin-table table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>City Name</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $select_city = "select * from cities";
                    $select_city_query = mysqli_query($connect, $select_city);
                    if (mysqli_num_rows($select_city_query) > 0) {
                        while ($city_table_row = mysqli_fetch_assoc($select_city_query)) {
                            $city_id = $city_table_row["city_id"];
                            $city_name = $city_table_row["city_name"];
                            $city_status = $city_table_row["city_status"];
                            ?>
                            <tr>
                                
                                    <form method="POST" action="">
                                <td class="fw-600 text-primary-custom">#<?= $city_id ?></td>
                                
                                        <input type="hidden" name="city_id" value="<?= $city_id ?>">
                                <td>
                                        <div class="user-cell">
                                            <div class="user-avatar av1"
                                                style="border-radius:9px;background:linear-gradient(135deg,#0d6efd,#6610f2);">
                                                <i class="bi bi-geo-alt-fill" style="font-size:14px;"></i>
                                            </div>
                                            <span class="user-name"><input class="form-control" name="update_city_name"
                                                    type="text" value="<?= $city_name ?>"></span>
                                        </div>
                                </td>
                                <td><span class=""><input class="form-control" name="update_city_status" type="text"
                                            value="<?= $city_status ?>"></span>
                                
                                </td>
                                <td>
                                        <button type="submit" class="btn-action alert alert-warning" name="btn-edit-row">
                                            <i class="bi bi-pencil-fill"></i>Edit
                                        </button>
                                </td>
                                <td>
                                    <button type="submit" class="btn-action alert alert-danger"
                                        onclick="return confirm('are you sure to delete this this row.')" name="btn-delete-row">
                                        <i class="bi bi-trash-fill"></i>Delete
                                    </button>
                                </td>
                                </form>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
            </form>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>