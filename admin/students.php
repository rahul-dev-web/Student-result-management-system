<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$pdo = db(); $error=''; $success=''; $editStudent=null;

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action=$_POST['action']??'';
    try {
        if ($action==='save') {
            $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $roll=trim($_POST['roll_number']??'');
            $email=trim($_POST['email']??''); $course=trim($_POST['course']??'BCA'); $semester=(int)($_POST['semester']??4); $password=$_POST['password']??'';
            if ($name==='' || $roll==='' || $email==='') throw new RuntimeException('Name, roll number and email are required.');
            if (!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Please enter a valid email address.');
            if ($semester<1 || $semester>12) throw new RuntimeException('Semester must be between 1 and 12.');
            if ($id) {
                if ($password!=='') {
                    $stmt=$pdo->prepare('update students set name=:name,roll_number=:roll,email=:email,course=:course,semester=:semester,password=:password where id=:id');
                    $stmt->execute(['name'=>$name,'roll'=>$roll,'email'=>$email,'course'=>$course,'semester'=>$semester,'password'=>password_hash($password,PASSWORD_DEFAULT),'id'=>$id]);
                } else {
                    $stmt=$pdo->prepare('update students set name=:name,roll_number=:roll,email=:email,course=:course,semester=:semester where id=:id');
                    $stmt->execute(['name'=>$name,'roll'=>$roll,'email'=>$email,'course'=>$course,'semester'=>$semester,'id'=>$id]);
                }
                $success='Student updated successfully.';
            } else {
                if ($password==='') $password='student123';
                $stmt=$pdo->prepare('insert into students (name,roll_number,email,course,semester,password) values (:name,:roll,:email,:course,:semester,:password)');
                $stmt->execute(['name'=>$name,'roll'=>$roll,'email'=>$email,'course'=>$course,'semester'=>$semester,'password'=>password_hash($password,PASSWORD_DEFAULT)]);
                $success='Student added successfully.';
            }
        } elseif ($action==='delete') {
            $stmt=$pdo->prepare('delete from students where id=:id'); $stmt->execute(['id'=>(int)$_POST['id']]); $success='Student deleted successfully.';
        }
    } catch (Throwable $e) { $error=$e->getCode()==='23505'?'Roll number or email already exists.':$e->getMessage(); }
}
if (isset($_GET['edit'])) { $stmt=$pdo->prepare('select id,name,roll_number,email,course,semester from students where id=:id'); $stmt->execute(['id'=>(int)$_GET['edit']]); $editStudent=$stmt->fetch(); }
$search=trim($_GET['q']??'');
if ($search!=='') { $stmt=$pdo->prepare('select id,name,roll_number,email,course,semester from students where name ilike :q or roll_number ilike :q order by id desc'); $stmt->execute(['q'=>"%{$search}%"]); $students=$stmt->fetchAll(); }
else $students=$pdo->query('select id,name,roll_number,email,course,semester from students order by id desc')->fetchAll();

function student_initials(string $name): string {
    $parts=preg_split('/\s+/',trim($name));
    if (!$parts || $parts[0]==='') return 'ST';
    $initials=strtoupper(substr($parts[0],0,1));
    if (count($parts)>1) $initials.=strtoupper(substr($parts[count($parts)-1],0,1));
    return $initials;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Student Management</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin-students.css">
</head>
<body class="students-page">
<div class="admin-layout">
    <aside class="sidebar admin-sidebar">
        <a class="brand" href="../index.php">Result <span>System</span></a>
        <nav aria-label="Admin navigation">
            <a href="dashboard.php">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>Dashboard</span>
            </a>
            <a class="active" href="students.php" aria-current="page">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Students</span>
            </a>
            <a href="results.php">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/><path d="M8 6h8M8 10h8M8 14h5"/></svg>
                <span>Results</span>
            </a>
            <a href="logout.php" class="logout-link">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <main class="admin-main students-main">
        <header class="students-heading">
            <div class="students-heading-copy">
                <p class="eyebrow">Administration</p>
                <h1>Student Management</h1>
                <p class="muted">Manage student profiles, academic details and account access from one place.</p>
            </div>
            <div class="students-heading-action">
                <button class="btn btn-primary" type="button" data-open-student-drawer>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Add Student</span>
                </button>
            </div>
        </header>

        <?php if($error): ?>
            <div class="student-alert error" role="alert">
                <span class="alert-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.8 2.5 17.2A2 2 0 0 0 4.2 20h15.6a2 2 0 0 0 1.7-2.8L13.7 3.8a2 2 0 0 0-3.4 0Z"/></svg></span>
                <span class="alert-copy"><?=e($error)?></span>
                <button class="alert-close" type="button" aria-label="Dismiss" data-dismiss-alert>×</button>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="student-alert success" role="status">
                <span class="alert-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></span>
                <span class="alert-copy"><?=e($success)?></span>
                <button class="alert-close" type="button" aria-label="Dismiss" data-dismiss-alert>×</button>
            </div>
        <?php endif; ?>

        <section class="panel student-list-panel" aria-labelledby="student-list-title">
            <div class="student-list-toolbar">
                <div class="student-list-title">
                    <h2 id="student-list-title">Students</h2>
                    <p><?php if($search!==''): ?>Showing <strong><?=count($students)?></strong> matching student<?=count($students)===1?'':'s'?><?php else: ?><strong><?=count($students)?></strong> student<?=count($students)===1?'':'s'?> registered<?php endif; ?></p>
                </div>
                <form class="student-search" method="get" role="search">
                    <div class="search-field">
                        <span class="search-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></span>
                        <input id="student-search" name="q" value="<?=e($search)?>" placeholder="Search by name or roll number" autocomplete="off" aria-label="Search students by name or roll number">
                        <?php if($search!==''): ?><a class="search-clear" href="students.php" aria-label="Clear search">×</a><?php endif; ?>
                    </div>
                    <button class="btn btn-secondary" type="submit">Search</button>
                </form>
            </div>

            <?php if($students): ?>
                <div class="student-table-wrap">
                    <table class="student-table">
                        <thead>
                            <tr><th>Student</th><th>Roll Number</th><th>Course</th><th>Semester</th><th aria-label="Actions"></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach($students as $s): ?>
                            <tr>
                                <td>
                                    <div class="student-cell">
                                        <span class="student-avatar" aria-hidden="true"><?=e(student_initials($s['name']))?></span>
                                        <span>
                                            <span class="student-name"><?=e($s['name'])?></span>
                                            <span class="student-email"><?=e($s['email'])?></span>
                                        </span>
                                    </div>
                                </td>
                                <td><span class="roll-number"><?=e($s['roll_number'])?></span></td>
                                <td><span class="course-text"><?=e($s['course'])?></span></td>
                                <td><span class="semester-badge">Semester <?=e((string)$s['semester'])?></span></td>
                                <td>
                                    <div class="student-actions">
                                        <a class="btn icon-btn edit" href="?edit=<?=$s['id']?>" aria-label="Edit <?=e($s['name'])?>" title="Edit student">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                                        </a>
                                        <button class="btn icon-btn delete" type="button" data-delete-student data-id="<?=$s['id']?>" data-name="<?=e($s['name'])?>" aria-label="Delete <?=e($s['name'])?>" title="Delete student">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 15H6L5 6M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="student-cards">
                    <?php foreach($students as $s): ?>
                        <article class="student-card">
                            <div class="student-card-top">
                                <div class="student-cell">
                                    <span class="student-avatar" aria-hidden="true"><?=e(student_initials($s['name']))?></span>
                                    <span>
                                        <span class="student-name"><?=e($s['name'])?></span>
                                        <span class="student-email"><?=e($s['email'])?></span>
                                    </span>
                                </div>
                            </div>
                            <div class="student-card-meta">
                                <span class="semester-badge"><?=e($s['roll_number'])?></span>
                                <span class="semester-badge"><?=e($s['course'])?></span>
                                <span class="semester-badge">Semester <?=e((string)$s['semester'])?></span>
                            </div>
                            <div class="student-card-actions">
                                <a class="btn btn-secondary" href="?edit=<?=$s['id']?>">Edit Student</a>
                                <button class="btn danger" type="button" data-delete-student data-id="<?=$s['id']?>" data-name="<?=e($s['name'])?>">Delete</button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="students-empty">
                    <span class="empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg></span>
                    <?php if($search!==''): ?>
                        <h3>No students found</h3>
                        <p>We couldn't find a student matching “<?=e($search)?>”. Try a different name or roll number.</p>
                        <a class="btn btn-secondary" href="students.php">Clear Search</a>
                    <?php else: ?>
                        <h3>No students yet</h3>
                        <p>Add your first student to start managing academic records.</p>
                        <button class="btn btn-primary" type="button" data-open-student-drawer>Add Student</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<div class="drawer-backdrop<?= $editStudent ? ' open' : '' ?>" data-drawer-backdrop></div>
<aside class="student-drawer<?= $editStudent ? ' open' : '' ?>" data-student-drawer aria-hidden="<?= $editStudent ? 'false' : 'true' ?>" aria-labelledby="drawer-title">
    <div class="drawer-header">
        <div>
            <p class="eyebrow"><?= $editStudent ? 'Student record' : 'New record' ?></p>
            <h2 id="drawer-title"><?= $editStudent ? 'Edit Student' : 'Add New Student' ?></h2>
            <p><?= $editStudent ? 'Update the student profile and academic details.' : 'Create a student account and academic profile.' ?></p>
        </div>
        <button class="drawer-close" type="button" data-close-student-drawer aria-label="Close student form">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
        </button>
    </div>
    <form class="drawer-body" method="post" id="student-form">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?=e((string)($editStudent['id']??''))?>">
        <section class="drawer-section">
            <h3>Student Information</h3>
            <div class="drawer-grid">
                <div class="drawer-field full"><label for="student-name">Full Name</label><input id="student-name" name="name" required value="<?=e($editStudent['name']??'')?>" placeholder="Enter student's full name"></div>
                <div class="drawer-field"><label for="student-roll">Roll Number</label><input id="student-roll" name="roll_number" required value="<?=e($editStudent['roll_number']??'')?>" placeholder="e.g. BCA2026001"></div>
                <div class="drawer-field"><label for="student-email">Email Address</label><input id="student-email" type="email" name="email" required value="<?=e($editStudent['email']??'')?>" placeholder="student@example.com"></div>
            </div>
        </section>
        <section class="drawer-section">
            <h3>Academic Information</h3>
            <div class="drawer-grid">
                <div class="drawer-field"><label for="student-course">Course</label><input id="student-course" name="course" required value="<?=e($editStudent['course']??'BCA')?>" placeholder="e.g. BCA"></div>
                <div class="drawer-field"><label for="student-semester">Semester</label><select id="student-semester" name="semester"><?php for($i=1;$i<=12;$i++): ?><option value="<?=$i?>" <?=((int)($editStudent['semester']??4)===$i)?'selected':''?>>Semester <?=$i?></option><?php endfor; ?></select></div>
            </div>
        </section>
        <section class="drawer-section">
            <h3>Account Security</h3>
            <div class="drawer-field">
                <label for="student-password"><?= $editStudent ? 'New Password' : 'Password' ?></label>
                <input id="student-password" type="password" name="password" <?= $editStudent ? '' : 'placeholder="Set a password or leave blank for the system default"' ?> autocomplete="new-password">
                <span class="field-hint"><?= $editStudent ? 'Leave blank to keep the current password.' : 'For this project, leaving the field blank keeps the existing default-password behavior.' ?></span>
            </div>
        </section>
    </form>
    <div class="drawer-footer">
        <button class="btn btn-secondary" type="button" data-close-student-drawer>Cancel</button>
        <button class="btn btn-primary" type="submit" form="student-form"><?= $editStudent ? 'Save Changes' : 'Add Student' ?></button>
    </div>
</aside>

<div class="confirm-backdrop" data-confirm-backdrop aria-hidden="true">
    <div class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
        <span class="confirm-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.8 2.5 17.2A2 2 0 0 0 4.2 20h15.6a2 2 0 0 0 1.7-2.8L13.7 3.8a2 2 0 0 0-3.4 0Z"/></svg></span>
        <h3 id="confirm-title">Delete student?</h3>
        <p data-confirm-message>This will permanently remove the student and their associated result. This action cannot be undone.</p>
        <div class="confirm-actions">
            <button class="btn btn-secondary" type="button" data-cancel-delete>Cancel</button>
            <form method="post" id="delete-form">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="" id="delete-student-id">
                <button class="btn btn-danger-solid" type="submit">Delete Student</button>
            </form>
        </div>
    </div>
</div>

<script>
(function(){
    const body=document.body;
    const drawer=document.querySelector('[data-student-drawer]');
    const backdrop=document.querySelector('[data-drawer-backdrop]');
    const confirmBackdrop=document.querySelector('[data-confirm-backdrop]');
    const deleteId=document.getElementById('delete-student-id');
    const confirmMessage=document.querySelector('[data-confirm-message]');
    const firstField=document.getElementById('student-name');
    const closeDrawer=()=>{
        if(!drawer) return;
        drawer.classList.remove('open');
        backdrop.classList.remove('open');
        drawer.setAttribute('aria-hidden','true');
        body.classList.remove('drawer-open');
    };
    const openDrawer=()=>{
        if(!drawer) return;
        drawer.classList.add('open');
        backdrop.classList.add('open');
        drawer.setAttribute('aria-hidden','false');
        body.classList.add('drawer-open');
        window.setTimeout(()=>firstField && firstField.focus(),220);
    };
    document.querySelectorAll('[data-open-student-drawer]').forEach(btn=>btn.addEventListener('click',openDrawer));
    document.querySelectorAll('[data-close-student-drawer]').forEach(btn=>btn.addEventListener('click',closeDrawer));
    if(backdrop) backdrop.addEventListener('click',closeDrawer);

    const closeConfirm=()=>{
        confirmBackdrop.classList.remove('open');
        confirmBackdrop.setAttribute('aria-hidden','true');
    };
    document.querySelectorAll('[data-delete-student]').forEach(btn=>btn.addEventListener('click',()=>{
        deleteId.value=btn.dataset.id||'';
        confirmMessage.textContent='Delete '+(btn.dataset.name||'this student')+'? This will permanently remove the student and their associated result. This action cannot be undone.';
        confirmBackdrop.classList.add('open');
        confirmBackdrop.setAttribute('aria-hidden','false');
        const cancel=confirmBackdrop.querySelector('[data-cancel-delete]');
        window.setTimeout(()=>cancel && cancel.focus(),40);
    }));
    document.querySelector('[data-cancel-delete]')?.addEventListener('click',closeConfirm);
    confirmBackdrop?.addEventListener('click',e=>{if(e.target===confirmBackdrop) closeConfirm();});

    document.querySelectorAll('[data-dismiss-alert]').forEach(btn=>btn.addEventListener('click',()=>btn.closest('.student-alert')?.remove()));
    document.querySelectorAll('.student-alert.success').forEach(alert=>window.setTimeout(()=>alert.remove(),5000));

    document.addEventListener('keydown',e=>{
        if(e.key==='Escape'){
            if(confirmBackdrop?.classList.contains('open')) closeConfirm();
            else if(drawer?.classList.contains('open')) closeDrawer();
        }
    });
})();
</script>
</body>
</html>
