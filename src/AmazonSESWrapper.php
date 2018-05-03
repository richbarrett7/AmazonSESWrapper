<?PHP

namespace richbarrett\AmazonSESWrapper;

class AmazonSESWrapper {
  
  var $to=array();
  var $cc=array();
  var $bcc=array();
  var $subject='';
  var $body='';
  var $replyTo='';
  var $attachments = array();
  var $stringAttachments = array();
  var $client;
    
  function __construct($accessKeyId, $secretAccessKey, $awsRegion='eu-west-1') {
    
  	$credentials = new \Aws\Common\Credentials\Credentials($accessKeyId, $secretAccessKey);

    $this->client = \Aws\Ses\SesClient::factory(array(
        'version' => 'latest',
        'region'  => $awsRegion,
        'credentials' => $credentials
    ));
        
  }
  
  public function addTo($email) {
    $this->to[]=$email;
  }
  
  public function addCc($email) {
    $this->cc[]=$email;
  }
  
  public function addBcc($email) {
    $this->bcc[]=$email;
  }
  
  public function setSource($email,$name) {
    $this->source = $email;
    $this->source_name = $name;
  } 
  
  public function setSubject($subject) {
    $this->subject = $subject;
  }
  
  public function setBody($body) {
    $this->body = $body;
  }
  
  public function setReplyTo($email) {
    $this->replyTo = $email;
  }
  
  public function addAttachment($path,$name='') {
    
    $tmp['path'] = $path;
    $tmp['name'] = $name;
    $this->attachments[]=(object)$tmp;
    
  }
  
  public function addBase64StringAttachment($base64String,$filename) {
    $this->stringAttachments[]=(object) array( 'string'=>$base64String,'filename'=>$filename);
  }
  
  public function send() {
    
    // Use PHP Mailer to construct the email
    $mail = new \PHPMailer();

    foreach($this->to as $to) $mail->addAddress($to);
    foreach($this->cc as $to) $mail->addCC($to);
    foreach($this->to as $to) $mail->addBCC($to);
    if(strlen($this->replyTo) > 0) $mail->addReplyTo($this->replyTo);
    $mail->setFrom($this->source, $this->source_name);
    $mail->Subject = $this->subject;
    $mail->CharSet = 'UTF-8';
    $mail->Body = $this->body;
    $mail->isHTML(true);
    
    foreach($this->attachments as $attachment) {
      $mail->addAttachment($attachment->path, $attachment->name);
    }
    
    foreach($this->stringAttachments as $attachment) {
      $mail->addStringAttachment(base64_decode($attachment->string), $attachment->filename,'base64','','attachment');
    }
    
    $mail->preSend();
    
    $args = [
        'Source'       => $this->source,
        'Destinations' => $this->to,
        'RawMessage'   => [
            'Data' => base64_encode( $mail->getSentMIMEMessage() )
        ]
    ];
    
    return $this->client->sendRawEmail($args);
    
  }
  
}

?>