<?php
function genPassword($len=8)
{
	$num=range(0,9);
	$uchar=range(A,Z);
	$lchar=range(a,z);

	$raw_pass=array_merge($num,$uchar,$lchar);
	
	for($i=0;$i<$len;$i++)
	{
		shuffle($raw_pass);
		$pass.=$raw_pass[$i];
	}  
	return $pass; 
}

function sendMail($_str_title, $_str_content, $_str_toAddress, $_str_toUser = null)
{ 
     
    $_str_thisPlace = "Function: sendMail >> "; 
    $_void_result = false; 
     
    if(empty($_str_toUser)) 
        $_str_toUser = $_str_toAddress; 
    
    $_obj_sendMail = new SendMail($_str_toAddress ,$_str_toUser ,$_str_title); 
    $_obj_sendMail->setMailContent($_str_content); 
    if( !$_obj_sendMail->send()) return $_void_result; 
     
    //Return 
    $_void_result = true; 
    return $_void_result; 
     
} 
class SendMail
{ 
    private $popServer; 
    private $popServerPort; 
    private $senderAddress; 
    private $loginUser; 
    private $password; 
    private $fromUser; 
    private $toMailAddress; 
    private $toUser; 
    private $mailTitle; 
    private $mailContent; 
    private $debug = false; 
    private $error = null; 
    private $result; 
    private $boundary; 
    private $socketConnect; 
     
    public function __construct( $getToMailAddress , $getToUser , $getMailTitle )
    { 

        $this->popServer = "wanglab.ucsd.edu"; 
        $this->popServerPort = 25; 
        $this->senderAddress = "star@wanglab.ucsd.edu"; 
        $this->loginUser = "star"; 
        $this->password = "star123456"; 
        $this->fromUser = "star@wanglab.ucsd.edu"; 
        $this->boundary = uniqid(""); 

        $this->toMailAddress = $getToMailAddress; 
        $this->toUser = $getToUser; 
        $this->mailTitle = $getMailTitle; 
         
    } 

    public function __destruct()
    { 
        socket_close( $this->socketConnect ); 
    } 

     private function createConnect()
     { 
        if(!($this->socketConnect = socket_create(AF_INET, SOCK_STREAM, SOL_TCP )))
        { 
            print "Exception: Socket Create Error!"; 
            exit; 
        }
         
        if(!(socket_connect( $this->socketConnect , $this->popServer , $this->popServerPort )))
        { 
            print "Exception: Socket Connect Error!"; 
            exit; 
        }
	else
	{ 
            $this->resouceCom(); 
        }
    } 
      private function comArray_Auth()
      { 
        $comArray = array( 
            "HELO EHLO\r\n" ,  
            "AUTH LOGIN\r\n" ,  
            base64_encode( $this->loginUser ) . "\r\n" ,  
            base64_encode( $this->password ) . "\r\n" ,  
            "MAIL FROM:<{$this->senderAddress}>\r\n" ,  
            "RCPT TO:<{$this->toMailAddress}>\r\n" ,  
            "DATA\r\n" ,  
            "TO:{$this->toUser}\r\n" .  
            "FROM:{$this->fromUser}\r\n" .  
            "SUBJECT:{$this->mailTitle}\r\n" .  
            "MIME-Version: 1.0\r\n" .  
            "Content-Type: text/html; charset=\"UTF-8\"\r\n" .  
            $this->mailContent . "\r\n" .  
            ".\r\n" ,  
            "QUIT\r\n" 
        ); 
        return $comArray; 
    } 
     
      private function comArray()
      { 
        $comArray = array( 
            "HELO EHLO\r\n" ,  
            "MAIL FROM:<{$this->senderAddress}>\r\n" ,  
            "RCPT TO:<{$this->toMailAddress}>\r\n" ,  
            "DATA\r\n" ,  
            "TO:{$this->toUser}\r\n" .  
            "FROM:{$this->fromUser}\r\n" .  
            "SUBJECT:{$this->mailTitle}\r\n" .  
            "MIME-Version: 1.0\r\n" .  
            "Boundary: \r\n" .  $this->boundary  .  
            "Content-Type: text/html; charset=\"UTF-8\"\r\n" .  
            $this->mailContent . "\r\n" .  
            ".\r\n" ,  
            "QUIT\r\n" 
        ); 
        return $comArray; 
      } 
    private function sendCom( $setCom )
    { 
        if( $this->debug )
	{ 
            echo $setCom."<BR>"; 
        }
        socket_write( $this->socketConnect , $setCom , strlen( $setCom )); 
        return $this->setError(); 
    } 
     
 
    private function resouceCom()
    { 
        $this->result = socket_read( $this->socketConnect , 1024 ); 
        if( $this->debug )
	{ 
            echo $this->result."<BR>"; 
        }
        return $this->setError( $this->result ); 
    } 
     
    private function setError( $checkString = null )
    { 
        $this->error = null; 
        if( !empty( $checkString ) ){ 
            if( ( $j = intval( substr( $checkString , 0 , 1 ) ) ) > 3 )
	    { 
                $this->error = $checkString; 
            }
        }
        if( !empty( $this->error ) ){ 
            if( $this->debug ){ 
                echo "<span style = 'color:#ff0000;'>Error: ".$this->error."</span>"; 
            }
            return false; 
        }
        return true; 
    } 
     
    public function setDebug( $setDebug = false )
    { 
        $this->debug = $setDebug; 
    } 
     
    public function setMailContent( $setMailContent )
    { 
        $this->mailContent = $setMailContent; 
    } 
     
    public function getMail()
    { 
        if( $this->debug ){ 
            $comArray = $this->comArray(); 
            for( $i = 0 ; current( $comArray ) ; next( $comArray ))
            { 
                echo $comArray[$i]."<BR>"; 
                $i++; 
            }
        }
        return $this->mailContent; 
    } 
     
    public function send()
    {
        $this->createConnect(); 
        $comArray = $this->comArray(); 
        for( $i = 0 ; current( $comArray ) ; next($comArray))
        { 
            if( !$this->sendCom( $comArray[$i] ))
            {
                return false; 
            } 
            if( !$this->resouceCom())
            { 
               return false; 
            } 
            $i++; 
        } 
        return true; 
    }    
}
?>
