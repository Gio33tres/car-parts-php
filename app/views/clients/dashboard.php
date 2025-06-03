<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Repuestos - Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Bienvenido, <?= htmlspecialchars($_SESSION['username'] ?? 'Invitado') ?></h1>
            <a href="/logout" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        
        <!-- Products Section -->
        <h2 class="my-4">Repuestos Disponibles</h2>
        <div class="row">
            <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text">Precio: $<?= number_format($product['price'], 2) ?></p>
                        <p class="card-text">En stock: <?= $product['quantity'] ?></p>
                        <button class="btn btn-primary" data-bs-toggle="modal" 
                                data-bs-target="#purchaseModal" 
                                data-product-id="<?= $product['id'] ?>"
                                data-product-name="<?= htmlspecialchars($product['name']) ?>"
                                data-product-price="<?= $product['price'] ?>"
                                data-max-quantity="<?= $product['quantity'] ?>">
                            Comprar
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Purchase History Section -->
        <h2 class="my-4">Historial de Compras</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Precio Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($purchaseHistory)): ?>
                        <?php foreach ($purchaseHistory as $purchase): ?>
                            <tr>
                                <td><?= date('Y-m-d H:i', strtotime($purchase['purchase_date'])) ?></td>
                                <td><?= htmlspecialchars($purchase['product_name']) ?></td>
                                <td><?= $purchase['quantity'] ?></td>
                                <td>$<?= number_format($purchase['unit_price'], 2) ?></td>
                                <td>$<?= number_format($purchase['total_price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay historial de compras disponible.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Purchase Modal -->
    <div class="modal fade" id="purchaseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Comprar <span id="productName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/purchase" method="POST">
                    <input type="hidden" name="product_id" id="modalProductId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1">
                            <small id="maxQuantityHelp" class="form-text text-muted"></small>
                        </div>
                        <p>Total: $<span id="totalPrice">0.00</span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar Compra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const purchaseModal = document.getElementById('purchaseModal');
        purchaseModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const productPrice = parseFloat(button.getAttribute('data-product-price'));
            const maxQuantity = parseInt(button.getAttribute('data-max-quantity'));
            
            document.getElementById('productName').textContent = productName;
            document.getElementById('modalProductId').value = productId;
            document.getElementById('quantity').max = maxQuantity;
            document.getElementById('maxQuantityHelp').textContent = `Máximo disponible: ${maxQuantity}`;
            
            const quantityInput = document.getElementById('quantity');
            quantityInput.addEventListener('input', function() {
                const quantity = parseInt(this.value) || 0;
                document.getElementById('totalPrice').textContent = (quantity * productPrice).toFixed(2);
            });
        });
    </script>
</body>
</html>