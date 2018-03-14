<?php
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['ownerID'] = '1';
$_SESSION['username'] = 'admin';
$_SESSION['google_id'] = '111';
$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";

chdir('ajax/');
use PHPUnit\Framework\TestCase;


class ajaxQueryTest extends TestCase
{
/**
* @beforeClass
*/
    public function testInsertUser() {
		ob_start();
		$_REQUEST['p'] = 'saveUser';
		$_REQUEST['google_id'] = '111';
		$_REQUEST['name'] = "onur yukselen";
		$_REQUEST['google_image'] = "https://lh4.googleusercontent.com/-h7_FO3k9sB4/AAAAAAAAAAI/AAAAAAAAAAA/AGi4gfw9MqsLVfHz5xXsoOzA1KIZ1yLwXw/s96-c/photo.jpg";
		$_REQUEST['username'] = "admin";
		$_REQUEST['email'] = "admin@gmail.com";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertProject() {
		ob_start();
		$_REQUEST['p'] = 'saveProject';
		$_REQUEST['name'] = 'testProject';
		$_REQUEST['summary'] = "testSummary";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
        public function testInsertParameter() {
		ob_start();
		$_REQUEST['p'] = 'saveParameter';
		$_REQUEST['name'] = "inputDir";
		$_REQUEST['qualifier'] = "val";
		$_REQUEST['file_type'] = "inputDir";
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
