<header class="header">
    <div class="header-container">
        <nav class="navbar navbar-expand-lg">
            <button class="navbar-toggler" type="button" id="sidebarToggle">
                <span class="ti-menu"></span>
            </button>
            
            <div class="navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <div class="avatar avatar-sm">
                                <span class="avatar-text rounded-circle bg-primary">
                                    <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
                                </span>
                            </div>
                            <span class="ml-2"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="profile.php">
                                <i class="ti-user"></i> Profil
                            </a>
                            <a class="dropdown-item" href="settings.php">
                                <i class="ti-settings"></i> Pengaturan
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">
                                <i class="ti-power-off"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
