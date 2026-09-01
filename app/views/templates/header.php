<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/scent'); // Adjust if your base path is different
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scent</title>
    <meta name="description" content="Découvrez notre collection exclusive de parfums de luxe.">
    
    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    
    <?php
    // Inclure des fichiers CSS spécifiques si nécessaire
    if (isset($page_css)) {
        foreach ($page_css as $css) {
            echo '<link rel="stylesheet" href="' . BASE_URL . '/css/' . $css . '.css">';
        }
    }
    ?>
    
    <!-- Custom background image styling -->
    <style>
    /* Custom color theme - Gold and Royal Blue */
    :root {
      --royal-blue:rgb(142, 219, 235);
      --royal-blue-light: #1e4176;
      --gold:rgb(3, 24, 41);
      --gold-light:rgb(8, 41, 78);
    }
    
    /* Background image */
    body {
      background-image: url('<?= BASE_URL ?>/public/images/backk.jpg');
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      color: white;
    }
    
    /* Add a semi-transparent overlay to improve text readability */
    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(19, 41, 75, 0.7); /* Semi-transparent royal blue */
      z-index: -1;
    }
    
    .container {
      background-color: transparent;
      color: white;
    }
    
    .bg-dark {
      background-color: var(--gold) !important;
    }
    
    .navbar, footer {
      background-color: var(--gold) !important;
    }
    
    /* Text colors for contrast */
    .text-white {
      color: white !important;
    }
    
    .navbar-dark .navbar-nav .nav-link, footer {
      color: var(--royal-blue) !important;
    }
    
    .navbar-brand {
      color: var(--royal-blue) !important;
      font-weight: bold;
    }
    
    /* Card styling */
    .card {
      background-color: rgba(30, 65, 118, 0.8); /* Semi-transparent royal blue light */
      border-color: var(--gold);
      color: white;
    }
    
    .card-body {
      background-color: transparent;
      color: white;
    }
    
    .card-title, .card-text {
      color: white;
    }
    
    /* Button styling */
    .btn-primary {
      background-color: var(--gold);
      border-color: var(--gold);
      color: var(--royal-blue);
    }
    
    .btn-primary:hover, .btn-primary:focus {
      background-color: var(--gold-light);
      border-color: var(--gold-light);
      color: var(--royal-blue);
    }
    
    .btn-outline-dark {
      color: var(--gold);
      border-color: var(--gold);
    }
    
    .btn-outline-dark:hover {
      background-color: var(--gold);
      border-color: var(--gold);
      color: var(--royal-blue);
    }
    
    .btn-dark {
      background-color: var(--gold);
      border-color: var(--gold);
      color: var(--royal-blue);
    }
    
    /* Section headings */
    h1, h2, h3, h4, h5, h6 {
      color: white;
    }
    /* Add to your <style> section in head */
.navbar-collapse {
    transition: all 0.3s ease;
}

.dropdown-menu {
    z-index: 1050; /* Ensure dropdowns appear above other elements */
}

@media (max-width: 992px) {
    .navbar-collapse {
        background-color: var(--gold);
        padding: 15px;
        margin-top: 10px;
        border-radius: 5px;
    }
    
    .dropdown-menu {
        background-color: rgba(30, 65, 118, 0.95);
    }
}
    
    /* Footer links */
    footer a {
      color: var(--royal-blue) !important;
    }
    
    footer a:hover {
      color: var(--royal-blue-light) !important;
    }
    
    /* Form elements */
    .form-control {
      background-color: rgba(30, 65, 118, 0.7);
      border-color: var(--gold);
      color: white;
    }
    
    .form-control:focus {
      background-color: rgba(30, 65, 118, 0.9);
      border-color: var(--gold-light);
      color: white;
    }
    
    /* Fix for container backgrounds */
    .container, .row, .col, .col-md-3, .col-md-4, .col-md-6, .col-md-8, .col-md-12 {
      background-color: transparent;
    }
    
    /* Make text more readable on background image */
    p, span, a, label, input, textarea, select {
      text-shadow: 0 0 3px rgba(0, 0, 0, 0.5);
    }
    
    /* Gender image styling (moved from homepage) */
    .gender-image-container {
        max-width: 200px;
        margin: 0 auto;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .gender-image-container img {
        width: 100%;
        height: auto;
        display: block;
        transition: all 0.3s ease;
        /* No initial drop-shadow */
        filter: drop-shadow(0 0 0 rgba(0, 0, 0, 0));
    }
    
    .gender-image-container:hover img {
        transform: scale(1.05);
        /* Golden glow that follows the exact shape of the PNG */
        filter: drop-shadow(0 0 8px rgba(57, 116, 164, 1)) 
               drop-shadow(0 0 12px rgb(26, 62, 154));
    }
    
    /* For mobile screens */
    @media (max-width: 768px) {
        .gender-image-container {
            max-width: 150px;
        }
    }
    
    /* Profile dropdown styling */
    .profile-dropdown {
        min-width: 250px;
    }
    
    .profile-dropdown .dropdown-header {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--royal-blue);
    }
    
    .profile-dropdown .dropdown-item {
        padding: 0.5rem 1.5rem;
        transition: all 0.2s;
    }
    
    .profile-dropdown .dropdown-item:hover {
        background-color: rgba(142, 219, 235, 0.1);
    }
    
    .profile-dropdown .dropdown-divider {
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    .profile-dropdown .user-info {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .profile-dropdown .user-info .user-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .profile-dropdown .user-info .user-email {
        font-size: 0.8rem;
        opacity: 0.8;
    }
    
    #editProductModal label {
        color: #001f4d !important;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL ?>/public/images/logo.jpg" alt="Scent" height="50" class="me-2">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="categoriesModalToggle" role="button" data-bs-toggle="modal" data-bs-target="#categoriesModal">
                            Catégories
                        </a>
                    </li>

                    <!-- Categories Modal -->
                    <div class="modal fade" id="categoriesModal" tabindex="-1" aria-labelledby="categoriesModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content bg-dark text-white">
                          <div class="modal-header">
                            <h5 class="modal-title" id="categoriesModalLabel">Catégories</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="container">
                              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
                                <?php
                                // Récupérer les catégories à partir de la base de données
                                $database = new Database();
                                $conn = $database->connect();
                                $query = 'SELECT category_id, name FROM categories ORDER BY name';
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($categories as $category) {
                                    echo '<div class="col">';
                                    echo '<a href="' . BASE_URL . '/product/search?category=' . $category['category_id'] . '&gender=Homme" class="btn btn-outline-light w-100 mb-2">' . htmlspecialchars($category['name']) . '</a>';
                                    echo '</div>';
                                }
                                ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="brandsModalToggle" role="button" data-bs-toggle="modal" data-bs-target="#brandsModal">
                            Marques
                        </a>
                    </li>

                    <!-- Brands Modal -->
                    <div class="modal fade" id="brandsModal" tabindex="-1" aria-labelledby="brandsModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content bg-dark text-white">
                          <div class="modal-header">
                            <h5 class="modal-title" id="brandsModalLabel">Marques</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="container">
                              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
                                <?php
                                // Récupérer les marques spécifiques à partir de la base de données
                                $allowed_brands = [
                                    'Dior', 'Chanel', 'Guerlain', 'Tom Ford', 'Yves Saint Laurent', 'Jo Malone', 'Hermès',
                                    'Byredo', 'Bvlgari', 'Davidoff', 'Paco Rabanne', 'Kenzo', 'Dolce & Gabbana',
                                    'Ralph Lauren', 'Versace', 'Acqua di Parma', 'Montblanc', 'Armaf', 'Givenchy',
                                    'Roja Parfums', 'Giorgio Armani', 'Mancera', 'Calvin Klein', 'Issey Miyake', 'Nautica'
                                ];
                                $placeholders = implode(',', array_fill(0, count($allowed_brands), '?'));
                                $query = "SELECT brand_id, name FROM brands WHERE name IN ($placeholders) ORDER BY name";
                                $stmt = $conn->prepare($query);
                                $stmt->execute($allowed_brands);
                                $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($brands as $brand) {
                                    echo '<div class="col">';
                                    echo '<a href="' . BASE_URL . '/product/search?brand=' . $brand['brand_id'] . '&gender=Homme" class="btn btn-outline-light w-100 mb-2">' . htmlspecialchars($brand['name']) . '</a>';
                                    echo '</div>';
                                }
                                ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="#" id="priceModalToggle" role="button" data-bs-toggle="modal" data-bs-target="#priceModal">
                            <i class="fas fa-euro-sign me-1"></i> Prix
                        </a>
                    </li>

                    <!-- Price Modal -->
                    <div class="modal fade" id="priceModal" tabindex="-1" aria-labelledby="priceModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-md modal-dialog-scrollable">
                        <div class="modal-content bg-dark text-white">
                          <div class="modal-header">
                            <h5 class="modal-title" id="priceModalLabel">Prix</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="list-group">
                                <a href="<?= BASE_URL ?>/product/search?price_range=100" class="list-group-item list-group-item-action btn-outline-light mb-2">Moins de 100 €</a>
                                <a href="<?= BASE_URL ?>/product/search?price_range=150" class="list-group-item list-group-item-action btn-outline-light mb-2">Moins de 150 €</a>
                                <a href="<?= BASE_URL ?>/product/search?price_range=200" class="list-group-item list-group-item-action btn-outline-light mb-2">Moins de 200 €</a>
                                <a href="<?= BASE_URL ?>/product/search?price_range=250" class="list-group-item list-group-item-action btn-outline-light mb-2">Moins de 250 €</a>
                                <a href="<?= BASE_URL ?>/product/search?price_range=300" class="list-group-item list-group-item-action btn-outline-light mb-2">Moins de 300 €</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </ul>
                <form class="d-flex me-3" action="<?= BASE_URL ?>/product/search" method="GET">
                    <input class="form-control me-2" type="search" name="keyword" placeholder="Rechercher..." aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= BASE_URL ?>/cart">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <?php
                            // Afficher le nombre d'articles dans le panier
                            global $session;
                            $cart_items = 0;
                            
                            if ($session->has('cart_id')) {
                                require_once APP_PATH . '/models/Cart.php';
                                $cart = new Cart();
                                if ($cart->getById($session->get('cart_id'))) {
                                    $cart_items = $cart->getItemCount();
                                }
                            }
                            
                            if ($cart_items > 0) {
                                echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' . $cart_items . '</span>';
                            }
                            ?>
                        </a>
                    </li>
                    <?php if ($session->has('user_id')) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle fa-lg me-1"></i>
                                <span class="d-none d-lg-inline">Mon Compte</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="userDropdown">
                                <li class="user-info">
                                    <div class="user-name"><?= $session->get('username') ?></div>
                                    <div class="user-email"><?= $session->get('email') ?></div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Mon Compte</h6></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile"><i class="fas fa-user me-2"></i>Profil</a></li>
                                <!-- Removed "Adresses" and "Liste de souhaits" as per user request -->
                                <!-- Removed the "Mot de passe" button as per user request -->
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Mes Achats</h6></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/orders/listOrders"><i class="fas fa-shopping-bag me-2"></i>Historique des commandes</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/orders/codes"><i class="fas fa-code me-2"></i>Codes</a></li>
                                <?php if ($session->get('is_admin')): ?>
                                    <li><a class="dropdown-item fw-bold text-primary" href="<?= BASE_URL ?>/admin"><i class="fas fa-tachometer-alt me-2"></i>Tableau de bord</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/user/logout"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt fa-lg me-1"></i>
                                <span class="d-none d-lg-inline">Connexion</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Bannière flash pour les messages -->
    <?php if ($session->has('flash')) : ?>
        <?php foreach ($session->get('flash') as $type => $message) : ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show mb-0" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
        <?php $session->remove('flash'); ?>
    <?php endif; ?>

    <!-- Your page content goes here -->

    <!-- jQuery (must be loaded BEFORE DataTables and any script using $) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <!-- Your custom JS -->
    <script src="<?= BASE_URL ?>/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Debug script to ensure Bootstrap is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Bootstrap initialized:', typeof bootstrap !== 'undefined');
        
        // Manually initialize dropdowns if needed
        var dropdowns = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            .map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
    });
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/**
 * Dropdown Diagnostic Test
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log("=== DROPDOWN DIAGNOSTIC TEST ===");
    
    // Test 1: Check if Bootstrap is loaded
    console.log("1. Bootstrap loaded:", typeof bootstrap !== 'undefined' ? "✅ Yes" : "❌ No");
    
    // Test 2: Check dropdown elements exist
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log(`2. Found ${dropdowns.length} dropdown toggles`);
    
    // Test 3: Verify click handlers
    dropdowns.forEach((dropdown, index) => {
        dropdown.addEventListener('click', function(e) {
            console.log(`3. Dropdown #${index+1} clicked - Default prevented: ${e.defaultPrevented}`);
        });
    });
    
    // Test 4: Mobile menu test
    const mobileToggle = document.querySelector('.navbar-toggler');
    if (mobileToggle) {
        console.log("4. Mobile toggle found:", mobileToggle);
        mobileToggle.addEventListener('click', function() {
            console.log("Mobile menu clicked - Target:", this.dataset.bsTarget);
        });
    }
    
    // Test 5: Manual dropdown creation test
    const testDropdown = dropdowns[0];
    if (testDropdown) {
        try {
            const dropdown = new bootstrap.Dropdown(testDropdown);
            console.log("5. Manual dropdown creation:", "✅ Success", dropdown);
        } catch (e) {
            console.log("5. Manual dropdown creation:", "❌ Failed", e.message);
        }
    }
    
    console.log("=== TEST COMPLETE ===");
});
</script>
</body>
</html>