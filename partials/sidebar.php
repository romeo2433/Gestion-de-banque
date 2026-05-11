<aside class="sidebar">

    <!-- Logo -->
    <div class="logo">
        <h2>🏦 IGOR PRO</h2>
    </div>

    <!-- Profil utilisateur -->
    <div class="user-card">
        <div class="user-avatar">
            <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
        </div>
        <div class="user-info">
            <h4><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></h4>
            <span>Administrateur</span>
        </div>
    </div>

    <!-- Menu -->
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <ul class="nav-menu">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="utilisateur.php" class="nav-link <?= ($current_page == 'utilisateur.php') ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Clients</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="demande.php" class="nav-link <?= ($current_page == 'demande.php') ? 'active' : '' ?>">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Demandes de prêt</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="planification.php" class="nav-link <?= ($current_page == 'planification.php') ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Échéanciers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="remboursement.php" class="nav-link <?= ($current_page == 'remboursement.php') ? 'active' : '' ?>">
                <i class="fas fa-wallet"></i>
                <span>Remboursements</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="historique.php" class="nav-link <?= ($current_page == 'historique.php') ? 'active' : '' ?>">
                <i class="fas fa-history"></i>
                <span>Historique</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="Agents Bancaires.php" class="nav-link <?= ($current_page == 'Agents Bancaires.php') ? 'active' : '' ?>">
                <i class="fas fa-user-shield"></i>
                <span>Agents Bancaires</span>
            </a>
        </li>
    </ul>

    <!-- Déconnexion -->
    <div class="logout-btn">
        <a href="logout.php" class="logout-link" onclick="return confirm('Déconnexion ?')">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </div>

</aside>