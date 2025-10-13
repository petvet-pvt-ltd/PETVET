<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Guest/GuestShopModel.php';

class GuestController extends BaseController {
    private $shopModel;

    public function __construct() {
        $this->shopModel = new GuestShopModel();
    }

    public function shop() {
        $products = $this->shopModel->getAllProducts();
        $categories = $this->shopModel->getCategories();
        
        $this->guestView('shop', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function shopProduct() {
        $productId = $_GET['id'] ?? 1;
        $product = $this->shopModel->getProductById($productId);
        $relatedProducts = $this->shopModel->getRelatedProducts($productId, $product['category'] ?? 'food');
        
        if (!$product) {
            // Redirect to shop if product not found
            $this->redirect('/PETVET/index.php?module=guest&page=shop');
            return;
        }

        $this->guestView('shop-product', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'productId' => $productId
        ]);
    }

    // Modified view method for guest pages (no sidebar)
    protected function guestView(string $name, array $data = []) {
        extract($data);
        $page = $name;
        include __DIR__ . '/../views/guest/' . $name . '.php';
    }
}
?>