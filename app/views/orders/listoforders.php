<?php

if (!isset($orders)) {
    echo '<div class="alert alert-danger">Error: Orders data not available</div>';
    exit;
}
?>
<div class="container my-5">
    <h2 class="mb-4">Mes commandes</h2>
    <?php if (empty($orders)): ?>
        <p>Vous n'avez aucune commande pour le moment.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Numéro de commande</th>
                        <th>Date</th>
                        <th>Montant total</th>
                        <th>Statut</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order->order_id) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($order->order_date)) ?></td>
                        <td><?= number_format($order->total_amount, 2) ?> €</td>
                        <td>
                            <?php
                            $statusClass = 'secondary';
                            if ($order->status === 'processing') {
                                $statusClass = 'warning';
                            } elseif ($order->status === 'confirmed') {
                                $statusClass = 'success';
                            } elseif ($order->status === 'cancelled') {
                                $statusClass = 'danger';
                            }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= htmlspecialchars(ucfirst($order->status)) ?></span>
                            <?php if ($order->status === 'processing'): ?>
                                <span id="timer-<?= $order->order_id ?>" class="ms-2 text-muted"></span>
                                <script>
                                    function startTimer(orderId, orderDate) {
                                        const timerElement = document.getElementById('timer-' + orderId);
                                        const endTime = new Date(orderDate);
                                        endTime.setHours(endTime.getHours() + 24);

                                        function updateTimer() {
                                            const now = new Date();
                                            const diff = endTime - now;

                                            if (diff <= 0) {
                                                timerElement.textContent = 'Status: done';
                                                clearInterval(interval);
                                                return;
                                            }

                                            const hours = Math.floor(diff / (1000 * 60 * 60));
                                            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                                            timerElement.textContent = `Time left: ${hours}h ${minutes}m ${seconds}s`;
                                        }

                                        updateTimer();
                                        const interval = setInterval(updateTimer, 1000);
                                    }

                                    startTimer(<?= $order->order_id ?>, '<?= $order->order_date ?>');
                                </script>
                            <?php endif; ?>
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
