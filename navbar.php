<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="chat.php" id="openChat" class="btn btn-primary">Ouvrir le chat</a>
            </li>
        </ul>
    </div>
</nav>
<div class="menu">
    <a href="add.php">Ajouter produit</a>
    <a href="chat.php">Support Chat</a>
    <form method="post" style="display:inline;">
        <button type="submit" name="toggle_maintenance" class="btn <?php echo file_exists('maintenance.flag') ? 'btn-maintenance-on' : 'btn-maintenance-off'; ?>">
            <?php echo file_exists('maintenance.flag') ? 'Désactiver la maintenance' : 'Activer la maintenance'; ?>
        </button>
    </form>
</div>
<script>
</script>