<?php
// details.php - Final Clean Version

// Remove if BASE_URL is already defined elsewhere
if (!defined('BASE_URL')) {
    // For XAMPP (local development)
    define('BASE_URL', 'http://localhost/scent');
    // For live server: define('BASE_URL', 'https://yourdomain.com');
}

// Database connection and product query
$database = new Database();
$conn = $database->connect();

// Get product details
$query = 'SELECT 
            p.*,
            b.name as brand_name,
            c.name as category_name
          FROM products p
          JOIN brands b ON p.brand_id = b.brand_id
          JOIN categories c ON p.category_id = c.category_id
          WHERE p.product_id = :id';
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $this->product_model->product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_OBJ);

// Check if product exists
if (!$product) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Produit non trouvé.</div></div>';
    return;
}

// Clean and prepare image path
$cleanImagePath = ltrim(str_replace('\\', '/', $product->image), '/');
$mainImage = !empty($cleanImagePath) ? 
    BASE_URL . '/' . $cleanImagePath : 
    BASE_URL . '/images/products/placeholder.jpg';

// Image display logic
$imageRelPath = ltrim($product->image, '/'); // Remove leading slash if present
$imageFile = $_SERVER['DOCUMENT_ROOT'] . '/scent/public/' . $imageRelPath;
$imagePath = BASE_URL . '/public/' . $imageRelPath;
$placeholder = BASE_URL . '/public/images/products/placeholder.jpg';
if (empty($product->image) || !file_exists($imageFile)) {
    $imagePath = $placeholder;
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$isInWishlist = false;
// Removed Wishlist usage as wishlist feature is removed
// if ($user_id) {
//     require_once APP_PATH . '/models/Wishlist.php';
//     $wishlist = new Wishlist();
//     $isInWishlist = $wishlist->isInWishlist($user_id, $product->product_id);
// }
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-5">
            <div class="product-image-container mb-4 position-relative">
                <img src="<?= $imagePath ?>" 
                     class="img-fluid rounded" 
                     alt="<?= htmlspecialchars($product->name) ?>"
                     style="max-height: 500px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                
<!-- Removed wishlist heart icon as per user request -->
<!-- <div class="wishlist-heart position-absolute top-0 start-0 m-3" data-product-id="<?= $product->product_id ?>">
    <i class="fa<?= ($user_id && $isInWishlist) ? 's' : 'r' ?> fa-heart text-danger" style="font-size: 2rem;"></i>
</div> -->
        </div>
    </div>
    
    <div class="col-md-7">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/product">Parfums</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $product->name ?></li>
            </ol>
        </nav>
        
        <h1 class="mb-1"><?= $product->name ?></h1>
        <h4 class="text-muted mb-3"><span style="color: #888;"><?= $product->brand_name ?></span></h4>
        
        <!-- Rating System -->
        <div class="mb-3">
            <div class="d-flex align-items-center">
                <div class="rating-stars" id="initial-rating-stars">
                    <?php
                    // Initialize rating to a random value between 1 and 5 if no average_rating
                    $rating = $product->average_rating ?? rand(1, 5);
                    $fullStars = floor($rating);
                    $halfStar = $rating - $fullStars >= 0.5;
                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                    
                    // Full stars
                    for ($i = 0; $i < $fullStars; $i++) {
                        echo '<i class="fas fa-star text-warning"></i>';
                    }
                    
                    // Half star
                    if ($halfStar) {
                        echo '<i class="fas fa-star-half-alt text-warning"></i>';
                    }
                    
                    // Empty stars
                    for ($i = 0; $i < $emptyStars; $i++) {
                        echo '<i class="far fa-star text-warning"></i>';
                    }
                    ?>
                </div>
                <span class="ms-2"><?= number_format($rating, 1) ?> / 5</span>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button type="button" class="btn btn-link ms-3" data-bs-toggle="modal" data-bs-target="#ratingModal">
                        Donner mon avis
                    </button>
                    <button type="button" class="btn btn-link ms-3" data-bs-toggle="modal" data-bs-target="#commentsModal">
                        Voir les commentaires
                    </button>
                <?php endif; ?>
            </div>
            <!-- Removed wishlist heart icon as per user request -->
            <!-- <div class="mt-2 wishlist-heart" data-product-id="<?= $product->product_id ?>">
                <i class="fa<?= ($user_id && $isInWishlist) ? 's' : 'r' ?> fa-heart text-danger" style="font-size: 2rem; cursor:pointer;"></i>
            </div> -->
        </div>
        
        <div class="mb-4">
            <span class="fs-3 fw-bold"><?= number_format($product->price, 2, ',', ' ') ?> €</span>
            <?php if (isset($GLOBALS['price_dz'])): ?>
                <span class="fs-5 fw-semibold ms-3">(<?= number_format($GLOBALS['price_dz'], 0, ',', ' ') ?> DZD)</span>
            <?php endif; ?>
            <?php if ($product->stock_quantity > 15) : ?>
                <span class="badge bg-success ms-3">En stock</span>
            <?php elseif ($product->stock_quantity > 5) : ?>
                <span class="badge bg-warning text-dark ms-3">Stock limité</span>
                <small class="text-muted ms-2">(<?= $product->stock_quantity ?> disponibles)</small>
            <?php elseif ($product->stock_quantity > 0) : ?>
                <span class="badge bg-danger ms-3">Derniers articles</span>
                <small class="text-muted ms-2">(<?= $product->stock_quantity ?> disponibles)</small>
            <?php else : ?>
                <span class="badge bg-danger ms-3">Rupture de stock</span>
            <?php endif; ?>
        </div>
        
        <div class="mb-4">
            <h5>Description</h5>
            <p><?= nl2br($product->description) ?></p>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Caractéristiques</h5>
                <ul class="list-unstyled">
                    <li><strong>Marque:</strong> <?= $product->brand_name ?></li>
                    <li><strong>Catégorie:</strong> <?= $product->category_name ?></li>
                    <li><strong>Genre:</strong> <?= $product->gender ?></li>
                    <li><strong>Volume:</strong> <?= $product->volume ?> ml</li>
                    <li><strong>Concentration:</strong> <?= $product->concentration ?></li>
                </ul>
            </div>
        </div>
        
        <?php if ($product->stock_quantity > 0) : ?>
            <form action="<?= BASE_URL ?>/cart/add/<?= $product->product_id ?>" method="POST" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="quantity" class="col-form-label">Quantité</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" id="quantity" name="quantity" class="form-control" 
                               value="1" min="1" max="<?= $product->stock_quantity ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Ajouter au panier
                        </button>
                    </div>
                </div>
            </form>
        <?php else : ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> Ce produit est actuellement en rupture de stock.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ratingModalLabel" style="color: navy;">Donner votre avis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/product/rate/<?= $product->product_id ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="color: navy;">Votre note</label>
                        <div class="rating-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" required>
                                <label for="star<?= $i ?>"><i class="far fa-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="review" class="form-label" style="color: navy;">Votre avis (optionnel)</label>
                        <textarea class="form-control" id="review" name="review" rows="3" style="color: navy;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Comments Modal -->
<div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentsModalLabel" style="color: navy;">Commentaires</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                // Fetch reviews and commenter names for this product
                $reviewsQuery = '
                    SELECT r.review, r.rating, u.username
                    FROM ratings r
                    JOIN users u ON r.user_id = u.user_id
                    WHERE r.product_id = :product_id
                    ORDER BY r.created_at DESC
                ';
                $reviewsStmt = $conn->prepare($reviewsQuery);
                $reviewsStmt->bindParam(':product_id', $product->product_id);
                $reviewsStmt->execute();
                $reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($reviews) === 0) {
                    echo '<p style="color: navy;">Aucun commentaire pour ce produit.</p>';
                } else {
                    foreach ($reviews as $review) {
                        echo '<div class="review-item mb-3" style="color: navy;">';
                        echo '<div class="d-flex align-items-center mb-2">';
                        echo '<strong class="me-2">' . htmlspecialchars($review['username']) . '</strong>';
                        echo '<div class="rating-stars">';
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<i class="fa' . ($i <= $review['rating'] ? 's' : 'r') . ' fa-star text-warning"></i>';
                        }
                        echo '</div>';
                        echo '</div>';
                        if (!empty($review['review'])) {
                            echo '<p class="mb-0">' . nl2br(htmlspecialchars($review['review'])) . '</p>';
                        }
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div id="wishlist-toast" class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-4" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999; display:none;">
  <div class="d-flex">
    <div class="toast-body">
      Ajouté à la liste de souhaits !
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>

<style>
.rating-stars {
    font-size: 1.2rem;
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input {
    display: none;
}

.rating-input label {
    cursor: pointer;
    font-size: 1.5rem;
    color: #ddd;
    padding: 0 0.1em;
}

.rating-input input:checked ~ label,
.rating-input label:hover,
.rating-input label:hover ~ label {
    color: #ffc107;
}

.rating-input label i {
    transition: color 0.2s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('.rating-input input');
    const ratingLabels = document.querySelectorAll('.rating-input label');
    
    ratingLabels.forEach(label => {
        label.addEventListener('mouseover', function() {
            const rating = this.previousElementSibling.value;
            updateStars(rating);
        });
        
        label.addEventListener('mouseout', function() {
            const checkedInput = document.querySelector('.rating-input input:checked');
            if (checkedInput) {
                updateStars(checkedInput.value);
            } else {
                resetStars();
            }
        });
    });
    
    function updateStars(rating) {
        ratingLabels.forEach(label => {
            const starValue = label.previousElementSibling.value;
            if (starValue <= rating) {
                label.querySelector('i').className = 'fas fa-star';
            } else {
                label.querySelector('i').className = 'far fa-star';
            }
        });
    }
    
    function resetStars() {
        ratingLabels.forEach(label => {
            label.querySelector('i').className = 'far fa-star';
        });
    }

    var heart = document.querySelector('.wishlist-heart');
    if (heart) {
        heart.addEventListener('click', function(e) {
            e.stopPropagation();
            var productId = this.getAttribute('data-product-id');
            fetch('<?= BASE_URL ?>/wishlist/toggle', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.in_wishlist) {
                    this.innerHTML = '<i class="fas fa-heart text-danger" style="font-size: 2rem; cursor:pointer;"></i>';
                    showWishlistToast();
                } else {
                    this.innerHTML = '<i class="far fa-heart text-danger" style="font-size: 2rem; cursor:pointer;"></i>';
                }
            }.bind(this));
        });
    }

    function showWishlistToast() {
        var toast = document.getElementById('wishlist-toast');
        if (toast) {
            toast.style.display = 'block';
            setTimeout(function() {
                toast.style.display = 'none';
            }, 2000);
        }
    }
});
</script>
