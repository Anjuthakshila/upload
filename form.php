<?php
// Initialize variables
$id = $first_name = $last_name = $email = $phone_number = '';
$isUpdating = false;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "update";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';

    if (isset($_POST['update'])) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE users SET First_Name=?, Last_Name=?, E_Mail=?, Phone_Number=? WHERE ID=?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone_number, $id);
        $isUpdating = true;
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO users (First_Name, Last_Name, E_Mail, Phone_Number) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $phone_number);
    }

    if ($stmt->execute()) {
        $message = $isUpdating ? "Record updated successfully." : "New record created successfully.";
        echo "<script>alert('$message');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle edit request
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE ID=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['ID'];
        $first_name = $row['First_Name'];
        $last_name = $row['Last_Name'];
        $email = $row['E_Mail'];
        $phone_number = $row['Phone_Number'];
        $isUpdating = true;
    }
    $stmt->close();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE ID=?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully.');</script>";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch existing records
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    
    <script>
        function confirmDeletion(firstName, lastName) {
            return confirm(Are you sure you want to delete ${firstName} ${lastName}?);
        }
    </script>
</head>
<body>
    <h1>User Management</h1>

    <h2>Submit Your Information</h2>
    <form action="index.php" method="POST">
        <label for="id">ID:</label>
        <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>" <?php echo $isUpdating ? 'readonly' : ''; ?> required><br><br>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required><br><br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required><br><br>
        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required><br><br>
        <?php if ($isUpdating): ?>
            <input type="submit" name="update" value="Update" class="update-btn">
        <?php else: ?>
            <input type="submit" value="Submit">
        <?php endif; ?>
    </form>

    <h2>Existing Records</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>E-Mail</th>
            <th>Phone Number</th>
            <th>Actions</th>
        </tr>
        <?php
        if (isset($result) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["ID"]) . "</td>
                        <td>" . htmlspecialchars($row["First_Name"]) . "</td>
                        <td>" . htmlspecialchars($row["Last_Name"]) . "</td>
                        <td>" . htmlspecialchars($row["E_Mail"]) . "</td>
                        <td>" . htmlspecialchars($row["Phone_Number"]) . "</td>
                        <td class='actions'>
                            <a href='index.php?edit=" . $row["ID"] . "' class='update-btn'>Update</a>
                            <a href='index.php?delete=" . $row["ID"] . "' class='delete-btn' onclick=\"return confirmDeletion('" . $row["First_Name"] . "', '" . $row["Last_Name"] . "');\">Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found</td></tr>";
        }
        ?>
    </table>
    <?php
    // Close connection after all operations are done
    $conn->close();
    ?>
</body>
</html>
