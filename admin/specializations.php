<?php
/**
 * specializations.php — Manage Specializations Page
 * Healthcare Admin Panel — UI Only
 */
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit();
}
include("..//connect.php");
$pageTitle = 'Manage Specializations';
include('includes/header.php');
include('includes/sidebar.php');

//adding specialization
$select_doctor = "SELECT specialize FROM specialization";
$select_check = mysqli_query($connect, $select_doctor);
$specialize_name_check = [];
while ($row_checking = mysqli_fetch_assoc($select_check)) {
    $specialize_name_check[] = $row_checking['specialize'];
}
if (isset($_POST['save_city_btn'])) {
    $specialize_name = $_POST["specialize_name"];
    $specialize_status = $_POST["specialize_status"];
    if ($specialize_name === '') {
        $specialize_table_message = "<h6 class='alert alert-danger'>First Select specialization.";
    } elseif (in_array($specialize_name, $specialize_name_check)) {
        $specialize_table_message = "specialize is already there";
    } elseif ($specialize_status === '') {
        $specialize_table_message = "<h6 class='alert alert-danger'>Select status.</h6>";
    } else {
        $insert_specialization = "insert into specialization (specialize,specialization_status)
         values('$specialize_name','$specialize_status')";
        $insert_specialize_query = mysqli_query($connect, $insert_specialization);
        $specialize_table_message = "<h6 class='alert alert-success'>specialization Added Successfully!</h6>";
    }
} else {
    echo '';

}

//delete specialize row
if (isset($_POST["btn-delete-rows"])) {
    $get_specialization_id = $_POST['specialization_id'];
    $delete_specialize = "delete  from specialization where specialize_id='$get_specialization_id'";
    mysqli_query($connect, $delete_specialize);
    // header("location:specializations.php");
} else {
    echo "";
}

//edit specialize row 
if (isset($_POST['btn-edit-rows'])) {
    $get_specialization_id = $_POST['specialization_id'];
    $update_specialize_name = $_POST['update_specialize_name'];
    $update_specialize_Status = $_POST['update_specialize_status'];
    $update_specialize = "UPDATE specialization
SET specialize = '$update_specialize_name',
    specialization_status = '$update_specialize_Status'
WHERE specialize_id = '$get_specialization_id'";
    $update_specialize_query = mysqli_query($connect, $update_specialize);
    // header("location:specializations.php");
} else {
    echo '';
}
?>

<div class="page-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h4><i class="bi bi-award-fill me-2 text-primary"></i>Manage Specializations</h4>
            <ul class="breadcrumb-custom">
                <li><a href="dashboard.php">Home</a></li>
                <li>Specializations</li>
            </ul>
        </div>
    </div>

    <div class="form-wrapper">
        <form method="POST" action="" class="city-form">
            <h1>Add New specialization</h1>
            <input type="text" name="specialize_name" placeholder="add new specialists" class="form-control">

            <select name="specialize_status" class="city-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit" name="save_city_btn" class="city-btn">
                <i class="bi bi-check-circle-fill"></i> Save specialization
            </button>

            <?php if (!empty($specialize_table_message)) {
                ?>
                <div class="city-message">
                    <?= $specialize_table_message ?>
                </div>
                <?php
            }
            ?>
        </form>
    </div>
    <!-- Search Bar -->
    <!-- <div class="search-filter-bar page-fade-in">
        <div class="search-input-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="tableSearch" placeholder="Search specializations…" />
        </div>
        <select class="filter-select" style="min-width:120px;">
            <option value="">All Status</option>
            <option>Active</option><option>Inactive</option>
        </select>
    </div> -->

    <!-- Specializations Table -->
    <div class="section-card page-fade-in stagger-2">
        <div class="section-card-header">
            <h5><i class="bi bi-table"></i> Specializations List
                <span class="info-chip ms-2">
                    <?php
                    $result = $connect->query("SELECT COUNT(*) AS total FROM specialization");
                    $row = $result->fetch_assoc();
                    $totalSpecialists = $row['total'];
                    echo $totalSpecialists;
                    ?>
                </span>
            </h5>
        </div>
        <div class="section-card-body table-responsive-custom">
            <table class="admin-table table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Specialization</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $select_specialization = "select * from specialization";
                    $select_specialize_query = mysqli_query($connect, $select_specialization);
                    if (mysqli_num_rows($select_specialize_query) > 0) {
                        while ($specialize_table_row = mysqli_fetch_assoc($select_specialize_query)) {
                            $specialize_id = $specialize_table_row["specialize_id"];
                            $specialize = $specialize_table_row["specialize"];
                            $specialization_status = $specialize_table_row["specialization_status"];
                            ?>
                            <tr>

                                <form method="POST" action="">
                                    <td class="fw-600 text-primary-custom">#<?= $specialize_id ?></td>

                                    <input type="hidden" name="specialization_id" value="<?= $specialize_id ?>">
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar av1"
                                                style="border-radius:9px;background:linear-gradient(135deg,#0d6efd,#6610f2);">
                                                <i class="bi bi-geo-alt-fill" style="font-size:14px;"></i>
                                            </div>
                                            <span class="user-name"><input class="form-control" name="update_specialize_name"
                                                    type="text" value="<?= $specialize ?>"></span>
                                        </div>
                                    </td>
                                    <td><span class=""><input class="form-control" name="update_specialize_status" type="text"
                                                value="<?= $specialization_status ?>"></span>

                                    </td>
                                    <td>
                                        <button type="submit" class="btn-action alert alert-warning" name="btn-edit-rows">
                                            <i class="bi bi-pencil-fill"></i>Edit
                                        </button>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn-action alert alert-danger"
                                            onclick="return confirm('Are you sure to delete this this row.')"
                                            name="btn-delete-rows">
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
        </div>
    </div>
</div>


<?php include('includes/footer.php'); ?>