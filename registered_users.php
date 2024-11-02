<?php
include 'db_connection.php';

// users variable indexes
$users = [];

// search variable
$searchValue = '';

// Check if a search term is submitted
if (isset($_POST['search'])) {
    $searchValue = $_POST['search_value'];

    // Fetch users from the database based on the search term
    $query = "SELECT id, username, email, role FROM users WHERE id LIKE :search_value OR username LIKE :search_value OR email LIKE :search_value OR role LIKE :search_value";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['search_value' => '%' . $searchValue . '%']);
} else {
    // Fetch all users if no search term is provided
    $query = "SELECT id, username, email, role FROM users";
    $stmt = $pdo->query($query);
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update the username if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $new_username = $_POST['username'];

    // Check if the new username already exists
    $checkQuery = "SELECT COUNT(*) FROM users WHERE username = :username AND id != :id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute(['username' => $new_username, 'id' => $user_id]);
    $count = $checkStmt->fetchColumn();

    if ($count == 0) {
        // Update the username in the database
        $updateQuery = "UPDATE users SET username = :username WHERE id = :id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(['username' => $new_username, 'id' => $user_id]);
        echo "<div class='alert alert-success'>Username updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>This username is already taken.</div>";
    }

    // Refresh the list of users after the update
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file if needed -->
</head>

<body>
    <?php include 'admin_sidebar.php'; ?>

    <div class="content container mt-4">
        <h1 class="mb-4">Users List</h1>

        <!-- Search Button -->
        <form method="POST" class="mb-4">
            <div class="input-group col-md-6">
                <input type="text" name="search_value" value="<?= htmlspecialchars($searchValue); ?>" class="form-control" placeholder="Search by Acc. ID, Username, Email, or Account Type" aria-label="Search" required>
                <div class="input-group-append">
                    <button type="submit" name="search" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <!-- Refresh Button -->
        <form method="POST" class="mb-4">
            <button type="submit" class="btn btn-info" name="refresh">Refresh</button>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>Acc. ID</th>
                        <th>Username</th>
                        <th>Parent Email</th>
                        <th>Account Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']); ?></td>
                            <td>
                                <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="form-control" readonly required>
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
                            </td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['role']); ?></td>
                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateModal-<?= htmlspecialchars($user['id']); ?>">
                                    Update
                                </button>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#viewModal-<?= htmlspecialchars($user['id']); ?>">
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for Viewing User Profile -->
                        <div class="modal fade" id="viewModal-<?= htmlspecialchars($user['id']); ?>" tabindex="-1" aria-labelledby="viewModalLabel-<?= htmlspecialchars($user['id']); ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewModalLabel-<?= htmlspecialchars($user['id']); ?>">User Profile: <?= htmlspecialchars($user['username']); ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        // Fetch profile data for this user
                                        $username = $user['username'];
                                        $profileStmt = $pdo->prepare("SELECT * FROM profile1 WHERE username = :username");
                                        $profileStmt->execute([':username' => $username]);
                                        $profile = $profileStmt->fetch(PDO::FETCH_ASSOC);
                                        ?>

                                        <div id="modalPage1-<?= htmlspecialchars($user['id']); ?>" style="display: block;">
                                            <form>
                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label>Last Name:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['last_name'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>First Name:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['first_name'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>Middle Name:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['middle_name'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>Birthdate:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['birthdate'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-1">
                                                        <label>Age:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['age'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label>Nationality:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['nationality'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>Gender:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['gender'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>Religion:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['religion'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>PWD ID:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['pwd_id'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-8">
                                                        <label>Address:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['address'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>Barangay:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['barangay'] ?? ''); ?>" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Diagnosis:</label>
                                                    <textarea class="form-control" readonly><?= htmlspecialchars($profile['diagnosis'] ?? ''); ?></textarea>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label>Petsa Ng Diagnosis:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['petsaNgDiagnosis'] ?? ''); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-5">
                                                        <label>Pangalan ng Developmental Pediatrician:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['DevelopPedia'] ?? ''); ?>" readonly>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label>Pangalan at Edad ng Kapatid:</label>
                                                        <input type="text" class="form-control" value="<?= htmlspecialchars($profile['Kapatid'] ?? ''); ?>" readonly>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>

                                        <!-- Placeholder for dynamic page content -->
                                        <div id="modalPage2-<?= htmlspecialchars($user['id']); ?>" style="display: none;">
                                            <form>
                                                <div class="row">
                                                    <!-- Pangalan ng Ina -->
                                                    <div class="form-group col-md-4">
                                                        <label for="Ina">Buong Pangalan ng Ina</label>
                                                        <input type="text" id="Ina" name="Ina" class="form-control" value="<?= htmlspecialchars($profile['ina_name'] ?? '') ?>" disabled>
                                                    </div>

                                                    <!-- Contact Number ng Ina -->
                                                    <div class="form-group col-md-4">
                                                        <label for="no_Ina">Contact Number ng Ina</label>
                                                        <input type="text" id="no_Ina" name="no_Ina" class="form-control" value="<?= htmlspecialchars($profile['ina_contact'] ?? '') ?>" disabled>
                                                    </div>

                                                    <!-- Hanap Buhay ng Ina -->
                                                    <div class="form-group col-md-3">
                                                        <label for="job_Ina">Hanap Buhay ng Ina</label>
                                                        <input type="text" id="job_Ina" name="job_Ina" class="form-control" value="<?= htmlspecialchars($profile['ina_job'] ?? '') ?>" disabled>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- Pangalan ng Ama -->
                                                    <div class="form-group col-md-4">
                                                        <label for="Ama">Buong Pangalan ng Ama</label>
                                                        <input type="text" id="Ama" name="Ama" class="form-control" value="<?= htmlspecialchars($profile['ama_name'] ?? '') ?>" disabled>
                                                    </div>

                                                    <!-- Contact Number ng Ama -->
                                                    <div class="form-group col-md-4">
                                                        <label for="no_Ama">Contact Number ng Ama</label>
                                                        <input type="text" id="no_Ama" name="no_Ama" class="form-control" value="<?= htmlspecialchars($profile['ama_contact'] ?? '') ?>" disabled>
                                                    </div>

                                                    <!-- Hanap Buhay ng Ama -->
                                                    <div class="form-group col-md-3">
                                                        <label for="job_Ama">Hanap Buhay ng Ama</label>
                                                        <input type="text" id="job_Ama" name="job_Ama" class="form-control" value="<?= htmlspecialchars($profile['ama_job'] ?? '') ?>" disabled>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- Parehong magulang ay may Trabaho -->
                                                    <div class="form-group col-md-6">
                                                        <label>Parehong magulang ay may Trabaho</label><br>
                                                        <input type="radio" id="Oo" name="trabaho" value="Oo" <?= ($profile['trabaho'] == 'Oo') ? 'checked' : '' ?> disabled>
                                                        <label for="Oo">Oo</label><br>
                                                        <input type="radio" id="Hindi" name="trabaho" value="Hindi" <?= ($profile['trabaho'] == 'Hindi') ? 'checked' : '' ?> disabled>
                                                        <label for="Hindi">Hindi</label><br>
                                                        <input type="radio" id="SoloParent" name="trabaho" value="SoloParent" <?= ($profile['trabaho'] == 'SoloParent') ? 'checked' : '' ?> disabled>
                                                        <label for="SoloParent">Solo Parent</label><br>
                                                    </div>

                                                    <!-- Buong Pangalan ng Tagapag-alaga -->
                                                    <div class="form-group col-md-6">
                                                        <label for="Tagapag_alaga_Name">(Sasagutin lamang kung ang mga magulang ang hindi pangunahing tagapag-alaga ng Kliyente).<br><br> Pangalan ng Tagapag-alaga</label>
                                                        <input type="text" id="Tagapag_alaga_Name" name="Tagapag_alaga_Name" class="form-control" value="<?= htmlspecialchars($profile['guardian_name'] ?? '') ?>" disabled>
                                                    </div>

                                                    <!-- Contact Number ng Tagapag-alaga -->
                                                    <div class="form-group col-md-6">
                                                        <label for="Tagapag_alaga_Contact">(Sasagutin lamang kung ang mga magulang ang hindi pangunahing tagapag-alaga ng Kliyente).<br><br> Contact Number ng Tagapag-alaga</label>
                                                        <input type="text" id="Tagapag_alaga_Contact" name="Tagapag_alaga_Contact" class="form-control" value="<?= htmlspecialchars($profile['guardian_contact'] ?? '') ?>" disabled>
                                                    </div>

                                                    <!-- Hanap Buhay ng Tagapag-alaga -->
                                                    <div class="form-group col-md-6">
                                                        <label for="Tagapag_alaga_HB">(Sasagutin lamang kung ang mga magulang ang hindi pangunahing tagapag-alaga ng Kliyente).<br><br> Hanap Buhay ng Tagapag-alaga</label>
                                                        <input type="text" id="Tagapag_alaga_HB" name="Tagapag_alaga_HB" class="form-control" value="<?= htmlspecialchars($profile['guardian_job'] ?? '') ?>" disabled>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label>Ano ang kasalukuyang estado ng relasyon ng mga biological na magulang?</label><br>
                                                        <?php
                                                        // Decode the JSON data into an associative array
                                                        $parentsStatus = json_decode($profile['parents_status'], true);

                                                        // Define an array of checkbox options
                                                        $checkboxes = [
                                                            'Kasal' => 'Kasal',
                                                            'LivingTogether' => 'Living Together (Common law relation)',
                                                            'Separated' => 'Separated',
                                                            'Annulled' => 'Annulled',
                                                            'Biyuda_o' => 'Biyuda/Biyudo',
                                                        ];

                                                        // Loop through the checkbox options
                                                        foreach ($checkboxes as $key => $label) {
                                                            $checked = (isset($parentsStatus[$key]) && $parentsStatus[$key]) ? 'checked' : '';
                                                            echo "<input type='checkbox' id='$key' name='parents_status[]' value='$key' $checked disabled>";
                                                            echo "<label for='$key'>$label</label><br>";
                                                        }
                                                        ?>
                                                    </div>

                                                    <!-- Taon ng Estado ng biological na magulang -->
                                                    <div class="form-group col-md-6">
                                                        <label for="estado_Taon">Ilang Taon ng nasa ganitong estado ang mga biological na magulang?</label>
                                                        <input type="text" id="estado_Taon" name="estado_Taon" class="form-control" value="<?= htmlspecialchars($profile['status_years'] ?? '') ?>" disabled>
                                                    </div>

                                                    <!-- Step-parents -->
                                                    <div class="form-group col-md-6">
                                                        <label for="Step_parents">Kung hiwalay ang mga magulang, paki lista ng pangalan ng step-parents.</label>
                                                        <input type="text" id="Step_parents" name="Step_parents" class="form-control" value="<?= htmlspecialchars($profile['step_parents'] ?? '') ?>" disabled>
                                                    </div>

                                                    <!-- Miyembro ng sumusunod -->
                                                    <div class="form-group col-md-6">
                                                        <label for="estado">Ang magulang/tagapangalaga ba ay miyembro ng mga sumusunod?</label><br>
                                                        <?php
                                                        // Decode the JSON data into an associative array
                                                        $parentsMember = json_decode($profile['parents_member'], true);
                                                        // Define an array of checkbox options
                                                        $checkboxes = [
                                                            'SSS' => 'SSS',
                                                            'PWD' => 'PWD',
                                                            '4Ps' => '4Ps',
                                                            'SeniorCitizen' => 'Senior Citizen',
                                                            'PAGIBIG' => 'PAG IBIG',
                                                            'GSIS' => 'GSIS',
                                                            'SoloParent' => 'Solo Parent',
                                                        ];
                                                        // Loop through the checkbox options
                                                        foreach ($checkboxes as $key => $label) {
                                                            $checked = isset($parentsMember[$key]) && $parentsMember[$key] ? 'checked' : '';
                                                            echo "<input type='checkbox' id='$key' name='$key' $checked disabled>";
                                                            echo "<label for='$key'>$label</label><br>";
                                                        }
                                                        ?>
                                                        <div style="display: flex; align-items: center;">
                                                            <input type='checkbox' id='otherCheckbox' name='parents_member[Other]' onclick="toggleOtherCheckboxInput()" <?= isset($parentsMember['Other']) && $parentsMember['Other'] ? 'checked' : ''; ?> disabled>
                                                            <label for='otherCheckbox'>Other</label>
                                                            <input type='text' id='other_input' name='other_input' class='form-control' value='<?= htmlspecialchars($parentsMember['Other'] ?? '') ?>' disabled style='display: <?= isset($parentsMember['Other']) && $parentsMember['Other'] ? "block" : "none"; ?>;'>
                                                        </div>
                                                    </div>

                                                    <!-- Buwanang Kita ng Pamilya -->
                                                    <div class="form-group col-md-6">
                                                        <label>Kabuuang Buwanang Kita ng Pamilya</label><br>
                                                        <input type="radio" id="L14k" name="kita" value="L14k" <?= ($profile['kita'] == 'L14k') ? 'checked' : '' ?> disabled>
                                                        <label for="L14k">Lower Than P14,000</label><br>
                                                        <input type="radio" id="P14k-P19k" name="kita" value="P14k-P19k" <?= ($profile['kita'] == 'P14k-P19k') ? 'checked' : '' ?> disabled>
                                                        <label for="P14k-P19k">P14,001 - P19,040</label><br>
                                                        <input type="radio" id="P19k-P38k" name="kita" value="P19k-P38k" <?= ($profile['kita'] == 'P19k-P38k') ? 'checked' : '' ?> disabled>
                                                        <label for="P19k-P38k">P19,041 - P38,080</label><br>
                                                        <input type="radio" id="P38k-P66k" name="kita" value="P38k-P66k" <?= ($profile['kita'] == 'P38k-P66k') ? 'checked' : '' ?> disabled>
                                                        <label for="P38k-P66k">P38,041 - P66,640</label><br>
                                                        <input type="radio" id="P66k-P114k" name="kita" value="P66k-P114k" <?= ($profile['kita'] == 'P66k-P114k') ? 'checked' : '' ?> disabled>
                                                        <label for="P66k-P114k">P66,541 - P114,240</label><br>
                                                        <input type="radio" id="P114k-P190k" name="kita" value="P114k-P190k" <?= ($profile['kita'] == 'P114k-P190k') ? 'checked' : '' ?> disabled>
                                                        <label for="P114k-P190k">P114,241 - P190,400</label><br>
                                                        <input type="radio" id="P190k" name="kita" value="P190k" <?= ($profile['kita'] == 'P190k') ? 'checked' : '' ?> disabled>
                                                        <label for="P190k">P190,401 and above</label><br>
                                                    </div>

                                                    <!-- SNED Allowance -->
                                                    <div class="form-group col-md-6">
                                                        <label>Kayo ba ay tumatanggap ng SNED allowance mula sa paaralan?</label><br>
                                                        <input type="radio" id="Oo" name="SNED" value="Oo" <?= ($profile['SNED'] == 'Oo') ? 'checked' : '' ?> disabled>
                                                        <label for="Oo">Oo</label><br>
                                                        <input type="radio" id="Hindi" name="SNED" value="Hindi" <?= ($profile['SNED'] == 'Hindi') ? 'checked' : '' ?> disabled>
                                                        <label for="Hindi">Hindi</label><br>
                                                    </div>

                                                    <!-- Teletherapy -->
                                                    <div class="form-group col-md-6">
                                                        <label>Resources para sa pagpapatupad ng teletherapy</label><br>
                                                        <input type="radio" id="Wifi" name="teletheraphy" value="Wifi" <?= ($profile['teletheraphy'] == 'Wifi') ? 'checked' : '' ?> disabled>
                                                        <label for="Wifi">Wifi</label><br>
                                                        <input type="radio" id="PD" name="teletheraphy" value="PD" <?= ($profile['teletheraphy'] == 'PD') ? 'checked' : '' ?> disabled>
                                                        <label for="PD">Prepaid Data (Cellphone)</label><br>
                                                        <input type="radio" id="PW" name="teletheraphy" value="PW" <?= ($profile['teletheraphy'] == 'PW') ? 'checked' : '' ?> disabled>
                                                        <label for="PW">Pocket Wifi</label><br>

                                                        <div style="display: flex; align-items: center;">
                                                            <input type="radio" id="otherRadio" name="teletheraphy" value="Other" <?= ($profile['teletheraphy'] == 'Other') ? 'checked' : '' ?> onclick="toggleOtherRadioInput()" disabled>
                                                            <label for="otherRadio">Other</label>
                                                            <input type="text" id="otherRadioInput" name="otherRadioInput" style="display: none; margin-left: 10px;" placeholder="Please specify" value="<?= htmlspecialchars($profile['otherRadioInput'] ?? '') ?>">
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>


                                    </div>
                                    <div class="modal-footer d-flex justify-content-between">
                                        <div>
                                            <button type="button" class="btn btn-info" onclick="showModalPage('modalPage1-<?= htmlspecialchars($user['id']); ?>')">1</button>
                                            <button type="button" class="btn btn-info" onclick="showModalPage('modalPage2-<?= htmlspecialchars($user['id']); ?>')">2</button>
                                            <button type="button" class="btn btn-info" onclick="showModalPage('modalPage3-<?= htmlspecialchars($user['id']); ?>')">3</button>
                                            <button type="button" class="btn btn-info" onclick="showModalPage('modalPage4-<?= htmlspecialchars($user['id']); ?>')">4</button>
                                            <button type="button" class="btn btn-info" onclick="showModalPage('modalPage5-<?= htmlspecialchars($user['id']); ?>')">5</button>
                                        </div>
                                        <button type="button" class="btn btn-danger ml-auto" onclick="showModalPage('modalPage1-<?= htmlspecialchars($user['id']); ?>');" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for Updating User Profile -->
                        <div class="modal fade" id="updateModal-<?= htmlspecialchars($user['id']); ?>" tabindex="-1" aria-labelledby="updateModalLabel-<?= htmlspecialchars($user['id']); ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel-<?= htmlspecialchars($user['id']); ?>">Update Username for <?= htmlspecialchars($user['username']); ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="">
                                            <div class="form-group">
                                                <label for="current_username">Current Username:</label>
                                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="new_username">New Username</label>
                                                <input type="text" class="form-control" name="username" required>
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

        </div>
    </div>
    </div>
    </div>

<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function showModalPage(pageId) {
        // Hide all modal pages
        const pages = document.querySelectorAll('[id^="modalPage"]');
        pages.forEach(page => {
            page.style.display = 'none';
        });

        // Show the specified page
        document.getElementById(pageId).style.display = 'block';
    }
</script>
</body>

</html>