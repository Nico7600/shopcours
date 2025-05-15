<?php
include 'header.php';

$lang = $_GET['lang'] ?? 'fr';

$available_languages = ['fr', 'en', 'nl'];

if (!in_array($lang, $available_languages)) {
    die('Langue non supportée.');
}

$translations = [
    'fr' => [
        'title' => 'Patch Notes',
        'no_patchnotes' => 'Aucun patch note disponible pour le moment.',
        'posted_by' => 'Posté par :',
        'date' => 'Date :',
        'error_vote' => 'Erreur lors du vote.',
    ],
    'en' => [
        'title' => 'Patch Notes',
        'no_patchnotes' => 'No patch notes available at the moment.',
        'posted_by' => 'Posted by:',
        'date' => 'Date:',
        'error_vote' => 'Error while voting.',
    ],
    'nl' => [
        'title' => 'Patchnotities',
        'no_patchnotes' => 'Momenteel geen patchnotities beschikbaar.',
        'posted_by' => 'Geplaatst door:',
        'date' => 'Datum:',
        'error_vote' => 'Fout bij het stemmen.',
    ],
];
$text = $translations[$lang];

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
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM patch_note ORDER BY posted_date DESC");
$patchnotes = $stmt->fetchAll();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($text['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .font-orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        .patchnote-container {
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        .patchnote {
            background-color: #1f2937;
            border: 1px solid #374151;
            color: #d1d5db;
            transition: box-shadow 0.2s;
            position: relative;
            padding-bottom: 4.5rem;
        }
        .patchnote:hover {
            box-shadow: 0 8px 24px 0 rgba(56,189,248,0.15);
            border-color: #38bdf8;
        }
        .patchnote h2 {
            color: #38bdf8;
            text-align: center;
        }
        .patchnote p {
            color: #9ca3af;
        }
        .votes {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.5rem;
        }
        .vote-btn {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            border: none;
            outline: none;
            background: #1e293b;
            box-shadow: 0 2px 8px 0 rgba(0,0,0,0.12);
            font-size: 1.25rem;
            transition: transform 0.15s, background 0.2s;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .vote-btn.up {
            color: #2563eb;
            border: 2px solid #2563eb;
        }
        .vote-btn.down {
            color: #ef4444;
            border: 2px solid #ef4444;
        }
        .vote-btn.selected {
            background: #2563eb;
            color: #fff;
        }
        .vote-btn.down.selected {
            background: #ef4444;
            color: #fff;
        }
        .vote-btn:active {
            transform: scale(1.15);
        }
        .vote-btn[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-900 text-gray-100 font-orbitron">
    <div class="container mx-auto flex-grow p-6 font-orbitron h-full">
        <h1 class="text-4xl font-bold text-center mb-8"><?= htmlspecialchars($text['title']) ?></h1>
        <div class="patchnote-container">
            <?php if (empty($patchnotes)): ?>
                <p class="text-center text-gray-500"><?= htmlspecialchars($text['no_patchnotes']) ?></p>
            <?php else: ?>
                <?php foreach ($patchnotes as $note): ?>
                    <?php
                        $score = intval($note['up_vote']) - intval($note['down_vote']);
                    ?>
                    <div class="patchnote shadow-md rounded-lg p-6 mb-6">
                        <h2 class="text-2xl font-semibold mb-4"><?= htmlspecialchars($note['title']) ?></h2>
                        <p class="mb-4"><?= nl2br(htmlspecialchars($note['message'])) ?></p>
                        <?php
                            $date = new DateTime($note['posted_date']);
                            $dateStr = $date->format('d/m/Y');
                            $heureStr = $date->format('H:i');
                        ?>
                        <div style="position: absolute; left: 1.5rem; bottom: 1.5rem; text-align: left;">
                            <span class="text-sm">
                                <strong><?= htmlspecialchars($text['posted_by']) ?></strong> <?= htmlspecialchars($note['grade']) ?> <?= htmlspecialchars($note['posted_by']) ?>
                            </span>
                        </div>
                        <div style="position: absolute; right: 1.5rem; bottom: 1.5rem; text-align: right;">
                            <span class="text-sm">
                                Posté le <?= $dateStr ?> à <?= $heureStr ?>
                            </span>
                        </div>
                        <div class="votes text-sm flex items-center justify-center" style="position: absolute; left: 0; right: 0; bottom: 1.5rem;">
                            <button class="vote-btn up flex items-center justify-center" onclick="voteOnce('up', <?= $note['id'] ?>, this)">
                                <i class="fas fa-thumbs-up"></i>
                            </button>
                            <span id="score-<?= $note['id'] ?>" class="text-lg font-bold mx-4"><?= $score ?></span>
                            <button class="vote-btn down flex items-center justify-center" onclick="voteOnce('down', <?= $note['id'] ?>, this)">
                                <i class="fas fa-thumbs-down"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
<script>
    // Empêche le spam de vote sur un même patchnote
    const voteInProgress = {};

    function voteOnce(type, id, btn) {
        if (voteInProgress[id]) return; // Ignore si déjà en cours
        voteInProgress[id] = true;

        const key = 'patchnote-voted-' + id;
        let prevVote = localStorage.getItem(key);

        let action = type;
        let removeVote = false;
        if (prevVote === type) {
            action = 'remove';
            removeVote = true;
        }
        const votesDiv = btn.closest('.votes');
        votesDiv.querySelectorAll('button').forEach(b => b.disabled = true);

        btn.classList.add('animate');
        setTimeout(() => btn.classList.remove('animate'), 150);

        fetch('vote.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type: action, id, prevVote })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('score-' + id).textContent = data.score;
                votesDiv.querySelectorAll('.vote-btn').forEach(b => b.classList.remove('selected'));
                if (!removeVote && (type === 'up' || type === 'down')) {
                    if (type === 'up') {
                        votesDiv.querySelector('.up').classList.add('selected');
                    } else if (type === 'down') {
                        votesDiv.querySelector('.down').classList.add('selected');
                    }
                    localStorage.setItem(key, type);
                } else {
                    localStorage.removeItem(key);
                }
            }
        })
        .catch(() => {
            alert('<?= htmlspecialchars($text['error_vote']) ?>');
        })
        .finally(() => {
            votesDiv.querySelectorAll('button').forEach(b => b.disabled = false);
            voteInProgress[id] = false;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.votes').forEach(function(votesDiv) {
            const scoreSpan = votesDiv.querySelector('span[id^="score-"]');
            if (!scoreSpan) return;
            const id = scoreSpan.id.replace('score-', '');
            const key = 'patchnote-voted-' + id;
            const vote = localStorage.getItem(key);
            votesDiv.querySelectorAll('.vote-btn').forEach(b => b.classList.remove('selected'));
            if (vote === 'up') {
                const upBtn = votesDiv.querySelector('.up');
                if (upBtn) upBtn.classList.add('selected');
            } else if (vote === 'down') {
                const downBtn = votesDiv.querySelector('.down');
                if (downBtn) downBtn.classList.add('selected');
            }
        });
    });
</script>
