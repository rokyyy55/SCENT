<style>
/* Custom Order History Styles */
.order-history-card {
    background: transparent;
    border-radius: 18px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    border: 1px solid rgba(3, 24, 41, 0.2);
    overflow: hidden;
}
.order-history-card .card-header {
    background: linear-gradient(90deg, #1e4176 0%, #8edbeb 100%);
    color: #fff;
    border-bottom: 2px solid #031829;
    font-size: 1.5rem;
    font-weight: 600;
    letter-spacing: 1px;
}
.order-history-card .table {
    background: transparent;
    color: #fff;
}
.order-history-card .table th, .order-history-card .table td {
    vertical-align: middle;
    background: rgba(30, 65, 118, 0.7);
    color: #fff;
}
.order-history-card .table-hover tbody tr:hover {
    background-color: rgba(142, 219, 235, 0.08);
    transition: background 0.2s;
}
.order-history-card .badge {
    font-size: 1em;
    padding: 0.5em 1em;
    border-radius: 12px;
    letter-spacing: 0.5px;
}
.order-history-card .btn-glow-gray {
    border-radius: 20px;
    font-weight: 500;
    background: #444;
    color: #fff;
    border: none;
    box-shadow: 0 0 8px 2px #bbb, 0 0 16px 4px #888;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    text-shadow: 0 1px 2px #000;
}
.order-history-card .btn-glow-gray:hover {
    background: #888;
    color: #fff;
    box-shadow: 0 0 16px 4px #fff, 0 0 32px 8px #bbb;
}
.order-history-card .alert-info {
    background: rgba(142, 219, 235, 0.15);
    color: #8edbeb;
    border: none;
    border-radius: 10px;
}

/* --- Dashboard Styles for User Orders, with Gray instead of Yellow --- */
.dashboard-btn {
    background: linear-gradient(145deg, #1a237e, #0d47a1);
    color: #888;
    border: 2px solid #888;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(26, 35, 126, 0.3);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.dashboard-btn:hover {
    background: linear-gradient(145deg, #0d47a1, #1a237e);
    color: #888;
    transform: translateY(-2px);
    box-shadow: 0 0 20px rgba(136, 136, 136, 0.5),
                0 0 40px rgba(136, 136, 136, 0.3),
                0 0 60px rgba(136, 136, 136, 0.1);
    border-color: #888;
}
.dashboard-btn:active {
    transform: translateY(1px);
    box-shadow: 0 2px 10px rgba(26, 35, 126, 0.3);
}
.dashboard-btn i {
    margin-right: 8px;
    color: #888;
}
.dashboard-card {
    background: transparent;
    border: 1px solid rgba(26, 35, 126, 0.1);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(26, 35, 126, 0.15);
}
.dashboard-card .card-header {
    background: linear-gradient(145deg, #1a237e, #0d47a1);
    color: #888;
    border-bottom: 2px solid #888;
    border-radius: 12px 12px 0 0 !important;
    font-size: 2.2rem;
    font-weight: bold;
    letter-spacing: 1px;
}
.dashboard-card .card-body {
    padding: 1.5rem;
    background: transparent;
}
.dashboard-table {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    margin-bottom: 0;
    background: transparent;
}
.dashboard-table thead {
    background: linear-gradient(145deg, #1a237e, #0d47a1);
    color: #888;
}
.dashboard-table th {
    border: none;
    padding: 15px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: transparent;
}
.dashboard-table td {
    padding: 12px 15px;
    vertical-align: middle;
    background: rgba(30, 65, 118, 0.7);
    color: #fff;
    font-weight: 500;
}
.dashboard-table tbody tr:hover {
    background-color: rgba(26, 35, 126, 0.15);
}
.dashboard-card .badge {
    font-size: 1em;
    padding: 0.5em 1em;
    border-radius: 12px;
    letter-spacing: 0.5px;
}
.dashboard-card .alert-info {
    background: rgba(142, 219, 235, 0.15);
    color: #8edbeb;
    border: none;
    border-radius: 10px;
}

/* Card-based order history, transparent for background image */
.order-list-card {
    background: rgba(30, 65, 118, 0.6);
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
    margin-bottom: 2rem;
    border: 1px solid #888;
    color: #fff;
    padding: 1.5rem 2rem;
}
.order-list-card .order-header {
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #fff;
}
.order-list-card .order-meta {
    font-size: 1rem;
    color: #bbb;
    margin-bottom: 0.5rem;
}
.order-list-card .order-status {
    font-size: 1rem;
    font-weight: 600;
    color: #888;
    margin-bottom: 0.5rem;
}
.order-list-card .order-products {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-top: 1rem;
}
.order-list-card .product-item {
    background: rgba(255,255,255,0.08);
    border-radius: 10px;
    padding: 0.7rem 1rem;
    display: flex;
    align-items: center;
    min-width: 220px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.order-list-card .product-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 1rem;
    border: 1px solid #888;
    background: #fff;
}
.order-list-card .product-info {
    flex: 1;
}
.order-list-card .product-name {
    font-size: 1.05rem;
    font-weight: 500;
    color: #fff;
}
.order-list-card .product-qty {
    font-size: 0.95rem;
    color: #bbb;
}
.order-list-card .dashboard-btn {
    margin-top: 1rem;
    background: linear-gradient(145deg, #1a237e, #0d47a1);
    color: #888;
    border: 2px solid #888;
    padding: 8px 18px;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(26, 35, 126, 0.3);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.order-list-card .dashboard-btn:hover {
    background: linear-gradient(145deg, #0d47a1, #1a237e);
    color: #888;
    border-color: #888;
}
</style>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="mb-4" style="color:#fff;"><i class="fas fa-shopping-bag me-2"></i>Mes Commandes</h2>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $i => $order): ?>
                    <div class="order-list-card">
                        <div class="order-header">Commande #<?= $order->order_number ?? $order->order_id ?></div>
                        <div class="order-meta">
                            Date : <?= isset($order->created_at) ? date('d/m/Y H:i', strtotime($order->created_at)) : (isset($order->order_date) ? date('d/m/Y H:i', strtotime($order->order_date)) : '') ?>
                            &nbsp;|&nbsp; Total : <b><?= isset($order->total) ? number_format($order->total, 2, ',', ' ') : (isset($order->total_amount) ? number_format($order->total_amount, 2, ',', ' ') : '') ?> €</b>
                        </div>
                        <div class="order-status">
                            Statut : <span class="badge bg-<?=
                                (isset($order->status) && strtolower($order->status) == 'completed') ? 'success' :
                                ((isset($order->status) && strtolower($order->status) == 'processing') ? 'warning' : 'secondary')
                            ?>"> <?= isset($order->status) ? ucfirst($order->status) : 'Inconnu' ?> </span>
                        </div>
                        <div class="order-products">
                            <?php
                            // Fetch order items for this order
                            require_once APP_PATH . '/models/Order.php';
                            $orderModel = new Order();
                            $orderModel->order_id = $order->order_id;
                            $items = $orderModel->getItems();
                            foreach ($items as $item):
                            ?>
                                <div class="product-item">
                                    <?php
                                    $imgFile = !empty($item['image']) ? $item['image'] : 'placeholder.jpg';
                                    $imgPath = $_SERVER['DOCUMENT_ROOT'] . '/scent/public/images/products/' . $imgFile;
                                    $imgUrl = BASE_URL . '/public/images/products/' . $imgFile;
                                    if (!file_exists($imgPath)) {
                                        $imgUrl = BASE_URL . '/public/images/products/placeholder.jpg';
                                    }
                                    ?>
                                    <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <div class="product-info">
                                        <div class="product-name"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="product-qty">Quantité : <?= $item['quantity'] ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?= BASE_URL ?>/order/details/<?= $order->order_id ?>" class="dashboard-btn btn btn-sm">
                            <i class="fas fa-eye"></i> Voir détails
                        </a>
                        <?php if (isset($order->status) && strtolower($order->status) !== 'cancelled'): ?>
                            <form method="POST" action="<?= BASE_URL ?>/order/cancel/<?= $order->order_id ?>" style="display:inline-block; margin-left: 10px;">
                                <button type="submit" class="dashboard-btn btn btn-sm" style="background:#888; color:#fff;">Annuler</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($i === 0): ?>
                            <div class="thank-you">Merci pour votre commande !</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">Vous n'avez pas encore passé de commande.</div>
                <a href="<?= BASE_URL ?>/product/search" class="btn btn-primary mt-3">Découvrir nos produits</a>
            <?php endif; ?>
        </div>
    </div>
</div> 