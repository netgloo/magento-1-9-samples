<?php
 
/*
|------------------------------------------------------------------------------
| Soap Test
|------------------------------------------------------------------------------
|
| Usage: php -f soap-test.php
|
| References:
|   * http://stackoverflow.com/questions/8139128/connecting-to-magento-api-with-soap
|
*/

// Set here your own parameters
$username = 'username';
$password = 'password';

$host = "example.com/index.php";


// Api urls
$api_url_v1 = "http://" . $host . "/api/soap/?wsdl=1";
$api_url_v2 = "http://" . $host . "/api/v2_soap/?wsdl=1";


// Test N. 1

echo "TEST N. 1 \n";
 
echo "--- 1 \n";
 
$cli = new SoapClient($api_url_v1);
 
echo "--- 2 \n";

// Retreive session id from login
$session_id = $cli->login($username, $password);
 
echo "--- 3 \n";

$result = $cli->call($session_id, 'customer.list', array(array()));

echo "--- end: " . count($result) . " \n";


// Test N. 2

echo "TEST N. 2 \n";
 
echo "--- 1 \n";
 
$cli = new SoapClient($api_url_v2);
 
echo "--- 2 \n";

// Retreive session id from login
$session_id = $cli->login($username, $password);
 
echo "--- 3 \n";

$result = $cli->customerCustomerList($session_id);

echo "--- end: " . count($result) . " \n";


// Test N. 3

echo "TEST N. 3 \n";

echo "--- 1 \n";

//soap handle
$client = new SoapClient("http://" . $host . "/api/soap/?wsdl");

echo "--- 2 \n";

// An action to call later (loading Sales Order List)
$action = "sales_order.list";

echo "--- 3 \n";

try { 

  // We do login
  $sess_id= $client->login($username, $password);

  print_r($client->call($sess_id, $action));

}
catch (Exception $e) { 
  // If an error has occured
  echo "==> Error: " . $e->getMessage();
  exit();
}

echo "--- 4 \n";
