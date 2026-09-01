<!-- Gender Images Display - Updated Version -->
<div class="container-fluid py-5" style="background-color: rgba(19, 41, 75, 0.8);">
    <div class="row justify-content-center g-4">
        <!-- Women's Perfume Image -->
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/product/search?gender=Femme" class="perfume-link">
                <div class="gender-image-container">
                    <img src="<?= BASE_URL ?>/public/images/gender/womann1.png" class="img-fluid perfume-image" alt="Parfums Femme">
                </div>
            </a>
        </div>
        
        <!-- Unisex Perfume Image -->
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/product/search?gender=Unisexe" class="perfume-link">
                <div class="gender-image-container">
                    <img src="<?= BASE_URL ?>/public/images/gender/unisex.png" class="img-fluid perfume-image" alt="Parfums Unisexe">
                </div>
            </a>
        </div>
        
        <!-- Men's Perfume Image -->
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/product/search?gender=Homme" class="perfume-link">
                <div class="gender-image-container">
                    <img src="<?= BASE_URL ?>/public/images/gender/mann.png" class="img-fluid perfume-image" alt="Parfums Homme">
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.18s cubic-bezier(.4,2,.6,1), box-shadow 0.18s;
    z-index: 1;
    position: relative;
}
.product-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 6px 18px rgb(66, 126, 216);
    z-index: 10;
}
.product-card-img {
    transition: none;
}
.product-card-link {
    color: inherit;
}
.product-card-link:hover {
    color: inherit;
}
.product-card-link:focus {
    outline: none;
}
</style>

<!-- Produits en vedette -->
<section class="mb-5">
    
    <div class="row g-4">
        <?php
        // Récupérer quelques produits aléatoires
        $database = new Database();
        $conn = $database->connect();
        $query = 'SELECT 
                    p.product_id, 
                    p.name, 
                    p.description, 
                    p.price, 
                    p.image, 
                    p.gender,
                    b.name as brand_name
                FROM products p
                JOIN brands b ON p.brand_id = b.brand_id
                ORDER BY RAND()
                LIMIT 4';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($featured_products as $product) :
            // Correctly format the product image path
            $imagePath = $product['image'] ? $product['image'] : 'images/products/placeholder.jpg';
            // Remove any possible double slashes in the path
            $imagePath = ltrim($imagePath, '/');
        ?>
        <div class="col-md-3">
            <a href="<?= BASE_URL ?>/product/details/<?= $product['product_id'] ?>" class="text-decoration-none product-card-link" style="display:block; height:100%;">
                <div class="card h-100 product-card">
                    <div class="badge bg-<?= $product['gender'] === 'Homme' ? 'primary' : ($product['gender'] === 'Femme' ? 'danger' : 'success') ?> position-absolute end-0 m-2">
                        <?= $product['gender'] ?>
                    </div>
                    <img src="<?= BASE_URL ?>/public/<?= $imagePath ?>" 
                         class="card-img-top product-card-img" 
                         alt="<?= $product['name'] ?>"
                         onerror="this.src='<?= BASE_URL ?>/public/images/products/placeholder.jpg'" 
                         style="height: 300px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <p class="text-muted mb-1"><?= $product['brand_name'] ?></p>
                        <h5 class="card-title"><?= $product['name'] ?></h5>
                        <p class="card-text flex-grow-1"><?= substr($product['description'], 0, 80) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="fw-bold"><?= number_format($product['price'], 2, ',', ' ') ?> €</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/product/search" class="btn btn-outline-dark">Voir tous les parfums</a>
    </div>
</section>




