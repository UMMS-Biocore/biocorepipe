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
</div>


<?php
require_once("config/config.php");

if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";

class dbfuncs {


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
        $sql= "SELECT DISTINCT pp.name, pp.id 
        FROM project pp
        LEFT JOIN user_group ug ON pp.group_id=ug.g_id
        where pp.owner_id = '$ownerID' OR pp.perms = 63 OR (ug.u_id ='$ownerID' and pp.perms = 15)";
     return self::queryTable($sql);
   }
    
   
   function getSubMenuFromSideBar($parent, $ownerID)
   {
           $where = "(pp.project_id='$parent' AND (pp.owner_id = '$ownerID' OR pp.perms = 63 OR (ug.u_id ='$ownerID' and pp.perms = 15)))";
       $sql="SELECT DISTINCT pp.id, pp.name, pj.owner_id, pp.project_id
             FROM project_pipeline pp
             LEFT JOIN user_group ug ON pp.group_id=ug.g_id
             INNER JOIN project pj ON pp.project_id = pj.id and $where ";

      return self::queryTable($sql);
   }
}

function getSideMenuItem($obj)
{
$html="";
foreach ($obj as $item):
    $nameSub = substr($item->{'name'}, 0, 20);
        $html.='<li><a href="index.php?np=3&id='.$item->{'id'}.'" class="projectItems"  draggable="false" id="propipe-'.$item->{'id'}.'"><i class="fa fa-angle-double-right"></i>'.$nameSub.'</a></li>';
endforeach;
return $html;
}

$query = new dbfuncs();

$parentMenus = json_decode($query->getParentSideBar($ownerID));


$menuhtml='<ul id="autocompletes1" class="sidebar-menu" data-widget="tree">';
$menuhtml.='<li class="header">PROJECTS</li>';

foreach ($parentMenus as $parentitem):
    $nameSub = substr($parentitem->{'name'}, 0, 15);
    $menuhtml.='<li class="treeview">';

    $menuhtml.='<a href="" draggable="false"><i  class="fa fa-circle-o"></i> <span>'.$nameSub.'</span>';
    
    $items = json_decode($query->getSubMenuFromSideBar($parentitem->{'id'}, $ownerID));

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