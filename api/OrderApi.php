<?php

//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('X-Auth-Key: qwerty123');

include_once __DIR__ . '/../models/Order.php';

//DB
$database = new Database();
$db = $database->checkPdo();
//Order object
$order = new Order($db);
getAction($order);

/**
 * Get Action
 */
function getAction($order)
{
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $key = array_search('orders', $uri);
    if ($key) {
        $method = $_SERVER['REQUEST_METHOD'];
        $key = (int)$key;

        if ($method === 'GET') {
            if (!isset($uri[$key + 1]) || isset($_GET["done"])) {
                $done = NULL;
                if (isset($_GET["done"])) {
                    $done = $_GET["done"];
                }
                $result = $order->getOrders($done);
                getRow($result);

            } elseif (isset($uri[$key + 1])) {
                $result = $order->getOrder($uri[$key + 1]);
                getRow($result);

            } else {
                echo json_encode(array('message' => 'Error action'));
            }

        } elseif ($method === 'POST') {
            if (!isset($uri[$key + 1]) && !empty($_POST["items"])) {
                $result = $order->createOrder($_POST["items"]);
                getRow($result);

            } elseif (isset($uri[$key + 2]) && $uri[$key + 2] === 'items' && !empty($_POST["items"])) {
                $result = $order->addOrderItems($uri[$key + 1], $_POST["items"]);
                echo json_encode(array('message' => $result));

            } elseif (isset($uri[$key + 2]) && $uri[$key + 2] === 'done') {
                $result = $order->updateOrderStatus($uri[$key + 1]);
                echo json_encode(array('message' => $result));

            } else {
                echo json_encode(array('message' => 'Error action'));
            }

        } else {
            echo json_encode(array('message' => 'Error: undefined method'));
        }
    }
}

/**
 * Get Row
 */
function getRow($result)
{
    $num = $result->rowCount();
    if ($num > 0) {
        $orders_arr = [];
        $orders_arr['data'] = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $order_item = array(
                'order_id' => $order_id,
                'items' => $items,
                'done' => (bool)$done,
            );
            array_push($orders_arr['data'], $order_item);
        }
        echo json_encode($orders_arr);
    } else {
        echo json_encode(array('message' => 'The result is missing'));
    }
}
