<?php
/**
 * patients.php — Manage Patients Page
 * Healthcare Admin Panel — UI Only
 */
include "..//connect.php";
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit();
}
//delete specialize row
if (isset($_POST["btn-delete-patient"])) {
    $patients_update_id = $_POST['patients_update_id'];
    $delete_patients = "delete  from patients where patient_id='$patients_update_id'";
    mysqli_query($connect, $delete_patients);
    // header("location:specializations.php");
}
//  else {
//     echo "<h1>not clxicked</h1>";
// }
//edit specialize row 
if (isset($_POST['btn-edit-patient'])) {
    $patients_update_id = $_POST['patients_update_id'];
    $update_patient_status = $_POST['update_patient_status'];
    $update_status = "UPDATE patients set 
    status ='$update_patient_status'
WHERE patient_id = '$patients_update_id'";
    $update_patient_query = mysqli_query($connect, $update_status);
    // header("location:specializations.php");
} 
// else {
//     echo 'not clicked';
// }
$pageTitle = 'Manage Patients';
include('includes/header.php');
include('includes/sidebar.php');
?>

<div class="page-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h4><i class="bi bi-people-fill me-2 text-primary"></i>Manage Patients</h4>
            <ul class="breadcrumb-custom">
                <li><a href="dashboard.php">Home</a></li>
                <li>Manage Patients</li>
            </ul>
        </div>
        <button class="btn-primary-custom" id="addNewBtn" data-bs-toggle="modal" data-bs-target="#patientModal">
            <i class="bi bi-plus-lg"></i> Add New Patient
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="search-filter-bar page-fade-in">
        <div class="search-input-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="tableSearch" placeholder="Search patients by name, phone, city…" />
        </div>
        <select class="filter-select">
            <option value="">All Cities</option>
            <option>Karachi</option>
            <option>Lahore</option>
            <option>Islamabad</option>
            <option>Rawalpindi</option>
        </select>
        <select class="filter-select" style="min-width:120px;">
            <option value="">All Status</option>
            <option>Active</option>
            <option>Inactive</option>
        </select>
    </div>

    <!-- Patients Table -->
    <div class="section-card page-fade-in stagger-2">
        <div class="section-card-header">
            <h5><i class="bi bi-table"></i> Patients List
                <span class="info-chip ms-2">
                    <?php
                    $result_patient = $connect->query("SELECT COUNT(*) AS total FROM patients");
                    $row_patient = $result_patient->fetch_assoc();
                    $total_patient = $row_patient['total'];
                    echo $total_patient;
                    ?>
                </span>
            </h5>
            <button class="btn-outline-custom" onclick="showToast('success','Exported','Patients list exported.')">
                <i class="bi bi-file-earmark-excel"></i> Export
            </button>
        </div>
        <div class="section-card-body table-responsive-custom">
            <table class="admin-table table">
                <thead>
                    <tr>
                        <th>Patients ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $select_patients = "select * from patients";
                    $select_patients_query = mysqli_query($connect, $select_patients);
                    if (mysqli_num_rows($select_patients_query) > 0) {
                        while ($patients_table_row = mysqli_fetch_assoc($select_patients_query)) {
                            $patients_id = $patients_table_row["patient_id"];
                            $patients_name = $patients_table_row["name"];
                            $patients_gender = $patients_table_row["gender"];
                            $patients_email = $patients_table_row["email"];
                            $patients_number = $patients_table_row["phone"];
                            $patients_status = $patients_table_row["status"];
                            $city_id = $patients_table_row["city_id"];
                            $select_city_patients = "select * from cities where city_id='$city_id'";
                            $select_city_patients_query = mysqli_query($connect, $select_city_patients);
                            if (mysqli_num_rows($select_city_patients_query) > 0) {
                                while ($city_patients_table_row = mysqli_fetch_assoc($select_city_patients_query)) {
                                    $city_patients = $city_patients_table_row["city_name"];
                                    ?>
                                    <tr>
                                        <form method="POST">
                                        <input type="hidden" name="patients_update_id" value="<?= $patients_id ?>">
                                        <td class="fw-600 text-primary-custom"><?= $patients_id ?></td>
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar av2">
                                                    <?= strtoupper(substr($patients_name, 0, 1)); ?>
                                                </div>
                                                <div class="user-name"><?= $patients_name ?></div>
                                            </div>
                                        </td>
                                        <td><?= $patients_gender ?></td>
                                        <td><?= $patients_number ?></td>
                                        <td><?= $city_patients ?></td>
                                        <td><input type="text" class="form-control" name="update_patient_status"
                                                value="<?= $patients_status ?>"></td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <button type="submit" class="btn-action btn-edit"
                                                 name="btn-edit-patient">
                                                    <i class="bi bi-pencil-fill"></i> Edit
                                                </button>
                                                <button type="submit" name="btn-delete-patient"
                                                 onclick="return confirm('Are you sure to delete this row?')"
                                                    class="btn-action btn-delete">
                                                    <i class="bi bi-trash-fill"></i> Del
                                                </button>
                                            </div>
                                        </td>
                                        </form>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>