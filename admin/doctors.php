<?php
use PHPMailer\PHPMailer\PHPMailer;
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit();
}
include "..//connect.php";

//delete city row
if (isset($_POST["btn-delete-doc"])) {
    $get_doctor_id = $_POST['doctor_id'];
    $delete_doctor = "delete  from doctors where doctor_id='$get_doctor_id'";
    mysqli_query($connect, $delete_doctor);
    header("location:doctors.php");
} else {
    echo "";
}
//edit city row 
if (isset($_POST['btn-edit-doc'])) {
    $get_doctor_id = $_POST['doctor_id'];
    $status = intval($_POST['doctor_status_update']);

    $update_query = "UPDATE doctors SET
        doctor_status =$status
        WHERE doctor_id = $get_doctor_id";

    if (mysqli_query($connect, $update_query)) {
        header("Location: doctors.php");
        // exit;
    } else {
        echo "Update failed: " . mysqli_error($connect);
    }
}
$pageTitle = 'Manage Doctors';
include('includes/header.php');
include('includes/sidebar.php');
?>
<style>
    .card {
        background: #fff;
        border-radius: 20px;
        padding: 28px 26px 22px;
        width: 310px;
        box-shadow: 0 8px 32px rgba(30, 100, 180, 0.10), 0 1.5px 4px rgba(0, 0, 0, 0.06);
        transition: transform 0.25s, box-shadow 0.25s;
        animation: fadeUp 0.5s ease both;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 48px rgba(30, 100, 180, 0.16), 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-top {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }

    .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #60a5fa);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Playfair Display', serif;
        font-size: 1.35rem;
        color: #fff;
        letter-spacing: 1px;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    }

    .info h2 {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem;
        color: #111827;
        line-height: 1.2;
        margin-bottom: 5px;
    }

    .badge {
        display: inline-block;
        background: #eff6ff;
        color: #2563eb;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 3px 10px;
        border-radius: 999px;
        margin-bottom: 6px;
    }

    .stars {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.78rem;
    }

    .stars-icons {
        color: #f59e0b;
        letter-spacing: 1px;
    }

    .rating-text {
        color: #6b7280;
        font-size: 0.75rem;
    }

    .divider {
        border: none;
        border-top: 1px solid #f1f5f9;
        margin: 0 0 16px;
    }

    .meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.82rem;
        color: #374151;
    }

    .meta-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .meta-icon.exp {
        background: #eff6ff;
    }

    .meta-icon.loc {
        background: #fef2f2;
    }

    .card-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .fee-label {
        font-size: 0.7rem;
        color: #9ca3af;
        font-weight: 500;
        margin-bottom: 2px;
        letter-spacing: 0.3px;
    }

    .fee-amount {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1d4ed8;
        font-family: 'Playfair Display', serif;
        line-height: 1;
    }

    .book-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 12px 22px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        transition: background 0.2s, transform 0.15s, box-shadow 0.15s;
    }

    .book-btn:hover {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        transform: scale(1.04);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
    }

    .book-btn:active {
        transform: scale(0.98);
    }
</style>
<!-- ╔══════════════════════════════════════════════════════╗
     ║  MANAGE DOCTORS PAGE                                 ║
     ╚══════════════════════════════════════════════════════╝ -->
<div class="page-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h4><i class="bi bi-person-badge-fill me-2 text-primary"></i>Manage Doctors</h4>
            <ul class="breadcrumb-custom">
                <li><a href="dashboard.php">Home</a></li>
                <li>Manage Doctors</li>
            </ul>
        </div>
        <button class="btn-primary-custom" id="addNewBtn" data-bs-toggle="modal" data-bs-target="#doctorModal">
            <i class="bi bi-plus-lg"></i><a href="#form_id" class="btn text-decoration-none text-light btn-sm"> Add New
                Doctor </a>
        </button>
    </div>

    <!-- Search & Filter Bar -->
    <div class="search-filter-bar page-fade-in">
        <div class="search-input-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="tableSearch" placeholder="Search doctors by name, email, city…" />
        </div>
        <select class="filter-select">
            <option value="">All Cities</option>
            <?php
            $city_query = mysqli_query($connect, "SELECT city_id, city_name FROM cities");
            while ($row1 = mysqli_fetch_assoc($city_query)) {
                echo "<option value='" . $row1['city_id'] . "'>" . $row1['city_name'] . "</option>";
            }
            ?>
        </select>
        <select class="filter-select">
            <option value="">All Specializations</option>
            <?php
            $specialize_query = mysqli_query($connect, "SELECT specialize_id, specialize FROM specialization");
            while ($row2 = mysqli_fetch_assoc($specialize_query)) {
                echo "<option value='" . $row2['specialize_id'] . "'>" . $row2['specialize'] . "</option>";
            }
            ?>
        </select>
        <select class="filter-select" style="min-width:120px;">
            <option value="">All Status</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>
    <div class="container-fluid p-0">
        <div class="row p-0">
            <?php
            $select_doctors = "select * from doctors";
            $select_doctors_query = mysqli_query($connect, $select_doctors);
            if (mysqli_num_rows($select_doctors_query) > 0) {
                while ($doctors_table_row = mysqli_fetch_assoc($select_doctors_query)) {
                    $doctor_id = $doctors_table_row["doctor_id"];
                    $first_name = $doctors_table_row["first_name"];
                    $last_name = $doctors_table_row["last_name"];
                    $doctor_qualification = $doctors_table_row["qualification"];
                    $doctor_phone = $doctors_table_row["phone"];
                    $consultation_fee = $doctors_table_row["consultation_fee"];
                    $doctor_experience = $doctors_table_row["experience"];
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
                                    <div class="col-12 col-sm-6 col-xl-3 m-3">
                                        <form method="POST" action="">
                                            <div class="card">
                                                <div class="card-top">
                                                    <div class="avatar"> <?= strtoupper(substr($first_name . $last_name, 0, 1)); ?></div>
                                                    <div class="info">
                                                        <input type="hidden" value="<?= $doctor_id ?>" name="doctor_id">
                                                        <h2><?= $first_name . " " . $last_name ?></h2>
                                                        <div class="badge"><?= $specialize ?></div>
                                                    </div>
                                                </div>
                                                <hr class="divider">
                                                <div class="meta">
                                                    <div class="meta-item">
                                                        <div class="meta-icon exp">🩺</div>
                                                        <span><?= $doctor_experience ?> years experience</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <div class="meta-icon loc">📍</div>
                                                        <span><?= $city_name ?></span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <div class="meta-icon loc">🎓</div>
                                                        <span><?= $doctor_qualification ?></span>
                                                    </div>
                                                </div>

                                                <div class="card-bottom">
                                                    <div>
                                                        <div class="fee-label">Consultation Fee</div>
                                                        <div class="fee-amount"><?= $consultation_fee ?></div>
                                                    </div>
                                                </div>
                                                <label class="fee-label text-capitalize" style="font-size: 15px;">status</label>
                                                <input type="text" class="form-control mb-3" name="doctor_status_update"
                                                    value="<?= $doctor_status ?>">
                                                <button class="btn btn-warning d-inline my-2" type="submit" name="btn-edit-doc">update</button>
                                                <button class="btn btn-danger d-inline my-2" type="submit"
                                                    onclick="return confirm('are you sure to delete this this row.')"
                                                    name="btn-delete-doc">delete</button>
                                            </div>
                                        </form>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                }
            }
            ?>
        </div>
    </div>
    <form id="form_id" action="" style="height: auto;" class="m-5 alert alert-primary" method="POST"
        enctype="multipart/form-data">
        <div class="d-flex justify-content-center">
            <h2 class="modal-title">
                <i class="bi bi-person-plus-fill me-2"></i>Add New Doctor
            </h2>
        </div>
        <!-- 
    <div class=""> -->
        <div class="row g-3">

            <!-- First Name -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>First Name *</label>
                    <input type="text" class="form-control-custom" name="first_name" placeholder="First Name">
                </div>
            </div>
            <!-- Last Name -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control-custom" placeholder="Dr. last Name">
                </div>
            </div>

            <!-- Email -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Email Address *</label>
                    <input type="text" name="email" class="form-control-custom" placeholder="Dr. Email Address">
                </div>
            </div>

            <!-- Phone -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Phone Number</label>
                    <input type="number" name="phone_number" pattern="03[0-9]{9}" class="form-control-custom"
                        placeholder="03XXXXXXXXX" maxlength="11">
                </div>
            </div>

            <!-- Experience -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Years of Experience</label>
                    <input type="number" name="experience" class="form-control-custom" placeholder="Experience">
                </div>
            </div>

            <!-- Fee -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Rs consulation fee(Rs)</label>
                    <input type="number" name="fees" class="form-control-custom" placeholder="fees" min="200"
                        maxlength="5000">
                </div>
            </div>

            <!-- City -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>City *</label>
                    <select name="cities" class="form-control-custom filter-select"
                        style="width:100%;border-radius:9px;">
                        <option value="">Select City</option>
                        <?php
                        $city_query = mysqli_query($connect, "SELECT city_id, city_name FROM cities");
                        while ($row1 = mysqli_fetch_assoc($city_query)) {
                            echo "<option value='" . $row1['city_id'] . "'>" . $row1['city_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Specialization -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Specialization *</label>
                    <select name="specialization" class="form-control-custom filter-select"
                        style="width:100%;border-radius:9px;">
                        <option value="">Select Specialization</option>
                        <?php
                        $specialize_query = mysqli_query($connect, "SELECT specialize_id, specialize FROM specialization");
                        while ($row2 = mysqli_fetch_assoc($specialize_query)) {
                            echo "<option value='" . $row2['specialize_id'] . "'>" . $row2['specialize'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Profile Photo -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Profile Photo</label>
                    <input type="file" name="profile_photo" class="form-control-custom form-control" accept="image/*"
                        style="padding:7px 14px;">
                </div>
            </div>

            <!-- Status -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Qualification</label>
                    <input type="text" class="form-control-custom" max="100" name="qualification"
                        placeholder="MBBS,FBCS etc.">
                </div>
            </div>

            <!-- Bio -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Short Bio</label>
                    <textarea name="doctor_bio" class="form-control-custom" rows="3" minlength="" maxlength="500"
                        placeholder="Brief professional description…" style="resize:vertical;"></textarea>
                </div>
            </div>

            <!-- Address -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>Addres</label>
                    <textarea name="doctor_address" minlength="10" maxlength="300" class="form-control-custom" rows="3"
                        placeholder="Addres" style="resize:vertical;"></textarea>
                </div>
            </div>
            <!-- password -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>password</label>
                    <input name="password" type="text" class="form-control-custom" rows="3" minlength="10"
                        maxlength="500" placeholder="password" style="resize:vertical;" />
                </div>
            </div>

            <!-- confirm password -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="form-group-custom">
                    <label>confirm password</label>
                    <input name="confirmPassword" type="text" minlength="10" maxlength="300" class="form-control-custom"
                        rows="3" placeholder="Addres" style="resize:vertical;" />
                </div>
            </div>

        </div>
        <!-- </div> -->

        <button type="submit" name="add_doc_btn"
            class="btn text-capitalize bg-primary text-light btn-lg d-block mx-auto w-75">
            add doctor
        </button>
        <?php
        $error_message = addDoctor();

        if (!empty($error_message)) {
            echo "<h6 style='color:red;' class='text-center text-capitalize my-2'>$error_message</h6>";
        }

        ?>
    </form>
    <?php
    function addDoctor()
    {
        $error_message = '';
        global $connect;
        $select_doctor = "SELECT email,phone FROM doctors";
        $select_check = mysqli_query($connect, $select_doctor);
        $emails = [];
        $phones_check = [];

        while ($row_checking = mysqli_fetch_assoc($select_check)) {
            $emails[] = $row_checking['email'];
            $phones_check[] = $row_checking["phone"];
        }
        if (isset($_POST['add_doc_btn'])) {
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name']);
            $doctor_email = trim($_POST['email']);
            $phone_number = trim($_POST['phone_number']);
            $experience = trim($_POST['experience']);
            $qualification = trim($_POST['qualification']);
            $doctor_fees = trim($_POST['fees']);
            $doctor_city = $_POST['cities'];
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirmPassword']);
            $doctor_specialization = trim($_POST['specialization']);
            $profile_photo = $_FILES['profile_photo']['name'];
            $tmp_name = $_FILES['profile_photo']['tmp_name'];
            $doctor_bio = trim($_POST['doctor_bio']);
            $doctor_address = trim($_POST['doctor_address']);
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 2 * 1024 * 1024; // 2MB
    
            if (empty($first_name)) {
                $error_message = "Enter your first name.";
            } elseif (!preg_match("/^[a-zA-Z\s]{2,20}$/", $first_name)) {
                $error_message = "First name must contain only letters and be 2 to 20 characters.";
            } elseif (empty($last_name)) {
                $error_message = "Enter your last name.";
            } elseif (!preg_match("/^[a-zA-Z\s]{2,20}$/", $last_name)) {
                $error_message = "Last name must contain only letters and be 2 to 20 characters.";
            } elseif (empty($doctor_email)) {
                $error_message = "enter your email.";
            } elseif (
                !empty($doctor_email) &&
                !preg_match("/@gmail\.com$/", $doctor_email) &&
                !preg_match("/@aptechsite\.net$/", $doctor_email) &&
                !preg_match("/@yahoo\.com$/", $doctor_email)
            ) {
                $error_message = "Enter email in ( @gmail.com OR @aptechsite.net OR yahoo.com ).";
            } elseif (in_array($doctor_email, $emails)) {
                $error_message = "Your email is already there. Try a different email.";
            } elseif (empty($phone_number)) {
                $error_message = "enter your phone number.";
            } elseif (!empty($phone_number) && !preg_match("/^03[0-9]{9}$/", $phone_number)) {
                $error_message = "Invalid Phone Number";
            } elseif (in_array($phone_number, $phones_check)) {
                $error_message = "Your phone number is already there. Try a different number.";
            } elseif (empty($experience)) {
                $error_message = "Enter your experiecnce.";
            } elseif (!is_numeric($experience) || $experience < 1 || $experience > 60) {
                $error_message = "experience allowed between 0 to 60";
            } elseif (empty($doctor_fees)) {
                $error_message = "Enter your fees.";
            } elseif (!is_numeric($doctor_fees) || $doctor_fees < 200 || $doctor_fees > 5000) {
                $error_message = "enter valid fee between 200 to 5000";
            } elseif (empty($doctor_city)) {
                $error_message = "Enter your City.";
            } elseif (empty($doctor_specialization)) {
                $error_message = "Enter specialization.";
            } elseif (empty($qualification)) {
                $error_message = "Qualification is required";
            } elseif (strlen($qualification) > 100) {
                $error_message = "Qualification length is too long";
            } elseif (!preg_match("/^[a-zA-Z0-9\s,.-]+$/", $qualification)) {
                $error_message = "Qualification contains invalid characters";
            } elseif (empty($doctor_bio)) {
                $error_message = "enter your bio.";
            } elseif (strlen($doctor_bio) > 500) {
                $error_message = "Bio too long";
            } elseif (empty($doctor_address)) {
                $error_message = "enter your address.";
            } elseif (strlen($doctor_address) < 10 || strlen($doctor_address) > 300) {
                $error_message = "Address must be between 10 and 300 characters";
            } 
            elseif(!empty($profile_photo)){ 
                if($_FILES['profile_photo']['size'] > $max_size) {
                $error_message = "File too large";
            } elseif (!in_array($_FILES['profile_photo']['type'], $allowed_types)) {
                $error_message = "Invalid Image Type";
            } 
            }elseif (empty($password)) {
                $error_message = "Enter password.";
            } elseif (strlen($password) < 8) {
                $error_message = "Password must be at least 8 characters.";
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $error_message = "Password must contain at least 1 uppercase letter.";
            } elseif (!preg_match('/[0-9]/', $password)) {
                $error_message = "Password must contain at least 1 number.";
            } elseif (!preg_match('/[!@#$%^&*()\-_=+\[\]{};:\'",.<>?\/\\\\|]/', $password)) {
                $error_message = "Password must contain at least 1 special character.";
            } elseif ($password !== $confirmPassword) {
                $error_message = "Passwords do not match.";
            } else {

                $src = "src/" . $profile_photo;

                move_uploaded_file($tmp_name, $src);

                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $insert_doctor = "INSERT INTO doctors 
(first_name,last_name,email,phone,address,
qualification,experience,consultation_fee,
city_id,specialize_id,doctor_image,password)
VALUES
('$first_name','$last_name','$doctor_email',$phone_number,'$doctor_address',
'$qualification',$experience,$doctor_fees,
$doctor_city,$doctor_specialization,'$profile_photo','$hashed_password')";

                $insert_doctor_query = mysqli_query($connect, $insert_doctor);
                $error_message = "<h4 class='alert alert-success text-center'>Doctor added sucessfully<h4>";
                require '../PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
                require '../PHPMailer-master/PHPMailer-master/src/SMTP.php';
                require '../PHPMailer-master/PHPMailer-master/src/Exception.php';

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'syedddsamadd@gmail.com';
                $mail->Password = 'cjij xsvl yfhr dafo';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom("syedddsamadd@gmail.com", "CARE Group");
                $mail->addAddress($doctor_email);
                $mail->Subject = "Welcome to Our Service";
                $mail->Body = "Assalamualaikum,\n\n"
                    . "Aapka account successfully create ho gaya hai. "
                    . "Aap ab login karke apni services use kar sakte hain.\n\n"
                    . "Shukriya,\n"
                    . "Team XYZ.$password";

                if ($mail->send()) {
                    echo "Message Sent!";
                } else {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                }
            }
        } else {
            echo "<h6 class='text-center my-2 text-capitalize'>all fields are mandatory to filed</h6>";
        }

        return $error_message;
    }

    ?>
    <?php include('includes/footer.php'); ?>