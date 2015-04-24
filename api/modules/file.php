<?php 
class file {

	function _list() {

	}


    function getList($dataList)
    {
        return $dataList;
    }


	function update($path, $data) {
        $dbFile = $this->getFileByPath($path);
        $db = new db();

        if(!$dbFile) {
            $data["id"] = UUID::Generate();
            $data["path"] = $path;
            $data["last_access"] = gmdate("Y-m-d H:i:s");
            $db->insert("files", $data);
        }
        else {
            $data = array_merge($dbFile, $data);
            $data["last_access"] = gmdate("Y-m-d H:i:s");

            $bind = array(
                ":path" => $path
            );
            $db->update("files", $data, "path = :path", $bind);
        }

        return $data;
	}

	function delete($courseId) {

	}


    function getFileByPath($path) {
        $bind = array(
            ":path" => $path
        );

        

        $db = new db();
        try {
            $r = $db->select('files', 'path = :path', $bind);
        } catch (Exception $e) {
            print $e;
            $r = null;
        }



        if(!$r || count($r) == 0)
            return null;

        return $r[0];
    }

    function getFilesByTag($tagName) {
        $tagEngine = new tag();
        $tag = $tagEngine->getTag($tagName, false);

        if(!$tag)
            return array();

        $bind = array(":tagId" => $tag["id"]);

        $db = new db();

        $r = $db->run("select f.id as id, f.path as path from file_tags ft, files f where ft.file_id = f.id and ft.tag_id = :tagId", $bind);


        if(!$r || count($r) == 0)
            return array();

        return $r;
    }
}
