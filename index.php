<pre>
<?php
use HishabKitab\Payment\Payment;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require_once 'vendor/autoload.php';
$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();
$payment = new Payment();
