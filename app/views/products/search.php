<style>
/* ... existing styles ... */
.custom-card, .product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    z-index: 1;
    position: relative;
    overflow: hidden;
}
.custom-card:hover, .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(30,65,118,0.15);
    z-index: 2;
}
.product-card-img {
    transition: transform 0.3s ease;
    height: 300px;
    object-fit: cover;
}
.product-card:hover .product-card-img {
    transform: scale(1.05);
}
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    z-index: 1;
    position: relative;
    background-color: rgba(30, 65, 118, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(30,65,118,0.15);
    z-index: 2;
}
.product-card-img {
    transition: none;
}
.product-card-link {
    color: inherit;
    text-decoration: none;
}
.product-card-link:hover {
    color: inherit;
    text-decoration: none;
}
.product-card-link:focus {
    outline: none;
}
.card-body {
    padding: 1rem;
}
.card-title {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}
.card-text {
    font-size: 0.9rem;
    margin-bottom: 1rem;
}
</style>

<div class="container mt-5">
    <!-- Search Form -->
    <div class="card mb-4 search-card">
        <div class="card-body">
            <form action="/scent/product/search" method="GET" class="row g-3 align-items-center" id="product-search-form">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Nom du parfum" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-light w-100 filter-btn" data-bs-toggle="modal" data-bs-target="#categoriesModal">
                        <?php echo isset($data['selected_category_name']) ? htmlspecialchars($data['selected_category_name']) : 'Toutes les catégories'; ?>
                    </button>
                    <input type="hidden" name="category" id="selectedCategory" value="<?php echo isset($_GET['category']) ? htmlspecialchars($_GET['category']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-light w-100 filter-btn" data-bs-toggle="modal" data-bs-target="#brandsModal">
                        <?php echo isset($data['selected_brand_name']) ? htmlspecialchars($data['selected_brand_name']) : 'Toutes les marques'; ?>
                    </button>
                    <input type="hidden" name="brand" id="selectedBrand" value="<?php echo isset($_GET['brand']) ? htmlspecialchars($_GET['brand']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-light w-100 filter-btn" data-bs-toggle="modal" data-bs-target="#priceModal">
                        <?php 
                        $priceLabels = [
                            '' => 'Tous les prix',
                            '100' => 'Moins de 100 €',
                            '150' => 'Moins de 150 €',
                            '200' => 'Moins de 200 €',
                            '250' => 'Moins de 250 €',
                            '300' => 'Moins de 300 €',
                        ];
                        $selectedPrice = $_GET['price_range'] ?? '';
                        echo $priceLabels[$selectedPrice] ?? $priceLabels[''];
                        ?>
                    </button>
                    <input type="hidden" name="price_range" id="selectedPrice" value="<?php echo isset($_GET['price_range']) ? htmlspecialchars($_GET['price_range']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <select name="gender" class="form-select filter-dropdown">
                        <option value="">Tous</option>
                        <option value="Homme" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'Homme') ? 'selected' : ''; ?>>Homme</option>
                        <option value="Femme" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'Femme') ? 'selected' : ''; ?>>Femme</option>
                        <option value="Unisexe" <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'Unisexe') ? 'selected' : ''; ?>>Unisexe</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-custom text-white w-100">Rechercher</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Modal -->
    <div class="modal fade" id="categoriesModal" tabindex="-1" aria-labelledby="categoriesModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="categoriesModalLabel">Catégories</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
                <?php
                if(isset($data['categories'])) {
                    foreach ($data['categories'] as $category) {
                        echo '<div class="col">';
                        echo '<a href="#" class="btn btn-outline-light w-100 mb-2 select-category" data-id="' . $category->category_id . '" data-name="' . htmlspecialchars($category->name) . '">' . htmlspecialchars($category->name) . '</a>';
                        echo '</div>';
                    }
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Brands Modal -->
    <div class="modal fade" id="brandsModal" tabindex="-1" aria-labelledby="brandsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="brandsModalLabel">Marques</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
                <?php
                if(isset($data['brands'])) {
                    foreach ($data['brands'] as $brand) {
                        echo '<div class="col">';
                        echo '<a href="#" class="btn btn-outline-light w-100 mb-2 select-brand" data-id="' . $brand->brand_id . '" data-name="' . htmlspecialchars($brand->name) . '">' . htmlspecialchars($brand->name) . '</a>';
                        echo '</div>';
                    }
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Price Modal -->
    <div class="modal fade" id="priceModal" tabindex="-1" aria-labelledby="priceModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="priceModalLabel">Prix</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action btn-outline-light mb-2 select-price" data-id="100">Moins de 100 €</a>
                <a href="#" class="list-group-item list-group-item-action btn-outline-light mb-2 select-price" data-id="150">Moins de 150 €</a>
                <a href="#" class="list-group-item list-group-item-action btn-outline-light mb-2 select-price" data-id="200">Moins de 200 €</a>
                <a href="#" class="list-group-item list-group-item-action btn-outline-light mb-2 select-price" data-id="250">Moins de 250 €</a>
                <a href="#" class="list-group-item list-group-item-action btn-outline-light mb-2 select-price" data-id="300">Moins de 300 €</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Results -->
    <div class="row">
        <?php
        // Removed Wishlist usage as wishlist feature is removed
        // require_once APP_PATH . '/models/Wishlist.php';
        // $wishlist = null;
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        // if ($user_id) {
        //     $wishlist = new Wishlist();
        //     $user_wishlist = $wishlist->getUserWishlist($user_id);
        // } else {
        //     $user_wishlist = [];
        // }
        ?>
        <?php if(isset($data['products']) && !empty($data['products'])): ?>
            <?php foreach($data['products'] as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 custom-card product-card position-relative">
                        <a href="<?= BASE_URL ?>/product/details/<?= $product->product_id ?>" class="text-decoration-none product-card-link" style="display:block; height:100%;">
                            <div class="badge badge-custom-<?= strtolower($product->gender) ?> position-absolute end-0 m-2">
                                <?= $product->gender ?>
                            </div>
                            <?php 
                            $defaultImage = "/scent/public/images/placeholder.jpg";
                            if (!empty($product->image)) {
                                $imagePath = "/scent/public" . $product->image;
                            } else {
                                $imagePath = $defaultImage;
                            }
                            ?>
                            <img src="<?php echo $imagePath; ?>" 
                                 class="card-img-top product-card-img" 
                                 alt="<?php echo $product->name; ?>" 
                                 onerror="this.onerror=null; this.src='<?php echo $defaultImage; ?>'">
                            <div class="card-body d-flex flex-column">
                                <p class="text-muted mb-1"><?= $product->brand_name ?></p>
                                <h5 class="card-title"><?= $product->name ?></h5>
                                <p class="card-text flex-grow-1"><?= substr($product->description, 0, 80) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="fw-bold"><?= number_format($product->price, 2, ',', ' ') ?> €</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Aucun produit trouvé correspondant à votre recherche.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.select-category').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('selectedCategory').value = this.getAttribute('data-id');
        document.querySelector('.filter-btn[data-bs-target="#categoriesModal"]').textContent = this.getAttribute('data-name');
        var modal = bootstrap.Modal.getInstance(document.getElementById('categoriesModal'));
        modal.hide();
        document.getElementById('product-search-form').submit();
    });
});
document.querySelectorAll('.select-brand').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('selectedBrand').value = this.getAttribute('data-id');
        document.querySelector('.filter-btn[data-bs-target="#brandsModal"]').textContent = this.getAttribute('data-name');
        var modal = bootstrap.Modal.getInstance(document.getElementById('brandsModal'));
        modal.hide();
        document.getElementById('product-search-form').submit();
    });
});
document.querySelectorAll('.select-price').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('selectedPrice').value = this.getAttribute('data-id');
        document.querySelector('.filter-btn[data-bs-target="#priceModal"]').textContent = this.textContent;
        var modal = bootstrap.Modal.getInstance(document.getElementById('priceModal'));
        modal.hide();
        document.getElementById('product-search-form').submit();
    });
});
</script>