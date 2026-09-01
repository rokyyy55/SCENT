<?php
/**
 * Save this as app/views/order/confirmation.php
 */

// Add this at the top of confirmation.php
if (!isset($order)) {
    echo '<div class="alert alert-danger">Error: Order data not available</div>';
    var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2));
    exit;
}

// If the order is delivered, show delivered.php and exit
if (isset($order->status) && $order->status === 'Delivered') {
    include APP_PATH . '/views/orders/delivered.php';
    return;
}
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        
                    </div>
                    
                    <h2 class="mb-3 text-success">Merci pour votre commande!</h2>
                    <p class="lead mb-4">Votre commande #<?= $order->order_id ?> a été confirmée.</p>
                    
<div class="order-details p-4 mb-4 bg-light rounded" style="color: navy;">
    <h4 class="mb-3" style="color: navy;">Détails de la commande</h4>
                        <div class="row">
                            <div class="col-sm-6 text-start">
                                <p><strong>Date:</strong> <?= date('d/m/Y H:i', strtotime($order->order_date)) ?></p>
                                <p><strong>Statut:</strong> <span class="badge bg-success">Confirmé</span></p>
                                <?php if (!empty($order->phone_number)): ?>
                                    <p><strong>Téléphone:</strong> <?= htmlspecialchars($order->phone_number) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6 text-start">
                                <p><strong>Total:</strong> <?= number_format($order->total_amount, 2) ?> €</p>
                                <p><strong>Adresse de livraison:</strong><br><?= htmlspecialchars($order->delivery_town) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($order_items)): ?>
                    <div class="order-items mb-4">
                        <h4 class="mb-3">Produits commandés</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th>Prix</th>
                                        <th>Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php foreach ($order_items as $item): ?>
<?php
    $imageRelPath = ltrim($item->image, '/'); // Remove leading slash if present
    $imageFile = $_SERVER['DOCUMENT_ROOT'] . '/scent/public/' . $imageRelPath;
    $placeholder = BASE_URL . '/public/images/products/placeholder.jpg';
    if (empty($imageRelPath) || !file_exists($imageFile)) {
        $imagePath = $placeholder;
    } else {
        $imagePath = BASE_URL . '/public/' . $imageRelPath;
    }
?>
<tr>
    <td>
        <div class="d-flex align-items-center">
            <img src="<?= $imagePath ?>" 
                 alt="<?= $item->name ?>" 
                 class="img-thumbnail me-2" 
                 style="width: 50px; height: 50px;">
            <div><?= $item->name ?></div>
        </div>
    </td>
    <td><?= $item->quantity ?></td>
    <td><?= number_format($item->price, 2) ?> €</td>
    <td><?= number_format($item->price * $item->quantity, 2) ?> €</td>
</tr>
<?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th><?= number_format($order->total_amount, 2) ?> €</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary">Continuer vos achats</a>
                        <a href="<?= BASE_URL ?>/orders/cancelOrder/<?= $order->order_id ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande?')">
                            Annuler la commande
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>