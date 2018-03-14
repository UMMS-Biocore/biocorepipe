<?php
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['ownerID'] = '1';
$_SESSION['username'] = 'admin';
$_SESSION['google_id'] = '111';
$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";
$google_id = isset($_SESSION['google_id']) ? $_SESSION['google_id'] : "";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "";


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
    
        //***discuss
//    public function testInsertSSH() {
//		ob_start();
//		$_REQUEST['p'] = 'saveSSHKeys';
//		$_REQUEST['name'] = "dockerKey";
//		$_REQUEST['check_userkey'] = "on";
//		$_REQUEST['check_ourkey'] = "";
////		$_REQUEST['prikey'] = "";
////		$_REQUEST['pubkey'] = "";
//		include('ajaxquery.php');
//		$this->assertEquals(json_decode($data)->id,'1');
//		ob_end_clean();
//	}
    public function testInsertAmz() {
		ob_start();
		$_REQUEST['p'] = 'saveAmzKeys';
		$_REQUEST['name'] = "amzKey";
		$_REQUEST['amz_def_reg'] = "test1";
		$_REQUEST['amz_acc_key'] = "test2";
		$_REQUEST['amz_suc_key'] = "test3";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
    public function testInsertProfileCluster() {
		ob_start();
		$_REQUEST['p'] = 'saveProfileCluster';
		$_REQUEST['name'] = "localtest";
		$_REQUEST['cmd'] = "source /etc/profile";
		$_REQUEST['executor'] = "local";
		$_REQUEST['next_memory'] = "";
		$_REQUEST['next_queue'] = "";
		$_REQUEST['next_time'] = "";
		$_REQUEST['next_cpu'] = "";
		$_REQUEST['executor_job'] = "local";
		$_REQUEST['job_memory'] = "";
		$_REQUEST['job_queue'] = "";
		$_REQUEST['job_time'] = "";
		$_REQUEST['job_cpu'] = "";
		$_REQUEST['username'] = "docker";
		$_REQUEST['hostname'] = "localhost";
		$_REQUEST['next_path'] = "";
		$_REQUEST['ssh_id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'2');
		ob_end_clean();
	}
    public function testInsertProfileAmazon() {
		ob_start();
		$_REQUEST['p'] = 'saveProfileAmazon';
		$_REQUEST['name'] = "amazontest";
		$_REQUEST['cmd'] = "";
		$_REQUEST['executor'] = "local";
		$_REQUEST['next_memory'] = "";
		$_REQUEST['next_queue'] = "";
		$_REQUEST['next_time'] = "";
		$_REQUEST['next_cpu'] = "";
		$_REQUEST['executor_job'] = "local";
		$_REQUEST['job_memory'] = "";
		$_REQUEST['job_queue'] = "";
		$_REQUEST['job_time'] = "";
		$_REQUEST['job_cpu'] = "";
		$_REQUEST['ins_type'] = "";
		$_REQUEST['image_id'] = "";
		$_REQUEST['subnet_id'] = "";
		$_REQUEST['shared_storage_id'] = "";
		$_REQUEST['shared_storage_mnt'] = "";
		$_REQUEST['next_path'] = "";
		$_REQUEST['ssh_id'] = "1";
		$_REQUEST['amazon_cre_id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertInput() {
		ob_start();
		$_REQUEST['p'] = 'saveInput';
		$_REQUEST['name'] = "testinput";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertProPipeInput() {
		ob_start();
		$_REQUEST['p'] = 'saveProPipeInput';
		$_REQUEST['input_id'] = "1";
		$_REQUEST['project_id'] = "1";
		$_REQUEST['pipeline_id'] = "1";
		$_REQUEST['project_pipeline_id'] = "1";
		$_REQUEST['g_num'] = "1";
		$_REQUEST['given_name'] = "test_inputparam";
		$_REQUEST['qualifier'] = "test_inputparam";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertProjectInput() {
		ob_start();
		$_REQUEST['p'] = 'saveProjectInput';
		$_REQUEST['input_id'] = "1";
		$_REQUEST['project_id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testcheckLogin() {
		ob_start();
		$_REQUEST['p'] = 'checkLogin';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testcheckLoginError() {
		ob_start();
		$_REQUEST['p'] = 'checkLogin';
        $google_id = "";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->error,'1');
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
