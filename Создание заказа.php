<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Sale\Order;

CModule::IncludeModule("sale");
//товар
$products = array(
    array(
        'PRODUCT_ID' => 1,
        'NAME' => 'LADA VESTA',
        'PRODUCT_PROVIDER_CLASS' => '\Bitrix\Catalog\Product\CatalogProvider',
        'PRICE' => 2500,
        'CURRENCY' => 'RUB',
        'QUANTITY' => 1
    ),
    array(
        'PRODUCT_ID' => 2,
        'NAME' => 'LADA GRANTA',
        'PRODUCT_PROVIDER_CLASS' => '\Bitrix\Catalog\Product\CatalogProvider',
        'PRICE' => 1500,
        'CURRENCY' => 'RUB',
        'QUANTITY' => 1
    ),
    );

//Создать корзину
    $basket = \Bitrix\Sale\Basket::create(SITE_ID);
    foreach ($products as $product) {
        $item = $basket->createItem("catalog", $product["PRODUCT_ID"]);
        $item->setField('QUANTITY', 1);
        unset($product["PRODUCT_ID"]);
        $item->setFields($product);
    }

//Создать заказ 1 - for admin
$order = Bitrix\Sale\Order::create(SITE_ID, 1);
$order->setField('CURRENCY', 'RUB');
$order->setField('USER_DESCRIPTION', 'Беру на один год');
$order->setField('PRICE', 1555);
$order->setPersonTypeId(1);
$order->setBasket($basket);

//Службы доставки
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem(
    /*
     * 2-pickup
     * 3-small radius
     * 4-big radius
     * */
    Bitrix\Sale\Delivery\Services\Manager::getObjectById(2)
);

//Отгрузка
$shipmentItemCollection = $shipment->getShipmentItemCollection();
foreach ($basket as $basketItem){
    $item = $shipmentItemCollection->createItem($basketItem);
    $item->setQuantity($basketItem->getQuantity());
}

//Оплата
$paymentCollection = $order->getPaymentCollection();
$payment = $paymentCollection->createItem(
    Bitrix\Sale\PaySystem\Manager::getObjectById(2)
);

//Установка оплаты
$payment->setField("SUM", $order->getPrice());
$payment->setField("CURRENCY", $order->getCurrency());


//Установка свойств
$propertyCollection = $order->getPropertyCollection();

//Создание свойств
$propertyValue = $propertyCollection->createItem(['ID' => 1, 'NAME' => 'Телефон', 'TYPE' => 'STRING', 'CODE' => 'PHONE']);
$propertyValue = $propertyCollection->createItem(['ID' => 2,'NAME' => 'Почта', 'TYPE' => 'STRING', 'CODE' => 'ORDER_MAIL']);

//Установка значений свойств
$phoneProperty = getPropertyByCode($propertyCollection, 'PHONE');
$phoneProperty->setValue("88002000600");
$emailProperty = getPropertyByCode($propertyCollection, 'ORDER_MAIL');
$emailProperty->setValue("test@test.ru");

//Установка заказа
$result = $order->save();

if (!$result->isSuccess()){
    $result->getErrors();
}

/*Адрес доставки
 $propertyCollection = $this->order->getPropertyCollection();

foreach ($propertyCollection->getGroups() as $group) {
   foreach ($propertyCollection->getGroupProperties($group['ID']) as $property) {
       $p = $property->getProperty();

       if($p["CODE"]  == "ADDRESS")
           $property->setValue("<Адрес из поля ввода формы>");
   }
}

$this->order->save();
 * */
function getPropertyByCode($propertyCollection, $code)  {
    foreach ($propertyCollection as $property)
    {
        if($property->getField('CODE') == $code)
            return $property;
    }
}
?>

