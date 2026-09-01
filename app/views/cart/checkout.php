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
        $item['cart_item_id'] = $product_id;
        
        // Add to cart items array
        $cart_items[] = $item;
        
        // Add to total
        $total += $subtotal;
    }
}

// Calculate shipping
$shipping_standard = $total >= 50 ? 0 : 4.90;

// Loyalty info for modal
$loyalty_info = null;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../../config/database.php';
    $db = (new Database())->connect();
    $user_id = $_SESSION['user_id'];
    // Count delivered items
    $stmt = $db->prepare("SELECT SUM(oi.quantity) FROM orders o JOIN order_items oi ON o.order_id = oi.order_id WHERE o.user_id = ? AND o.status = 'Delivered'");
    $stmt->execute([$user_id]);
    $delivered_items = (int)$stmt->fetchColumn();
    // Get code if available
    $stmt = $db->prepare("SELECT last_discount_code, last_discount_used FROM user_loyalty WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $code = $row && !$row['last_discount_used'] ? $row['last_discount_code'] : null;
    $loyalty_info = [
        'delivered_items' => $delivered_items,
        'code' => $code,
        'needed' => max(0, 5 - $delivered_items)
    ];
}
?>

<h1 class="mb-4">Finaliser ma commande</h1>

<div class="row">
    <div class="col-lg-8">
        <!-- Afficher les erreurs de validation s'il y en a -->
        <?php
        global $session;
        if (isset($session) && $session->has('checkout_errors')) {
            $errors = $session->get('checkout_errors');
            echo '<div class="alert alert-danger mb-4">';
            echo '<h5 class="alert-heading">Erreurs de validation</h5>';
            echo '<ul class="mb-0">';
            foreach ($errors as $error) {
                echo '<li>' . $error . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            $session->remove('checkout_errors');
        }
        ?>

        <div class="card mb-4">
<div class="card-header bg-white py-3">
    <h5 class="mb-0" style="color: navy;">Mode de paiement</h5>
</div>
            <div class="card-body">
                <div id="card-info" class="mt-3">
                    <form id="payment-form" method="POST" action="<?= BASE_URL ?>/orders/placeOrder">
                        <div class="mb-3">
                            <label for="delivery_town" class="form-label">Delivery Town <span style="color:red">*</span></label>
                            <input type="text" class="form-control" id="delivery_town" name="delivery_town" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number (optional)</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number">
                        </div>
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Numéro de carte</label>
                            <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                        </div>
<div class="mb-3 row">
    <label class="form-label">Date d'expiration</label>
    <div class="col-6">
        <input type="text" class="form-control" id="card_expiry_month" name="card_expiry_month" placeholder="MM" maxlength="2" required>
    </div>
    <div class="col-6">
        <input type="text" class="form-control" id="card_expiry_year" name="card_expiry_year" placeholder="AA" maxlength="2" required>
    </div>
</div>
                        <div class="mb-3">
                            <label for="card_cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="123" required>
                        </div>
                        <div class="mb-3">
                            <label for="card_name" class="form-label">Nom sur la carte</label>
                            <input type="text" class="form-control" id="card_name" name="card_name" placeholder="NOM PRÉNOM" required>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="mb-3">
                            <label for="discount_code" class="form-label">Discount Code (if you have one)</label>
                            <input type="text" class="form-control" id="discount_code" name="discount_code">
                        </div>
                        <?php endif; ?>
                        <input type="hidden" name="payment_method" value="card">
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="confirm-btn">
                            Confirmer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Récapitulatif de commande</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($cart_items as $item) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <span class="badge bg-secondary me-2"><?= $item['quantity'] ?></span>
                                <?= htmlspecialchars($item['name']) ?>
                            </div>
                            <span><?= number_format($item['subtotal'], 2, ',', ' ') ?> €</span>
                        </li>
                    <?php endforeach; ?>
                    
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0 mt-3">
                        Sous-total
                        <span><?= number_format($total, 2, ',', ' ') ?> €</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Livraison
                        <span class="shipping-cost"><?= $shipping_standard > 0 ? number_format($shipping_standard, 2, ',', ' ') . ' €' : 'Gratuit' ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                        <div>
                            <strong>Total</strong>
                            <p class="mb-0 text-muted">(TVA incluse)</p>
                        </div>
                        <span class="fs-5 fw-bold total-price"><?= number_format($total + $shipping_standard, 2, ',', ' ') ?> €</span>
                    </li>
                </ul>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        J'accepte les <a href="#" target="_blank">conditions générales de vente</a>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($loyalty_info && $loyalty_info['needed'] == 0): ?>
<!-- Loyalty Modal -->
<div class="modal" id="loyaltyModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="color:navy;">Récompense Fidélité</h5>
      </div>
      <div class="modal-body">
        <p style="color:navy;">Félicitations ! Vous avez gagné un code de réduction de 50&nbsp;$.</p>
        <?php if ($loyalty_info['code']): ?>
          <p>Votre code : <strong id="userLoyaltyCode" style="color:navy;"><?= htmlspecialchars($loyalty_info['code']) ?></strong></p>
        <?php endif; ?>
        <div class="mb-3">
          <label for="modal_discount_code" class="form-label" style="color:navy;">Entrez votre code ici :</label>
          <input type="text" class="form-control" id="modal_discount_code" value="<?= $loyalty_info['code'] ? htmlspecialchars($loyalty_info['code']) : '' ?>">
        </div>
        <button class="btn btn-primary" id="applyLoyaltyCode">Appliquer le code</button>
        <button class="btn btn-secondary" id="closeLoyaltyModal">Fermer</button>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('closeLoyaltyModal').onclick = function() {
  document.getElementById('loyaltyModal').style.display = 'none';
};
document.getElementById('applyLoyaltyCode').onclick = function() {
  var code = document.getElementById('modal_discount_code').value;
  fetch('<?= BASE_URL ?>/orders/validateCodeAjax?code=' + encodeURIComponent(code))
    .then(response => response.json())
    .then(data => {
      if (data.valid) {
        document.getElementById('discount_code').value = code;
        var totalSpan = document.querySelector('.total-price');
        if (totalSpan) {
          var total = parseFloat(totalSpan.textContent.replace(/[^0-9.]/g, ''));
          var newTotal = Math.max(0, total - 50);
          totalSpan.textContent = newTotal.toFixed(2) + ' €';
        }
        document.getElementById('loyaltyModal').style.display = 'none';
      } else {
        alert('Code invalide !');
      }
    });
};
</script>
<?php endif; ?>
