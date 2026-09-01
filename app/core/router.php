// Admin routes
'admin' => ['controller' => 'AdminController', 'action' => 'index'],
'admin/products' => ['controller' => 'AdminController', 'action' => 'products'],
'admin/products/delete/{id}' => ['controller' => 'AdminController', 'action' => 'delete'],
'admin/orders' => ['controller' => 'AdminController', 'action' => 'orders'],
'admin/orders/update-status' => ['controller' => 'AdminController', 'action' => 'updateStatus'],
'admin/customers' => ['controller' => 'AdminController', 'action' => 'customers'],
'admin/categories' => ['controller' => 'AdminController', 'action' => 'categories'],
'admin/products/get' => ['controller' => 'AdminController', 'action' => 'getProductAjax'],
'admin/products/update' => ['controller' => 'AdminController', 'action' => 'updateProductAjax'],

'orders/cancelOrder/:order_id' => ['controller' => 'OrdersController', 'action' => 'cancelOrder'],
