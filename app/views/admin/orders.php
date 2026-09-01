<?php
// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ' . BASE_URL);
    exit();
}
?>

<div class="container mt-4">
    <h1>Liste des commandes</h1>
    <?php if (empty($orders)): ?>
        <p>Aucune commande trouvée.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Numéro de commande</th>
                        <th>Utilisateur</th>
                        <th>Date</th>
                        <th>Montant total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order->order_id) ?></td>
                        <td><?= htmlspecialchars($order->username) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($order->order_date)) ?></td>
                        <td><?= number_format($order->total_amount, 2) ?> €</td>
                        <td>
                            <?php
                            $statusClass = 'secondary';
                            if ($order->status === 'Pending') {
                                $statusClass = 'info';
                            } elseif ($order->status === 'Processing') {
                                $statusClass = 'warning';
                            } elseif ($order->status === 'Shipped') {
                                $statusClass = 'primary';
                            } elseif ($order->status === 'Delivered') {
                                $statusClass = 'success';
                            } elseif ($order->status === 'Cancelled') {
                                $statusClass = 'danger';
                            }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= htmlspecialchars(ucfirst($order->status)) ?></span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/orders/orderconfirmation/<?= $order->order_id ?>" class="btn btn-sm btn-primary">Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
