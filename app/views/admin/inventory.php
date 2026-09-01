<div class="container-fluid">
    <h1 class="mt-4">Gestion de l'inventaire</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Inventaire</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-boxes me-1"></i>
            État du stock
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="inventoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Parfum</th>
                            <th>Marque</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product) : ?>
                            <tr>
                                <td><?= $product['product_id'] ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($product['brand_name']) ?></td>
                                <td><?= number_format($product['price'], 2, ',', ' ') ?> €</td>
                                <td>
                                    <?php if ($product['stock_quantity'] <= 0) : ?>
                                        <span class="badge bg-danger">En rupture</span>
                                    <?php elseif ($product['stock_quantity'] <= 5) : ?>
                                        <span class="badge bg-warning"><?= $product['stock_quantity'] ?></span>
                                    <?php else : ?>
                                        <span class="badge bg-success"><?= $product['stock_quantity'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateStockModal<?= $product['product_id'] ?>">
                                        <i class="fas fa-edit"></i> Mettre à jour
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal for updating stock -->
                            <div class="modal fade" id="updateStockModal<?= $product['product_id'] ?>" tabindex="-1" aria-labelledby="updateStockModalLabel<?= $product['product_id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateStockModalLabel<?= $product['product_id'] ?>">Mettre à jour le stock de <?= htmlspecialchars($product['name']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?= BASE_URL ?>/admin/update-stock" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                <div class="form-group">
                                                    <label for="stock_quantity<?= $product['product_id'] ?>">Nouvelle quantité en stock</label>
                                                    <input type="number" min="0" class="form-control" id="stock_quantity<?= $product['product_id'] ?>" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new DataTable('#inventoryTable', {
            order: [[4, 'asc']], // Sort by stock quantity (ascending)
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
            }
        });
    });
</script>