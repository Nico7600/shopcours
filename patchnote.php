<?php
include 'header.php';

$lang = $_GET['lang'] ?? 'fr';

// Langues disponibles
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
        }
        .patchnote h2 {
            color: #38bdf8;
        }
        .patchnote p {
            color: #9ca3af;
        }
        .votes span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
                    <div class="patchnote shadow-md rounded-lg p-6 mb-6 relative">
                        <h2 class="text-2xl font-semibold mb-4"><?= htmlspecialchars($note['title']) ?></h2>
                        <p class="mb-4"><?= nl2br(htmlspecialchars($note['message'])) ?></p>
                        <p class="text-sm mb-2">
                            <strong><?= htmlspecialchars($text['posted_by']) ?></strong> <?= htmlspecialchars($note['grade']) ?> <?= htmlspecialchars($note['posted_by']) ?> | 
                            <strong><?= htmlspecialchars($text['date']) ?></strong> <?= htmlspecialchars($note['posted_date']) ?>
                        </p>
                        <div class="votes text-sm absolute bottom-4 right-4 flex items-center gap-4">
                            <button class="text-green-500 flex items-center gap-1" onclick="vote('up', <?= $note['id'] ?>)">
                                <i class="fas fa-thumbs-up"></i> <span id="up-vote-<?= $note['id'] ?>"><?= $note['up_vote'] ?></span>
                            </button>
                            <button class="text-red-500 flex items-center gap-1" onclick="vote('down', <?= $note['id'] ?>)">
                                <i class="fas fa-thumbs-down"></i> <span id="down-vote-<?= $note['id'] ?>"><?= $note['down_vote'] ?></span>
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
    function vote(type, id) {
        fetch(`/vote.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type, id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`${type}-vote-${id}`).textContent = data.newCount;
            } else {
                alert('<?= htmlspecialchars($text['error_vote']) ?>');
            }
        })
        .catch(() => alert('<?= htmlspecialchars($text['error_vote']) ?>'));
    }
</script>
