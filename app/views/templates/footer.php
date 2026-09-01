</div>
    <!-- Fin du contenu principal -->
    
    <!-- Pied de page -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5 class="text-uppercase mb-4">Scent</h5>
                    <p>Votre boutique en ligne de parfums de luxe. Découvrez notre vaste collection de parfums pour hommes et femmes des plus grandes marques.</p>
                    <div class="mt-4">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5 class="text-uppercase mb-4">Liens rapides</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= BASE_URL ?>" class="text-white text-decoration-none">Accueil</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/product" class="text-white text-decoration-none">Parfums</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/product/search?gender=Homme" class="text-white text-decoration-none">Parfums Homme</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/product/search?gender=Femme" class="text-white text-decoration-none">Parfums Femme</a></li>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/product/search?gender=Unisexe" class="text-white text-decoration-none">Parfums Unisexe</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5 class="text-uppercase mb-4">Mon compte</h5>
                    <ul class="list-unstyled">
                        <?php if ($session->has('user_id')) : ?>
                            <li class="mb-2"><a href="<?= BASE_URL ?>/user/profile" class="text-white text-decoration-none">Mon profil</a></li>
                            <li class="mb-2"><a href="<?= BASE_URL ?>/order/history" class="text-white text-decoration-none">Mes commandes</a></li>
                            <li class="mb-2"><a href="<?= BASE_URL ?>/user/logout" class="text-white text-decoration-none">Déconnexion</a></li>
                        <?php else : ?>
                            <li class="mb-2"><a href="<?= BASE_URL ?>/user/login" class="text-white text-decoration-none">Connexion</a></li>
                            <li class="mb-2"><a href="<?= BASE_URL ?>/user/register" class="text-white text-decoration-none">Inscription</a></li>
                        <?php endif; ?>
                        <li class="mb-2"><a href="<?= BASE_URL ?>/cart" class="text-white text-decoration-none">Mon panier</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5 class="text-uppercase mb-4">Contactez-nous</h5>
                    <p>
                        <i class="fas fa-envelope me-3"></i> contact@scent.com
                    </p>
                    <p>
                        <i class="fas fa-phone me-3"></i> 0541598783
                    </p>
                    <p>
                        <i class="fas fa-globe me-2"></i> Algeria
                    </p>
                </div>
            </div>
            
            <hr class="mb-4">
            
            <div class="row align-items-center">
                <div class="col-md-7 col-lg-8">
                    <p>
                        © 2025 <strong>Scent</strong> - Tous droits réservés
                    </p>
                </div>
                
                <div class="col-md-5 col-lg-4">
                    <div class="text-center text-md-end">
                        <ul class="list-unstyled list-inline">
                            <li class="list-inline-item">
                                <a href="#" class="text-white text-decoration-none fs-5">
                                    <i class="fab fa-cc-visa"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="text-white text-decoration-none fs-5">
                                    <i class="fab fa-cc-mastercard"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="text-white text-decoration-none fs-5">
                                    <i class="fab fa-cc-paypal"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="text-white text-decoration-none fs-5">
                                    <i class="fab fa-cc-apple-pay"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript personnalisé -->
    <script src="<?= BASE_URL ?>/js/main.js"></script>
    
    <?php
    // Inclure des fichiers JavaScript spécifiques si nécessaire
    if (isset($page_js)) {
        foreach ($page_js as $js) {
            echo '<script src="' . BASE_URL . '/js/' . $js . '.js"></script>';
        }
    }
    ?>
    <!-- Modal de choix connexion/inscription -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel">Votre compte</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row text-center">
          <div class="col-md-6 mb-3">
            <a href="<?= BASE_URL ?>/user/login" class="d-block p-4 h-100 border rounded text-decoration-none">
              <img src="<?= BASE_URL ?>/public/images/connexion.png" alt="Connexion" style="max-width:300px;max-height:300px;object-fit:contain;" class="mb-3">
              <h5>Connexion</h5>
              <p class="text-muted small">Accédez à votre compte</p>
            </a>
          </div>
          <div class="col-md-6 mb-3">
            <a href="<?= BASE_URL ?>/user/register" class="d-block p-4 h-100 border rounded text-decoration-none">
              <img src="<?= BASE_URL ?>/public/images/inscreption.png" alt="Inscription" style="max-width:300px;max-height:300px;object-fit:contain;" class="mb-3">
              <h5>Inscription</h5>
              <p class="text-muted small">Créez un nouveau compte</p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<!-- Toast de confirmation d'ajout au panier -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
    <div class="toast-header bg-success text-white">
      <i class="fas fa-check-circle me-2"></i>
      <strong class="me-auto">Produit ajouté au panier</strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      <div class="d-flex mb-3">
        <div id="toast-product-image" class="me-2" style="width: 60px; height: 60px; flex-shrink: 0;">
          <img src="" class="img-fluid rounded" alt="Product image" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div>
          <p id="toast-product-name" class="mb-0 fw-bold"></p>
          <p id="toast-product-brand" class="mb-0 small text-muted"></p>
          <p id="toast-product-price" class="mb-0">Prix: <span></span> €</p>
          <p id="toast-product-quantity" class="mb-0">Quantité: <span></span></p>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <a href="<?= BASE_URL ?>/cart" class="btn btn-sm btn-primary">
          <i class="fas fa-shopping-cart me-1"></i>Voir le panier
        </a>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="toast">
          <i class="fas fa-times me-1"></i>Fermer
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Style CSS pour le toast -->
<style>
#cartToast {
  min-width: 320px;
  max-width: 350px;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

#cartToast .toast-header {
  border-bottom: none;
}

#cartToast .btn-close-white {
  filter: invert(1) grayscale(100%) brightness(200%);
}

/* Animation pour le toast */
@keyframes slideInRight {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

.toast.show {
  animation: slideInRight 0.3s ease-out;
}

/* Remove card background and borders for floating effect */
#loginModal .modal-content {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
}
#loginModal .modal-body {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  padding: 0 !important;
}
#loginModal .modal-header {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  justify-content: flex-end;
}
#loginModal .modal-title {
  display: none;
}
#loginModal .modal-dialog {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}
#loginModal .modal-body .row {
  width: 100%;
  justify-content: center;
  align-items: center;
  margin: 0;
}
#loginModal .modal-body .row > div {
  display: flex;
  justify-content: center;
  align-items: center;
  background: none !important;
  box-shadow: none !important;
  border: none !important;
}
#loginModal .modal-body .row > div > a {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  transition: box-shadow 0.3s, filter 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-bottom: 0;
}
#loginModal .modal-body .row > div > a img {
  transition: box-shadow 0.3s, filter 0.3s;
  filter: drop-shadow(0 0 0px gold);
}
#loginModal .modal-body .row > div > a:hover img {
  filter: drop-shadow(0 0 20px gold) drop-shadow(0 0 40px gold);
}
#loginModal .modal-body .row > div > a:hover h5 {
  color: gold;
  text-shadow: 0 0 10px gold, 0 0 20px gold;
}
</style>

<!-- Script JavaScript pour intercepter les formulaires d'ajout au panier -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Intercepter tous les formulaires d'ajout au panier
  const addToCartForms = document.querySelectorAll('form[action^="<?= BASE_URL ?>/cart/add/"]');
  
  addToCartForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Récupérer l'ID du produit depuis l'URL du formulaire
      const productId = this.action.split('/').pop();
      
      // Récupérer l'image du produit (si disponible dans la page)
      let productImage = '';
      
      // Chercher l'image du produit - pour la page détail
      const detailImage = document.querySelector('.product-image-container img');
      if (detailImage) {
        productImage = detailImage.src;
      } 
      // Pour les cartes produit (produits similaires, page catalogue)
      else {
        const productCard = this.closest('.product-card, .card');
        if (productCard) {
          const cardImage = productCard.querySelector('img');
          if (cardImage) {
            productImage = cardImage.src;
          }
        }
      }
      
      // Envoyer le formulaire via AJAX
      const formData = new FormData(this);
      
      fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Mettre à jour le toast avec les informations du produit
          document.querySelector('#toast-product-name').textContent = data.productName;
          document.querySelector('#toast-product-quantity span').textContent = data.quantity;
          document.querySelector('#toast-product-price span').textContent = 
            parseFloat(data.productPrice).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
          
          // Afficher la marque si disponible
          const brandElement = document.querySelector('#toast-product-brand');
          if (data.productBrand) {
            brandElement.textContent = data.productBrand;
            brandElement.style.display = 'block';
          } else {
            brandElement.style.display = 'none';
          }
          
          // Mettre à jour l'image du produit
          if (productImage) {
            document.querySelector('#toast-product-image img').src = productImage;
            document.querySelector('#toast-product-image').style.display = 'block';
          } else {
            // Utiliser l'image retournée par l'API si disponible
            if (data.productImage) {
              document.querySelector('#toast-product-image img').src = 
                '<?= BASE_URL ?>/public/images/products/' + data.productImage;
              document.querySelector('#toast-product-image').style.display = 'block';
            } else {
              document.querySelector('#toast-product-image').style.display = 'none';
            }
          }
          
          // Mettre à jour le mini-panier dans l'en-tête si existe
          const cartCountElements = document.querySelectorAll('.cart-count, .cart-badge');
          cartCountElements.forEach(element => {
            element.textContent = data.cartCount;
          });
          
          // Afficher le toast
          const toast = new bootstrap.Toast(document.getElementById('cartToast'));
          toast.show();
        } else {
          // En cas d'erreur, afficher un message
          alert(data.message || 'Une erreur est survenue lors de l\'ajout au panier.');
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        // En cas d'erreur de connexion, soumettre le formulaire normalement
        form.submit();
      });
    });
  });
});
</script>