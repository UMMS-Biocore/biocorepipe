<?php
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['ownerID'] = '1';
$_SESSION['username'] = 'onuryukselen';
$_SESSION['google_id'] = '105130646152672654297';
$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";

chdir('ajax/');
use PHPUnit\Framework\TestCase;


class ajaxQueryTest extends TestCase
{
/**
* @beforeClass
*/
    public function testInsertProject() {
		ob_start();
		$_REQUEST['p'] = 'saveProject';
		$_REQUEST['name'] = 'testProject';
		$_REQUEST['summary'] = "testSummary";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
        public function testInsertUser() {
		ob_start();
		$_REQUEST['p'] = 'saveUser';
		$_REQUEST['google_id'] = '12111';
		$_REQUEST['name'] = "onur yukselen";
		$_REQUEST['google_image'] = "/img/avatar5.png";
		$_REQUEST['username'] = "onuryukselen";
		$_REQUEST['email'] = "onuryukselen@gmail.com";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
    
    
    
    
    
//    /**
//     * @afterClass
//     */
//	public function testGetProjects() {
//		ob_start();
//		$_REQUEST['p'] = 'getProjects';
//		$_REQUEST['id'] = 2;
//		include('ajaxquery.php');
//		$this->assertEquals(json_decode($data)[0]->id,'2');
//		$this->assertEquals(json_decode($data)[0]->name,'testProject');
//		$this->assertEquals(json_decode($data)[0]->summary,'testSummary');
//		ob_end_clean();
//	}

}






?>
