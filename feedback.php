<?php
include "connect.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Patient Feedback — MedCare Portal</title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --blue-deep: #0a3d6b;
            --blue-mid: #1565c0;
            --blue-light: #e8f1fb;
            --blue-accent: #2196f3;
            --teal: #00acc1;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-200: #e2e8f0;
            --gray-400: #94a3b8;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --error: #e53e3e;
            --success: #2f855a;
            --radius: 14px;
            --shadow: 0 8px 40px rgba(10, 61, 107, 0.13);
            --transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        
        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 18px;
        }

        .header-badge svg {
            width: 14px;
            height: 14px;
        }

        .portal-header h1 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(26px, 5vw, 42px);
            color: #fff;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .portal-header p {
            color: rgba(255, 255, 255, 0.78);
            font-size: 15px;
            font-weight: 300;
            max-width: 380px;
            margin: 0 auto;
        }

        /* ── Card ────────────────────────────────────── */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 40px 44px 44px;
            width: 100%;
            max-width: 560px;
            margin-top: -44px;
            position: relative;
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-title {
            font-family: 'DM Serif Display', serif;
            font-size: 22px;
            color: var(--blue-deep);
            margin-bottom: 6px;
        }

        .card-sub {
            font-size: 13px;
            color: var(--gray-400);
            margin-bottom: 30px;
        }

        .divider {
            height: 1px;
            background: var(--gray-200);
            margin-bottom: 28px;
        }

        /* ── Form fields ─────────────────────────────── */
        .form-row {
            display: flex;
            gap: 18px;
        }

        .form-row .field {
            flex: 1;
        }

        .field {
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-800);
            margin-bottom: 7px;
            letter-spacing: 0.2px;
        }

        label .opt {
            font-weight: 400;
            color: var(--gray-400);
            font-size: 12px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--gray-200);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--gray-800);
            background: var(--gray-50);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        input:focus,
        textarea:focus {
            border-color: var(--blue-accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.1);
        }

        input.error-field,
        textarea.error-field {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .error-msg {
            font-size: 12px;
            color: var(--error);
            margin-top: 5px;
            display: none;
            animation: shake 0.3s ease;
        }

        .error-msg.visible {
            display: block;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-4px);
            }

            75% {
                transform: translateX(4px);
            }
        }

        /* ── Star rating ─────────────────────────────── */
        .rating-label {
            margin-bottom: 10px;
        }

        .stars {
            display: flex;
            gap: 8px;
        }

        .stars input {
            display: none;
        }

        .stars label {
            font-size: 32px;
            color: var(--gray-200);
            cursor: pointer;
            transition: color 0.15s, transform 0.15s;
            margin: 0;
            line-height: 1;
        }

        .stars label:hover,
        .stars label.active {
            color: #f6c90e;
        }

        .stars label:hover {
            transform: scale(1.18);
        }

        .rating-hint {
            font-size: 12px;
            color: var(--gray-400);
            margin-top: 6px;
            min-height: 16px;
            transition: color 0.2s;
        }

        /* ── Submit button ───────────────────────────── */
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--blue-mid) 0%, var(--teal) 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.4px;
            cursor: pointer;
            transition: transform var(--transition), box-shadow var(--transition), filter var(--transition);
            box-shadow: 0 4px 20px rgba(21, 101, 192, 0.35);
            margin-top: 4px;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0);
            transition: background var(--transition);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(21, 101, 192, 0.45);
        }

        .submit-btn:hover::after {
            background: rgba(255, 255, 255, 0.08);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* ── Success state ───────────────────────────── */
        .success-card {
            display: none;
            text-align: center;
            padding: 20px 0 10px;
            animation: fadeUp 0.5s ease both;
        }

        .success-card.visible {
            display: block;
        }

        .success-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #43a047, #00acc1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 6px 24px rgba(0, 172, 193, 0.3);
        }

        .success-icon svg {
            width: 34px;
            height: 34px;
        }

        .success-card h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 24px;
            color: var(--blue-deep);
            margin-bottom: 10px;
        }

        .success-card p {
            font-size: 14px;
            color: var(--gray-600);
            line-height: 1.6;
        }

        .reset-link {
            display: inline-block;
            margin-top: 22px;
            font-size: 13px;
            color: var(--blue-accent);
            cursor: pointer;
            text-decoration: underline;
            background: none;
            border: none;
            font-family: inherit;
        }

        .reset-link:hover {
            color: var(--blue-deep);
        }

        /* ── Footer ──────────────────────────────────── */
        .portal-footer {
            margin-top: 28px;
            font-size: 12px;
            color: var(--gray-400);
            text-align: center;
        }

        .portal-footer span {
            color: var(--blue-mid);
            font-weight: 500;
        }

        /* ── Responsive ──────────────────────────────── */
        @media (max-width: 520px) {
            .card {
                padding: 30px 22px 32px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .portal-header {
                padding: 24px 20px 68px;
            }
        }
    </style>
</head>
<body>
    <!-- Feedback Form -->
    <main class="card mt-5 d-block mx-auto col-lg-12">
        <div id="formWrapper">
            <p class="card-title">Patients Feedback</p>
            <p class="card-sub">All fields marked are required unless noted as optional.</p>
            <div class="divider"></div>

            <form id="feedbackForm" method="POST">

                <div class="form-row">
                    <div class="field">
                        <label for="fullName">Full Name</label>
                        <input type="text" name="fullName" id="fullName" placeholder="Dr. / Mr. / Ms. …"
                            autocomplete="name" />
                        <div class="error-msg" id="nameError">Please enter your full name.</div>
                    </div>
                </div>

                <div class="field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="patient@example.com"
                        autocomplete="email" />
                    <div class="error-msg" id="emailError">Please enter a valid email address.</div>
                </div>

                <div class="field">
                    <label class="rating-label">Overall Rating</label>

                    <div class="stars" id="starWidget">
                        <label data-value="1">★</label>
                        <label data-value="2">★</label>
                        <label data-value="3">★</label>
                        <label data-value="4">★</label>
                        <label data-value="5">★</label>
                    </div>

                    <input type="hidden" name="rating" id="ratingInput">

                    <div class="error-msg" id="ratingError">Please select a rating.</div>
                </div>

                <div class="field">
                    <label for="message">Feedback Message</label>
                    <textarea id="message" name="feedback_message"
                        placeholder="Tell us about your visit, care quality, staff behaviour, or any suggestions…"></textarea>
                    <div class="error-msg" id="messageError">Please share your feedback (min 20 characters).</div>
                </div>

                    <?php
    $errors_feedback = handleFeedback();

    if (!empty($errors_feedback)) {
        echo "<h6 style='color:red;' class='text-center text-capitalize my-2'>$errors_feedback</h6>";
    }

    ?>
                <button type="submit" name="submit_feedback" class="submit-btn" id="submitBtn">
                    Submit Feedback
                </button>

            </form>
        </div>
    </main>
    <?php
    function handleFeedback()
    {
        global $connect;
        $errors_feedback = "";

        if (isset($_POST['submit_feedback'])) {

            // Trim & sanitize
            $full_name = trim($_POST['fullName']);
            $email = trim($_POST['email']);
            $message = trim($_POST['feedback_message']);
            $rating = intval($_POST['rating'] ?? 0);

            /* ---------------- VALIDATION ---------------- */

            // Full Name Validation
            if (empty($full_name)) {
                $errors_feedback = "Full name is required.";
            } elseif (strlen($full_name) < 3) {
                $errors_feedback = "Full name must be at least 3 characters.";
            } elseif (!preg_match("/^[a-zA-Z\s\.]+$/", $full_name)) {
                $errors_feedback = "Full name contains invalid characters.";
            } elseif (empty($email)) {
                $errors_feedback = "Email is required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors_feedback = "Invalid email format.";
            } elseif ($rating < 1 || $rating > 5) {
                $errors_feedback = "Please select a rating between 1 and 5.";
            } elseif (empty($message)) {
                $errors_feedback = "Feedback message is required.";
            } elseif (strlen($message) < 20) {
                $errors_feedback = "Feedback must be at least 20 characters.";
            } else {

                $query = "INSERT INTO feedback (full_name, email, message, rating)
                      VALUES ('$full_name', '$email', '$message', '$rating')";
                      mysqli_query($connect,$query);

            }
        } else {
            echo "";
        }
        return $errors_feedback;
    }
    ?>
    <script>
        const stars = document.querySelectorAll('.stars label');
const ratingInput = document.getElementById('ratingInput'); // ⭐ IMPORTANT
let selectedRating = 0;

stars.forEach((star, i) => {

    star.addEventListener('click', () => {

        selectedRating = i + 1;

        ratingInput.value = selectedRating;   // ⭐ YEH LINE MISSING THI

        highlightStars(selectedRating);

        document.getElementById('ratingError').classList.remove('visible');
    });

});

function highlightStars(n) {
    stars.forEach((s, i) => {
        s.classList.toggle('active', i < n);
    });
}
if (!ratingInput.value) {
    e.preventDefault();
    showError('ratingError', null);
}
    </script>
</body>
</html>