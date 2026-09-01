<?php
// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ' . BASE_URL);
    exit();
}
?>

<div class="container mt-4">
    <h1>Détails de l'utilisateur</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($user->username); ?></h5>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user->email); ?></p>
            <p><strong>Nom complet:</strong> <?php echo htmlspecialchars($user->full_name ?? 'N/A'); ?></p>
            <p><strong>Rôle:</strong> <?php echo $user->is_admin ? 'Administrateur' : 'Utilisateur'; ?></p>
            <p><strong>Date d'inscription:</strong> <?php echo htmlspecialchars($user->created_at ?? 'N/A'); ?></p>
            <a href="<?php echo BASE_URL; ?>/admin/customers" class="btn btn-secondary mt-3">Retour à la liste des utilisateurs</a>
        </div>
    </div>
</div>
