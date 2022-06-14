<?php

class Order
{
    private $dbh;
    private $table = 'orders';

    public $id;
    public $order_id;
    public $items;
    public $done;


    /**
     * Constructor DB
     */
    public function __construct($db)
    {
        $this->dbh = $db;
    }

    /**
     * Get Order
     */
    public function getOrder($order_id)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE order_id="' . $order_id . '"';
        $sth = $this->dbh->prepare($query);
        $sth->execute();
        return $sth;
    }

    /**
     * Get Orders
     */
    public function getOrders($status)
    {
        $query = 'SELECT * FROM ' . $this->table;
        if ($status !== NULL) {
            $query .= ' WHERE done=' . $status;
        }
        $sth = $this->dbh->prepare($query);
        $sth->execute();
        return $sth;
    }

    /**
     * Create Order
     */
    public function createOrder($items)
    {
        $random_string = new RandomString();
        $order_id = $random_string->generateRandomString();

        $query = 'INSERT INTO ' . $this->table . ' (order_id, items, done)  VALUES ("' . $order_id . '", "' . $items . '", 0)';
        $sth = $this->dbh->prepare($query);
        $sth->execute();

        $query = 'SELECT * FROM ' . $this->table . ' WHERE order_id="' . $order_id . '"';
        $sth = $this->dbh->prepare($query);
        $sth->execute();
        return $sth;
    }

    /**
     * Add Order Items
     */
    public function addOrderItems($order_id, $add_items)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE order_id="' . $order_id . '"';
        $sth = $this->dbh->prepare($query);
        $sth->execute();
        $result = $sth->fetch();
        $items = $result['items'];
        $items = str_replace(']','', $items);
        $items = explode("," , $items);
        $add_items = str_replace('[',' ', $add_items);
        $items[] = $add_items;
        $items = implode(',', $items);

        if ($result['done'] == 0) {
            $query = 'UPDATE ' . $this->table . ' SET items="' . $items . '" WHERE order_id="' . $order_id . '"';
            $sth = $this->dbh->prepare($query);
            $sth->execute();
            return 'Order "' . $order_id . '" add items';
        } else {
            return 'This order is already ready';
        }
    }

    /**
     * Update Order Status
     */
    public function updateOrderStatus($order_id)
    {
        $query = 'SELECT done FROM ' . $this->table . ' WHERE order_id="' . $order_id . '"';
        $sth = $this->dbh->prepare($query);
        $sth->execute();
        $result = $sth->fetch();

        if ($result['done'] == 0) {
            $query = 'UPDATE ' . $this->table . ' SET done=1 WHERE order_id="' . $order_id . '"';
            $sth = $this->dbh->prepare($query);
            $sth->execute();
            return 'Order "' . $order_id . '" status changed';
        } else {
            return 'This order is already ready';
        }
    }
}