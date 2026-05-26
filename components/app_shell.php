<?php

if (!function_exists('render_app_shell')) {
    function render_app_shell(array $options = []): void
    {
        $scope = $options['scope'] ?? 'root';
        $active = $options['active'] ?? '';
        $searchPlaceholder = $options['search_placeholder'] ?? 'Search';
        $searchValue = $options['search_value'] ?? '';
        $searchAttributes = trim($options['search_attributes'] ?? '');

        $isComponentScope = $scope === 'components';
        $routes = $isComponentScope
            ? [
                'home' => '../home.php',
                'explore' => 'explore.php',
                'boards' => 'profile.php',
                'create' => 'create-pin.php',
                'notifications' => 'notifications.php',
                'settings' => 'settings.php',
                'business' => '../business_request.php',
                'landing' => '../landing.php',
            ]
            : [
                'home' => 'home.php',
                'explore' => 'components/explore.php',
                'boards' => 'components/profile.php',
                'create' => 'components/create-pin.php',
                'notifications' => 'components/notifications.php',
                'settings' => 'components/settings.php',
                'business' => 'business_request.php',
                'landing' => 'landing.php',
            ];

        $sessionName = $_SESSION['user_name'] ?? 'User';
        $sessionEmail = $_SESSION['user_email'] ?? '';
        $profileImage = $_SESSION['profile_image'] ?? '';
        $firstLetter = strtoupper(mb_substr(trim($sessionName), 0, 1));

        if ($profileImage !== '' && !preg_match('#^https?://#', $profileImage)) {
            $profileImage = $isComponentScope ? '../' . ltrim($profileImage, '/') : ltrim($profileImage, '/');
        }
        ?>
        <aside class="app-sidebar">
            <a href="<?= htmlspecialchars($routes['home']) ?>" class="app-side-logo" aria-label="Pinterest">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"></path>
                </svg>
            </a>

            <nav class="app-side-nav" aria-label="Primary">
                <a href="<?= htmlspecialchars($routes['home']) ?>" class="app-side-link<?= $active === 'home' ? ' active' : '' ?>" title="Home">
                    <i class="bi bi-house-fill"></i>
                </a>
                <a href="<?= htmlspecialchars($routes['explore']) ?>" class="app-side-link<?= $active === 'explore' ? ' active' : '' ?>" title="Explore">
                    <i class="bi bi-compass"></i>
                </a>
                <a href="<?= htmlspecialchars($routes['boards']) ?>" class="app-side-link<?= $active === 'boards' ? ' active' : '' ?>" title="Boards">
                    <i class="bi bi-grid-3x3-gap"></i>
                </a>
                <a href="<?= htmlspecialchars($routes['create']) ?>" class="app-side-link<?= $active === 'create' ? ' active' : '' ?>" title="Create">
                    <i class="bi bi-plus-square"></i>
                </a>
                <a href="<?= htmlspecialchars($routes['notifications']) ?>" class="app-side-link<?= $active === 'notifications' ? ' active' : '' ?>" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span id="notifBadge" class="app-side-badge" style="display:none;"></span>
                </a>
                <a href="#" class="app-side-link" id="msgSideBtn" title="Messages" onclick="event.preventDefault(); if (typeof toggleMessages === 'function') { toggleMessages(); }">
                    <i class="bi bi-chat-dots"></i>
                </a>
            </nav>

            <a href="<?= htmlspecialchars($routes['settings']) ?>" class="app-side-link app-side-settings<?= $active === 'settings' ? ' active' : '' ?>" title="Settings">
                <i class="bi bi-gear"></i>
            </a>
        </aside>

        <header class="app-header">
            <div class="app-header-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    placeholder="<?= htmlspecialchars($searchPlaceholder) ?>"
                    value="<?= htmlspecialchars($searchValue) ?>"
                    <?= $searchAttributes ?>
                >
                <i class="bi bi-mic"></i>
            </div>

            <div class="app-header-user right-side">
                <button type="button" class="app-avatar-btn" id="avatarBtn" aria-label="Account">
                    <?php if ($profileImage !== ''): ?>
                        <img src="<?= htmlspecialchars($profileImage) ?>" alt="<?= htmlspecialchars($sessionName) ?>">
                    <?php else: ?>
                        <span><?= htmlspecialchars($firstLetter) ?></span>
                    <?php endif; ?>
                </button>
                <button type="button" class="app-user-caret" id="appUserCaret" aria-label="Open menu">
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div class="app-account-menu avatar-dropdown" id="avatarDD">
                    <div class="app-account-label">Currently in</div>
                    <a href="<?= htmlspecialchars($routes['boards']) ?>" class="app-account-card">
                        <?php if ($profileImage !== ''): ?>
                            <img src="<?= htmlspecialchars($profileImage) ?>" alt="<?= htmlspecialchars($sessionName) ?>">
                        <?php else: ?>
                            <span class="app-account-initial"><?= htmlspecialchars($firstLetter) ?></span>
                        <?php endif; ?>
                        <span class="app-account-copy">
                            <strong><?= htmlspecialchars($sessionName) ?></strong>
                            <small><?= htmlspecialchars($sessionEmail) ?></small>
                        </span>
                    </a>
                    <a href="<?= htmlspecialchars($routes['business']) ?>" class="app-account-item">Convert to business</a>
                    <a href="<?= htmlspecialchars($routes['landing']) ?>" class="app-account-item app-account-logout">Log out</a>
                </div>
            </div>
        </header>

        <script>
            (function () {
                const avatarBtn = document.getElementById('avatarBtn');
                const caretBtn = document.getElementById('appUserCaret');
                const menu = document.getElementById('avatarDD');

                if (!avatarBtn || !caretBtn || !menu) {
                    return;
                }

                function toggleMenu(event) {
                    event.stopPropagation();
                    menu.classList.toggle('show');
                }

                avatarBtn.addEventListener('click', toggleMenu);
                caretBtn.addEventListener('click', toggleMenu);
                document.addEventListener('click', function (event) {
                    if (!event.target.closest('.app-header-user')) {
                        menu.classList.remove('show');
                    }
                });
            })();
        </script>
        <?php
    }
}
