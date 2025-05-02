<?php
session_start();
ini_set('display_errors', 0); // Disable errors for production
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db_connect.php';

// Log start of profile access
$details = "Profile access attempt";
$log_stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (0, 'student', 'Profile access', ?)");
$log_stmt->bind_param("s", $details);
$log_stmt->execute();
$log_stmt->close();

// Validate session
if (isset($_SESSION['admin_id'])) {
    $details = "Session conflict: admin_id {$_SESSION['admin_id']} and student_id " . (isset($_SESSION['student_id']) ? $_SESSION['student_id'] : 'unset') . " present";
    $log_stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (0, 'student', 'Profile access failed', ?)");
    $log_stmt->bind_param("s", $details);
    $log_stmt->execute();
    $log_stmt->close();
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['student_id']) || !is_numeric($_SESSION['student_id']) || $_SESSION['student_id'] <= 0) {
    $details = "Invalid or missing student_id in session: " . (isset($_SESSION['student_id']) ? $_SESSION['student_id'] : 'unset');
    $log_stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (0, 'student', 'Profile access failed', ?)");
    $log_stmt->bind_param("s", $details);
    $log_stmt->execute();
    $log_stmt->close();
    header("Location: login.php");
    exit;
}

$student_id = (int)$_SESSION['student_id'];

// Log student ID
$details = "Fetching profile for student ID $student_id";
$log_stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'student', 'Profile fetch', ?)");
$log_stmt->bind_param("is", $student_id, $details);
$log_stmt->execute();
$log_stmt->close();

// Fetch student details with prepared statement
$stmt = $db->prepare("SELECT id, full_name, email, phone, dob, gender, city, university, filiere, level_id, status, created_at, latitude, longitude, session_status FROM students WHERE id = ? AND is_archived = 0");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->num_rows > 0 ? $result->fetch_assoc() : null;
$stmt->close();

// Store student data in session to preserve it
$_SESSION['student_data'] = $student;

// Log query result
$details = $student ? "Successfully fetched student ID $student_id" : "Failed to fetch student ID $student_id: No data or archived";
$log_stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'student', 'Profile query', ?)");
$log_stmt->bind_param("is", $student_id, $details);
$log_stmt->execute();
$log_stmt->close();

if (!$student) {
    header("Location: logout.php");
    exit;
}

// Fetch level name
$level = 'N/A';
if (!empty($student['level_id'])) {
    $stmt = $db->prepare("SELECT name FROM levels WHERE id = ? AND is_archived = 0");
    $stmt->bind_param("i", $student['level_id']);
    $stmt->execute();
    $level_result = $stmt->get_result();
    if ($level_result->num_rows > 0) {
        $level = $level_result->fetch_assoc()['name'];
    }
    $stmt->close();
}

// Function to get location name from coordinates
function getLocationName($lat, $lon) {
    if (!$lat || !$lon) return 'N/A';
    $url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json&zoom=16&addressdetails=1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZouhairElearning/1.0');
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if (isset($data['address'])) {
        $address = $data['address'];
        $neighborhood = $address['suburb'] ?? $address['neighbourhood'] ?? '';
        $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? '';
        if ($neighborhood && $city) return "$neighborhood, $city";
        elseif ($city) return $city;
        elseif ($address['country']) return $address['country'];
    }
    return 'Inconnu';
}

// Handle profile update
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $university = trim($_POST['university'] ?? '');
    $filiere = trim($_POST['filiere'] ?? '');

    // Validation
    if (empty($full_name)) {
        $errors[] = "Le nom complet est requis.";
    } elseif (strlen($full_name) > 100 || !preg_match('/^[a-zA-Z\s\'-]+$/', $full_name)) {
        $errors[] = "Le nom complet doit contenir jusqu'à 100 caractères alphanumériques, espaces, apostrophes ou tirets.";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email est invalide.";
    } else {
        // Check email uniqueness (excluding current student)
        $stmt = $db->prepare("SELECT id FROM students WHERE email = ? AND id != ? AND is_archived = 0");
        $stmt->bind_param("si", $email, $student_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        $stmt->close();
    }

    if (!empty($phone) && !preg_match('/^[0-9+()-]{7,20}$/', $phone)) {
        $errors[] = "Le numéro de téléphone doit contenir 7 à 20 chiffres, avec +, -, ou () si nécessaire.";
    }

    if (!empty($dob)) {
        $dob_date = DateTime::createFromFormat('Y-m-d', $dob);
        $today = new DateTime();
        if (!$dob_date || $dob_date > $today) {
            $errors[] = "La date de naissance est invalide ou dans le futur.";
        }
    }

    if (!empty($gender) && !in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Le genre doit être Homme, Femme ou Autre.";
    }

    if (!empty($city) && (strlen($city) > 100 || !preg_match('/^[a-zA-Z\s\'-]+$/', $city))) {
        $errors[] = "La ville doit contenir jusqu'à 100 caractères alphanumériques, espaces, apostrophes ou tirets.";
    }

    if (!empty($university) && (strlen($university) > 100 || !preg_match('/^[a-zA-Z\s\'-]+$/', $university))) {
        $errors[] = "L'université doit contenir jusqu'à 100 caractères alphanumériques, espaces, apostrophes ou tirets.";
    }

    if (!empty($filiere) && (strlen($filiere) > 100 || !preg_match('/^[a-zA-Z\s\'-]+$/', $filiere))) {
        $errors[] = "La filière doit contenir jusqu'à 100 caractères alphanumériques, espaces, apostrophes ou tirets.";
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            UPDATE students 
            SET full_name = ?, email = ?, phone = ?, dob = ?, gender = ?, city = ?, university = ?, filiere = ?
            WHERE id = ? AND is_archived = 0
        ");
        $dob = $dob ?: null; // Convert empty string to NULL
        $stmt->bind_param(
            "ssssssssi",
            $full_name,
            $email,
            $phone,
            $dob,
            $gender,
            $city,
            $university,
            $filiere,
            $student_id
        );
        if ($stmt->execute()) {
            $success = "Profil mis à jour avec succès !";
            // Update session data
            $_SESSION['student_data'] = [
                'id' => $student_id,
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'dob' => $dob,
                'gender' => $gender,
                'city' => $city,
                'university' => $university,
                'filiere' => $filiere,
                'level_id' => $student['level_id'],
                'status' => $student['status'],
                'created_at' => $student['created_at'],
                'latitude' => $student['latitude'],
                'longitude' => $student['longitude'],
                'session_status' => $student['session_status']
            ];
            $student = $_SESSION['student_data'];
            // Log activity
            $details = "Updated profile for student ID $student_id";
            $log_stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'student', 'Updated profile', ?)");
            $log_stmt->bind_param("is", $student_id, $details);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            $errors[] = "Erreur lors de la mise à jour du profil : " . $db->error;
        }
        $stmt->close();
    }
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "Tous les champs de mot de passe sont requis.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Les nouveaux mots de passe ne correspondent pas.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
    } else {
        // Verify old password
        $stmt = $db->prepare("SELECT password FROM students WHERE id = ? AND is_archived = 0");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_password = $result->fetch_assoc()['password'] ?? '';
        $stmt->close();

        if (!password_verify($old_password, $current_password)) {
            $errors[] = "L'ancien mot de passe est incorrect.";
        } else {
            // Update password
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE students SET password = ? WHERE id = ? AND is_archived = 0");
            $stmt->bind_param("si", $new_password_hash, $student_id);
            if ($stmt->execute()) {
                $success = "Mot de passe mis à jour avec succès !";
                // Log activity
                $details = "Changed password for student ID $student_id";
                $log_stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, 'student', 'Changed password', ?)");
                $log_stmt->bind_param("is", $student_id, $details);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                $errors[] = "Erreur lors de la mise à jour du mot de passe : " . $db->error;
            }
            $stmt->close();
        }
    }
}
// Define dropdown options
$cities = [
    'Agadir', 'Al Hoceima', 'Azilal', 'Beni Mellal', 'Casablanca', 'Chefchaouen', 'El Jadida', 'Fes',
    'Ifrane', 'Kenitra', 'Marrakech', 'Meknes', 'Ouarzazate', 'Rabat', 'Salé', 'Tangier', 'Taza',
    'Tétouan', 'Taroudant', 'Safi', 'Nador', 'Oujda', 'Dakhla', 'Laâyoune', 'Errachidia', 'Guelmim',
    'Tinghir', 'El Kelaâ des Sraghna', 'Settat', 'Ksar el-Kébir', 'Ouezzane', 'Berkane', 'Midelt',
    'Figuig', 'Tantan', 'Sidi Kacem', 'Tiznit', 'Chichaoua', 'Boudnib', 'Sidi Ifni', 'Benslimane', 'Boujdour'
];
$universities = [
    'Université Abdelmalek Essaadi' => 'Université Abdelmalek Essaadi (Tétouan, Tanger)',
    'Université Al Akhawayn' => 'Université Al Akhawayn (Ifrane)',
    'Université Cadi Ayyad' => 'Université Cadi Ayyad (Marrakech)',
    'Université Chouaib Doukkali' => 'Université Chouaib Doukkali (El Jadida)',
    'Université Euro-Méditerranéenne de Fès' => 'Université Euro-Méditerranéenne de Fès (Fès)',
    'Université Hassan I' => 'Université Hassan I (Settat)',
    'Université Hassan II de Casablanca' => 'Université Hassan II de Casablanca (Casablanca)',
    'Université Ibn Tofail' => 'Université Ibn Tofail (Kénitra)',
    'Université Ibn Zohr' => 'Université Ibn Zohr (Agadir)',
    'Université Internationale de Casablanca' => 'Université Internationale de Casablanca (Casablanca)',
    'Université Internationale de Rabat' => 'Université Internationale de Rabat (Rabat)',
    'Université Mohammed V' => 'Université Mohammed V (Rabat)',
    'Université Mohammed VI Polytechnique' => 'Université Mohammed VI Polytechnique (Benguerir)',
    'Université Moulay Ismail' => 'Université Moulay Ismail (Meknès)',
    'Université Sidi Mohamed Ben Abdellah' => 'Université Sidi Mohamed Ben Abdellah (Fès)',
    'Université Al Quaraouiyyin' => 'Université Al Quaraouiyyin (Fès)',
    'ENSIAS' => 'ENSIAS (Rabat)',
    'EHTP' => 'EHTP (Casablanca)',
    'ENSEM' => 'ENSEM (Casablanca)',
    'INSEA' => 'INSEA (Rabat)',
    'EMI' => 'EMI (Rabat)',
    'ISCAE' => 'ISCAE (Casablanca)',
    'ENCG' => 'ENCG (Diverses Villes)',
    'ENSA' => 'ENSA (Diverses Villes)',
    'EST' => 'EST (Diverses Villes)',
    'Autre' => 'Autre'
];
$filieres = [
    'Informatique', 'Génie Civil', 'Génie Électrique', 'Génie Mécanique', 'Génie Industriel', 'Médecine',
    'Pharmacie', 'Sciences Économiques', 'Gestion', 'Commerce International', 'Droit', 'Sciences Politiques',
    'Mathématiques', 'Physique', 'Chimie', 'Biologie', 'Géologie', 'Architecture', 'Agriculture',
    'Sciences de l’Éducation', 'Langues et Littératures', 'Études Islamiques', 'Psychologie', 'Sociologie',
    'Tourisme et Hôtellerie', 'Autre'
];

// Determine if university or filiere is custom
$is_custom_university = !array_key_exists($student['university'], $universities) && $student['university'];
$is_custom_filiere = !in_array($student['filiere'], $filieres) && $student['filiere'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Zouhair E-Learning</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .profile-container { max-width: 800px; margin: 20px auto; padding: 20px; background: #f9f9f9; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .profile-container h2 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        .form-group input[type="date"] { padding: 7px; }
        .form-group select { width: 100%; padding: 12px 40px; border: none; border-bottom: 2px solid #d1d5db; border-radius: 8px; font-size: 16px;   box-sizing: border-box; appearance: none; -webkit-appearance: none; -moz-appearance: none; background: #fff; }        .form-actions { margin-top: 20px; display: flex; gap: 10px; }
        .btn-action { padding: 10px 20px; background: #4CAF50; color: #fff; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        .btn-action:hover { background: #45a049; }
        .info-card { margin: 20px 0; padding: 15px; background: #e0f7fa; border-radius: 5px; }
        .info-card p { margin: 5px 0; }
        .error, .success { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e9; color: #2e7d32; }
    </style>
</head>
<body>
    <?php include '../includes/student_header.php'; ?>
    <main class="dashboard">
        <h1><i class="fas fa-user"></i> Mon Profil</h1>
        <div class="profile-container">
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Profile Information Form -->
            <h2><i class="fas fa-user-edit"></i> Informations Personnelles</h2>
            <?php if (empty($_SESSION['student_data'])): ?>
                <div class="error">Erreur : Impossible de charger les données du profil.</div>
            <?php else: ?>
                <?php $student = $_SESSION['student_data']; ?>
                <form method="POST" class="edit-form">
                    <input type="hidden" name="update_profile" value="1">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom Complet</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Téléphone</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>" placeholder="Ex: +212612345678">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Date de Naissance</label>
                        <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob'] ?? ''); ?>">
                    </div>
                    <div class="form-group" > 
                        <label><i class="fas fa-venus-mars"></i> Genre</label>
                        <select name="gender">
                            <option value="">Sélectionner</option>
                            <option value="Male" <?php echo ($student['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Homme</option>
                            <option value="Female" <?php echo ($student['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Femme</option>
                            <option value="Other" <?php echo ($student['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                    <div class="form-group" style="padding: 0;">
                        <label for="city"><i class="fas fa-city"></i> Ville</label>
                        <select name="city" id="city">
                            <option value="">Sélectionnez une ville</option>
                            <?php foreach ($cities as $city_option): ?>
                                <option value="<?php echo htmlspecialchars($city_option); ?>" <?php echo ($student['city'] ?? '') == $city_option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city_option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="padding: 0;">
                        <label for="university"><i class="fas fa-university"></i> Université</label>
                        <select name="university" id="university" onchange="toggleCustomInput('university')">
                            <option value="">Sélectionnez une université</option>
                            <?php foreach ($universities as $key => $value): ?>
                                <option value="<?php echo htmlspecialchars($key); ?>" <?php echo ($student['university'] ?? '') == $key ? 'selected' : ($is_custom_university && $key === 'Autre' ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($value); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="custom_university" id="custom_university" class="<?php echo $is_custom_university ? '' : 'hidden'; ?>" value="<?php echo $is_custom_university ? htmlspecialchars($student['university']) : ''; ?>" placeholder="Entrez le nom de l’université">
                    </div>
                    <div class="form-group">
                        <label for="filiere"><i class="zmdi zmdi-graduation-cap"></i></label>
                        <select name="filiere" id="filiere" onchange="toggleCustomInput('filiere')">
                            <option value="">Sélectionnez une filière</option>
                            <?php foreach ($filieres as $filiere_option): ?>
                                <option value="<?php echo htmlspecialchars($filiere_option); ?>" <?php echo ($student['filiere'] ?? '') == $filiere_option ? 'selected' : ($is_custom_filiere && $filiere_option === 'Autre' ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($filiere_option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="custom_filiere" id="custom_filiere" class="<?php echo $is_custom_filiere ? '' : 'hidden'; ?>" value="<?php echo $is_custom_filiere ? htmlspecialchars($student['filiere']) : ''; ?>" placeholder="Entrez le nom de la filière">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-action"><i class="fas fa-save"></i> Enregistrer</button>
                    </div>
                </form>            <?php endif; ?>

            <!-- Password Change Form -->
            <h2><i class="fas fa-lock"></i> Changer le Mot de Passe</h2>
            <form method="POST" class="edit-form">
                <input type="hidden" name="update_password" value="1">
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Ancien Mot de Passe</label>
                    <input type="password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Nouveau Mot de Passe</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Confirmer le Nouveau Mot de Passe</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-action"><i class="fas fa-save"></i> Mettre à Jour</button>
                </div>
            </form>

            <!-- Non-Editable Information -->
            <h2><i class="fas fa-info-circle"></i> Informations du Compte</h2>
            <div class="info-card">
                <p><strong><i class="fas fa-layer-group"></i> Niveau :</strong> <?php echo htmlspecialchars($level); ?></p>
                <p><strong><i class="fas fa-check-circle"></i> Statut :</strong> 
                    <?php echo htmlspecialchars(isset($student['status']) && $student['status'] == 'approved' ? 'Approuvé' : (isset($student['status']) && $student['status'] == 'pending' ? 'En attente' : 'Rejeté')); ?>
                </p>
                <p><strong><i class="fas fa-clock"></i> Inscrit le :</strong> 
                    <?php echo isset($student['created_at']) ? date('d/m/Y H:i', strtotime($student['created_at'])) : 'Non disponible'; ?>
                </p>
                <p><strong><i class="fas fa-map-marker-alt"></i> Dernière Position :</strong> 
                    <?php echo htmlspecialchars(getLocationName($student['latitude'] ?? null, $student['longitude'] ?? null)); ?>
                </p>
                <p><strong><i class="fas fa-user-shield"></i> Statut de la Session :</strong> 
                    <?php echo isset($student['session_status']) ? htmlspecialchars($student['session_status'] == 'active' ? 'Active' : 'Inactive') : 'Non disponible'; ?>
                </p>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Fade-in animation
            $('.profile-container').fadeIn(500);

            // Inactivity and tab switch detection
            let inactivityTimer;
            const INACTIVITY_TIMEOUT = 1800000; // 30 minutes in milliseconds

            // Reset timer on activity
            function resetInactivityTimer() {
                clearTimeout(inactivityTimer);
                inactivityTimer = setTimeout(logout, INACTIVITY_TIMEOUT);
            }

            // Logout function
            function logout() {
                window.location.href = 'logout.php';
            }

            // Track activity on profile page
            $(document).on('mousemove keydown', function() {
                resetInactivityTimer();
            });

            // Track tab visibility
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Tab is hidden, start checking for activity in other tabs
                    startActivityCheck();
                } else {
                    // Tab is visible, reset timer and stop activity check
                    resetInactivityTimer();
                    stopActivityCheck();
                }
            });

            let activityCheckInterval;
            function startActivityCheck() {
                activityCheckInterval = setInterval(function() {
                    $.ajax({
                        url: 'check_activity.php',
                        method: 'POST',
                        data: { student_id: <?php echo $student_id; ?> },
                        success: function(response) {
                            if (response.isActive) {
                                // Activity detected in another tab, start logout timer
                                clearTimeout(inactivityTimer);
                                inactivityTimer = setTimeout(logout, INACTIVITY_TIMEOUT);
                            }
                        },
                        error: function() {
                            console.error('Error checking activity');
                        }
                    });
                }, 5000); // Check every 5 seconds
            }

            function stopActivityCheck() {
                clearInterval(activityCheckInterval);
            }

            // Initialize timer
            resetInactivityTimer();
        });
    </script>
    <?php unset($_SESSION['student_data']); ?>
</body>
</html> 