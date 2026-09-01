<!-- Marques partenaires -->
<section class="mb-5">
    <h2 class="text-center mb-4">Nos marques partenaires</h2>
    <div class="row g-4 align-items-center justify-content-center">
        <?php
        // Récupérer toutes les marques
        $query = 'SELECT brand_id, name, image FROM brands ORDER BY name';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($brands as $brand) :
            // Correctly format the brand image path
            $brandImagePath = $brand['image'] ? ltrim($brand['image'], '/') : '';
        ?>
        <div class="col-md-2 col-4 text-center">
            <a href="<?= BASE_URL ?>/product/search?brand=<?= $brand['brand_id'] ?>" class="text-decoration-none">
                <?php if ($brandImagePath) : ?>
                    <img src="<?= BASE_URL ?>/public/<?= $brandImagePath ?>" alt="<?= htmlspecialchars($brand['name']) ?>" class="img-fluid brand-logo" style="max-height: 80px;">
                <?php else : ?>
                    <div class="py-3 border rounded">
                        <h6 class="m-0 text-dark"><?= htmlspecialchars($brand['name']) ?></h6>
                    </div>
                <?php endif; ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>
