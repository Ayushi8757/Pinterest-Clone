<?php

session_start();

require_once '../config/database.php';

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {

    header("Location: login.php");
    exit;
}

$query = "
SELECT * FROM users
WHERE account_type='business'
AND business_status='pending'
ORDER BY created_at DESC
";

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Business Requests</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            background:#f5f5f5;
            padding:40px;
        }

        h2{
            color:#E60023;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:white;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        th,td{
            padding:15px;
            border:1px solid #ddd;
            text-align:center;
        }

        th{
            background:#E60023;
            color:white;
        }

        .approve{
            background:green;
            color:white;
            padding:8px 15px;
            text-decoration:none;
            border-radius:5px;
        }

        .reject{
            background:red;
            color:white;
            padding:8px 15px;
            text-decoration:none;
            border-radius:5px;
        }

        .top-btn{
            display:inline-block;
            margin-bottom:20px;
            background:#111;
            color:white;
            padding:10px 20px;
            text-decoration:none;
            border-radius:5px;
        }

    </style>

</head>

<body>

<a href="dashboard.php" class="top-btn">
    Back to Dashboard
</a>

<h2>Business Account Requests</h2>

<table>

<tr>
    <th>User ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Account Type</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

    <td><?php echo $row['user_id']; ?></td>

    <td><?php echo $row['name']; ?></td>

    <td><?php echo $row['email']; ?></td>

    <td><?php echo $row['account_type']; ?></td>

    <td><?php echo $row['business_status']; ?></td>

    <td>

        <a
            class="approve"
            href="approve.php?id=<?php echo $row['user_id']; ?>"
        >
            Accept
        </a>

        <a
            class="reject"
            href="reject.php?id=<?php echo $row['user_id']; ?>"
        >
            Reject
        </a>

    </td>

</tr>

<?php } ?>

</table>

</body>
</html>