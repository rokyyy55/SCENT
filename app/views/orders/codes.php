<?php
// Check if user is logged in
if (!$session->has('user_id')) {
    header('Location: ' . BASE_URL . '/user/login');
    exit;
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-code me-2"></i>
                        Mes Codes Promo
                    </h4>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($codes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3">Code</th>
                                        <th class="py-3">Date de création</th>
                                        <th class="py-3">Statut</th>
                                        <th class="py-3">Date d'utilisation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($codes as $code): ?>
                                        <tr class="code-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="code-badge"><?= htmlspecialchars($code->code) ?></span>
                                                    <button class="btn btn-sm btn-outline-primary ms-2 copy-btn" 
                                                            data-code="<?= htmlspecialchars($code->code) ?>"
                                                            title="Copier le code">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($code->created_at)) ?></td>
                                            <td>
                                                <?php if ($code->is_used): ?>
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>
                                                        Utilisé
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        Disponible
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($code->used_at): ?>
                                                    <?= date('d/m/Y H:i', strtotime($code->used_at)) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-ticket-alt fa-3x mb-3 text-muted"></i>
                            <h5 class="text-muted">Vous n'avez pas encore de codes promo</h5>
                            <p class="text-muted">Restez à l'écoute pour nos prochaines offres !</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styling for the codes page */
.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    border-bottom: none;
}

.table {
    margin-bottom: 0;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: var(--gold);
}

.code-badge {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: var(--gold);
    border: 1px solid #dee2e6;
    font-size: 0.9rem;
}

.copy-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.copy-btn:hover {
    transform: scale(1.05);
}

.badge {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    font-size: 0.85rem;
}

.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}

.code-row {
    transition: all 0.3s ease;
}

.code-row:hover {
    background-color: rgba(142, 219, 235, 0.05);
    transform: translateY(-1px);
}

/* Animation for copy button */
@keyframes copySuccess {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.copy-success {
    animation: copySuccess 0.3s ease;
    background-color: #28a745 !important;
    color: white !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy code functionality
    const copyButtons = document.querySelectorAll('.copy-btn');
    
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const code = this.dataset.code;
            navigator.clipboard.writeText(code).then(() => {
                // Visual feedback
                this.classList.add('copy-success');
                this.innerHTML = '<i class="fas fa-check"></i>';
                
                setTimeout(() => {
                    this.classList.remove('copy-success');
                    this.innerHTML = '<i class="fas fa-copy"></i>';
                }, 1500);
            });
        });
    });
});
</script> 