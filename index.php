<?php
// ---------- Database connection ----------
$DB_HOST = "localhost";
$DB_USER = "root";       // change to your MySQL/MariaDB user
$DB_PASS = "";           // change to your password
$DB_NAME = "proteins_test";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$search = trim($_GET['search'] ?? '');
$rows = [];

if ($search !== '') {
    $stmt = $conn->prepare("SELECT protein_name, pdbid, uniprot_id, gene_name FROM proteins WHERE protein_name LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Protein PDB Search</title>
<style>
    body { font-family: Arial, sans-serif; max-width: 500px; margin: 60px auto; }
    input[type=text] { font-size: 16px; padding: 8px; width: 70%; box-sizing: border-box; }
    input[type=submit] { font-size: 16px; padding: 8px 16px; }
    .result { margin-top: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 6px; background: #f9f9f9; }
    .result p { margin: 4px 0; }
    hr { border: none; border-top: 1px solid #ddd; }
</style>
</head>
<body>

<h2>Protein PDB Search</h2>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Type protein name..." value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<?php if ($search !== ''): ?>
    <div class="result">
        <?php if (count($rows) > 0): ?>
            <?php foreach ($rows as $i => $r): ?>
                <?php if ($i > 0): ?><hr><?php endif; ?>
                <p><strong>Protein Name:</strong> <?php echo htmlspecialchars($r['protein_name']); ?></p>
                <p><strong>PDB ID:</strong> <?php echo htmlspecialchars($r['pdbid']); ?></p>
                <p><strong>UniProt ID:</strong> <?php echo htmlspecialchars($r['uniprot_id']); ?></p>
                <p><strong>Gene Name:</strong> <?php echo htmlspecialchars($r['gene_name']); ?></p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Data not available.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>
