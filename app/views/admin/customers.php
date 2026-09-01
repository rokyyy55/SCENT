<?php
// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ' . BASE_URL);
    exit();
}
?>

<div class="container mt-4">
    <h1>Liste des clients</h1>
    <?php if (empty($customers)): ?>
        <p>Aucun client trouvé.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= htmlspecialchars($customer->user_id) ?></td>
                        <td><?= htmlspecialchars($customer->username) ?></td>
                        <td><?= htmlspecialchars($customer->email) ?></td>
                        <td><?= $customer->is_admin ? 'Administrateur' : 'Utilisateur' ?></td>
                        <td><?= htmlspecialchars($customer->created_at) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/customers/<?= $customer->user_id ?>" class="btn btn-sm btn-primary">Voir détails</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
