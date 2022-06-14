# Pizza api
1. Создайте базу данных MySql
2. Добавьте конфиги в `\config\Database.php`
3. Запустите `pizza_dump.sql` для создания таблицы `orders`

## HTTP-методы:

### `POST` /orders
Создание нового заказа, список товаров не может быть пустой, товары могут повторяться.

### `POST` /orders/{order_id}/items
Добавление товаров в созданный заказ, заказ не должен быть в статусе  done = false

### `GET` /orders/{order_id}
Информация по заказу

### `POST` /orders/{order_id}/done
Пометить заказ как выполненный. Данный метод защищен ключом. Приготовить можно только заказы в статусе done = false

### `GET` /orders/[?done=1|0]
Список всех заказов, также может быть передан необязательный параметр done который фильтрует заказы по данному полю,
если параметр не передан, то выводятся все заказы. Данный метод защищен ключом