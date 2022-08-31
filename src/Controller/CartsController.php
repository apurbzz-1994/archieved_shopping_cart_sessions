<?php 
namespace App\Controller;

use function PHPUnit\Framework\isNull;

class CartsController extends AppController{
    
    
    
    // displays all available items in the session cart
    public function index()
    {   

        //loading the products model
        $products = $this->loadModel('Products');  

        $prodIds = $this->getProductKeysFromSession();

        $checkOutList = array();

        if(isNull($prodIds)){
            $cartSession = $this->request->getSession()->read('cart');

            $products = $this->Products->find()->where(['id IN' => $prodIds]);
            
            foreach($products as $product):
                $checkOutItem = array();
                $checkOutItem['name'] = $product->name;
                $checkOutItem['sale_price'] = $product->sale_price;
                $checkOutItem['quantity'] = $cartSession[$product->id];
                $checkOutItem['subtotal'] = (int)$checkOutItem['sale_price']*(int)$checkOutItem['quantity'];

                //add to array
                array_push($checkOutList, $checkOutItem);
            endforeach;
        }
        else{
            $checkOutList = 0;
        }
        
        $this->set('checkOutList', $checkOutList);
    }

    
    
    protected function getProductKeysFromSession(){
        // getting cart session
        $cartSession = $this->request->getSession();
        if($cartSession->check('cart')){
            $productIds = array_keys($cartSession->read('cart'));
        }else{
            $productIds = null;
        }

        return $productIds;
    }

}

?>


