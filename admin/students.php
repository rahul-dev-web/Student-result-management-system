<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$pdo = db();
$error = '';
$success = '';
$editStudent = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? ''); $roll = trim($_POST['roll_number'] ?? '');
            $email = trim($_POST['email'] ?? ''); $course = trim($_POST['course'] ?? 'BCA');
            $semester = (int)($_POST['semester'] ?? 4); $password = $_POST['password'] ?? '';
            if ($name === '' || $roll === '') throw new RuntimeException('Name and roll number are required.');
            if ($id) {
                if ($password !== '') {
                    $stmt = $pdo->prepare('update students set name=:name, roll_number=:roll, email=:email, course=:course, semester=:semester, password=:password where id=:id');
                    $stmt->execute(['name'=>$name,'roll'=>$roll,'email'=>$email ?: null,'course'=>$course,'semester'=>$semester,'password'=>password_hash($password,PASSWORD_DEFAULT),'id'=>$id]);
                } else {
                    $stmt = $pdo->prepare('update students set name=:name, roll_number=:roll, email=:email, course=:course, semester=:semester where id=:id');
                    $stmt->execute(['name'=>$name,'roll'=>$roll,'email'=>$email ?: null,'course'=>$course,'semester'=>$semester,'id'=>$id]);
                }
                $success = 'Student updated successfully.';
            } else {
                if ($password === '') $password = 'student123';
                $stmt = $pdo->prepare('insert into students (name,roll_number,email,course,semester,password) values (:name,:roll,:email,:course,:semester,:password)');
                $stmt->execute(['name'=>$name,'roll'=>$roll,'email'=>$email ?: null,'course'=>$course,'semester'=>$semester,'password'=>password_hash($password,PASSWORD_DEFAULT)]);
                $success = 'Student added successfully.';
            }
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('delete from students where id=:id'); $stmt->execute(['id'=>(int)$_POST['id']]);
            $success = 'Student deleted successfully.';
        }
    } catch (Throwable $e) { $error = $e->getCode() === '23505' ? 'Roll number already exists.' : $e->getMessage(); }
}
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('select id,name,roll_number,email,course,semester from students where id=:id'); $stmt->execute(['id'=>(int)$_GET['edit']]); $editStudent = $stmt->fetch();
}
$search = trim($_GET['q'] ?? '');
if ($search !== '') { $stmt = $pdo->prepare('select id,name,roll_number,email,course,semester from students where name ilike :q or roll_number ilike :q order by id desc'); $stmt->execute(['q'=>"%{$search}%"]); $students=$stmt->fetchAll(); }
else { $students=$pdo->query('select id,name,roll_number,email,course,semester from students order by id desc')->fetchAll(); }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Manage Students</title><link rel="stylesheet" href="../assets/css/style.css"></head><body><div class="admin-layout"><aside class="sidebar"><a class="brand" href="../index.php">Result <span>System</span></a><nav><a href="dashboard.php">Dashboard</a><a class="active" href="students.php">Students</a><a href="results.php">Results</a><a href="logout.php">Logout</a></nav></aside><main class="admin-main"><div class="page-heading"><p class="eyebrow">Administration</p><h1>Students</h1><p class="muted">Add and manage student records.</p></div><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?><section class="panel"><div class="panel-head"><div><h2><?= $editStudent ? 'Edit Student' : 'Add Student' ?></h2></div><?php if ($editStudent): ?><a class="btn btn-ghost" href="students.php">Cancel</a><?php endif; ?></div><form class="form-grid" method="post"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e((string)($editStudent['id'] ?? '')) ?>"><label>Name<input name="name" required value="<?= e($editStudent['name'] ?? '') ?>"></label><label>Roll Number<input name="roll_number" required value="<?= e($editStudent['roll_number'] ?? '') ?>"></label><label>Email<input type="email" name="email" value="<?= e($editStudent['email'] ?? '') ?>"></label><label>Course<input name="course" value="<?= e($editStudent['course'] ?? 'BCA') ?>"></label><label>Semester<select name="semester"><?php for($i=1;$i<=8;$i++): ?><option value="<?= $i ?>" <?= ((int)($editStudent['semester'] ?? 4)===$i)?'selected':'' ?>><?= $i ?></option><?php endfor; ?></select></label><label>Password<input type="password" name="password" placeholder="<?= $editStudent ? 'Leave blank to keep current' : 'Default: student123' ?>"></label><div class="form-actions"><button class="btn btn-primary" type="submit"><?= $editStudent ? 'Update Student' : 'Add Student' ?></button></div></form></section><section class="panel"><div class="panel-head"><h2>Student List</h2><form class="inline-search"><input name="q" value="<?= e($search) ?>" placeholder="Search name or roll no"><button class="btn btn-secondary" type="submit">Search</button></form></div><div class="table-wrap"><table><thead><tr><th>Roll No</th><th>Name</th><th>Course</th><th>Semester</th><th>Actions</th></tr></thead><tbody><?php foreach($students as $s): ?><tr><td><?= e($s['roll_number']) ?></td><td><?= e($s['name']) ?></td><td><?= e($s['course']) ?></td><td><?= e((string)$s['semester']) ?></td><td class="actions"><a class="btn btn-small" href="?edit=<?= (int)$s['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this student and their result?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>"><button class="btn btn-small danger" type="submit">Delete</button></form></td></tr><?php endforeach; ?><?php if(!$students): ?><tr><td colspan="5" class="empty-cell">No students found.</td></tr><?php endif; ?></tbody></table></div></section></main></div></body></html>
