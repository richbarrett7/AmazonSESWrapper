<?PHP

include_once('vendor/autoload.php');

$AWS_ACCESS_KEY_ID = '';
$AWS_SECRET_ACCESS_KEY = ''; 

try {
  
  $e = new richbarrett\AmazonSESWrapper\AmazonSESWrapper($AWS_ACCESS_KEY_ID, $AWS_SECRET_ACCESS_KEY, 'eu-west-1');
  $e->addTo('rich@example.com');
  $e->setSource('noreply@example.com', 'My Company Name');
  $e->setSubject('Test AWS Wrapper');
  $e->setBody('Hello world');
  $e->setReplyTo('rich@example.com');
  $e->addBase64StringAttachment(base64_encode('testing 123'), 'test.txt');
  $e->send();
  
  die('done');
  
} catch (Exception $e) {
  
  die('Error: '.$e->getMessage());
  
}