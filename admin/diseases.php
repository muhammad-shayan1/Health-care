<?php
include "..//connect.php";
//delete city row
if (isset($_POST["btn-delete-disease"])) {
    $delete_doctor_id = $_POST['Disease_id'];
    $delete_disease = "delete  from disease where disease_id='$delete_doctor_id'";
    mysqli_query($connect, $delete_disease);
    header("location:diseases.php");
} else {
    echo "";
}
if (isset($_POST['btn-edit-disease'])) {

    $disease_id = intval($_POST['Disease_id']);
    $disease_name = mysqli_real_escape_string($connect, trim($_POST['disease_name_update']));
    $specialize_id = intval($_POST['specialize_id_update']);
    $status = intval($_POST['status_update']);

    if (empty($disease_name)) {
        echo "Disease name required.";
        exit;
    }

    $update_query = "UPDATE disease SET
        disease_name = '$disease_name',
        specialize_id = $specialize_id,
        status = $status
        WHERE disease_id = $disease_id";

    if (mysqli_query($connect, $update_query)) {
        header("Location: diseases.php");
        exit;
    } else {
        echo "Update failed: " . mysqli_error($connect);
    }
}


session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit();
}
$pageTitle = 'Manage Diseases';
include('includes/header.php');
include('includes/sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Disease Information Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-card {
            background: #ffffff;
            padding: 30px 25px;
            border-radius: 15px;
            width: 100%;
            margin: 20px;
            max-width: 600px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .form-card h2 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            font-weight: 500;
            color: #0d6efd;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #0d6efd;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #6610f2;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.3);
        }

        .btn-submit {
            width: 100%;
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background-color: #6610f2;
        }
    </style>
</head>

<body>
    <div class="page-wrapper">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left">
                <h4><i class="bi bi-virus2 me-2 text-primary"></i>Manage Diseases</h4>
                <ul class="breadcrumb-custom">
                    <li><a href="dashboard.php">Home</a></li>
                    <li>Manage Diseases</li>
                </ul>
            </div>
        <button class="btn-primary-custom" id="addNewBtn" data-bs-toggle="modal" data-bs-target="#doctorModal">
            <i class="bi bi-plus-lg"></i><a href="#diseaseForm" class="btn text-decoration-none text-light btn-sm"> Add New
                Doctor </a>
        </button>
        </div>

        <!-- Search Bar -->
        <div class="search-filter-bar page-fade-in">
            <div class="search-input-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="tableSearch" placeholder="Search diseases by name or category…" />
            </div>
            <select class="filter-select">
                <option value="">All Categories</option>
                <option>Cardiovascular</option>
                <option>Neurological</option>
                <option>Respiratory</option>
                <option>Infectious</option>
                <option>Metabolic</option>
            </select>
            <select class="filter-select" style="min-width:120px;">
                <option value="">All Status</option>
                <option>Active</option>
                <option>Inactive</option>
            </select>
        </div>
    
        <!-- Doctors Table Card -->
        <div class="section-card page-fade-in stagger-2">
            <div class="section-card-header">
                <h5><i class="bi bi-table"></i> Disease List
                    <span class="info-chip ms-2">
                        <?php
              $result_disease = $connect->query("SELECT COUNT(*) AS total FROM disease");
              $row_disease = $result_disease->fetch_assoc();
              $total_disease = $row_disease['total'];
              echo $total_disease;
              ?>
                    </span>
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn-outline-custom"
                        onclick="showToast('success','Exported','Doctors list exported.')">
                        <i class="bi bi-file-earmark-excel"></i> Export
                    </button>
                </div>
            </div>
            <div class="section-card-body table-responsive-custom">
                <table class="admin-table table">
                    <thead>
                        <tr>
                            <th>disease_id</th>
                            <th>disease_name</th>
                            <th>treatement</th>
                            <th>specialist</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        ?>
                        <?php
                        $select_diseases = "select * from disease";
                        $select_diseases_query = mysqli_query($connect, $select_diseases);
                        if (mysqli_num_rows($select_diseases_query) > 0) {
                            while ($disease_table_row = mysqli_fetch_assoc($select_diseases_query)) {
                                $disease_id = $disease_table_row["Disease_id"];
                                $disease_name = $disease_table_row["disease_name"];
                                $treatment = $disease_table_row["treatment"];
                                $status = $disease_table_row["status"];
                                $specialize_id = $disease_table_row["specialize_id"];

                                        $select_specialization = "select * from specialization where specialize_id='$specialize_id'";
                                        $select_specialize_query = mysqli_query($connect, $select_specialization);
                                        if (mysqli_num_rows($select_specialize_query) > 0) {
                                            while ($specialize_table_row = mysqli_fetch_assoc($select_specialize_query)) {
                                                $specialize = $specialize_table_row["specialize"];
                                                ?>
                                                <tr>
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="Disease_id" value="<?= $disease_id ?>">
                                                        <td class="fw-600 text-primary-custom"><?= $disease_id ?></td>
                                                        <td>
                                                                    <b class="user-name"><input type="text" name="disease_name_update"
                                                                            class="form-control" value="<?= $disease_name ?>"></b>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?= $treatment?></td>
                                                        

                                                        <td><select name="specialize_id_update" class="form-control">
                                                                <?php
                                                                $sp_query = mysqli_query($connect, "SELECT specialize_id, specialize
                                                                 FROM specialization where specialization_status='active'");
                                                                while ($sp = mysqli_fetch_assoc($sp_query)) {
                                                                    $selected = ($sp['specialize_id'] == $specialize_id) ? "selected" : "";
                                                                    echo "<option value='{$sp['specialize_id']}' $selected>{$sp['specialize']}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td><span class="badge-status"><input type="text" class="form-control"
                                                                    name="status_update" value="<?= $status ?>"></span></td>
                                                        <td>
                                                            <div class="d-flex gap-1 flex-wrap">
                                                                <button class="btn-action btn-edit" name="btn-edit-disease">
                                                                    <i class="bi bi-pencil-fill"></i> Edit
                                                                </button>
                                                                <button onclick="return confirm('are you sure to delete this this row.')"
                                                                    class="btn-action btn-delete btn-delete-row" name="btn-delete-disease">
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
<div class="conteiner-fluid">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-primary p-5 m-3">
                <h2>Disease Information Form</h2>
                <form method="POST" id="diseaseForm" action="">

                    <div class="form-group">
                        <label for="disease_title">Disease Title</label>
                        <input type="text" name="disease_title" id="disease_title" class="form-control"
                            placeholder="Enter disease name">
                    </div>

                    <div class="form-group">
                        <label for="symptoms">Symptoms</label>
                        <textarea name="symptoms" id="symptoms" class="form-control" rows="3"
                            placeholder="Enter symptoms"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="causes">Causes</label>
                        <textarea name="causes" id="causes" class="form-control" rows="3"
                            placeholder="Enter causes"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="prevention">Prevention</label>
                        <textarea name="prevention" id="prevention" class="form-control" rows="3"
                            placeholder="Enter prevention measures"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="treatment">Treatment</label>
                        <textarea name="treatment" id="treatment" class="form-control" rows="3"
                            placeholder="Enter treatment"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="specialist">Specialist</label>
                        <select name="specialist" id="specialist" class="form-control">
                            <option value="">Select Specialist</option>
                            <?php
                            $specialize_query = mysqli_query($connect, "SELECT specialize_id, specialize FROM specialization where specialization_status='active'");
                            while ($row2 = mysqli_fetch_assoc($specialize_query)) {
                                echo "<option value='" . $row2['specialize_id'] . "'>" . $row2['specialize']. "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary d-block btn-lg mx-auto w-75 mt-5">Submit</button>
                    <?php
                    $error_disease = addDisease();

                    if (!empty($error_disease)) {
                        echo "<h6 style='color:red;' class='text-center text-capitalize my-2'>$error_disease</h6>";
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
    </div>
</body>

</html>

<?php

function addDisease()
{
    global $connect;
    $disease_title = "";
    $symptoms = "";
    $causes = "";
    $prevention = "";
    $treatment = "";
    $specialist_id = "";
    $error_disease = "";
    $success = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $disease_title = trim($_POST['disease_title'] ?? '');
        $symptoms = trim($_POST['symptoms'] ?? '');
        $causes = trim($_POST['causes'] ?? '');
        $prevention = trim($_POST['prevention'] ?? '');
        $treatment = trim($_POST['treatment'] ?? '');
        $specialist_id = trim($_POST['specialist'] ?? '');

        // Title Validation
        if ($disease_title === "") {
            $error_disease = "Disease title is required.";
        } elseif (strlen($disease_title) > 30) {
            $error_disease = "Disease title cannot exceed 30 characters.";
        }

        // Symptoms Validation
        elseif ($symptoms === "") {
            $error_disease = "Symptoms are required.";
        } elseif (strlen($symptoms) > 200) {
            $error_disease = "Symptoms cannot exceed 200 characters.";
        }

        // Causes Validation
        elseif ($causes === "") {
            $error_disease = "Causes are required.";
        } elseif (strlen($causes) > 200) {
            $error_disease = "Causes cannot exceed 200 characters.";
        }

        // Prevention Validation
        elseif ($prevention === "") {
            $error_disease = "Prevention is required.";
        } elseif (strlen($prevention) > 200) {
            $error_disease = "Prevention cannot exceed 200 characters.";
        }

        // Treatment Validation
        elseif ($treatment === "") {
            $error_disease = "Treatment is required.";
        } elseif (strlen($treatment) > 200) {
            $error_disease = "Treatment cannot exceed 200 characters.";
        }

        // Specialist Validation
        elseif ($specialist_id === "") {
            $error_disease = "Please select a specialist.";
        } else {
            $error_disease = "Form submitted successfully!";

            // Yahan database insert kar sakte ho
            $insert_disease = "insert into disease (disease_name,symptoms,causes,prevention,treatment,
            specialize_id) values('$disease_title','$symptoms','$causes','$prevention','$treatment',
            $specialist_id)";
            
            $insert_disease_query = mysqli_query($connect, $insert_disease);
            // Clear form after success
            $disease_title = "";
            $symptoms = "";
            $causes = "";
            $prevention = "";
            $treatment = "";
            $specialist_id = "";
        }
    }
    return $error_disease;
}
?>
<?php if ($error_disease != ""): ?>
    <script>
        window.onload = function () {
            document.getElementById("diseaseForm").scrollIntoView();
        };
    </script>
<?php endif; ?>

<?php include('includes/footer.php'); ?>