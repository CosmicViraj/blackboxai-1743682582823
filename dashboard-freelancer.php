<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'freelancer') {
    header("Location: login.html");
    exit();
}

// Fetch available jobs
$jobs_sql = "SELECT j.*, u.name as employer_name 
             FROM jobs j 
             JOIN users u ON j.employer_id = u.id 
             WHERE j.status = 'active'";
$jobs_result = $conn->query($jobs_sql);

// Fetch user's applications
$user_id = $_SESSION['user_id'];
$applications_sql = "SELECT a.*, j.title as job_title 
                     FROM applications a
                     JOIN jobs j ON a.job_id = j.id
                     WHERE a.freelancer_id = $user_id";
$applications_result = $conn->query($applications_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Freelancer Dashboard</h1>
            <a href="logout.php" class="text-red-500 hover:underline">Logout</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Available Jobs Section -->
            <div class="md:col-span-2">
                <h2 class="text-xl font-semibold mb-4">Available Jobs</h2>
                <div class="space-y-4">
                    <?php while($job = $jobs_result->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold"><?= htmlspecialchars($job['title']) ?></h3>
                        <p class="text-gray-600 mb-2">Posted by: <?= htmlspecialchars($job['employer_name']) ?></p>
                        <p class="text-gray-600 mb-2">Budget: $<?= number_format($job['budget'], 2) ?></p>
                        <p class="mb-4"><?= htmlspecialchars($job['description']) ?></p>
                        <form action="apply.php" method="POST">
                            <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                            <textarea name="proposal" required 
                                      class="w-full p-2 border rounded mb-2" 
                                      placeholder="Write your proposal..."></textarea>
                            <button type="submit" 
                                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Apply
                            </button>
                        </form>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- My Applications Section -->
            <div>
                <h2 class="text-xl font-semibold mb-4">My Applications</h2>
                <div class="space-y-4">
                    <?php while($app = $applications_result->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="font-semibold"><?= htmlspecialchars($app['job_title']) ?></h3>
                        <p class="text-gray-600 mb-2">
                            Status: <span class="<?= 
                                $app['status'] === 'accepted' ? 'text-green-500' : 
                                ($app['status'] === 'rejected' ? 'text-red-500' : 'text-yellow-500')
                            ?>"><?= ucfirst($app['status']) ?></span>
                        </p>
                        <p class="text-sm text-gray-500">Proposal: <?= htmlspecialchars($app['proposal']) ?></p>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>