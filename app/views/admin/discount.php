<?php
// Check if user is admin
if (!$session->has('user_id') || !$session->get('is_admin')) {
    header('Location: ' . BASE_URL);
    exit;
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-ticket-alt me-1"></i>
                        Codes de réduction
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Utilisateur</th>
                                    <th>Date de création</th>
                                    <th>Statut</th>
                                    <th>Date d'utilisation</th>
                                    <th>Montant de réduction</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($codes)): ?>
                                    <?php foreach ($codes as $code): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-light text-dark"><?= htmlspecialchars($code->code) ?></span>
                                                    <button class="btn btn-sm btn-outline-primary ms-2 copy-btn" 
                                                            data-code="<?= htmlspecialchars($code->code) ?>"
                                                            title="Copier le code">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($code->username ?? 'Utilisateur inconnu') ?></td>
                                            <td><?= date('d/m/Y', strtotime($code->created_at)) ?></td>
                                            <td>
                                                <?php if ($code->is_used): ?>
                                                    <span class="badge bg-danger">Utilisé</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Disponible</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($code->used_at): ?>
                                                    <?= date('d/m/Y H:i', strtotime($code->used_at)) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($code->discount_amount, 2) ?> €</td>
                                            <td>
                                                <button class="btn btn-info btn-sm loyalty-details-btn" 
                                                        data-user-id="<?= $code->user_id ?>"
                                                        title="Voir les détails de fidélité">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Aucun code de réduction trouvé</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for user loyalty details -->
<div class="modal fade" id="loyaltyDetailsModal" tabindex="-1" aria-labelledby="loyaltyDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loyaltyDetailsModalLabel">Détails fidélité utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loyaltyDetailsContent">
                Chargement...
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy code functionality
    const copyButtons = document.querySelectorAll('.copy-btn');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const code = this.dataset.code;
            navigator.clipboard.writeText(code).then(() => {
                // Visual feedback
                this.classList.add('btn-success');
                this.innerHTML = '<i class="fas fa-check"></i>';
                
                setTimeout(() => {
                    this.classList.remove('btn-success');
                    this.innerHTML = '<i class="fas fa-copy"></i>';
                }, 1500);
            });
        });
    });

    // Loyalty details functionality
    document.querySelectorAll('.loyalty-details-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            var modal = new bootstrap.Modal(document.getElementById('loyaltyDetailsModal'));
            var content = document.getElementById('loyaltyDetailsContent');
            content.innerHTML = 'Chargement...';
            
            fetch('<?= BASE_URL ?>/admin/userLoyaltyAjax?user_id=' + userId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        content.innerHTML = `
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Utilisateur:</strong> ${data.loyalty.username}</li>
                                <li class="list-group-item"><strong>Articles livrés:</strong> ${data.loyalty.delivered_items}</li>
                                <li class="list-group-item"><strong>Dernier code:</strong> ${data.loyalty.last_discount_code}</li>
                                <li class="list-group-item"><strong>Dernier code utilisé:</strong> ${data.loyalty.last_discount_used == 1 ? 'Oui' : 'Non'}</li>
                            </ul>
                        `;
                    } else {
                        content.innerHTML = '<span class="text-danger">' + (data.message || 'Aucune donnée trouvée') + '</span>';
                    }
                })
                .catch(() => {
                    content.innerHTML = '<span class="text-danger">Erreur lors du chargement des données.</span>';
                });
            modal.show();
        });
    });
});
</script> 