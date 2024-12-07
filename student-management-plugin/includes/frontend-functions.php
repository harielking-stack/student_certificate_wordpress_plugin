<?php

// Shortcode to Search Students by ID
function smp_student_search_shortcode() {
    ob_start(); // Start output buffering
    ?>
    <style>
        .student-search-form {
            margin: 20px 0;
            text-align: center;
        }
        .student-search-form input[type="text"] {
            width: 300px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .student-search-form input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #0073aa;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .student-search-form input[type="submit"]:hover {
            background-color: #005177;
        }
        .student-details {
            margin-top: 20px;
            width: 150%;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
        .student-details h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        .student-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-details table td {
            padding: 30px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }
        .student-details table td.label {
            font-weight: bold;
            width: 30%;
        }
        .student-details img {
            max-width: 100%;
            border-radius: 10px;
        }
        .student-details .button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .student-details .button:hover {
            background-color: #218838;
        }
        .no-student {
            color: red;
            font-size: 18px;
            margin-top: 20px;
            text-align: center;
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .student-details {
                width: 90%;
            }
            .student-search-form input[type="text"] {
                width: 80%;
                margin-bottom: 10px;
            }
            .student-search-form input[type="submit"] {
                width: 80%;
            }
            .student-details table td.label {
                display: block;
                width: 100%;
                font-weight: normal;
                padding: 5px;
                text-align: center;
            }
            .student-details table td {
                display: block;
                width: 100%;
                padding: 5px;
                text-align: center;
            }
            .student-details table {
                border: none;
            }
        }

        @media screen and (max-width: 480px) {
            .student-search-form input[type="text"],
            .student-search-form input[type="submit"] {
                width: 100%;
            }
            .student-details {
                width: 95%;
            }
            .student-details .button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
    
    <div class="student-search-form">
        <form method="get">
            <input type="text" name="student_id" placeholder="Enter Student ID" value="<?php echo isset($_GET['student_id']) ? esc_attr($_GET['student_id']) : ''; ?>">
            <input type="submit" value="Search">
        </form>
    </div>

    <?php
    if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'student';
        $student_id = sanitize_text_field($_GET['student_id']);
        $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE student_id = %s", $student_id));

        if ($student) {
            echo "<div class='student-details'>";
           
            echo "<table>";
            echo "<tr><td class='label'><strong>Name:</strong></td><td>" . esc_html($student->student_name) . "</td></tr>";
            echo "<tr><td class='label'><strong>Course:</strong></td><td>" . esc_html($student->course) . "</td></tr>";
            echo "<tr><td class='label'><strong>Phone:</strong></td><td>" . esc_html($student->phone) . "</td></tr>";
            echo "<tr><td class='label'><strong>Email:</strong></td><td>" . esc_html($student->email) . "</td></tr>";
            if ($student->photo) {
    echo "<tr>
            <td class='label'><strong>Photo:</strong></td>
            <td><img src='" . esc_url($student->photo) . "' alt='Student Photo' style='width: 132px; height: 170px; object-fit: cover;' /></td>
          </tr>";
            }
            if ($student->certificate) {
                echo "<tr><td class='label'><strong>Certificate:</strong></td><td><img src='" . esc_url($student->certificate) . "' alt='Student Certificate' style='width:500px;height:400px;' /></td></tr>";
               
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p class='no-student'>No student found with this ID.</p>";
        }
    }

    return ob_get_clean(); // Return the output buffer contents
}
add_shortcode('student_search', 'smp_student_search_shortcode');
