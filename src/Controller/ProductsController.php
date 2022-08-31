<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $products = $this->paginate($this->Products);


        $session = $this->request->getSession();
        //debug($session->read('cart'));

        $this->set(compact('products'));
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $product = $this->Products->get($id);
        $subTotal = 0;
        $quantity = 0;

        if($this->checkCartSessionExists()){
            $quantity = $this->request->getSession()->read('cart');
            //debug($quantity);
            //exit;

            if(array_key_exists($id, $quantity)){
                $quantity = $this->request->getSession()->read('cart')[$id];
            }
            else{
                $subTotal = 0;
                $quantity = 0;
            }

            //debug($quantity);

            if(isset($quantity)){
                $subTotal = (int)$product['sale_price'] * $quantity;
            }
            else{
                $subTotal = 0;
                $quantity = 0;
            }
        }
        if($this->request->is('post')){
            //debug($this->request->getData());
            //debug($product['name']);

            // saving these in a session
            $prodId = $product['id'];
            $prodQn = $this->request->getData()['quantity'];
            $quantity = $prodQn;
            $this->saveToCartSession($prodId, $prodQn);

            // calculating new subtotal
            $prodPrice = $product['sale_price'];
            $subTotal = $prodPrice * (int)$prodQn;
        }

        $this->set(compact('product'));
        $this->set('subTotal', $subTotal);
        $this->set('quantity', $quantity);
    }


    protected function checkCartSessionExists(){
        $cartSession = $this->request->getSession();
        return $cartSession->check('cart');
    }

    protected function saveToCartSession($productId, $productQuant){
        // getting cart session
        $cartSession = $this->request->getSession();
        // check if session is empty. If empty, then put the array in 
        if($cartSession->check('cart')==false){
            $cartArray = array();
            $cartArray[strval($productId)] = $productQuant;
            $cartSession->write(['cart'=>$cartArray]);
        }
        else{
            $cartArray = $cartSession->read('cart');
            //adding new product
            $cartArray[strval($productId)] = $productQuant;

            //write new merged array to session
            $cartSession->write(['cart'=>$cartArray]);
        }

    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $product = $this->Products->newEmptyEntity();
       
        if ($this->request->is('post')) {
            debug($this->request->getData());
            $product = $this->Products->patchEntity($product, $this->request->getData());
            if ($this->Products->save($product)) {
                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The product could not be saved. Please, try again.'));
        }
        $orders = $this->Products->Orders->find('list', ['limit' => 200]);
        $this->set(compact('product', 'orders'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['Orders'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $product = $this->Products->patchEntity($product, $this->request->getData());
            if ($this->Products->save($product)) {
                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The product could not be saved. Please, try again.'));
        }
        $orders = $this->Products->Orders->find('list', ['limit' => 200]);
        $this->set(compact('product', 'orders'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The product has been deleted.'));
        } else {
            $this->Flash->error(__('The product could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
