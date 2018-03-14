<?php
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['ownerID'] = '1';
$_SESSION['username'] = 'nephantes';
$_SESSION['google_id'] = '107923577997088216371';
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
		$this->assertEquals(json_decode($data)->id,'5');
		ob_end_clean();
	}
    /**
     * @afterClass
     */
	public function testGetProjects() {
		ob_start();
		$_REQUEST['p'] = 'getProjects';
		$_REQUEST['id'] = 5;
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id,'5');
		$this->assertEquals(json_decode($data)[0]->name,'testProject');
//		$this->assertEquals(json_decode($data)[0]->summary,'testSummary');
		ob_end_clean();
	}

}






?>
