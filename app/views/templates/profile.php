<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-user-circle me-2"></i>Mon Profil</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= BASE_URL ?>/user/profile" class="list-group-item list-group-item-action active">
                        <i class="fas fa-user me-2"></i>Informations personnelles
                    </a>
                    <a href="<?= BASE_URL ?>/user/addresses" class="list-group-item list-group-item-action">
                        <i class="fas fa-map-marker-alt me-2"></i>Adresses
                    </a>
                    <a href="<?= BASE_URL ?>/user/orders" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-bag me-2"></i>Mes Commandes
                    </a>
                    <a href="<?= BASE_URL ?>/user/wishlist" class="list-group-item list-group-item-action">
                        <i class="fas fa-heart me-2"></i>Liste de souhaits
                    </a>
                    <a href="<?= BASE_URL ?>/user/password" class="list-group-item list-group-item-action">
                        <i class="fas fa-lock me-2"></i>Changer mot de passe
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-user me-2"></i>Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/user/update-profile" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" name="first_name" value="<?= $user->first_name ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" name="last_name" value="<?= $user->last_name ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= $user->email ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" name="phone" value="<?= $user->phone ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-shopping-bag me-2"></i>Dernières Commandes</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Commande #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= $order->order_number ?></td>
                                        <td><?= date('d/m/Y', strtotime($order->created_at)) ?></td>
                                        <td><?= number_format($order->total, 2, ',', ' ') ?> €</td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $order->status == 'completed' ? 'success' : 
                                                ($order->status == 'processing' ? 'warning' : 'secondary')
                                            ?>">
                                                <?= ucfirst($order->status) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/order/details/<?= $order->id ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?= BASE_URL ?>/user/orders" class="btn btn-primary mt-3">Voir toutes mes commandes</a>
                    <?php else: ?>
                        <p>Vous n'avez pas encore passé de commande.</p>
                        <a href="<?= BASE_URL ?>/product" class="btn btn-primary">Découvrir nos produits</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>