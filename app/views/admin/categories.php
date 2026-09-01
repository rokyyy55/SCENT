<!-- app/views/admin/categories.php -->
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestion des Catégories</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Catégories</h6>
            <a href="<?= BASE_URL ?>/admin/category/add" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajouter une catégorie
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $category['category_id'] ?></td>
                                    <td><?= $category['name'] ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/admin/category/edit/<?= $category['category_id'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/admin/category/delete/<?= $category['category_id'] ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Aucune catégorie trouvée.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>