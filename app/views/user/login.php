<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-white py-3">
                <h3 class="mb-0 text-center">Connexion</h3>
            </div>
            <div class="card-body p-4">
                <?php
                global $session;
                if ($session->getFlash('error')) {
                    echo '<div class="alert alert-danger">' . $session->getFlash('error') . '</div>';
                }
                if ($session->getFlash('success')) {
                    echo '<div class="alert alert-success">' . $session->getFlash('success') . '</div>';
                }
                ?>
                
                <form action="<?= BASE_URL ?>/user/authenticate" method="POST">
                    <div class="mb-3">
                        <label for="username_or_email" class="form-label">Nom d'utilisateur ou Email</label>
                        <input type="text" class="form-control" id="username_or_email" name="username_or_email" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                        <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Connexion</button>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">Mot de passe oublié ?</a>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p>Vous n'avez pas de compte ?</p>
                    <a href="<?= BASE_URL ?>/user/register" class="btn btn-outline-primary">Créer un compte</a>
                </div>
            </div>
        </div>
    </div>
</div>