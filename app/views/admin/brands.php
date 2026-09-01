<?php
// Vérifier que l'utilisateur est connecté et administrateur
global $session;
if (!$session->has('user_id') || !$session->get('is_admin')) {
    header('Location: ' . BASE_URL);
    exit;
}
?>
<?php
// Vérifier que l'utilisateur est connecté et administrateur
global $session;
if (!$session->has('user_id') || !$session->get('is_admin')) {
    header('Location: ' . BASE_URL);
    exit;
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des Marques</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/dashboard">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Marques</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-tags me-1"></i>
            Liste des Marques
        </div>
        <div class="card-body">
            <?php if (!empty($brands)): ?>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td><?= htmlspecialchars($brand['brand_id']) ?></td>
                                <td><?= htmlspecialchars($brand['name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune marque trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
