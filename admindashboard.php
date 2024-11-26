<?php
include('dbconn.php');

$config = new Config();
$conn = $config->conn;

// Handle fetch request to get all records
if (isset($_POST['fetch'])) {
    $sql = "SELECT * FROM user_info";
    $result = $conn->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
    exit;
}

// Handle record update if AJAX request is made
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $update_data = $_POST['update_data']; // Array of updated data

    foreach ($update_data as $data) {
        $column = $data['column'];
        $new_value = $data['new_value'];

        // Sanitize input values to avoid SQL injection
        $new_value = $conn->real_escape_string($new_value);

        // Create SQL query to update the database record
        $sql = "UPDATE user_info SET $column = '$new_value' WHERE id = $id";

        // Execute the update query
        if ($conn->query($sql) !== TRUE) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
            exit;
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Record updated successfully']);
    exit;
}

// Handle record delete if AJAX request is made
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Create SQL query to delete the record
    $sql = "DELETE FROM user_info WHERE id = $id";

    // Execute the delete query
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
    }
    exit;
}

// Handle adding a new record
if (isset($_POST['add'])) {
    // Get data from the form and sanitize it
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $mobile_number = $conn->real_escape_string($_POST['mobile_number']);
    $age = $_POST['age'];
    $year_section = $conn->real_escape_string($_POST['year_section']);
    $course = $conn->real_escape_string($_POST['course']);
    $gender = $conn->real_escape_string($_POST['gender']);

    // Set the default username to "admin"
    $username = "admin"; 

    // Ensure all fields are provided (simple validation)
    if (empty($fname) || empty($lname) || empty($mobile_number) || empty($age) || empty($year_section) || empty($course) || empty($gender)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Check if username exists in the users table (optional, in case you want to enforce it)
    $sql_check_user = "SELECT * FROM users WHERE username = '$username'";
    $result_check = $conn->query($sql_check_user);
    if ($result_check->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username does not exist in users table']);
        exit;
    }

    // Insert the new record into the user_info table with the default username "admin"
    $sql = "INSERT INTO user_info (fname, lname, mobile_number, age, year_section, course, gender, username) 
            VALUES ('$fname', '$lname', '$mobile_number', '$age', '$year_section', '$course', '$gender', '$username')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'New record added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
    }
    exit;
}

// Close connection (optional)
$conn->close();
?>
