<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-white py-3">
                <h3 class="mb-0 text-center">Créer un compte</h3>
            </div>
            <div class="card-body p-4">
                <?php
                global $session;
                if ($session->getFlash('error')) {
                    echo '<div class="alert alert-danger">' . $session->getFlash('error') . '</div>';
                }
                if ($session->has('register_errors')) {
                    $errors = $session->get('register_errors');
                    echo '<div class="alert alert-danger">';
                    echo '<ul class="mb-0">';
                    foreach ($errors as $error) {
                        echo '<li>' . $error . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                    $session->remove('register_errors');
                }
                
                // Récupérer les données du formulaire précédemment soumises
                $form_data = $session->has('register_form_data') ? $session->get('register_form_data') : [];
                $session->remove('register_form_data');
                ?>
                
                <form action="<?= BASE_URL ?>/user/create" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= isset($form_data['username']) ? $form_data['username'] : '' ?>" required>
                            <div class="form-text">Minimum 3 caractères, lettres et chiffres uniquement.</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= isset($form_data['email']) ? $form_data['email'] : '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Minimum 6 caractères.</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= isset($form_data['first_name']) ? $form_data['first_name'] : '' ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= isset($form_data['last_name']) ? $form_data['last_name'] : '' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            J'accepte les <a href="#" target="_blank">conditions générales d'utilisation</a> et la <a href="#" target="_blank">politique de confidentialité</a>
                        </label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Créer mon compte</button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p>Vous avez déjà un compte ?</p>
                    <a href="<?= BASE_URL ?>/user/login" class="btn btn-outline-primary">Se connecter</a>
                </div>
            </div>
        </div>
    </div>
</div>