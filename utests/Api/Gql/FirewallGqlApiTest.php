<?php 

namespace FreepPBX\firewall\utests;

require_once('../api/utests/ApiBaseTestCase.php');

use FreePBX\modules\firewall;
use FreePBX\modules\Api\utests\ApiBaseTestCase;

/**
 * FirewallGqlApiTest
 */
class FirewallGqlApiTest extends ApiBaseTestCase {
  protected static $firewall;
  
  /**
   * setUpBeforeClass
   *
   * @return void
   */
  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    self::$firewall = self::$freepbx->firewall;
  }
        
  /**
   * tearDownAfterClass
   *
   * @return void
   */
  public static function tearDownAfterClass() {
    parent::tearDownAfterClass();
  }
  
   /**
    * testFetchBlacklistedIpTrue
    *
    * @return void
    */
   public function testFetchBlacklistedIpTrue(){
    $ip = "123.123.123.123";

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('getBlacklist'))
    ->getMock();
      
	  $mockfirewall->method('getBlacklist')
		->willReturn(Array('123.123.123.123' => false, '124.124.124.124' => false));
    
    self::$freepbx->firewall->setServices($mockfirewall); 

    $response = $this->request("{
    fetchAllBlacklistIPs{
    status, message
    blacklistIps  {
      sourceIp
      trusted
      }
      }
    }");
      
    $json = (string)$response->getBody();
    $this->assertEquals('{"data":{"fetchAllBlacklistIPs":{"status":true,"message":"List of all blacklistedIPs","blacklistIps":[{"sourceIp":"123.123.123.123","trusted":false},{"sourceIp":"124.124.124.124","trusted":false}]}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());

   }

  /**
   * testAddBlacklistIPShouldReturnTrueWhenIPPassed
   *
   * @return void
   */
  public function testAddBlacklistIPShouldReturnTrueWhenIPPassed(){
    $ip = "100.100.100.100";

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('addToBlacklist'))
    ->getMock();
      
	  $mockfirewall->method('addToBlacklist')
		->willReturn(true);
    
    self::$freepbx->firewall->setServices($mockfirewall);   

    $response = $this->request("mutation {
      addBlackListIPs(input: { 
      IP: \"$ip\" 
    }) {
      status message 
    }
    }");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"data":{"addBlackListIPs":{"status":true,"message":"IP has been added to blacklist"}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());
   }
   
   /**
    * testRemoveBlacklistIPShouldReturnTrueWhenIPPassed
    *
    * @return void
    */
   public function testRemoveBlacklistIPShouldReturnTrueWhenIPPassed(){
    $ip = "100.100.100.100";

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('removeFromBlacklist'))
    ->getMock();
      
    self::$freepbx->firewall->setServices($mockfirewall);   
    
	  $mockfirewall->method('removeFromBlacklist')
		->willReturn(true);

    $response = $this->request("mutation {
      deleteBlackListIPs(input: { 
      IP: \"$ip\" 
    }) {
      status message 
    }
    }");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"data":{"deleteBlackListIPs":{"status":true,"message":"IP removed from blacklist"}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());
   }
   
   /**
    * testEnableFirewallShouldReturnTrueWhenTrue
    *
    * @return void
    */
   public function testEnableFirewallShouldReturnTrueWhenTrue(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Firewall::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('runHook','preEnableFW'))
    ->getMock();
      
	  $mockfirewall->method('runHook','preEnableFW')
    ->willReturn(true);
    
    $response = $this->request("mutation {
      enableFirewall(input: { }){
      message
      status
      }
    }");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"data":{"enableFirewall":{"message":"Firewall enable process has been completed.","status":true}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());
   }
   
   /**
    * testDiableFirewallShouldReturnTrueWhenTrue
    *
    * @return void
    */
   public function testDiableFirewallShouldReturnTrueWhenTrue(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Firewall::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('setConfig'))
    ->getMock();
      
	  $mockfirewall->method('setConfig')
		->willReturn(true);

    $response = $this->request("mutation {
      disableFirewall(input: { }){
      message
      status
      }
    }");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"data":{"disableFirewall":{"message":"Firewall has been disabled","status":true}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());
   }
   

   /**
    * test_addWhitelist_fireall_when_no_paramamets_send_should_set_default_values_return_true
    *
    * @return void
    */
   public function test_addWhitelist_when_parameter_send_should_return_true(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('addToWhitelist'))
    ->getMock();
      
    self::$freepbx->firewall->setServices($mockfirewall);   
    
	  $mockfirewall->method('addToWhitelist')
		->willReturn(true);

    $response = $this->request("mutation{
      addWhiteListIPs(input : {
        IPs: [
          \" { 'sourceIp': '100.100.100.100', 'zone': 'none', 'hidden':'true' }\",
          \" { 'sourceIp': '100.100.100.100', 'zone': 'none', 'hidden':'true' }\"
        ]
    }) {
      status message 
    }
    }");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"data":{"addWhiteListIPs":{"status":true,"message":"IPs has been added to Whitelisted"}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());
   }
      
   /**
    * test_addWhitelist_fireall_when_no_paramamets_send_should_set_default_values_return_false
    *
    * @return void
    */
   public function test_addWhitelist_when_return_false_should_return_false(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('addToWhitelist'))
    ->getMock();
      
    self::$freepbx->firewall->setServices($mockfirewall);   
    
	  $mockfirewall->method('addToWhitelist')
		->willReturn(false);

    $response = $this->request("mutation{
      addWhiteListIPs(input : {
         IPs: [
          \" { 'sourceIp': '100.100.100.100', 'zone': 'none', 'hidden':'true' }\",
          \" { 'sourceIp': '100.100.100.100', 'zone': 'none', 'hidden':'true' }\"
        ]
    }) {
      status message 
    }
    }");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"errors":[{"message":"Sorry, failed to added IPs to Whitelist","status":false}]}',$json);

    $this->assertEquals(400, $response->getStatusCode());
   }
   
   /**
    * test_addWhitelist_firewall_when_none_passed_instedd_of_trusted_sent_should_throught_exception_and_return_false
    *
    * @return void
    */
   public function test_addWhitelist_when_none_passed_instead_of_trusted_sent_should_throught_exception_and_return_false(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('addToWhitelist'))
    ->getMock();
      
    self::$freepbx->firewall->setServices($mockfirewall);   
    
	  $mockfirewall->method('addToWhitelist')
		->willThrowException(new \Exception('Can only add to trusted zone at the moment'));

    $response = $this->request("mutation{
      addWhiteListIPs(input : {
        IPs: [
          \" { 'sourceIp': '100.100.100.100', 'zone': 'none', 'hidden':'true' }\",
          \" { 'sourceIp': '100.100.100.100', 'zone': 'none', 'hidden':'true' }\"
        ]
    }) {
      status message 
    }
    }");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"errors":[{"message":"Can only add to trusted zone at the moment","status":false}]}',$json);

    $this->assertEquals(400, $response->getStatusCode());
   }
   
   /**
    * test_addWhitelist_firewall_when_none_passed_instead_of_IPAddress_should_throught_exception_and_return_false
    *
    * @return void
    */
   public function test_addWhitelist_firewall_when_none_passed_instead_of_IPAddress_should_throught_exception_and_return_false(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('addToWhitelist'))
    ->getMock();
      
    self::$freepbx->firewall->setServices($mockfirewall);   
    
	  $mockfirewall->method('addToWhitelist')
		->willThrowException(new \Exception('Can only add IP addressess'));

    $response = $this->request("mutation{
      addWhiteListIPs(input : {
         IPs: [
          \" { 'sourceIp': 'none', 'zone': 'none', 'hidden':'true' }\"
        ]
    }) {
      status message 
    }
    }");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"errors":[{"message":"Can only add IP addressess","status":false}]}',$json);

    $this->assertEquals(400, $response->getStatusCode());
   }
  
  /**
   * test_fetchFirewallConfiguration_should_return_the_configurations
   *
   * @return void
   */
  public function test_fetchFirewallConfiguration_should_return_the_configurations(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('getFirewallConfigurations'))
    ->getMock();
      
	  $mockfirewall->method('getFirewallConfigurations')
		->willReturn(array(['firewallStatus' => true, 'responsiveFirewall' => true, 'chainSip' => true, 'pjSip' => true, 'safemodeEnabled' => 'disabled', 'currentJiffies' => "100", 'provis' => true]));
    
    self::$freepbx->firewall->setServices($mockfirewall); 

    $response = $this->request("query{
    fetchFirewallConfiguration{
      status, 
      message,
      configurations {
        status
        responsiveFirewall
        chainSip
        pjSip
        safemode
        currentJiffies
        provision
    }
  }}");
      
    $json = (string)$response->getBody();
    $this->assertEquals('{"data":{"fetchFirewallConfiguration":{"status":true,"message":"List of firewall configurations","configurations":[{"status":true,"responsiveFirewall":true,"chainSip":true,"pjSip":true,"safemode":"disabled","currentJiffies":"100","provision":true}]}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());
   }

  
  /**
   * test_updateFirewallConfiguration_when_all_good_should_return_true
   *
   * @return void
   */
  public function test_updateFirewallConfiguration_when_all_good_should_return_true(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('setFirewallConfigurations'))
    ->getMock();
      
    self::$freepbx->firewall->setServices($mockfirewall);   
    
	  $mockfirewall->method('setFirewallConfigurations')
		->willReturn(true);

    $response = $this->request("mutation{
  updateFirewallConfiguration(input : {
		status	: true
		responsiveFirewall : true
		chansip :  [\"true\", \"rfw\"]
		pjsip : [\"true\", \"rfw\"]
		safeMode: \"enabled\"
		currentJiffies : \"1000\"
		enableTrustedHost : true 
		enableResponsive : true 
		externalSetup : true
		serviceZone : [ \"external\", \"other\", \"internal\" ]
     }){
    status message
  }
}");
      
    $json = (string)$response->getBody();

    $this->assertEquals('{"data":{"updateFirewallConfiguration":{"status":true,"message":"Firewall configurations have been saved successfully"}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());
   }

  
  /**
   * test_fetchAllWhitelistIPs_should_return_listof_whitelist_ips
   *
   * @return void
   */
  public function test_fetchAllWhitelistIPs_should_return_listof_whitelist_ips(){

    $mockfirewall = $this->getMockBuilder(\FreePBX\modules\firewall\Services::class)
		->disableOriginalConstructor()
		->disableOriginalClone()
		->setMethods(array('getServiceZones'))
    ->getMock();
      
	  $mockfirewall->method('getServiceZones')
		->willReturn(array(array("sourceIp" => "100.100.100.100", "trusted" => "trusted"),array("sourceIp" => "100.100.100.101", "trusted" => "trusted")));
    
    self::$freepbx->firewall->setServices($mockfirewall); 

    $response = $this->request("query{
    fetchAllWhitelistIPs{
      status, 
      message,
      whitelistIps  {
      sourceIp
      trusted
    }
    }}");
      
    $json = (string)$response->getBody();
    $this->assertEquals('{"data":{"fetchAllWhitelistIPs":{"status":true,"message":"List of all whiltelistedIPs","whitelistIps":[{"sourceIp":"100.100.100.100","trusted":true},{"sourceIp":"100.100.100.101","trusted":true}]}}}',$json);

    //status 200 success check
    $this->assertEquals(200, $response->getStatusCode());
   }
  }
?>