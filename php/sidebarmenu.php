<!-- sidebar menu: : style can be found in sidebar.less -->
<!--        Control Sidebar-->
<div class=" dropdown messages-menu ">
    <a id="newPipeline" class="btn btn-warning" style=" margin-left:15px;" href="index.php?np=1" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="New Pipeline">
                  <span class="glyphicon-stack">
                    <i class="fa fa-plus-circle glyphicon-stack-2x" style="color:white;"></i>
                      <i class="fa fa-spinner glyphicon-stack-1x" style="color:white;"></i>
                  </span>
              </a>
    <button type="button" id="addprocess" class="btn btn-default btn-success" data-toggle="modal" name="button" data-target="#addProcessModal" data-backdrop="false" style=" margin-left:0px;">
              <a data-toggle="tooltip" data-placement="bottom" title="" data-original-title="New Process">
                  <span class="glyphicon-stack">
                    <i class="fa fa-plus-circle glyphicon-stack-2x" style="color:white;"></i>
                      <i class="fa fa-circle-o glyphicon-stack-1x" style="color:white;"></i>
                  </span>
              </a>
            </button>
    <?php
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
  $ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";
                    if ($ownerID != ''){
                    echo '<button type="button" class="btn btn-primary dropdown-toggle" style="float:right; margin-right:10px;" data-toggle="dropdown"><a data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Filter"><span class="glyphicon glyphicon-filter" style="color:white;"></span> </a><span class="caret"></span></button>
    <ul id="filterMenu" class="dropdown-menu dropdown-menu-right filterM">
    </ul>"';
                    } 
 ?>
</div>

<?php
require_once("config/config.php");
//    session_start();
//$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";

class sidebarfuncs {

    private $dbhost = DBHOST;
    private $db = DB;
    private $dbuser = DBUSER;
    private $dbpass = DBPASS;
    private $dbport = DBPORT;
//    private $last_modified_user = LMUSER;
    private static $link;

    function __construct() {
        if (!isset(self::$link)) {
            self::$link = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->db, $this->dbport);
            // check connection
            if (mysqli_connect_errno()) {
                exit('Connect failed: ' . mysqli_connect_error());
            }
        }
    }

    function __destruct() {
        if (isset(self::$link)) {
            self::$link->close();
        }
    }
   function runSQL($sql)
   {
        $link = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->db);
        // check connection
        if (mysqli_connect_errno()) {
                exit('Connect failed: '. mysqli_connect_error());
        }
        $result=self::$link->query($sql);
		if (!$result) {
            trigger_error('Database Error: ' . self::$link->error);
        }
        if ($result && $result!="1")
        {
            return $result;
        }
        return json_encode (json_decode ("{}"));
   }
   function queryTable($sql)
   {
     $data = array();
     if ($res = $this->runSQL($sql))
     {
        while(($row=$res->fetch_assoc())){$data[]=$row;}
        $res->close();
     }
     return json_encode($data);
   }
      public function getParentSideBar($ownerID)
   {
      if ($ownerID != ''){
            $userRoleArr = json_decode($this->getUserRole($ownerID));
            $userRole = $userRoleArr[0]->{'role'};
            if ($userRole == "admin"){
                $sql= "SELECT DISTINCT pg.group_name name, pg.id, pg.perms, pg.group_id
                FROM process_group pg ";
                return self::queryTable($sql);
            }
        $sql= "SELECT DISTINCT pg.group_name name, pg.id, pg.perms, pg.group_id
        FROM process_group pg
        LEFT JOIN user_group ug ON  pg.group_id=ug.g_id
        where pg.owner_id='$ownerID' OR pg.perms = 63 OR (ug.u_id ='$ownerID' and pg.perms = 15) ";
      } else {
        $sql= "SELECT DISTINCT group_name name, id FROM process_group where perms = 63";
      }
     return self::queryTable($sql);
   }
    
    public function getUserRole($ownerID) {
		$sql = "SELECT role 
                FROM users  
                where id = '$ownerID'";
		return self::queryTable($sql);
    }
    
      public function getPipelineSideBar($ownerID)
   {
      if ($ownerID != ''){
          $userRoleArr = json_decode($this->getUserRole($ownerID));
          $userRole = $userRoleArr[0]->{'role'};
          if ($userRole == "admin"){
              $where = " ";
          } else {
              $where = " WHERE (p.owner_id='$ownerID' OR (p.perms = 63 AND p.pin = 'true') OR (ug.u_id ='$ownerID' and p.perms = 15)) ";
          }
          $sql= "SELECT DISTINCT pip.id, pip.name, pip.perms, pip.group_id, pip.pin
               FROM biocorepipe_save pip
               LEFT JOIN user_group ug ON  pip.group_id=ug.g_id
               INNER JOIN (
                SELECT p.pipeline_gid, MAX(p.rev_id) rev_id
                FROM biocorepipe_save p
                LEFT JOIN user_group ug ON p.group_id=ug.g_id 
                $where
                GROUP BY p.pipeline_gid
                ) b ON pip.rev_id = b.rev_id AND pip.pipeline_gid=b.pipeline_gid";
          
      } else {
          $sql= "SELECT DISTINCT pip.id, pip.name, pip.perms, pip.group_id, pip.pin
               FROM biocorepipe_save pip
               INNER JOIN (
                SELECT p.pipeline_gid, MAX(p.rev_id) rev_id
                FROM biocorepipe_save p
                WHERE p.perms = 63 
                GROUP BY p.pipeline_gid
                ) b ON pip.rev_id = b.rev_id AND pip.pipeline_gid=b.pipeline_gid AND pip.pin = 'true' ";
       }     
     return self::queryTable($sql);
   }
   
   function getSubMenuFromSideBar($parent, $ownerID)
   {
       if ($ownerID != ''){
          $userRoleArr = json_decode($this->getUserRole($ownerID));
          $userRole = $userRoleArr[0]->{'role'};
          if ($userRole == "admin"){
                $sql="SELECT DISTINCT p.id, p.name, p.perms, p.group_id, p.owner_id, p.publish
                FROM process p
                INNER JOIN process_group pg 
                ON p.process_group_id = pg.id and pg.group_name='$parent' 
                INNER JOIN (
                SELECT pr.process_gid, MAX(pr.rev_id) rev_id
                FROM process pr
                GROUP BY pr.process_gid
                ) b ON p.rev_id = b.rev_id AND p.process_gid=b.process_gid  ";
          return self::queryTable($sql);
          }
       
           $where_pg = "(pg.owner_id='$ownerID' OR pg.perms = 63 OR (ug.u_id ='$ownerID' and pg.perms = 15))";
           $where_pr = "(pr.owner_id='$ownerID' OR pr.perms = 63 OR (ug.u_id ='$ownerID' and pr.perms = 15))";
           } else {
           $where_pg = "pg.perms = 63";
           $where_pr = "pr.perms = 63";
           }
       $sql="SELECT DISTINCT p.id, p.name, p.perms, p.group_id
             FROM process p
             LEFT JOIN user_group ug ON  p.group_id=ug.g_id
             INNER JOIN process_group pg 
             ON p.process_group_id = pg.id and pg.group_name='$parent' and $where_pg
             INNER JOIN (
                SELECT pr.process_gid, MAX(pr.rev_id) rev_id
                FROM process pr
                LEFT JOIN user_group ug ON pr.group_id=ug.g_id where $where_pr
                GROUP BY pr.process_gid
                ) b ON p.rev_id = b.rev_id AND p.process_gid=b.process_gid";

      return self::queryTable($sql);
   }
}

function getSideMenuItem($obj)
{
$html="";
foreach ($obj as $item):
    $nameSub = substr($item->{'name'}, 0, 20);
    $html.='<li pub="'.$item->{'publish'}.'" p="'.$item->{'perms'}.'" g="'.$item->{'group_id'}.'"><a data-toggle="modal" data-target="#addProcessModal" data-backdrop="false" href="" ondragstart="dragStart(event)" ondrag="dragging(event)" draggable="true" id="'.$item->{'name'}.'@'.$item->{'id'}.'" ><i class="fa fa-angle-double-right"></i>'.$nameSub.'</a></li>';
endforeach;
return $html;
}

function getSideMenuPipelineItem($obj)
{
$html="";
foreach ($obj as $item):
    $nameSub = substr($item->{'name'}, 0, 20);
    $html.='<li pin="'.$item->{'pin'}.'" p="'.$item->{'perms'}.'" g="'.$item->{'group_id'}.'"><a href="index.php?np=1&id='.$item->{'id'}.'" class="pipelineItems"  draggable="false" id="pipeline-'.$item->{'id'}.'" ><i class="fa fa-angle-double-right"></i>'.$nameSub.'</a></li>';
endforeach;
return $html;
}


$query = new sidebarfuncs();

$parentMenus = json_decode($query->getParentSideBar($ownerID));


$menuhtml='<ul id="autocompletes1" class="sidebar-menu" data-widget="tree">';
//add initial input parameters
  

$menuhtml.='<li id="Pipelines" class="treeview">  <a href="" draggable="false"><i  class="fa fa-spinner"></i><span> Pipelines </span><i class="fa fa-angle-left pull-right"></i></a><ul id="allPipelines" class="treeview-menu">';    
    $items = json_decode($query->getPipelineSideBar($ownerID));
    $menuhtml.= getSideMenuPipelineItem($items);
    $menuhtml.='</ul>';
    $menuhtml.='</li>';
$menuhtml.='<li class="header">INPUT/OUTPUT PARAMETERS</li>';

$menuhtml.='<li id="inputs" >  <a ondragstart="dragStart(event)" ondrag="dragging(event)" draggable="true" id="inputparam@inPro"> <i class="fa fa-plus"></i>  <text id="text-inPro" font-family="FontAwesome" font-size="0.9em" x="-6" y="15"></text> <span> Input Parameters </span> </a></li>';  
$menuhtml.='<li id="outputs" class="treeview">  <a ondragstart="dragStart(event)" ondrag="dragging(event)" draggable="true" id="outputparam@outPro"> <i class="fa fa-plus"></i>  <text id="text-outPro" font-family="FontAwesome" font-size="0.9em" x="-6" y="15"></text> <span> Output Parameters </span> </a></li>';  
$menuhtml.='<li class="header">PROCESSES</li>';


foreach ($parentMenus as $parentitem):
    $nameSub = substr($parentitem->{'name'}, 0, 15);

    $menuhtml.='<li class="treeview">';
    $menuhtml.='<a href="" draggable="false"><i  class="fa fa-circle-o"></i> <span p="'.$parentitem->{'perms'}.'" g="'.$parentitem->{'group_id'}.'" >'.$nameSub.'</span>';
    
    $items = json_decode($query->getSubMenuFromSideBar($parentitem->{'name'}, $ownerID));

    $menuhtml.='<i class="fa fa-angle-left pull-right"></i></a>';
    $menuhtml.='<ul id="side-'.$parentitem->{'id'}.'" class="treeview-menu">';
    $menuhtml.= getSideMenuItem($items);
    $menuhtml.='</ul>';
    $menuhtml.='</li>';
endforeach;
$menuhtml.='                    <ul>';

echo $menuhtml;

?>
    <!-- /.sidebar -->
