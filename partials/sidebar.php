<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">IGOR PRO</span>
    </a>

    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">
                    Bienvenue, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                </a>
            </div>
        </div>

        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>Tableau de bord</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="utilisateur.php" class="nav-link">
                        <i class="fa fa-user"></i>
                        <p>Clients</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="demande.php" class="nav-link">
                        <i class="fa fa-credit-card"></i>
                        <p>Demandes de prêt</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="planification.php" class="nav-link">
                        <i class="fas fa-chart-pie"></i>
                        <p>Échéanciers</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="remboursement.php" class="nav-link">
                        <i class="fas fa-money-bill-wave"></i>
                        <p>Remboursement</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="historique.php" class="nav-link">
                        <i class="fas fa-history"></i>
                        <p>Historique</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="Agents Bancaires.php" class="nav-link">
                        <i class="fas fa-user-shield"></i>
                        <p>Agents Bancaires</p>
                    </a>
                </li>                

                <li class="nav-item">
                    <a href="logout.php" class="nav-link" onclick="return confirm('Déconnexion ?')">
                        <i class="fa fa-power-off"></i>
                        <p>Déconnexion</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>