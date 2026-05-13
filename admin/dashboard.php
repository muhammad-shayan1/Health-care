<?php
/**
 * dashboard.php — Main Dashboard Page
 * Healthcare Admin Panel — UI Only
 */
include "..//connect.php";
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["btn-delete-feedback"])) {
    $get_feedback_id = $_POST['get_feedback_id'];
    $delete_feedback = "delete  from feedback where feedback_id='$get_feedback_id'";
    mysqli_query($connect, $delete_feedback);
    header("location:dashboard.php");
} else {
    echo "";
}

//edit city row 
if (isset($_POST['btn-edit-feedback'])) {
    $get_feedback_id = $_POST['get_feedback_id'];
    $update_feedback_Status = $_POST['update_feedback_status'];
    $update_feedback = "UPDATE feedback
SET feedback_status =$update_feedback_Status
WHERE feedback_id = '$get_feedback_id'";
    $update_query = mysqli_query($connect, $update_feedback);
    header("location:dashboard.php");
} else {
    echo '';
}
$pageTitle = 'Dashboard';
include('includes/header.php');
include('includes/sidebar.php');
?>

<!-- ╔══════════════════════════════════════════════════════╗
     ║  DASHBOARD PAGE CONTENT                              ║
     ╚══════════════════════════════════════════════════════╝ -->
<div class="page-wrapper" id="dashboardPage">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Overview</h4>
            <ul class="breadcrumb-custom">
                <li><a href="#">Home</a></li>
                <li>Dashboard</li>
            </ul>
        </div>
    </div>

    <!-- ── STATS CARDS ──────────────────────────────────── -->
    <div class="row row-g4 mb-4">
        <!-- Cities -->
        <div class="col-lg-4 col-md-6 col-12 page-fade-in stagger-1">
            <div class="stat-card blue w-100">
                <div class="stat-icon blue">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="number">
                        <?php
                        $result2 = $connect->query("SELECT COUNT(*) AS total FROM cities");
                        $row2 = $result2->fetch_assoc();
                        $totalcities = $row2['total'];
                        echo $totalcities;
                        ?>
                    </div>
                    <div class="label">Total Cities</div>
                </div>
            </div>
        </div>

        <!-- Doctors -->
        <div class="col-lg-4 col-md-6 col-12 page-fade-in stagger-2">
            <div class="stat-card gree w-100">
                <div class="stat-icon green">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="number">
                        <?php
                        $result_doctor = $connect->query("SELECT COUNT(*) AS total FROM doctors");
                        $row_doctor = $result_doctor->fetch_assoc();
                        $total_doctor = $row_doctor['total'];
                        echo $total_doctor;
                        ?>
                    </div>
                    <div class="label">Total Doctors</div>
                </div>
            </div>
        </div>

        <!-- Patients -->
        <div class="col-lg-4 col-md-6 col-12 page-fade-in stagger-3">
            <div class="stat-card red">
                <div class="stat-icon red">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="number">
                        <?php
                        $result_patient = $connect->query("SELECT COUNT(*) AS total FROM patients");
                        $row_patient = $result_patient->fetch_assoc();
                        $total_patient = $row_patient['total'];
                        echo $total_patient;
                        ?>
                    </div>
                    <div class="label">Total Patients</div>
                </div>
            </div>
        </div>

        <!-- Specializations -->
        <div class="col-lg-4 col-md-6 col-12 page-fade-in stagger-4">
            <div class="stat-card purp w-100">
                <div class="stat-icon purple">
                    <i class="bi bi-award-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="number">
                        <?php
                        $result = $connect->query("SELECT COUNT(*) AS total FROM specialization");
                        $row = $result->fetch_assoc();
                        $totalSpecialists = $row['total'];
                        echo $totalSpecialists;
                        ?>
                    </div>
                    <div class="label">Specializations</div>
                </div>
            </div>
        </div>

        <!-- Diseases -->
        <div class="col-lg-4 col-md-6 col-12 page-fade-in stagger-5">
            <div class="stat-card oran w-100">
                <div class="stat-icon orange">
                    <i class="bi bi-virus2"></i>
                </div>
                <div class="stat-info">
                    <div class="number">
                        <?php
                        $result_disease = $connect->query("SELECT COUNT(*) AS total FROM disease");
                        $row_disease = $result_disease->fetch_assoc();
                        $total_disease = $row_disease['total'];
                        echo $total_disease;
                        ?>
                    </div>
                    <div class="label">Total Diseases</div>
                </div>
            </div>
        </div>

        <!-- News Posts -->
        <div class="col-lg-4 col-md-6 col-12 page-fade-in stagger-6">
            <div class="stat-card teal w-100">
                <div class="stat-icon teal">
                    <i class="bi bi-newspaper"></i>
                </div>
                <div class="stat-info">
                    <div class="number">
                        <?php
                        $result_appointments = $connect->query("SELECT COUNT(*) AS total FROM appointments");
                        $row_appointments = $result_appointments->fetch_assoc();
                        $total_appointments = $row_appointments['total'];
                        echo $total_appointments;
                        ?>
                    </div>
                    <div class="label">Total Appointment</div>
                </div>
            </div>
        </div>

    </div>
    <!-- END STATS CARDS -->

    <!-- ── RECENT TABLES ROW ────────────────────────────── -->
    <div class="row g-3">
        <!-- Recent Doctors Table -->
        <div class="col-xl-6 col-lg-6 page-fade-in stagger-2">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-person-badge-fill"></i> Recent Doctors</h5>
                    <a href="doctors.php" class="btn-view btn-action">
                        <i class="bi bi-arrow-right-circle"></i> View All
                    </a>
                </div>
                <div class="section-card-body table-responsive-custom">
                    <table class="admin-table table">
                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>City</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_doctors = "SELECT * FROM doctors where doctor_status=1 ORDER BY created_at DESC LIMIT 5;";
                            $select_doctors_query = mysqli_query($connect, $select_doctors);
                            if (mysqli_num_rows($select_doctors_query) > 0) {
                                while ($doctors_table_row = mysqli_fetch_assoc($select_doctors_query)) {
                                    $doctor_id = $doctors_table_row["doctor_id"];
                                    $first_name = $doctors_table_row["first_name"];
                                    $last_name = $doctors_table_row["last_name"];
                                    $doctor_email = $doctors_table_row["email"];
                                    $doctor_status = $doctors_table_row["doctor_status"];
                                    $doctor_city_id = $doctors_table_row["city_id"];
                                    $specialize_id = $doctors_table_row["specialize_id"];

                                    $select_city = "select * from cities where city_id='$doctor_city_id'";
                                    $select_city_query = mysqli_query($connect, $select_city);
                                    if (mysqli_num_rows($select_city_query) > 0) {
                                        while ($city_table_row = mysqli_fetch_assoc($select_city_query)) {
                                            $city_name = $city_table_row["city_name"];

                                            $select_specialization = "select * from specialization where specialize_id='$specialize_id'";
                                            $select_specialize_query = mysqli_query($connect, $select_specialization);
                                            if (mysqli_num_rows($select_specialize_query) > 0) {
                                                while ($specialize_table_row = mysqli_fetch_assoc($select_specialize_query)) {
                                                    $specialize = $specialize_table_row["specialize"];
                                                    ?>
                                                    <tr>
                                                        <td class="fw-600 text-primary-custom"><?= $doctor_id ?></td>
                                                        <td>
                                                            <div class="user-cell">
                                                                <div class="user-avatar av1">
                                                                    <?= strtoupper(substr($first_name, 0, 1)); ?>
                                                                </div>
                                                                <div>
                                                                    <div class="user-name"><?= $first_name . $last_name ?></div>
                                                                    <div class="user-email"><?= $doctor_email ?></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?= $specialize ?></td>
                                                        <td><?= $city_name ?></td>
                                                        <td><span class="badge-status badge-active"><?= $doctor_status ?></span></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
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
        <!-- Recent Patients Table -->
        <div class="col-xl-6 col-lg-6 page-fade-in stagger-3">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-people-fill"></i> Recent Patients</h5>
                    <a href="patients.php" class="btn-view btn-action">
                        <i class="bi bi-arrow-right-circle"></i> View All
                    </a>
                </div>
                <div class="section-card-body table-responsive-custom">
                    <table class="admin-table table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient</th>
                                <th>gender</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_patients = "select * from patients where status='active'";
                            $select_patients_query = mysqli_query($connect, $select_patients);
                            if (mysqli_num_rows($select_patients_query) > 0) {
                                while ($patients_table_row = mysqli_fetch_assoc($select_patients_query)) {
                                    $patients_id = $patients_table_row["patient_id"];
                                    $patients_name = $patients_table_row["name"];
                                    $patients_gender = $patients_table_row["gender"];
                                    $patients_status = $patients_table_row["status"];
                                    ?>
                                    <tr>
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
                                        <td><span class="badge-status badge-active"><?= $patients_status ?></span></td>
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
        <!-- feedback Table -->
        <div class="col-xl-12 col-lg-12 page-fade-in stagger-3">
            <div class="section-card">
                <div class="section-card-header">
                    <h5><i class="bi bi-people-fill"></i> feedback </h5>
                    <h5 class="btn-view btn-action"><?php
                    $result_feedback = $connect->query("SELECT COUNT(*) AS total FROM feedback");
                    $row_feedback = $result_feedback->fetch_assoc();
                    $total_feedback = $row_feedback['total'];
                    echo $total_feedback;
                    ?></h5>
                </div>
                <div class="section-card-body table-responsive-custom">
                    <table class="admin-table table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>name</th>
                                <th>email</th>
                                <th>message</th>
                                <th>status</th>
                                <th>update/delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_feedback = "select * from feedback";
                            $select_feedback_query = mysqli_query($connect, $select_feedback);
                            if (mysqli_num_rows($select_feedback_query) > 0) {
                                while ($feedback_table_row = mysqli_fetch_assoc($select_feedback_query)) {
                                    $feedback_id = $feedback_table_row["feedback_id"];
                                    $feedback_name = $feedback_table_row["full_name"];
                                    $feedback_email = $feedback_table_row["email"];
                                    $feedback_message = $feedback_table_row["message"];
                                    $feedback_status = $feedback_table_row["feedback_status"];
                                    ?>
                                    <tr>
                                        <form method="POST">
                                            <td class="fw-600 text-primary-custom"><?= $feedback_id ?></td>
                                            <td>
                                                <input type="hidden" value="<?= $feedback_id ?>" name="get_feedback_id" id="">
                                                <div class="user-cell">
                                                    <div class="user-avatar av2">
                                                        <?= strtoupper(substr($feedback_name, 0, 1)) ?>
                                                    </div>
                                                    <div class="user-name" >
                                                        <?= $feedback_name ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= $feedback_email ?></td>
                                            <td><?= $feedback_message ?></td>
                                            <td><input type="text" class="form-control" name="update_feedback_status"
                                                    value="<?= $feedback_status ?>"></td>
                                            <td>
                                                <div style="display: flex;" class="d-flex flex-wrap">
                                                    <button class="btn-action btn-edit" name="btn-edit-feedback">
                                                        <i class="bi bi-pencil-fill"></i> Edit
                                                    </button>
                                                    <button onclick="return confirm('are you sure to delete this this row.')"
                                                        class="btn-action btn-delete btn-delete-row" name="btn-delete-feedback">
                                                        <i class="bi bi-trash-fill"></i> Del
                                                    </button>
                                                </div>
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

    </div>
    <!-- END TABLES ROW -->

</div>
<!-- END DASHBOARD PAGE -->

<?php include('includes/footer.php'); ?>