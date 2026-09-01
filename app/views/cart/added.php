<?php
/**
 * Vue de confirmation d'ajout au panier
 * Cette vue est utilisée comme fallback pour les utilisateurs sans JavaScript
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get cart count and total
$cart_count = 0;
$cart_total = 0;

foreach ($_SESSION['cart'] as $item) {
    $cart_count += $item['quantity'];
    $cart_total += $item['price'] * $item['quantity'];
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i>Produit ajouté au panier</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= $_SESSION['success']; ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($product) && $product): ?>
                        <div class="d-flex mb-4">
                            <div class="me-3" style="width: 100px; height: 100px;">
                                <?php 
                                // Fix the image path handling
                                $imagePath = $product['image'] ?: 'placeholder.jpg';
                                // Remove the leading slash if it exists
                                if (strpos($imagePath, '/') === 0) {
                                    $imagePath = ltrim($imagePath, '/');
                                }
                                ?>
                                <img src="<?= BASE_URL ?>/public/images/products/<?= $imagePath ?>"
                                     class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <h5><?= htmlspecialchars($product['name']) ?></h5>
                                <?php if (isset($product['brand_name']) && $product['brand_name']): ?>
                                    <p class="mb-1">Marque: <?= htmlspecialchars($product['brand_name']) ?></p>
                                <?php endif; ?>
                                <p class="mb-1">Prix: <?= number_format($product['price'], 2, ',', ' ') ?> €</p>
                                <p class="mb-0">Quantité: 1</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <p class="mb-1">Votre panier contient maintenant <?= $cart_count ?> article(s).</p>
                            <p class="mb-0">Total: <strong><?= number_format($cart_total, 2, ',', ' ') ?> €</strong></p>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                        <a href="<?= BASE_URL ?>/cart" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>Voir le panier
                        </a>
                        <a href="<?= BASE_URL ?>/product" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Continuer mes achats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>