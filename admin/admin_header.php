<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-header {
            background: #333;
            color: white;
            padding: 1rem 0;
        }
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-left: 2rem;
        }
        .admin-nav a:hover {
            color: #007bff;
        }
        .admin-dashboard {
            padding: 2rem 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .admin-button {
            background: #007bff;
            color: white;
            padding: 1rem;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            transition: background 0.3s ease;
        }
        .admin-button:hover {
            background: #0056b3;
        }
        .admin-footer {
            background: #f8f9fa;
            padding: 1rem 0;
            text-align: center;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <div>
                    <h1><?php echo SITE_NAME; ?> Admin</h1>
                </div>
                <nav>
                    <a href="index.php">Dashboard</a>
                    <a href="urls.php">Domains</a>
                    <a href="stats.php">Statistics</a>
                    <a href="contact.php">Contact</a>
                    <a href="clear_cache.php">Cache</a>
                    <a href="logout.php">Logout</a>
                </nav>
            </div>
        </div>
    </header>
    <main class="main"> 