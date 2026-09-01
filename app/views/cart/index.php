<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prepare cart items from session
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {
        // Calculate subtotal for each item
        $subtotal = $item['price'] * $item['quantity'];
        
        // Add subtotal and cart_item_id to item array
        $item['subtotal'] = $subtotal;
        $item['cart_item_id'] = $product_id; // Use product_id as cart_item_id
        
        // Add to cart items array
        $cart_items[] = $item;
        
        // Add to total
        $total += $subtotal;
    }
}
?>

<h1 class="mb-4">Mon Panier</h1>

<?php if (empty($cart_items)) : ?>
    <div class="alert alert-info">
        <i class="fas fa-shopping-cart me-2"></i>Votre panier est vide.
    </div>
    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/product" class="btn btn-primary btn-lg">
            Continuer mes achats
        </a>
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Articles du panier (<?= count($cart_items) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cart_items as $item) : ?>
                        <div class="row mb-4 border-bottom pb-4">
                        <div class="col-md-2 col-4">
    <?php 
    // Get the image path from the item
    $imagePath = $item['image'] ?: 'placeholder.jpg';
    ?>
    <img src="<?= BASE_URL ?>/public/images/products/<?= $imagePath ?>" 
         class="img-fluid rounded" 
         alt="<?= htmlspecialchars($item['name']) ?>">
</div>
                            <div class="col-md-6 col-8">
                                <h5><?= htmlspecialchars($item['name']) ?></h5>
                                <p class="text-muted mb-2">Prix unitaire : <?= number_format($item['price'], 2, ',', ' ') ?> €</p>
                                <p class="text-muted small mb-0">Disponible en stock: <?= isset($item['stock_quantity']) ? $item['stock_quantity'] : 'En stock' ?></p>
                                
                                <div class="d-flex align-items-center mt-3">
                                    <form action="<?= BASE_URL ?>/cart/update" method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="product_id" value="<?= $item['cart_item_id'] ?>">
                                        <div class="input-group input-group-sm me-3" style="width: 100px;">
                                            <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity(this.parentNode)">-</button>
                                            <input type="number" name="quantity" class="form-control text-center" value="<?= $item['quantity'] ?>" min="1" max="<?= isset($item['stock_quantity']) ? $item['stock_quantity'] : 10 ?>">
                                            <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity(this.parentNode, <?= isset($item['stock_quantity']) ? $item['stock_quantity'] : 10 ?>)">+</button>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Mettre à jour</button>
                                    </form>
                                    
                                    <a href="<?= BASE_URL ?>/cart/remove/<?= $item['cart_item_id'] ?>" class="btn btn-sm btn-outline-danger ms-auto">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 col-12 mt-3 mt-md-0 text-md-end">
                                <h5 class="text-success"><?= number_format($item['subtotal'], 2, ',', ' ') ?> €</h5>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="<?= BASE_URL ?>/product" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Continuer mes achats
                        </a>
                        <a href="<?= BASE_URL ?>/cart/clear" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-2"></i>Vider le panier
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Récapitulatif</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            Sous-total
                            <span><?= number_format($total, 2, ',', ' ') ?> €</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            Livraison
                            <?php $shipping = $total >= 50 ? 0 : 4.90; ?>
                            <span><?= $shipping > 0 ? number_format($shipping, 2, ',', ' ') . ' €' : 'Gratuit' ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                            <div>
                                <strong>Total</strong>
                                <p class="mb-0 text-muted">(TVA incluse)</p>
                            </div>
                            <span class="fs-5 fw-bold"><?= number_format($total + $shipping, 2, ',', ' ') ?> €</span>
                        </li>
                    </ul>
                    
                    <a href="<?= BASE_URL ?>/cart/checkout" class="btn btn-primary btn-lg w-100">
                        Passer au paiement
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function incrementQuantity(parent, max) {
            const input = parent.querySelector('input');
            const currentValue = parseInt(input.value);
            if (currentValue < max) {
                input.value = currentValue + 1;
            }
        }
        
        function decrementQuantity(parent) {
            const input = parent.querySelector('input');
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }
    </script>
<?php endif; ?>