<?php
require_once __DIR__ . '/config.php';

$message = '';
$error = '';
$showTable = false;
$rows = [];

$formData = [
    'name' => '',
    'email' => '',
    'password' => '',
    'confirmPassword' => '',
    'usn' => '',
    'gender' => '',
    'languages' => [],
    'dob' => '',
    'description' => ''
];

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    $error = 'Database connection failed. Check config.php values.';
} else {
    $createTableSql = "CREATE TABLE IF NOT EXISTS student_forms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(180) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        usn VARCHAR(60) NOT NULL,
        gender VARCHAR(20) NOT NULL,
        languages VARCHAR(255) NOT NULL,
        dob DATE NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!$conn->query($createTableSql)) {
        $error = 'Table creation failed: ' . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $formData['name'] = trim($_POST['name'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['password'] = $_POST['password'] ?? '';
    $formData['confirmPassword'] = $_POST['confirmPassword'] ?? '';
    $formData['usn'] = trim($_POST['usn'] ?? '');
    $formData['gender'] = $_POST['gender'] ?? '';
    $formData['languages'] = $_POST['languages'] ?? [];
    $formData['dob'] = $_POST['dob'] ?? '';
    $formData['description'] = trim($_POST['description'] ?? '');

    if ($action === 'save' && $error === '') {
        if ($formData['name'] === '' || $formData['email'] === '' || $formData['usn'] === '' || $formData['dob'] === '') {
            $error = 'Please fill all required fields: Name, Email, USN, Date of Birth.';
        } elseif ($formData['password'] !== $formData['confirmPassword']) {
            $error = 'Passwords do not match.';
        } else {
            $passwordHash = password_hash($formData['password'], PASSWORD_DEFAULT);
            $languages = count($formData['languages']) > 0 ? implode(', ', $formData['languages']) : 'None selected';
            $gender = $formData['gender'] !== '' ? $formData['gender'] : 'Not selected';

            $stmt = $conn->prepare('INSERT INTO student_forms (name, email, password_hash, usn, gender, languages, dob, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            if ($stmt) {
                $stmt->bind_param(
                    'ssssssss',
                    $formData['name'],
                    $formData['email'],
                    $passwordHash,
                    $formData['usn'],
                    $gender,
                    $languages,
                    $formData['dob'],
                    $formData['description']
                );

                if ($stmt->execute()) {
                    $message = 'Form saved successfully.';
                    $showTable = true;
                    $formData = [
                        'name' => '',
                        'email' => '',
                        'password' => '',
                        'confirmPassword' => '',
                        'usn' => '',
                        'gender' => '',
                        'languages' => [],
                        'dob' => '',
                        'description' => ''
                    ];
                } else {
                    $error = 'Save failed: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = 'Insert preparation failed: ' . $conn->error;
            }
        }
    }

    if ($action === 'show') {
        $showTable = true;
    }
}

if ($showTable && $error === '' && isset($conn) && !$conn->connect_error) {
    $query = $conn->query('SELECT id, name, email, usn, gender, languages, dob, description, created_at FROM student_forms ORDER BY id DESC');
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $rows[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exp 10 - Form with MySQL</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>

<body>
    <main class="container">
        <section class="card">
            <h1>Student Form (PHP + MySQL)</h1>
            <p class="muted">Exp 10 - Save and display form data from database.</p>

            <?php if ($message !== ''): ?>
                <p class="status success"><?php echo e($message); ?></p>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <p class="status error"><?php echo e($error); ?></p>
            <?php endif; ?>

            <form method="post" id="formContainer">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo e($formData['name']); ?>" placeholder="Ace">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo e($formData['email']); ?>" placeholder="example@gmail.com">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="<?php echo e($formData['password']); ?>">
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" value="<?php echo e($formData['confirmPassword']); ?>">
                </div>

                <div class="form-group">
                    <label for="usn">USN:</label>
                    <input type="text" id="usn" name="usn" value="<?php echo e($formData['usn']); ?>" placeholder="24BTRXXXXX">
                </div>

                <div class="form-group">
                    <label>Gender:</label>
                    <div class="inline-group">
                        <input type="radio" id="male" name="gender" value="Male" <?php echo $formData['gender'] === 'Male' ? 'checked' : ''; ?>>
                        <label for="male">Male</label>

                        <input type="radio" id="female" name="gender" value="Female" <?php echo $formData['gender'] === 'Female' ? 'checked' : ''; ?>>
                        <label for="female">Female</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Languages:</label>
                    <div class="inline-group">
                        <?php
                        $langs = ['HTML', 'CSS', 'JavaScript', 'Java', 'Python', 'Rust'];
                        foreach ($langs as $lang):
                            $checked = in_array($lang, $formData['languages'], true) ? 'checked' : '';
                        ?>
                            <input type="checkbox" id="lang_<?php echo strtolower($lang); ?>" name="languages[]" value="<?php echo e($lang); ?>" <?php echo $checked; ?>>
                            <label for="lang_<?php echo strtolower($lang); ?>"><?php echo e($lang); ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo e($formData['dob']); ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" placeholder="Tell us about yourself..."><?php echo e($formData['description']); ?></textarea>
                </div>

                <div class="button-row">
                    <button type="submit" name="action" value="save" class="btn">Save Form</button>
                    <button type="submit" name="action" value="show" class="btn ghost">Display Filled Forms</button>
                </div>
            </form>
        </section>

        <?php if ($showTable): ?>
            <section class="card">
                <h2>Student Records from MySQL</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>USN</th>
                                <th>Gender</th>
                                <th>Languages</th>
                                <th>DOB</th>
                                <th>Description</th>
                                <th>Saved At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($rows) === 0): ?>
                                <tr>
                                    <td colspan="9" class="muted">No saved forms yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?php echo e((string) $row['id']); ?></td>
                                        <td><?php echo e($row['name']); ?></td>
                                        <td><?php echo e($row['email']); ?></td>
                                        <td><?php echo e($row['usn']); ?></td>
                                        <td><?php echo e($row['gender']); ?></td>
                                        <td><?php echo e($row['languages']); ?></td>
                                        <td><?php echo e($row['dob']); ?></td>
                                        <td><?php echo e($row['description'] !== '' ? $row['description'] : '-'); ?></td>
                                        <td><?php echo e($row['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>

</html>
