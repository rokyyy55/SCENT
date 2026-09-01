<?php
// Vérifier que l'utilisateur est connecté et administrateur
global $session;
if (!$session->has('user_id') || !$session->get('is_admin')) {
    header('Location: ' . BASE_URL);
    exit;
}
?>

<style>
    .dashboard-btn {
        background: linear-gradient(145deg, #1a237e, #0d47a1);
        color: #ffd700;
        border: 2px solid #ffd700;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(26, 35, 126, 0.3);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .dashboard-btn:hover {
        background: linear-gradient(145deg, #0d47a1, #1a237e);
        color: #ffd700;
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.5),
                    0 0 40px rgba(255, 215, 0, 0.3),
                    0 0 60px rgba(255, 215, 0, 0.1);
        border-color: #ffd700;
    }

    .dashboard-btn:active {
        transform: translateY(1px);
        box-shadow: 0 2px 10px rgba(26, 35, 126, 0.3);
    }

    .dashboard-btn i {
        margin-right: 8px;
        color: #ffd700;
    }

    /* Style for the cards */
    .dashboard-card {
        background: linear-gradient(145deg, #ffffff, #f5f5f5);
        border: 1px solid rgba(26, 35, 126, 0.1);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(26, 35, 126, 0.15);
    }

    .dashboard-card .card-header {
        background: linear-gradient(145deg, #1a237e, #0d47a1);
        color: #ffd700;
        border-bottom: 2px solid #ffd700;
        border-radius: 12px 12px 0 0 !important;
    }

    .dashboard-card .card-body {
        padding: 1.5rem;
    }

    /* Style for the stats numbers */
    .stat-number {
        color: #1a237e;
        font-size: 2rem;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Style for the table */
    .dashboard-table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .dashboard-table thead {
        background: linear-gradient(145deg, #1a237e, #0d47a1);
        color: #ffd700;
    }

    .dashboard-table th {
        border: none;
        padding: 15px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dashboard-table td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .dashboard-table tbody tr:hover {
        background-color: rgba(26, 35, 126, 0.05);
    }
</style>

<div class="container-fluid">
    <h1 class="mt-4">
        Tableau de bord administrateur
        <button id="toggleAddProductForm" style="margin-left: 15px; background-color: #ffd700; border: none; border-radius: 50%; width: 36px; height: 36px; color: #1a237e; font-size: 24px; line-height: 36px; text-align: center; box-shadow: 0 0 8px 2px rgba(128, 128, 128, 0.7); cursor: pointer;">
            +
        </button>
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tableau de bord</li>
    </ol>
    <div id="addProductForm" style="display:none; margin-bottom: 20px;">
        <form action="<?= BASE_URL ?>/admin/createProduct" method="POST" id="productForm">
            <div class="mb-2">
                <label for="product_name" class="form-label" style="color:grey;">Product Name</label>
                <input type="text" id="product_name" name="product_name" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="brand_name" class="form-label" style="color:grey;">Brand Name</label>
                <input type="text" id="brand_name" name="brand_name" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="price" class="form-label" style="color:grey;">Price</label>
                <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="category" class="form-label" style="color:grey;">Category</label>
                <input type="text" id="category" name="category" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="description" class="form-label" style="color:grey;">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-2">
                <label for="stock_quantity" class="form-label" style="color:grey;">Stock Quantity</label>
                <input type="number" min="0" id="stock_quantity" name="stock_quantity" class="form-control" value="10" required>
            </div>
            <div class="mb-2">
                <label for="volume" class="form-label" style="color:grey;">Volume (ml)</label>
                <input type="number" min="0" id="volume" name="volume" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="concentration" class="form-label" style="color:grey;">Concentration</label>
                <select id="concentration" name="concentration" class="form-control" required>
                    <option value="">Select concentration</option>
                    <option value="Eau de Cologne">Eau de Cologne</option>
                    <option value="Eau de Toilette">Eau de Toilette</option>
                    <option value="Eau de Parfum">Eau de Parfum</option>
                    <option value="Parfum">Parfum</option>
                </select>
            </div>
            <div class="mb-2">
                <label for="gender" class="form-label" style="color:grey;">Gender</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="">Select gender</option>
                    <option value="Homme">Homme</option>
                    <option value="Femme">Femme</option>
                    <option value="Unisexe">Unisexe</option>
                </select>
            </div>
            <div class="mb-2">
                <label for="image_path" class="form-label" style="color:grey;">Image Path</label>
                <input type="text" id="image_path" name="image_path" class="form-control" placeholder="Enter image path" style="color:grey;">
            </div>
            <button type="submit" class="dashboard-btn" style="background-color: #1a237e; border-color: #1a237e;">Add Product</button>
        </form>
    </div>
    <script>
        document.getElementById('toggleAddProductForm').addEventListener('click', function() {
            var form = document.getElementById('addProductForm');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });

        // Add form submission handling
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Send AJAX request
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to add product'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the product');
            });
        });
    </script>
    <div class="row">
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="dashboard-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-box me-1"></i>
                    Produits
                </div>
                <div class="card-body">
                    <div class="stat-number"><?= $total_products ?? 0 ?></div>
                    <div class="stat-label">Total des produits</div>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/admin/products" class="dashboard-btn">
                        <i class="fas fa-list"></i> Details
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="dashboard-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-shopping-cart me-1"></i>
                    Commandes
                </div>
                <div class="card-body">
                    <div class="stat-number"><?= $total_orders ?? 0 ?></div>
                    <div class="stat-label">Total des commandes</div>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/admin/orders" class="dashboard-btn">
                        <i class="fas fa-list"></i> Details
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="dashboard-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i>
                    Clients
                </div>
                <div class="card-body">
                    <div class="stat-number"><?= $total_customers ?? 0 ?></div>
                    <div class="stat-label">Total des clients</div>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/admin/customers" class="dashboard-btn">
                        <i class="fas fa-list"></i> Details
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="dashboard-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-tags me-1"></i>
                    Catégories
                </div>
                <div class="card-body">
                    <div class="stat-number"><?= $total_categories ?? 0 ?></div>
                    <div class="stat-label">Total des catégories</div>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/admin/categories" class="dashboard-btn">
                        <i class="fas fa-list"></i> Details
                    </a>
                    <!-- Removed add button for categories -->
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="dashboard-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-tags me-1"></i>
                    Marques
                </div>
                <div class="card-body">
                    <div class="stat-number"><?= $total_brands ?? 0 ?></div>
                    <div class="stat-label">Total des marques</div>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/admin/brands" class="dashboard-btn">
                        <i class="fas fa-list"></i> Details
                    </a>
                    <!-- Removed add button for brands -->
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="dashboard-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-ticket-alt me-1"></i>
                    Codes
                </div>
                <div class="card-body">
                    <div class="stat-number"><?= $total_codes ?? 0 ?></div>
                    <div class="stat-label">Total des codes</div>
                </div>
                <div class="card-footer">
                    <a href="<?= BASE_URL ?>/admin/discountCodes" class="dashboard-btn">
                        <i class="fas fa-list"></i> Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-1"></i>
                        Commandes récentes
                    </div>
                    <a href="<?php echo BASE_URL; ?>/admin/orders" class="dashboard-btn">
                        <i class="fas fa-list"></i> Voir toutes les commandes
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="dashboard-table table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Les commandes récentes seront affichées ici -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Stock faible
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Stock actuel</th>
                                    <th>Seuil d'alerte</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($low_stock_products)): ?>
                                    <?php foreach ($low_stock_products as $product): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($product->name) ?></td>
                                            <td><?= htmlspecialchars($product->stock_quantity) ?></td>
                                            <td>10</td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/products/edit/<?= $product->product_id ?>" class="btn btn-sm btn-primary">Modifier</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Aucun produit en stock faible</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-ticket-alt me-1"></i>
                        Tous les codes générés
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="dashboard-table table table-striped">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Utilisateur</th>
                                    <th>Date de création</th>
                                    <th>Statut</th>
                                    <th>Date d'utilisation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($all_codes)): ?>
                                    <?php foreach ($all_codes as $code): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($code->code) ?></td>
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
                                            <td>
                                                <button class="btn btn-info btn-sm loyalty-details-btn" data-user-id="<?= $code->user_id ?>">Détails fidélité</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center">Aucun code généré</td></tr>
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

<!-- Product Edit Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editProductForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editProductModalLabel">Modifier le produit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <!-- Form fields will be loaded here by JS -->
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enregistrer</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Open modal and load product data
    document.querySelectorAll('.edit-product-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.getAttribute('data-product-id');
            fetch('<?= BASE_URL ?>/admin/products/get?id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let p = data.product;
                        let formHtml = `
                            <input type="hidden" name="product_id" value="${p.product_id}">
                            <div class="mb-2">
                                <label>Nom</label>
                                <input type="text" name="name" class="form-control" value="${p.name}">
                            </div>
                            <div class="mb-2">
                                <label>Quantité en stock</label>
                                <input type="number" name="stock_quantity" class="form-control" value="${p.stock_quantity}">
                            </div>
                            <div class="mb-2">
                                <label>Prix</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="${p.price}">
                            </div>
                            <div class="mb-2">
                                <label>Concentration</label>
                                <input type="text" name="concentration" class="form-control" value="${p.concentration}">
                            </div>
                            <div class="mb-2">
                                <label>Genre</label>
                                <input type="text" name="gender" class="form-control" value="${p.gender}">
                            </div>
                            <div class="mb-2">
                                <label>Catégorie</label>
                                <input type="text" name="category_id" class="form-control" value="${p.category_id}">
                            </div>
                            <div class="mb-2">
                                <label>Marque</label>
                                <input type="text" name="brand_id" class="form-control" value="${p.brand_id}">
                            </div>
                            <div class="mb-2">
                                <label>Description</label>
                                <textarea name="description" class="form-control">${p.description || ''}</textarea>
                            </div>
                            <div class="mb-2">
                                <label>Image (URL)</label>
                                <input type="text" name="image" class="form-control" value="${p.image || ''}">
                            </div>
                            <div class="mb-2">
                                <label>Volume (ml)</label>
                                <input type="number" name="volume" class="form-control" value="${p.volume || ''}">
                            </div>
                        `;
                        document.querySelector('#editProductModal .modal-body').innerHTML = formHtml;
                    } else {
                        document.querySelector('#editProductModal .modal-body').innerHTML = '<div class="alert alert-danger">Erreur lors du chargement du produit.</div>';
                    }
                });
        });
    });

    // Handle form submission
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('<?= BASE_URL ?>/admin/products/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produit mis à jour avec succès !');
                location.reload();
            } else {
                alert('Erreur: ' + (data.message || 'Mise à jour échouée'));
            }
        });
    });
});
</script>