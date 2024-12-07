<?php
// Add Admin Menu for managing students
function smp_create_admin_menu() {
    add_menu_page('Student Management', 'Student Management', 'manage_options', 'smp-student-management', 'smp_admin_page', 'dashicons-welcome-learn-more');
    add_submenu_page('smp-student-management', 'Add New Student', 'Add New', 'manage_options', 'smp-add-student', 'smp_add_student_page');
}
add_action('admin_menu', 'smp_create_admin_menu');

// Admin Page to View Students
function smp_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'student';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<div class="wrap"><h1>Student List</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>ID</th><th>Photo</th><th>Name</th><th>Course</th><th>Phone</th><th>Email</th><th>Certificate</th><th>Download</th><th>Actions</th></tr></thead>';
    foreach ($results as $student) {
        echo "<tr><td>{$student->student_id}</td><td><img src='{$student->photo}' style='width:50px;height:100px;'/></td><td>{$student->student_name}</td><td>{$student->course}</td><td>{$student->phone}</td><td>{$student->email}</td><td><img src='{$student->certificate}' style='width:100px;height:100px;'/></td>
        <td><a href='" . esc_url($student->certificate) . "' download class='button'>Certificate</a></td>
        <td>
        <a href='?page=smp-edit-student&id={$student->id}'>Edit</a> | 
        <a href='?page=smp-student-management&delete={$student->id}'>Delete</a>
       </td></tr>";
    }
    echo '</table></div>';
}
// Add New Student Page
function smp_add_student_page() {
    echo '<div class="wrap"><h1>Add New Student</h1>';
    echo '<form method="post" enctype="multipart/form-data" action="' . esc_url(admin_url('admin-post.php')) . '">';
    wp_nonce_field('add_student_action', 'add_student_nonce');
    echo '<input type="hidden" name="action" value="submit_student_form">';
    echo '<label>Student ID</label><input type="text" name="student_id" required><br>';
    echo '<label>Name</label><input type="text" name="student_name" required><br>';
    echo '<label>Course</label><input type="text" name="course" required><br>';
    echo '<label>Phone</label><input type="text" name="phone"><br>';
    echo '<label>Email</label><input type="email" name="email"><br>';
    echo '<label>Photo</label><input type="file" name="photo" accept="image/*"><br>';
    echo '<label>Certificate</label><input type="file" name="certificate" accept="image/*"><br>';
    echo '<input type="submit" value="Add Student"></form></div>';
}

// Handle Student Form Submission
function smp_handle_student_submission() {
    if (isset($_POST['add_student_nonce']) && wp_verify_nonce($_POST['add_student_nonce'], 'add_student_action')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'student';

        $student_id = sanitize_text_field($_POST['student_id']);
        $student_name = sanitize_text_field($_POST['student_name']);
        $course = sanitize_text_field($_POST['course']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);

        // Handle photo upload
        $photo = wp_handle_upload($_FILES['photo'], ['test_form' => false]);
        $photo_url = $photo && !isset($photo['error']) ? $photo['url'] : '';

        // Handle certificate upload
        $certificate = wp_handle_upload($_FILES['certificate'], ['test_form' => false]);
        $certificate_url = $certificate && !isset($certificate['error']) ? $certificate['url'] : '';

        // Insert into the database
        $wpdb->insert($table_name, [
            'student_id' => $student_id,
            'student_name' => $student_name,
            'course' => $course,
            'phone' => $phone,
            'email' => $email,
            'photo' => $photo_url,
            'certificate' => $certificate_url
        ]);

        // Redirect to the student management page
        wp_redirect(admin_url('admin.php?page=smp-student-management'));
        exit;
    }
}
add_action('admin_post_submit_student_form', 'smp_handle_student_submission');
// Edit Student Page
function smp_edit_student_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'student';

    // Get student details
    if (isset($_GET['id'])) {
        $student_id = intval($_GET['id']);
        $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $student_id));

        if ($student) {
            echo '<div class="wrap"><h1>Edit Student</h1>';
            echo '<form method="post" enctype="multipart/form-data" action="' . esc_url(admin_url('admin-post.php')) . '">';
            wp_nonce_field('edit_student_action', 'edit_student_nonce');
            echo '<input type="hidden" name="action" value="submit_edit_student_form">';
            echo '<input type="hidden" name="student_id" value="' . esc_attr($student->id) . '">';
            echo '<label>Name</label><input type="text" name="student_name" value="' . esc_attr($student->student_name) . '" required><br>';
            echo '<label>Course</label><input type="text" name="course" value="' . esc_attr($student->course) . '" required><br>';
            echo '<label>Phone</label><input type="text" name="phone" value="' . esc_attr($student->phone) . '"><br>';
            echo '<label>Email</label><input type="email" name="email" value="' . esc_attr($student->email) . '"><br>';
            echo '<label>Photo</label><input type="file" name="photo" accept="image/*"><br>';
            echo '<label>Certificate</label><input type="file" name="certificate" accept="image/*"><br>';
            echo '<input type="submit" value="Update Student"></form></div>';
        } else {
            echo '<p>Student not found.</p>';
        }
    }
}

// Handle Edit Student Form Submission
function smp_handle_edit_student_submission() {
    if (isset($_POST['edit_student_nonce']) && wp_verify_nonce($_POST['edit_student_nonce'], 'edit_student_action')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'student';

        $student_id = intval($_POST['student_id']);
        $student_name = sanitize_text_field($_POST['student_name']);
        $course = sanitize_text_field($_POST['course']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);

        // Handle photo upload
        if (!empty($_FILES['photo']['name'])) {
            $photo = wp_handle_upload($_FILES['photo'], ['test_form' => false]);
            $photo_url = $photo && !isset($photo['error']) ? $photo['url'] : '';
        } else {
            $photo_url = $wpdb->get_var($wpdb->prepare("SELECT photo FROM $table_name WHERE id = %d", $student_id));
        }

        // Handle certificate upload
        if (!empty($_FILES['certificate']['name'])) {
            $certificate = wp_handle_upload($_FILES['certificate'], ['test_form' => false]);
            $certificate_url = $certificate && !isset($certificate['error']) ? $certificate['url'] : '';
        } else {
            $certificate_url = $wpdb->get_var($wpdb->prepare("SELECT certificate FROM $table_name WHERE id = %d", $student_id));
        }

        // Update student in the database
        $wpdb->update($table_name, [
            'student_name' => $student_name,
            'course' => $course,
            'phone' => $phone,
            'email' => $email,
            'photo' => $photo_url,
            'certificate' => $certificate_url
        ], ['id' => $student_id]);

        // Redirect back to student management page
        wp_redirect(admin_url('admin.php?page=smp-student-management'));
        exit;
    }
}
add_action('admin_post_submit_edit_student_form', 'smp_handle_edit_student_submission');

// Add "Edit Student" Submenu Page
function smp_create_edit_student_menu() {
    add_submenu_page(null, 'Edit Student', 'Edit Student', 'manage_options', 'smp-edit-student', 'smp_edit_student_page');
}
add_action('admin_menu', 'smp_create_edit_student_menu');

// Handle Deletion
function smp_handle_student_deletion() {
    if (isset($_GET['delete'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'student';
        $wpdb->delete($table_name, ['id' => intval($_GET['delete'])]);
        wp_redirect(admin_url('admin.php?page=smp-student-management'));
        exit;
    }
}
add_action('admin_init', 'smp_handle_student_deletion');
