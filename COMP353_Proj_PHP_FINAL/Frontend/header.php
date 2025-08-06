<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MVC Admin Panel</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background-color: #f2f2f2;
    }
    header, footer {
      background-color: #003366;
      color: white;
      padding: 15px;
      text-align: center;
    }
    nav {
      margin: 20px 0;
      background-color: #ddd;
      padding: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    nav a {
      text-decoration: none;
      font-weight: bold;
      color: #003366;
    }
    nav a:hover {
      text-decoration: underline;
    }
    section {
      background-color: white;
      padding: 20px;
      border-radius: 6px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 8px;
      border: 1px solid #aaa;
      text-align: left;
    }
    th {
      background-color: #eee;
    }
  </style>
</head>
<body>
  <header>
    <h1>Montréal Volleyball Club Admin Panel</h1>
  </header>

  <nav>
    <a href="index.php">Home</a>
    <a href="locations.php">Locations</a> <!-- Q1 -->
    <a href="personnel.php">Personnel</a> <!-- Q2 -->
    <a href="family.php">Family Members</a> <!-- Q3 -->
    <a href="members.php">Club Members</a> <!-- Q4 -->
    <a href="teams.php">Teams</a> <!-- Q5 -->
    <a href="generate_teams.php">Team Generation</a> <!-- Q6 -->
    <a href="make_payment.php">Make Payment</a> <!-- Q7 -->
    <a href="payments.php">View Payments</a> <!-- support -->
    <a href="emailLogs.php">Email Logs</a> <!-- support -->
  </nav>

  <section>
