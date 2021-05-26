<?php
	
class TreeView
{
    private $root;
    private $username;
    private $max_children;
    private $current_children;
    private $dbh;
 
    public function __construct($path, $username, $max_children, $dbh)
    {
        $this->root = $path;
        $this->username = $username;
        $this->max_children = $max_children;
        $this->current_children = 0;
        $this->dbh = $dbh;
    }
 
    public function getTree()
    {
        return $this->createStructure($this->root, true);
    }
  
    private function createStructure($directory, $root)
    {
        $structure = $root ? '<ul class="treeview"><li class="treeview-folder"><a href="dashboard.php?path='.$path.'/"><span title="' . $this->username . '" data-path="' . $directory . '/">' . $this->username . '</span></a><ul>' : '<ul>';
 
		
        $nodes = $this->getNodes($directory);
        foreach ($nodes as $node) {
            $path = $directory.'/'.$node;
            if (is_dir($path) ) {
	            
	            $dashpath = "$path/";
	            
	            // Check if the album has a custom name
	            $check_album_name = $this->dbh->prepare("SELECT title FROM album WHERE path = :path");
	            $check_album_name->bindParam(":path", $dashpath);
	            $check_album_name->execute();
	            
	            if($check_album_name->rowCount() > 0) {
		            		            
		            $album_name_row = $check_album_name->fetch();
		            $album_name = $album_name_row["title"];
		            
	            } else {
		            		            
		            $album_name = $node;
		            
	            }
	            
	            /*
	            // Get the number of files in this directory
	            $nb_files_query = $this->dbh->prepare("SELECT id FROM file WHERE folder_path = '$path/' AND is_deleted = 0");
	            $nb_files_query->execute();
	            
	            $nb_files_path = $nb_files_query->rowCount();
	            */
				$nb_files_path = "-";
	            
	            $path = str_replace("&amp;", "%26", $path);
	                                            
                $structure .= '<li class="treeview-folder">';
                $structure .= '<a href="dashboard.php?path='.$path.'/"><span title="'.$album_name.'" data-path="'.$path.'/">'.$album_name.' <small><i class="fas fa-circle-notch fa-spin"></i></small></span></a>';
				$structure .= self::createStructure($path, false);
                $structure .= '</li>';
            }
        }
        return $structure.'</li></ul>';
    }
 
    private function getNodes($directory = null)
    {
        $folders = [];
        $files = [];
 
        $nodes = scandir($directory);
        foreach ($nodes as $node) {
            if (!$this->exclude($node)) {
                if (is_dir($directory.'/'.$node)) {
                    $folders[] = $node;
                } else {
                    $files[] = $node;
                }
            }
        }
 
        return array_merge($folders, $files);
    }
 
    private function exclude($filename)
    {
        return in_array($filename, ['.', '..', 'index.php', '.htaccess', '.DS_Store']);
    }
}