<div class="container-fluid">
    <h1 class="mt-4">Ajouter un nouveau parfum</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/products">Produits</a></li>
        <li class="breadcrumb-item active">Ajouter un parfum</li>
    </ol>
    
    <?php
    global $session;
    if ($session->has('errors')) {
        $errors = $session->get('errors');
        echo '<div class="alert alert-danger">';
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
        echo '</div>';
        $session->remove('errors');
    }
    
    // Récupérer les données du formulaire si elles existent
    $form_data = $session->has('form_data') ? $session->get('form_data') : [];
    $session->remove('form_data');
    ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-wine-bottle me-1"></i>
            Détails du parfum
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/admin/create-product" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nom du parfum <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= $form_data['name'] ?? '' ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price">Prix (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="<?= $form_data['price'] ?? '' ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="brand_id">Marque <span class="text-danger">*</span></label>
                            <select class="form-control" id="brand_id" name="brand_id" required>
                                <option value="">Sélectionner une marque</option>
                                <?php foreach ($brands as $brand) : ?>
                                    <option value="<?= $brand['brand_id'] ?>" <?= isset($form_data['brand_id']) && $form_data['brand_id'] == $brand['brand_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($brand['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category['category_id'] ?>" <?= isset($form_data['category_id']) && $form_data['category_id'] == $category['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="stock_quantity">Quantité en stock <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" id="stock_quantity" name="stock_quantity" value="<?= $form_data['stock_quantity'] ?? '10' ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="image">Image du produit</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <small class="form-text text-muted">Formats acceptés : JPEG, PNG, GIF. Taille max : 2MB</small>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gender">Genre <span class="text-danger">*</span></label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Sélectionner un genre</option>
                                <option value="homme" <?= isset($form_data['gender']) && $form_data['gender'] == 'homme' ? 'selected' : '' ?>>Homme</option>
                                <option value="femme" <?= isset($form_data['gender']) && $form_data['gender'] == 'femme' ? 'selected' : '' ?>>Femme</option>
                                <option value="unisexe" <?= isset($form_data['gender']) && $form_data['gender'] == 'unisexe' ? 'selected' : '' ?>>Unisexe</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= $form_data['description'] ?? '' ?></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>/admin/products" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Ajouter le parfum</button>
                </div>
            </form>
        </div>
    </div>
</div>