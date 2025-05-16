<?php
session_start();

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$id_perm = (int)$_SESSION['user']['id_perm'];
if (!in_array($id_perm, [2, 3, 4, 5])) {
    header('Location: index.php');
    exit();
}

$languages = ['en' => 'English', 'fr' => 'Français', 'nl' => 'Nederlands'];
$selected_lang = $_GET['lang'] ?? 'fr';
if (!array_key_exists($selected_lang, $languages)) {
    $selected_lang = 'fr';
}

$translations = [
    'fr' => [
        'user_info' => 'Informations des utilisateurs',
        'id' => 'ID',
        'name' => 'Nom',
        'email' => 'Email',
        'grade' => 'Grade',
        'actions' => 'Actions',
        'modify' => 'Modifier',
        'delete' => 'Supprimer',
        'ticket_management' => 'Gestion des tickets',
        'title' => 'Titre',
        'status' => 'Statut',
        'view' => 'Voir',
        'add_patchnote' => 'Ajouter un patchnote',
        'content' => 'Contenu',
        'add' => 'Ajouter',
        'creator' => 'Créateur',
        'message' => 'Message',
        'created_at' => 'Créé le',
        'is_closed' => 'Statut',
        'closed_at' => 'Fermé le',
        'closed_by' => 'Fermé par',
        'open' => 'Ouvert',
        'closed' => 'Fermé',
        'archive_ticket' => 'Archive Ticket',
        'patch_note' => 'Notes de mise à jour',
        'permissions' => 'Permissions',
        'role' => 'Rôles',
        'ticket' => 'Tickets',
        'ticket_message' => 'Messages des tickets',
        'columns' => [
            'id' => 'ID',
            'creator' => 'Créateur',
            'ticket_name' => 'Nom du ticket',
            'message' => 'Bericht',
            'created_at' => 'Créé le',
            'is_closed' => 'Statut',
            'closed_at' => 'Fermé le',
            'closed_by' => 'Fermé par',
            'updated_at' => 'Mis à jour le',
            'deleted_at' => 'Supprimé le',
            'status' => 'Statut',
        ],
        'yes' => 'Oui',
        'no' => 'Non',
        'toggle_permission' => 'Changer la permission',
        'ticket_id' => 'ID du ticket',
        'description' => 'Description',
        'can_reply_ticket' => 'Peut répondre aux tickets',
        'can_ban_permanently' => 'Peut bannir définitivement',
        'can_ban_temporarily' => 'Peut bannir temporairement',
        'can_post_patchnotes' => 'Peut publier des patchnotes',
        'can_manage_users' => 'Peut gérer les utilisateurs',
        'can_view_reports' => 'Peut voir les rapports',
        'can_edit_roles' => 'Peut modifier les rôles',
        'can_delete_tickets' => 'Peut supprimer les tickets',
        'no_records_found' => 'Aucun enregistrement trouvé',
        'content' => 'Contenu',
        'title' => 'Titre',
        'admin_panel' => 'Panneau d\'administration',
        'status' => 'Statut',
        'role_columns' => [
            'id' => 'ID',
            'name' => 'Nom',
            'image_tag' => 'Tag de l\'image',
            'status' => 'Statut',
            'is_active' => 'Actif',
            'created_at' => 'Créé le',
            'updated_at' => 'Mis à jour le',
            'created_by' => 'Créé par',
            'updated_by' => 'Mis à jour par',
            'deleted_at' => 'Supprimé le',
            'autorisation' => 'Autorisation',
        ],
        'phone' => 'Téléphone',
        'total_tickets' => 'Total des tickets',
        'open_tickets' => 'Tickets ouverts',
        'closed_tickets' => 'Tickets fermés',
        'ban_user' => 'Bannir l\'utilisateur',
        'unban_user' => 'Débannir l\'utilisateur',
        'manage_role' => 'Gérer le rôle',
        'view_profile' => 'Voir le profil',
        'ban_user_modal_title' => 'Bannir l\'utilisateur',
        'ban_user_modal_reason' => 'Motif :',
        'ban_user_modal_select_duration' => 'Sélectionnez la durée du bannissement :',
        'ban_user_modal_cancel' => 'Annuler',
        'ban_user_modal_permanent' => 'Permanent',
        'manage_role_modal_title' => 'Gérer le rôle',
        'manage_role_modal_close' => 'Fermer',
        'view_profile_modal_title' => 'Voir le profil',
        'view_profile_modal_close' => 'Fermer',
        'view_message' => 'Voir le message',
        'ban_duration' => 'Durée du bannissement',
        'ban_reason' => 'Motif du bannissement',
        'expires_at' => 'Expire le',
        'view_details' => 'Voir les détails',
        'confirm' => 'Confirmer',
        'cancel' => 'Annuler',
        'edit' => 'Éditer',
        'save' => 'Enregistrer',
        'close' => 'Fermer',
        'view_user' => 'Voir l\'utilisateur',
        'view' => 'Voir',
        'ban' => 'Bannir',
        'role' => 'Rôle',
    ],
    'en' => [
        'user_info' => 'User Information',
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'grade' => 'Grade',
        'actions' => 'Actions',
        'modify' => 'Modify',
        'delete' => 'Delete',
        'ticket_management' => 'Ticket Management',
        'title' => 'Title',
        'status' => 'Status',
        'view' => 'View',
        'add_patchnote' => 'Add a Patchnote',
        'content' => 'Content',
        'add' => 'Add',
        'creator' => 'Creator',
        'message' => 'Message',
        'created_at' => 'Created At',
        'is_closed' => 'Status',
        'closed_at' => 'Closed At',
        'closed_by' => 'Closed By',
        'open' => 'Open',
        'closed' => 'Closed',
        'archive_ticket' => 'Archive Ticket',
        'patch_note' => 'Patch Notes',
        'permissions' => 'Permissions',
        'role' => 'Roles',
        'ticket' => 'Tickets',
        'ticket_message' => 'Ticket Messages',
        'columns' => [
            'id' => 'ID',
            'creator' => 'Creator',
            'ticket_name' => 'Ticket Name',
            'message' => 'Message',
            'created_at' => 'Created At',
            'is_closed' => 'Status',
            'closed_at' => 'Closed At',
            'closed_by' => 'Closed By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'status' => 'Status',
        ],
        'yes' => 'Yes',
        'no' => 'No',
        'toggle_permission' => 'Toggle Permission',
        'ticket_id' => 'Ticket ID',
        'description' => 'Description',
        'can_reply_ticket' => 'Can reply to tickets',
        'can_ban_permanently' => 'Can ban permanently',
        'can_ban_temporarily' => 'Can ban temporarily',
        'can_post_patchnotes' => 'Can post patchnotes',
        'can_manage_users' => 'Can manage users',
        'can_view_reports' => 'Can view reports',
        'can_edit_roles' => 'Can edit roles',
        'can_delete_tickets' => 'Can delete tickets',
        'no_records_found' => 'No records found',
        'content' => 'Content',
        'title' => 'Title',
        'admin_panel' => 'Admin Panel',
        'status' => 'Status',
        'role_columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'image_tag' => 'Image Tag',
            'status' => 'Status',
            'is_active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted At',
            'autorisation' => 'Authorization',
        ],
        'phone' => 'Phone',
        'total_tickets' => 'Total Tickets',
        'open_tickets' => 'Open Tickets',
        'closed_tickets' => 'Closed Tickets',
        'ban_user' => 'Ban User',
        'unban_user' => 'Unban User',
        'manage_role' => 'Manage Role',
        'view_profile' => 'View Profile',
        'ban_user_modal_title' => 'Ban User',
        'ban_user_modal_reason' => 'Reason:',
        'ban_user_modal_select_duration' => 'Select ban duration:',
        'ban_user_modal_cancel' => 'Cancel',
        'ban_user_modal_permanent' => 'Permanent',
        'manage_role_modal_title' => 'Manage Role',
        'manage_role_modal_close' => 'Close',
        'view_profile_modal_title' => 'View Profile',
        'view_profile_modal_close' => 'Close',
        'view_message' => 'View Message',
        'ban_duration' => 'Ban Duration',
        'ban_reason' => 'Ban Reason',
        'expires_at' => 'Expires At',
        'view_details' => 'View Details',
        'confirm' => 'Confirm',
        'cancel' => 'Cancel',
        'edit' => 'Edit',
        'save' => 'Save',
        'close' => 'Close',
        'view_user' => 'View User',
        'view' => 'View',
        'ban' => 'Ban',
        'role' => 'Role',
    ],
    'nl' => [
        'user_info' => 'Gebruikersinformatie',
        'id' => 'ID',
        'name' => 'Naam',
        'email' => 'Email',
        'grade' => 'Rang',
        'actions' => 'Acties',
        'modify' => 'Bewerken',
        'delete' => 'Verwijderen',
        'ticket_management' => 'Ticketbeheer',
        'title' => 'Titel',
        'status' => 'Status',
        'view' => 'Bekijken',
        'add_patchnote' => 'Patchnote toevoegen',
        'content' => 'Inhoud',
        'add' => 'Toevoegen',
        'creator' => 'Maker',
        'message' => 'Bericht',
        'created_at' => 'Aangemaakt op',
        'is_closed' => 'Status',
        'closed_at' => 'Gesloten op',
        'closed_by' => 'Gesloten door',
        'open' => 'Open',
        'closed' => 'Gesloten',
        'archive_ticket' => 'Archief Ticket',
        'patch_note' => 'Patchnotities',
        'permissions' => 'Machtigingen',
        'role' => 'Rollen',
        'ticket' => 'Tickets',
        'ticket_message' => 'Ticketberichten',
        'columns' => [
            'id' => 'ID',
            'creator' => 'Maker',
            'ticket_name' => 'Ticketnaam',
            'message' => 'Bericht',
            'created_at' => 'Aangemaakt op',
            'is_closed' => 'Status',
            'closed_at' => 'Gesloten op',
            'closed_by' => 'Gesloten door',
            'updated_at' => 'Bijgewerkt op',
            'deleted_at' => 'Verwijderd op',
            'status' => 'Status',
        ],
        'yes' => 'Ja',
        'no' => 'Nee',
        'toggle_permission' => 'Permissie wijzigen',
        'ticket_id' => 'Ticket ID',
        'description' => 'Beschrijving',
        'can_reply_ticket' => 'Kan reageren op tickets',
        'can_ban_permanently' => 'Kan permanent verbannen',
        'can_ban_temporarily' => 'Kan tijdelijk verbannen',
        'can_post_patchnotes' => 'Kan patchnotes plaatsen',
        'can_manage_users' => 'Kan gebruikers beheren',
        'can_view_reports' => 'Kan rapporten bekijken',
        'can_edit_roles' => 'Kan rollen bewerken',
        'can_delete_tickets' => 'Kan tickets verwijderen',
        'no_records_found' => 'Geen records gevonden',
        'content' => 'Inhoud',
        'title' => 'Titel',
        'admin_panel' => 'Beheerderspaneel',
        'status' => 'Status',
        'role_columns' => [
            'id' => 'ID',
            'name' => 'Naam',
            'image_tag' => 'Afbeeldingstag',
            'status' => 'Status',
            'is_active' => 'Actief',
            'created_at' => 'Aangemaakt op',
            'updated_at' => 'Bijgewerkt op',
            'created_by' => 'Aangemaakt door',
            'updated_by' => 'Bijgewerkt door',
            'deleted_at' => 'Verwijderd op',
            'autorisation' => 'Autorisatie',
        ],
        'phone' => 'Telefoon',
        'total_tickets' => 'Totaal Tickets',
        'open_tickets' => 'Open Tickets',
        'closed_tickets' => 'Gesloten Tickets',
        'ban_user' => 'Gebruiker verbannen',
        'unban_user' => 'Gebruiker deblokkeren',
        'manage_role' => 'Rol beheren',
        'view_profile' => 'Profiel bekijken',
        'ban_user_modal_title' => 'Gebruiker verbannen',
        'ban_user_modal_reason' => 'Reden:',
        'ban_user_modal_select_duration' => 'Selecteer de duur van de ban:',
        'ban_user_modal_cancel' => 'Annuleren',
        'ban_user_modal_permanent' => 'Permanent',
        'manage_role_modal_title' => 'Rol beheren',
        'manage_role_modal_close' => 'Sluiten',
        'view_profile_modal_title' => 'Profiel bekijken',
        'view_profile_modal_close' => 'Sluiten',
        'view_message' => 'Bericht bekijken',
        'ban_duration' => 'Duur van verbanning',
        'ban_reason' => 'Reden van verbanning',
        'expires_at' => 'Verloopt op',
        'view_details' => 'Bekijk details',
        'confirm' => 'Bevestigen',
        'cancel' => 'Annuleren',
        'edit' => 'Bewerken',
        'save' => 'Opslaan',
        'close' => 'Sluiten',
        'view_user' => 'Gebruiker bekijken',
        'view' => 'Bekijken',
        'ban' => 'Verbannen',
        'role' => 'Rol',
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['patchnote_title'], $_POST['patchnote_message'])) {
    $title = trim($_POST['patchnote_title']);
    $message = trim($_POST['patchnote_message']);
    $posted_by = $_SESSION['user']['name'] ?? 'admin';
    if ($title && $message) {
        $stmt = $pdo->prepare("INSERT INTO patch_note (title, message, posted_by, posted_date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$title, $message, $posted_by]);
        $patchnote_success = true;
    } else {
        $patchnote_error = true;
    }
}

$trans = $translations[$selected_lang];

// Fonction pour afficher les badges des rôles ayant une permission donnée
function displayRoleBadges($permissionCol, $pdo, $trans) {
    // Map id_perm vers badge et nom
    $roleMap = [
        1 => ['label' => 'Utilisateur', 'class' => 'badge-user'],
        2 => ['label' => 'Guide', 'class' => 'badge-guide'],
        3 => ['label' => 'Modérateur', 'class' => 'badge-modo'],
        4 => ['label' => 'Développeur', 'class' => 'badge-dev'],
        5 => ['label' => 'Administrateur', 'class' => 'badge-admin'],
    ];
    $badges = [];
    $stmt = $pdo->query("SELECT id_perm, nom, $permissionCol FROM permissions");
    while ($row = $stmt->fetch()) {
        if (!empty($row[$permissionCol])) {
            $role = $roleMap[$row['id_perm']] ?? null;
            if ($role) {
                $badges[] = "<span class='role-badge {$role['class']}'>" . htmlspecialchars($role['label']) . "</span>";
            }
        }
    }
    if ($badges) {
        echo "<div class='mb-2'>" . implode('', $badges) . "</div>";
    }
}

// Fonction pour vérifier si un utilisateur a une permission donnée
function userHasPermission($id_perm, $permissionCol, $pdo) {
    $stmt = $pdo->prepare("SELECT $permissionCol FROM permissions WHERE id_perm = :id_perm");
    $stmt->execute(['id_perm' => $id_perm]);
    return (bool)$stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($selected_lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $trans['admin_panel'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        :root {
            --purple-dark: #4c1d95;
            --red: #ef4444;
            --green: #22c55e;
            --purple: #6d28d9;
            --cyan-light: #a5f3fc;
            --gray-light: #e5e5e5;
            --hover-green: #22c55e;
            --white: #ffffff;
            --gray-dark: #2d3748;
            --hover-row: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--gray-dark);
            font-family: 'Orbitron', sans-serif;
            color: var(--gray-light);
            margin: 0;
            padding: 0;
        }

        header, footer {
            background-color: var(--purple-dark);
            color: var(--white);
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        h1, h2 {
            text-align: center;
            color: var(--cyan-light);
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        /* Modal container styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: var(--gray-dark); /* Match page background */
            color: var(--gray-light); /* Match text color */
            padding: 2rem;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .modal-content button {
            margin: 0.5rem;
        }

        .modal-content h3 {
            color: var(--cyan-light); /* Match heading color */
        }

        .modal-content textarea {
            background-color: var(--gray-light);
            color: var(--gray-dark);
            border: 1px solid var(--purple-dark);
            border-radius: 5px;
            padding: 0.5rem;
            width: 100%;
        }

        .notification {
            position: static;
            margin: 0 auto 1.5rem auto;
            min-width: 250px;
            max-width: 500px;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            display: none;
            text-align: center;
        }
        .notification.success { background: #22c55e; color: #fff; display: block; }
        .notification.error { background: #ef4444; color: #fff; display: block; }

        /* Ajout de styles pour rendre les tableaux responsives */
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            th, td {
                font-size: 0.875rem; /* Réduction de la taille de la police */
                padding: 0.5rem; /* Réduction de l'espacement */
            }

            .table-container {
                margin-bottom: 1rem; /* Ajustement de l'espacement */
            }
        }

        @media (max-width: 480px) {
            .card-container {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .card {
                background-color: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 10px;
                padding: 1rem;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }

            .card h3 {
                font-size: 1rem;
                color: var(--cyan-light);
                margin-bottom: 0.5rem;
            }

            .card p {
                font-size: 0.875rem;
                margin: 0.25rem 0;
            }

            button {
                font-size: 0.75rem; /* Réduction de la taille des boutons */
                padding: 0.25rem 0.5rem; /* Ajustement de l'espacement des boutons */
            }
        }

        /* Centrer le texte dans tous les tableaux */
        table, thead, tbody, th, td, tr {
            text-align: center !important;
        }
        /* Style pour les badges de rôles */
        .role-badge {
            display: inline-block;
            padding: 0.25em 0.75em;
            border-radius: 999px;
            font-size: 0.85em;
            font-weight: bold;
            margin: 0 0.2em 0.2em 0;
            color: #fff;
        }
        .badge-user { background: #64748b; }         /* Utilisateur */
        .badge-guide { background: #22d3ee; }        /* Guide */
        .badge-modo { background: #f59e42; }         /* Modérateur */
        .badge-dev { background: #a78bfa; }          /* Développeur */
        .badge-admin { background: #ef4444; }        /* Administrateur */
    </style>
    <script>
        // JavaScript to handle modal display
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            } else {
                console.error(`Modal with ID "${modalId}" not found.`);
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
            } else {
                console.error(`Modal with ID "${modalId}" not found.`);
            }
        }

        function showNotification(message, type = 'success') {
            let notif = document.getElementById('notification');
            notif.textContent = message;
            notif.className = 'notification ' + type;
            notif.style.display = 'block';
            setTimeout(() => { notif.style.display = 'none'; }, 3000);
        }

        function confirmPermissionChange(form) {
            return confirm('Êtes-vous sûr de vouloir modifier cette permission ?');
        }
    </script>
</head>
<body>
    <div id="notification" class="notification"></div>
    <?php include 'header.php'; ?>
    <div class="container mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-4xl text-gray-light"><?= $trans['admin_panel'] ?></h1>
            <button onclick="openModal('patchNoteModal')" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-plus"></i> Publier un patch note
            </button>
        </div>
        <?php if (!empty($patchnote_success)): ?>
            <script>window.onload = function(){ showNotification('Patch note publié !', 'success'); };</script>
        <?php elseif (!empty($patchnote_error)): ?>
            <script>window.onload = function(){ showNotification('Veuillez remplir tous les champs.', 'error'); };</script>
        <?php endif; ?>

        <!-- Modal Patch Note -->
        <div id="patchNoteModal" class="modal" style="display: none;">
            <div class="modal-content">
                <h3 class="mb-4 text-xl font-bold text-cyan-400">Publier un patch note</h3>
                <form method="POST" onsubmit="closeModal('patchNoteModal')">
                    <input type="text" name="patchnote_title" placeholder="Titre" required class="mb-3 w-full px-3 py-2 rounded border border-gray-300 text-gray-900">
                    <textarea name="patchnote_message" placeholder="Message" required class="mb-3 w-full px-3 py-2 rounded border border-gray-300 text-gray-900"></textarea>
                    <div class="flex justify-center gap-4">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Publier</button>
                        <button type="button" onclick="closeModal('patchNoteModal')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Annuler</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des utilisateurs -->
        <div class="section-container mb-10">
            <h2 class="section-title"><?= $trans['user_info'] ?></h2>
            <?php displayRoleBadges('can_manage_users', $pdo, $trans); ?>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full max-w-full max-h-screen">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <?php
                            $columns = [
                                'id' => $trans['id'],
                                'name' => $trans['name'],
                                'email' => $trans['email'],
                                'phone' => $trans['phone'],
                                'ticket_count' => $trans['total_tickets'],
                                'open_ticket_count' => $trans['open_tickets'],
                                'closed_ticket_count' => $trans['closed_tickets'],
                                'created_at' => $trans['created_at'],
                                'updated_at' => $trans['updated_at'],
                                'actions' => $trans['actions'],
                            ];
                            foreach ($columns as $column => $label) {
                                echo "<th scope='col' class='px-6 py-3'>" . htmlspecialchars($label) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM users");
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch()) {
                                echo "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200'>";
                                foreach (array_keys($columns) as $column) {
                                    if ($column === 'actions') {
                                        $viewModalId = "viewModal-" . $row['id'];
                                        $banModalId = "banModal-" . $row['id'];
                                        $roleModalId = "roleModal-" . $row['id'];

                                        // Permissions pour chaque action
                                        $canView = userHasPermission($id_perm, 'can_view_users', $pdo);
                                        $canBan = userHasPermission($id_perm, 'can_ban_permanently', $pdo) || userHasPermission($id_perm, 'can_ban_temporarily', $pdo);
                                        $canRole = userHasPermission($id_perm, 'can_edit_roles', $pdo);

                                        // Classes pour griser les boutons si pas la permission
                                        $disabledBtn = "opacity-50 cursor-not-allowed pointer-events-none";
                                        $enabledBtn = "";

                                        echo "<td class='px-6 py-4 text-center'>
                                                <div class='flex justify-center items-center gap-4'>
                                                    <button onclick=\"openModal('$viewModalId')\" class='flex items-center justify-center w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blue-400 " . ($canView ? "" : $disabledBtn) . "\" " . ($canView ? "" : "tabindex='-1' aria-disabled='true'") . ">
                                                        <span class='flex items-center justify-center w-full h-full'>
                                                            <i class='fas fa-eye text-xl'></i>
                                                        </span>
                                                    </button>
                                                    <button onclick=\"openModal('$banModalId')\" class='flex items-center justify-center w-12 h-12 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-red-400 " . ($canBan ? "" : $disabledBtn) . "\" " . ($canBan ? "" : "tabindex='-1' aria-disabled='true'") . ">
                                                        <span class='flex items-center justify-center w-full h-full'>
                                                            <i class='fas fa-ban text-xl'></i>
                                                        </span>
                                                    </button>
                                                    <button onclick=\"openModal('$roleModalId')\" class='flex items-center justify-center w-12 h-12 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-purple-400 " . ($canRole ? "" : $disabledBtn) . "\" " . ($canRole ? "" : "tabindex='-1' aria-disabled='true'") . ">
                                                        <span class='flex items-center justify-center w-full h-full'>
                                                            <i class='fas fa-user-cog text-xl'></i>
                                                        </span>
                                                    </button>
                                                </div>
                                              </td>";

                                        // Modal pour voir les détails
                                        echo "<div id='$viewModalId' class='modal' style='display: none;'>
                                                <div class='modal-content'>
                                                    <h3>Détails de l'utilisateur</h3>
                                                    <p>Nom : " . htmlspecialchars($row['name']) . "</p>
                                                    <p>Email : " . htmlspecialchars($row['email']) . "</p>
                                                    <button onclick=\"closeModal('$viewModalId')\" class='px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600'>Fermer</button>
                                                </div>
                                              </div>";

                                        // Modal pour bannir un utilisateur
                                        echo "<div id='$banModalId' class='modal' style='display: none;'>
                                                <div class='modal-content'>
                                                    <h3>Bannir l'utilisateur</h3>
                                                    <form onsubmit=\"event.preventDefault(); banUser({$row['id']}, document.getElementById('banDuration-{$row['id']}').value, document.getElementById('banReason-{$row['id']}').value)\">
                                                        <label>Motif :</label>
                                                        <textarea id='banReason-{$row['id']}' required></textarea>
                                                        <label>Durée :</label>
                                                        <select id='banDuration-{$row['id']}' required>
                                                            <option value='1'>1 jour</option>
                                                            <option value='7'>1 semaine</option>
                                                            <option value='30'>1 mois</option>
                                                            <option value='0'>Permanent</option>
                                                        </select>
                                                        <div class='mt-4'>
                                                            <button type='submit' class='px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600'>Confirmer</button>
                                                            <button type='button' onclick=\"closeModal('$banModalId')\" class='px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600'>Annuler</button>
                                                        </div>
                                                    </form>
                                                </div>
                                              </div>";

                                        // Modal pour gérer les rôles
                                        echo "<div id='$roleModalId' class='modal' style='display: none;'>
                                                <div class='modal-content'>
                                                    <h3>Gérer le rôle</h3>
                                                    <form onsubmit=\"event.preventDefault(); updateRole({$row['id']}, document.getElementById('roleSelect-{$row['id']}').value)\">
                                                        <label>Rôle :</label>
                                                        <select id='roleSelect-{$row['id']}' required>
                                                            <option value='1'>Utilisateur</option>
                                                            <option value='2'>Guide</option>
                                                            <option value='3'>Modérateur</option>
                                                            <option value='4'>Développeur</option>
                                                            <option value='5'>Administrateur</option>
                                                        </select>
                                                        <div class='mt-4'>
                                                            <button type='submit' class='px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600'>Enregistrer</button>
                                                            <button type='button' onclick=\"closeModal('$roleModalId')\" class='px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600'>Annuler</button>
                                                        </div>
                                                    </form>
                                                </div>
                                              </div>";
                                    } else {
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($row[$column] ?? '') . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='bg-white dark:bg-gray-800'>
                                    <td colspan='" . count($columns) . "' class='px-6 py-4 text-center'>{$trans['no_records_found']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau des permissions -->
        <div class="section-container mb-10">
            <h2 class="section-title"><?= $trans['permissions'] ?></h2>
            <?php displayRoleBadges('can_edit_roles', $pdo, $trans); ?>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <?php
                            $columns = [
                                'id_perm' => $trans['columns']['id'] ?? 'ID Permission',
                                'nom' => $trans['columns']['name'] ?? 'Nom',
                                'can_reply_ticket' => $trans['can_reply_ticket'],
                                'can_ban_permanently' => $trans['can_ban_permanently'],
                                'can_ban_temporarily' => $trans['can_ban_temporarily'],
                                'can_post_patchnotes' => $trans['can_post_patchnotes'],
                                'can_manage_users' => $trans['can_manage_users'],
                                'can_view_reports' => $trans['can_view_reports'],
                                'can_edit_roles' => $trans['can_edit_roles'],
                                'can_delete_tickets' => $trans['can_delete_tickets'],
                            ];
                            foreach ($columns as $column => $label) {
                                echo "<th scope='col' class='px-6 py-3'>" . htmlspecialchars($label) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM permissions");
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch()) {
                                echo "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200'>";
                                foreach (array_keys($columns) as $column) {
                                    if (in_array($column, ['can_reply_ticket', 'can_ban_permanently', 'can_ban_temporarily', 'can_post_patchnotes', 'can_manage_users', 'can_view_reports', 'can_edit_roles', 'can_delete_tickets'])) {
                                        $icon = $row[$column] 
                                            ? '<i class="fas fa-check text-green-500"></i>' 
                                            : '<i class="fas fa-times text-red-500"></i>';
                                        echo "<td class='px-6 py-4 text-center'>
                                                <form method='POST' action='update_permission.php' onsubmit='return confirmPermissionChange(this)'>
                                                    <input type='hidden' name='id_perm' value='{$row['id_perm']}'>
                                                    <input type='hidden' name='column' value='{$column}'>
                                                    <input type='hidden' name='lang' value='" . htmlspecialchars($selected_lang) . "'>
                                                    <button type='submit' class='w-10 h-10 flex items-center justify-center text-lg font-bold rounded-full' title='{$trans['toggle_permission']}'>
                                                        {$icon}
                                                    </button>
                                                </form>
                                              </td>";
                                    } else {
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($row[$column] ?? '') . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='bg-white dark:bg-gray-800'>
                                    <td colspan='" . count($columns) . "' class='px-6 py-4 text-center'>{$trans['no_records_found']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau des rôles -->
        <div class="section-container mb-10">
            <h2 class="section-title"><?= $trans['role'] ?></h2>
            <?php displayRoleBadges('can_edit_roles', $pdo, $trans); ?>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <?php
                            $columns = [
                                'id', 'name', 'image_tag', 'is_active', 
                                'created_at', 'updated_at', 'created_by', 
                                'updated_by', 'deleted_at', 'autorisation'
                            ];
                            foreach ($columns as $column) {
                                echo "<th scope='col' class='px-6 py-3'>" . htmlspecialchars($trans['role_columns'][$column] ?? $column) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM role");
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch()) {
                                echo "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200'>";
                                foreach ($columns as $column) {
                                    if ($column === 'is_active') {
                                        $value = $row[$column] ? $trans['yes'] : $trans['no'];
                                        echo "<td class='px-6 py-4 text-center'>{$value}</td>";
                                    } elseif ($column === 'created_by' || $column === 'updated_by') {
                                        $userStmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
                                        $userStmt->execute(['id' => $row[$column]]);
                                        $userName = $userStmt->fetchColumn() ?? $trans['no_records_found'];
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($userName) . "</td>";
                                    } elseif ($column === 'deleted_at') {
                                        $deletedAt = $row['deleted_at'] ?? '';
                                        if ($deletedAt) {
                                            $formattedDate = date('d/m/Y', strtotime($deletedAt));
                                            $formattedTime = date('H:i:s', strtotime($deletedAt));
                                            echo "<td class='px-6 py-4 text-center'>{$formattedDate}<br>{$formattedTime}</td>";
                                        } else {
                                            echo "<td class='px-6 py-4 text-center'>-</td>";
                                        }
                                    } elseif ($column === 'autorisation') {
                                        $permStmt = $pdo->prepare("SELECT nom FROM permissions WHERE id_perm = :id_perm");
                                        $permStmt->execute(['id_perm' => $row['autorisation']]);
                                        $permissionName = $permStmt->fetchColumn() ?? $trans['no_records_found'];
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($permissionName) . "</td>";
                                    } else {
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($row[$column] ?? '') . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='bg-white dark:bg-gray-800'>
                                    <td colspan='" . count($columns) . "' class='px-6 py-4 text-center'>{$trans['no_records_found']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau des bans -->
        <div class="section-container mb-10">
            <h2 class="section-title"><?= $trans['ban_user'] ?></h2>
            <?php displayRoleBadges('can_ban_permanently', $pdo, $trans); ?>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <?php
                            $columns = ['id', 'user_id', 'reason', 'duration', 'created_at', 'expires_at'];
                            foreach ($columns as $column) {
                                echo "<th scope='col' class='px-6 py-3'>" . htmlspecialchars($trans['columns'][$column] ?? $column) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM bans");
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch()) {
                                echo "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200'>";
                                foreach ($columns as $column) {
                                    if ($column === 'user_id') {
                                        $userStmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
                                        $userStmt->execute(['id' => $row['user_id']]);
                                        $userName = $userStmt->fetchColumn() ?? $trans['no_records_found'];
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($userName) . "</td>";
                                    } elseif (in_array($column, ['created_at', 'expires_at'])) {
                                        $date = $row[$column] ?? '';
                                        if ($date) {
                                            $formattedDate = date('d/m/Y', strtotime($date));
                                            $formattedTime = date('H:i:s', strtotime($date));
                                            echo "<td class='px-6 py-4 text-center'>{$formattedDate}<br>{$formattedTime}</td>";
                                        } else {
                                            echo "<td class='px-6 py-4 text-center'>-</td>";
                                        }
                                    } else {
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($row[$column] ?? '') . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='bg-white dark:bg-gray-800'>
                                    <td colspan='" . count($columns) . "' class='px-6 py-4 text-center'>{$trans['no_records_found']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau des tickets -->
        <div class="section-container mb-10">
            <h2 class="section-title"><?= $trans['ticket'] ?></h2>
            <?php displayRoleBadges('can_reply_ticket', $pdo, $trans); ?>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <?php
                            $columns = ['id', 'creator', 'ticket_name', 'message', 'created_at', 'is_closed', 'updated_at', 'deleted_at', 'actions'];
                            foreach ($columns as $column) {
                                echo "<th scope='col' class='px-6 py-3'>" . htmlspecialchars($trans['columns'][$column] ?? $column) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM ticket");
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch()) {
                                echo "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200'>";
                                foreach ($columns as $column) {
                                    if ($column === 'creator') {
                                        $userStmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
                                        $userStmt->execute(['id' => $row['creator']]);
                                        $creatorName = $userStmt->fetchColumn() ?? $trans['no_records_found'];
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($creatorName) . "</td>";
                                    } elseif ($column === 'message') {
                                        echo "<td class='px-6 py-4 text-center'>
                                                <button onclick=\"openModal('messageModal-{$row['id']}')\" class='px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600'>
                                                    <i class='fas fa-eye'></i> {$trans['view_message']}
                                                </button>
                                                <div id='messageModal-{$row['id']}' class='modal' style='display: none;'>
                                                    <div class='modal-content'>
                                                        <h3>{$trans['message']}</h3>
                                                        <p>" . htmlspecialchars($row['message']) . "</p>
                                                        <button onclick=\"closeModal('messageModal-{$row['id']}')\" class='px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600'>
                                                            {$trans['ban_user_modal_cancel']}
                                                        </button>
                                                    </div>
                                                </div>
                                              </td>";
                                    } elseif ($column === 'is_closed') {
                                        if (!empty($row['claimed_by'])) {
                                            $staffStmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
                                            $staffStmt->execute(['id' => $row['claimed_by']]);
                                            $staffName = $staffStmt->fetchColumn() ?? 'Staff inconnu';
                                            echo "<td class='px-6 py-4 text-center'>
                                                    <span class='inline-flex items-center px-3 py-1 bg-purple-600 text-white rounded'>
                                                        <i class='fas fa-user mr-1'></i> Pris par " . htmlspecialchars($staffName) . "
                                                    </span>
                                                  </td>";
                                        } else {
                                            echo "<td class='px-6 py-4 text-center'>
                                                    <span class='inline-flex items-center px-3 py-1 bg-gray-500 text-white rounded'>
                                                        <i class='fas fa-clock mr-1'></i> En attente
                                                    </span>
                                                  </td>";
                                        }
                                    } elseif (in_array($column, ['created_at', 'updated_at', 'deleted_at'])) {
                                        $date = $row[$column] ?? '';
                                        if ($date) {
                                            $formattedDate = date('d/m/Y', strtotime($date));
                                            $formattedTime = date('H:i:s', strtotime($date));
                                            echo "<td class='px-6 py-4 text-center'>{$formattedDate}<br>{$formattedTime}</td>";
                                        } else {
                                            echo "<td class='px-6 py-4 text-center'>-</td>";
                                        }
                                    } elseif ($column === 'actions') {
                                        echo "<td class='px-6 py-4 text-center'>
                                                <a href='open_ticket.php?id=" . urlencode($row['id']) . "' class='inline-flex items-center px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2' title='Ouvrir'>
                                                    <i class='fas fa-folder-open mr-1'></i> Ouvrir
                                                </a>";
                                        if (empty($row['claimed_by'])) {
                                            echo "<form method='post' action='claim_ticket.php' style='display:inline;'>
                                                    <input type='hidden' name='ticket_id' value='" . $row['id'] . "'>
                                                    <button type='submit' class='inline-flex items-center px-3 py-2 bg-orange-500 text-white rounded hover:bg-orange-600' title='Réclamer'>
                                                        <i class='fas fa-hand-paper mr-1'></i> Réclamer
                                                    </button>
                                                  </form>";
                                        } else {
                                            // Récupérer le nom du staff qui a réclamé le ticket
                                            $staffStmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
                                            $staffStmt->execute(['id' => $row['claimed_by']]);
                                            $staffName = $staffStmt->fetchColumn() ?? 'Staff inconnu';
                                            echo "<a href='view_profile.php?id=" . urlencode($row['claimed_by']) . "' class='inline-flex items-center px-3 py-2 bg-purple-600 text-white rounded ml-2 cursor-not-allowed opacity-60' title='Réclamé par' tabindex='-1' aria-disabled='true' onclick='return false;'>
                                                    <i class='fas fa-user mr-1'></i> " . htmlspecialchars($staffName) . "
                                                  </a>";
                                        }
                                        echo "</td>";
                                    } else {
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($row[$column] ?? '') . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='bg-white dark:bg-gray-800'>
                                    <td colspan='" . count($columns) . "' class='px-6 py-4 text-center'>{$trans['no_records_found']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau des messages des tickets -->
        <div class="section-container mb-10">
            <h2 class="section-title"><?= $trans['ticket_message'] ?></h2>
            <?php displayRoleBadges('can_reply_ticket', $pdo, $trans); ?>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 text-center">
                        <tr>
                            <?php
                            $columns = ['id', 'ticket_id', 'message', 'created_at', 'status'];
                            foreach ($columns as $column) {
                                echo "<th scope='col' class='px-6 py-3'>" . htmlspecialchars($trans['columns'][$column] ?? $column) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM ticket_message");
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch()) {
                                echo "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200'>";
                                foreach ($columns as $column) {
                                    if ($column === 'message') {
                                        echo "<td class='px-6 py-4 text-center'>
                                                <button onclick=\"openModal('messageModal-{$row['id']}')\" class='px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600'>
                                                    <i class='fas fa-eye'></i> {$trans['view_message']}
                                                </button>
                                                <div id='messageModal-{$row['id']}' class='modal' style='display: none;'>
                                                    <div class='modal-content'>
                                                        <h3>{$trans['message']}</h3>
                                                        <p>" . htmlspecialchars($row['message']) . "</p>
                                                        <button onclick=\"closeModal('messageModal-{$row['id']}')\" class='px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600'>
                                                            {$trans['ban_user_modal_cancel']}
                                                        </button>
                                                    </div>
                                                </div>
                                              </td>";
                                    } elseif ($column === 'created_at') {
                                        $date = $row[$column] ?? '';
                                        if ($date) {
                                            $formattedDate = date('d/m/Y', strtotime($date));
                                            $formattedTime = date('H:i:s', strtotime($date));
                                            echo "<td class='px-6 py-4 text-center'>{$formattedDate}<br>{$formattedTime}</td>";
                                        } else {
                                            echo "<td class='px-6 py-4 text-center'>-</td>";
                                        }
                                    } elseif ($column === 'status') {
                                        $status = $row[$column] == 0 ? $trans['open'] : $trans['closed'];
                                        echo "<td class='px-6 py-4 text-center'>{$status}</td>";
                                    } else {
                                        echo "<td class='px-6 py-4 text-center'>" . htmlspecialchars($row[$column] ?? '') . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='bg-white dark:bg-gray-800'>
                                    <td colspan='" . count($columns) . "' class='px-6 py-4 text-center'>{$trans['no_records_found']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tableau des archives des tickets -->
        <div class="section-container mb-10">
            <h2 class="section-title"><?= $trans['archive_ticket'] ?></h2>
            <?php displayRoleBadges('can_view_reports', $pdo, $trans); ?>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <?php
                            $columns = ['id', 'creator', 'ticket_name', 'created_at', 'closed_by', 'updated_at', 'message'];
                            foreach ($columns as $column) {
                                echo "<th scope='col' class='px-6 py-3'>" . htmlspecialchars($trans['columns'][$column] ?? $column) . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM archive_ticket");
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch()) {
                                echo "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200'>";
                                foreach ($columns as $column) {
                                    if ($column === 'creator') {
                                        $userStmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
                                        $userStmt->execute(['id' => $row['creator']]);
                                        $creatorName = $userStmt->fetchColumn() ?? $trans['no_records_found'];
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($creatorName) . "</td>";
                                    } elseif ($column === 'message') {
                                        echo "<td class='px-6 py-4 text-center'>
                                                <button onclick=\"openModal('messageModal-{$row['id']}')\" class='px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600'>
                                                    <i class='fas fa-eye'></i> {$trans['view_message']}
                                                </button>
                                                <div id='messageModal-{$row['id']}' class='modal' style='display: none;'>
                                                    <div class='modal-content'>
                                                        <h3>{$trans['message']}</h3>
                                                        <p>" . htmlspecialchars($row['message']) . "</p>
                                                        <button onclick=\"closeModal('messageModal-{$row['id']}')\" class='px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600'>
                                                            {$trans['ban_user_modal_cancel']}
                                                        </button>
                                                    </div>
                                                </div>
                                              </td>";
                                    } elseif (in_array($column, ['created_at', 'updated_at'])) {
                                        $date = $row[$column] ?? '';
                                        if ($date) {
                                            $formattedDate = date('d/m/Y', strtotime($date));
                                            $formattedTime = date('H:i:s', strtotime($date));
                                            echo "<td class='px-6 py-4 text-center'>{$formattedDate}<br>{$formattedTime}</td>";
                                        } else {
                                            echo "<td class='px-6 py-4 text-center'>-</td>";
                                        }
                                    } else {
                                        echo "<td class='px-6 py-4'>" . htmlspecialchars($row[$column] ?? '') . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='bg-white dark:bg-gray-800'>
                                    <td colspan='" . count($columns) . "' class='px-6 py-4 text-center'>{$trans['no_records_found']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>