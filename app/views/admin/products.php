<?php
// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ' . BASE_URL);
    exit();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des Produits</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/admin">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Produits</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Liste des Produits
            </div>
            <a href="<?php echo BASE_URL; ?>/admin/products/add" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajouter un produit
            </a>
        </div>
        <div class="card-body">
            <table id="productsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Marque</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product->product_id); ?></td>
                        <td>
                            <?php
                            $cleanImagePath = ltrim(str_replace('\\', '/', $product->image), '/');
                            $imageFile = $_SERVER['DOCUMENT_ROOT'] . '/scent/public/' . $cleanImagePath;
                            $placeholder = BASE_URL . '/public/images/products/placeholder.jpg';
                            $imagePath = !empty($cleanImagePath) && file_exists($imageFile) ? BASE_URL . '/public/' . $cleanImagePath : $placeholder;

                            // Debug output for image paths
                            echo "<!-- Debug: imageFile = $imageFile -->";
                            echo "<!-- Debug: imagePath = $imagePath -->";
                            ?>
                            <img src="<?php echo $imagePath; ?>" 
                                 alt="<?php echo htmlspecialchars($product->name); ?>"
                                 class="img-thumbnail"
                                 style="max-width: 50px;">
                        </td>
                        <td><?php echo htmlspecialchars($product->name); ?></td>
                        <td><?php echo htmlspecialchars($product->category_name); ?></td>
                        <td><?php echo htmlspecialchars($product->brand_name); ?></td>
                        <td><?php echo number_format($product->price, 2); ?> €</td>
                        <td>
                            <span class="badge bg-<?php echo $product->stock_quantity <= 5 ? 'danger' : 'success'; ?>">
                                <?php echo htmlspecialchars($product->stock_quantity); ?>
                            </span>
                        </td>
                        <td>
                            <a href="#" 
                               class="btn btn-sm btn-primary edit-product-btn" 
                               data-product-id="<?php echo $product->product_id; ?>"
                               data-bs-toggle="modal" 
                               data-bs-target="#editProductModal"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-danger"
                                    onclick="deleteProduct(<?php echo $product->product_id; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce produit ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Supprimer</button>
            </div>
        </div>
    </div>
</div>

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

<!-- DataTables 2.x vanilla CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>

<script>
let productToDelete = null;

function deleteProduct(productId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
        fetch('<?php echo BASE_URL; ?>/admin/products/delete/' + productId, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Supprimer la ligne du tableau
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (row) {
                    row.remove();
                }
                // Recharger la page pour mettre à jour le tableau
                location.reload();
            } else {
                alert('Erreur lors de la suppression du produit: ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la suppression du produit');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    new DataTable('#productsTable');

    // Open modal and load product data (remplacement natif de $(...).on('click'))
    document.querySelectorAll('.edit-product-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var productId = this.getAttribute('data-product-id');
            fetch('<?php echo BASE_URL; ?>/admin/products/get?id=' + productId)
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

    // Handle form submission (remplacement natif de $(...).on('submit'))
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch('<?php echo BASE_URL; ?>/admin/products/update', {
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