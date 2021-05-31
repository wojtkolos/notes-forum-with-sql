<?php
include("datatable.php");
include("uploadfile.php");
include("captcha.php");

class Forum
{
    // property declaration
    public $u=false;
    public $g=false;
    public $context;
    public $error="";
    public $baseurl;
    protected $db;
    protected $topic;
    protected $user;
    protected $image;

    public function __construct() {

      try{ 
         $this->db = new PDO('sqlite:'.dirname(__FILE__).'/db.sq3'); 
         $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch(PDOException $e){ echo $e->getMessage().": ".$e->getCode();  exit; }
      
      // Model danych - tablice bazy danych
      $this->topic = new Datatable( $this->db, "topic", array("topic","topic_body","date","userid","topicid"), "topicid" );
      $this->user  = new Datatable( $this->db, "user", array("userid", "username","userlevel","pass"), "userid", false );
      $this->guest  = new Datatable( $this->db, "guest", array("guestid", "username","userlevel"), "guestid", false );
      $this->activity= new Datatable( $this->db, "activity", array("userid", "IP", "URI", "date","id"), "id" );
      // komponenty
      $this->captcha = new Captcha;
      
      // inicjacja parametrów
      $this->baseurl = "index.php";
      $this->context = (isset($_SESSION["context"]))?$_SESSION["context"]:NULL;
      $this->u = (isset($_SESSION["user"]))?$_SESSION["user"]:false;
      $this->g = (isset($_SESSION["guest"]))?$_SESSION["guest"]:false;
      // administrator
      $admin = $this->user->get("admin");
      if( !isset($admin['userid']) and !isset($admin['userlevel']) and $admin['userlevel'] != 20)
          $this->user->insert(array( "userid"=>"admin", "username"=>"admin","userlevel"=>20,"pass"=>md5("admin") ));
    }                               
    
    public function insert_activity($uri){
     
      if($this->u and $this->u != false){
            $a = array(
            "userid" => $this->u["userid"],
            "IP" => $this->getIP(), 
            "URI" => $uri, 
            "date"=>date("Y-m-d H:i:s")
         );
      }
      else if($this->g and $this->g != false)
      {
         $a = array(
            "userid" => $this->g["guestid"],
            "IP" => $this->getIP(), 
            "URI" => $uri, 
            "date"=>date("Y-m-d H:i:s")
         );
      }
      else 
      {
         $a = array(
            "userid" => $_POST['guestid'],
            "IP" => $this->getIP(), 
            "URI" => $uri, 
            "date"=>date("Y-m-d H:i:s")
         );
      }
      if($this->activity->insert($a));
      else return false;
   }


    public function login($userid,$pass){
         if( !($this->u=$this->user->get($userid)) ) {
            $this->error="Bad user name or password!";
            $this->insert_activity("login-error");
            return false;
         } 
         if( $this->u["pass"]!=md5($pass) ){
            $this->insert_activity("login-error");
            $this->error="Bad user name or password!"; 
            return false;
         }
         $_SESSION = array();
         session_regenerate_id();
         $_SESSION["token"] = md5(session_id().__FILE__);
         $_SESSION["user"] = $this->u;
         $_SESSION["context"] = "topics";
         $this->insert_activity("login-succes");
         $this->reload();
    }

    public function logout(){
       $_SESSION = array();
       if (ini_get("session.use_cookies")) {
          $params = session_get_cookie_params();
          setcookie(session_name(), '', time() - 42000,
              $params["path"], $params["domain"],
              $params["secure"], $params["httponly"]
          );
       }
       session_destroy();
       $this->reload();
    }
    public function register($userid,$username,$pass){
       if($u=$this->user->get($userid)){
          $this->error .= "Bad username";
          $this->insert_activity("register-error");
          return false; 
         }
       $u = array("userid"=>$userid,"username"=>$username,"userlevel"=>10,"pass"=>md5($pass));   
       $this->user->insert($u);
       $_SESSION["user"] = $u;
       $_SESSION["context"] = "topics";
       $this->insert_activity("register-succes");
       $this->reload();
    }


    public function guestlogin(){
      $ses_id = session_regenerate_id();
      if( !($this->g=$this->guest->get($ses_id)) ) {
         $this->guestregister($ses_id);
      } 
      $_SESSION = array();
      /*session_regenerate_id();*/
      $_SESSION["token"] = md5(session_id().__FILE__);
      $_SESSION["guest"] = $this->g;
      $_SESSION["context"] = "topics";
      $this->insert_activity("guest-login");
      $this->reload();
 }
 public function  guestregister($ses_id){
    $g = array("guestid"=>$ses_id, "username"=>"Guest","userlevel"=>0, "IP"=>$this->getIP(), "URI"=>"topics", "date"=>date("Y-m-d H:i:s")); 
    $this->guest->insert($g);
    $_SESSION["guest"] = $g;
    $_SESSION["context"] = "topics";
    $this->insert_activity("guest-register");
 }

 public function getIP(){
    $ip = false;
   if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
   } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   } else {
      $ip = $_SERVER['REMOTE_ADDR'];
   } 
   return $ip;
 }



    public function insert_topic($topic,$topic_body){
       $this->topic->insert(array("topic"=>$topic,"topic_body"=>$topic_body,"date"=>date("Y-m-d H:i:s"),"userid"=>$this->u['userid'] ));
       $this->reload();
    }
    public function delete_topic($topicid){
       $thus->topic->delete($topicid);
       $this->reload();
    }
    public function update_topic($topicid,$topic,$topic_body){
       $this->topic->update(array("topicid"=>$topicid,"topic"=>$topic,"topic_body"=>$topic_body,"date"=>date("Y-m-d H:i:s"),"userid"=>$this->u['userid'] ));
       $this->reload();
    }
    public function delete_user($userid){
          if( $this->user->delete($userid) ) $this->reload();
          else return false;
    }
    public function update_user($userid){
       if($u = $this->user->get($userid)){
          $u['userlevel']=($u['userlevel']==20)?20:10;
          if( $this->user->update($u) ) $this->reload();
          else return false;
       }else return false;
    }
    
  
public function process(){

if( isset($_SESSION["token"]) and $_SESSION["token"] != md5(session_id().__FILE__)) $this->logout();

$data = array( "last_note"=>($last_note = $this->topic->getLastItem())?$last_note["date"]:"- brak wpisów -",
               "topic"=>false, 
             );
             
//---------- Akcje publiczne -------------

if(isset($_POST['userid']) and $_POST['userid']!="" and isset($_POST['pass'])){
   if( !$this->login($_POST['userid'],$_POST['pass']) ){
      $data["error1"]=$this->error;
   }
}

if(isset($_POST['userid']) and isset($_POST['pass1']) and $_POST['pass1']!="" and  $_POST['pass2']!="" and ($_POST['pass1']==$_POST['pass2'])){
   if( !$this->captcha->check(strtoupper($_POST['captcha']))) {
      $data["error"]="Wpisano niewłaściwy kod kontrolny";
   }else{
      if( !$this->register($_POST['userid'],$_POST['username'],$_POST['pass1']) )
         $data["error"]=$this->error;
     
   }
}
if(isset($_GET['cmd']) and $_GET['cmd']=='register'){
   $_SESSION["context"] = $this->context = "register";
   $this->reload();
}
if(isset($_GET['cmd']) and $_GET['cmd']=='login'){
   $_SESSION["context"] = $this->context = "login";
   $this->reload();
}
if(isset($_GET['cmd']) and $_GET['cmd']=='guestEntry'){
   $this->guestlogin();
}

if(isset($_GET['cmd']) and $_GET['cmd']=='logout'){
   $this->insert_activity($_GET['cmd']);
   $this->logout();
}
if(isset($_GET['capthaimg'])){
   echo $this->captcha->generate();
}


if($this->context){  // --- akcje wymagajace zalogowania ---

   if(isset($_GET['cmd']) and $_GET['cmd']=='topics'){
      $this->insert_activity($_GET['cmd']);
     $_SESSION['context']=$this->context='topics';
     $this->reload();
   }

   if(isset($_GET['cmd']) and $_GET['cmd']=='activity'){
      $this->insert_activity($_GET['cmd']);
     $_SESSION['context']=$this->context='activity';
     $this->reload();
   }

   if(isset($_GET['cmd']) and $_GET['cmd']=='userlist'){
      $this->insert_activity($_GET['cmd']);
     $_SESSION['userlist']=($_SESSION['userlist'])?false:true;
     $this->reload();
   }
   if(isset($_GET['cmd']) and $_GET['cmd']=='changeuser' and $this->u['userlevel']==20){
      $this->insert_activity($_GET['cmd']);
      if($_GET['userid']!="admin") $this->update_user($_GET['userid']);
   }
   if(isset($_GET['cmd']) and $_GET['cmd']=='deluser' and $this->u['userlevel']==20){
      $this->insert_activity($_GET['cmd']);
      if($_GET['userid']!="admin") {
         if($p=$this->topic->getAll($_GET['userid'],'userid')) 
             foreach( $p as $k=>$v) $this->topic->delete($k);  
         $this->delete_user($_GET['userid']);
      }   
   }
}

if($this->context=='activity'){
   $data['activity']=$this->activity->getAll(false,false, "id desc");
   $data['users']=$this->user->getAll();
   //$data['topics']=$this->topic->getAll(false,false, "date desc");
     
} 

if($this->context=='topics'){
 if( isset($_POST['topic']) and $_POST['topic'] and $_POST['topic_body'] ){
   if($_POST['topicid']=="")
   $this->topic->insert(array("topic"=>$_POST['topic'],"topic_body"=>$_POST['topic_body'],
                              "date"=>date("Y-m-d H:i:s"),"userid"=>$this->u['userid']));
   else
   $this->topic->update(array("topic"=>$_POST['topic'],"topic_body"=>$_POST['topic_body'],
                              "date"=>date("Y-m-d H:i:s"),"userid"=>$this->u['userid'],
                              "topicid"=>$_POST['topicid']));
   $this->reload();
   }
 if(isset($_GET['cmd']) and $_GET['cmd']=='topicdelete' and $this->u['userlevel']==20){
   $this->insert_activity($_GET['cmd']);
    $this->topic->delete($_GET['id']);
    $this->reload();
 }
 
 if(isset($_GET['cmd']) and $_GET['cmd']=='topicedit' and $this->u['userlevel']==20){
   $this->insert_activity($_GET['cmd']);
    $data["topic"]=$this->topic->get($_GET['id']);
 }
 $data['users']=$this->user->getAll();

 if(isset($_POST['keyWord'])){
   $data['topics']=$this->topic->getAllwithPattern($_POST['keyWord'],"topic_body", "date desc");
 }
 else{
   $data['topics']=$this->topic->getAll(false,false, "date desc");
 }
 if(isset($_GET['cmd']) and $_GET['cmd']=='gettopic'){
   $this->insert_activity("note" . $_GET['topicid']);
   if( $t = $this->topic->get($_GET['topicid']) ){
     echo json_encode( $t, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT );
   }else{
     echo "<p>ERROR: Topic ID: ".$_GET['topicid']." not  found!</p>\n";
   }  
   exit;
 } 
 

} // end of context topics

return $data;
} //---- end of function process()


//-----------------------------------------
//---------- metoda makepage() -------------
//-----------------------------------------    
  public function makepage($data){
    $this->view("header",$data);
    switch($this->context){
      case "topics":
         if($this->g != false) $this->view("guest-button",$data);
        $this->view("userinfo",$data);
        $this->view("topics",$data);
      break;
      case "activity":
         if($this->g != false) $this->view("guest-button",$data);
        $this->view("userinfo",$data);
        $this->view("activity",$data);
      break;
      case "register":
        $this->view("register-button",$data);
        $this->view("register",$data);
      break;  
      case "login":
      default:
       $this->view("login-button",$data);
       $this->view("login",$data);
    }
    $this->view("footer",$data);
  } 
    
//-----------------------------------------
//---------- metoda view() -------------
//-----------------------------------------    
  public function view($view,$data=NULL,$tostring=false){
      $buf="";
      if($data) extract($data);
      if($tostring) ob_start();
      include("view/$view.php");
      if($tostring) { 
         $buf = ob_get_contents();
         ob_end_clean();
         return $buf;
      }
  }
  
  protected function reload(){
     header("Location: $this->baseurl");
     exit;
  }
  
} //------ end of class Form ---------------------------------------------------   