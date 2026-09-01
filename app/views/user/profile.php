<?php
// profile.php - User Profile Page
$user = $this->user_model; // Assuming you pass the user model to the view
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-user-circle me-2"></i>Mon Profil</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Prénom</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($user->first_name ?? '') ?></dd>
                        
                        <dt class="col-sm-4">Nom</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($user->last_name ?? '') ?></dd>
                        
                        <dt class="col-sm-4">Nom d'utilisateur</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($user->username ?? '') ?></dd>
                        
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($user->email ?? '') ?></dd>
                        
                        <dt class="col-sm-4">Mot de passe</dt>
                        <dd class="col-sm-8">********</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>