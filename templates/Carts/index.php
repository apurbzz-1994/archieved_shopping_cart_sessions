<h3><?= __('Your Cart') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('Name') ?></th>
                    <th><?= $this->Paginator->sort('Selling Price') ?></th>
                    <th><?= $this->Paginator->sort('Quantity') ?></th>
                    <th><?= $this->Paginator->sort('Subtotal') ?></th>
                </tr>
            </thead>
            <?php if($checkOutList != 0){ ?>
            <tbody>
                <?php foreach ($checkOutList as $item): ?>
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td><?= $item['sale_price'] ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= $item['subtotal'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <?php }else{ ?>
                <h3>Empty</h3>
            <?php } ?>
        </table>
        <?= $this->Html->link('Checkout',['controller' => 'Orders', 'action' => 'add', 2000],['class' => 'button float-right'] ); ?>
    </div>