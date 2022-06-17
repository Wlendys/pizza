<?php

/**
 * OrderApi
 *
 * @var $x_auth_key
 */

//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../config/AuthKey.php';
include_once __DIR__ . '/../models/Order.php';

//AuthKey
$auth_key = FALSE;
if (isset($_SERVER['HTTP_X_AUTH_KEY']) && $_SERVER['HTTP_X_AUTH_KEY'] === $x_auth_key) {
    $auth_key = TRUE;
}

//DB
$database = new Database();
$db = $database->checkPdo();

//Order object
$order = new Order($db);
getAction($order, $auth_key);

/**
 * Get Action
 */
function getAction($order, $auth_key)
{
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $key = array_search('orders', $uri);
    if ($key) {
        $method = $_SERVER['REQUEST_METHOD'];
        $key = (int)$key;

        if ($method === 'GET') {
            requestsGet($uri, $key, $order, $auth_key);
        } elseif ($method === 'POST') {
            requestsPost($uri, $key, $order, $auth_key);
        } else {
            http_response_code(405);
            echo json_encode(array('message' => 'Error: undefined method'));
        }
    }
}

/**
 * Get Requests
 */
function requestsGet($uri, $key, $order, $auth_key) {
    if ((!isset($uri[$key + 1]) || isset($_GET["done"])) && $auth_key === TRUE) {
        $done = NULL;
        if (isset($_GET["done"])) {
            $done = $_GET["done"];
        }
        $result = $order->getOrders($done);
        getRow($result);

    } elseif (isset($uri[$key + 1]) && !isset($_GET["done"])) {
        $result = $order->getOrder($uri[$key + 1]);
        getRow($result);

    } else {
        http_response_code(404);
        echo json_encode(array('message' => 'Error action'));
    }
}

/**
 * Post Requests
 */
function requestsPost($uri, $key, $order, $auth_key) {
    if (!isset($uri[$key + 1])) {
        $items = str_replace(array("\r\n", "\r", "\n"), '', file_get_contents('php://input'));
        if ($items !== '') {
            $items = json_decode($items);
            $items = json_encode($items->items);
            $result = $order->createOrder($items);
            http_response_code(201);
            getRow($result);
        } else {
            http_response_code(204);
            echo json_encode(array('message' => 'Error: elements are missing'));
        }

    } elseif (isset($uri[$key + 2]) && $uri[$key + 2] === 'items') {
        $add_items = str_replace(array("\r\n", "\r", "\n"), '', file_get_contents('php://input'));
        $add_items = preg_replace('/ {2,}/',' ', $add_items);
        if ($add_items !== '') {
            $result = $order->addOrderItems($uri[$key + 1], $add_items);
            echo json_encode(array('message' => $result));
        } else {
            http_response_code(204);
            echo json_encode(array('message' => 'Error: elements are missing'));
        }

    } elseif (isset($uri[$key + 2]) && $uri[$key + 2] === 'done' && $auth_key === TRUE) {
        $result = $order->updateOrderStatus($uri[$key + 1]);
        echo json_encode(array('message' => $result));

    } else {
        http_response_code(404);
        echo json_encode(array('message' => 'Error action'));
    }
}

/**
 * Get Row
 *
 * @var $order_id
 * @var $items
 * @var $done
 */
function getRow($result)
{
    $num = $result->rowCount();
    if ($num > 0) {
        $orders_arr = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $order_item = array(
                'order_id' => $order_id,
            );
            if (isset($items)) {
                $order_item['items'] = $items;
            }
            $order_item['done'] = (bool) $done;
            array_push($orders_arr, $order_item);
        }
        echo json_encode($orders_arr);
    } else {
        http_response_code(204);
        echo json_encode(array('message' => 'The result is missing'));
    }
}
