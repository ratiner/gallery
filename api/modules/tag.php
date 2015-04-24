<?php
class tag {
    function saveOne($path, $tags) {
        $fileEngine = new file();
        $file = $fileEngine->update($path, array());
        $db_tags = $this->getTagsById($file["id"]);



        foreach($tags as $tag) {
            if(!$this->tagExists($tag, $db_tags))
                $this->addTag($file["id"], $tag);
        }

        foreach($db_tags as $db_tag) {
            if(!in_array($db_tag["tag_name"], $tags))
                $this->removeTag($file["id"], $db_tag["tag_name"]);
        }
    }

    function getTagsByPath($path) {
        $fileEngine = new file();

        $file = $fileEngine->getFileByPath($path);


        if(!$file)
            return array();

        return $this->getTagsById($file["id"]);
    }

    function searchTags($text) {

        $bind = array(
            ":name" => "$text"."%"
        );

        $db = new db();

        $r = $db->select("tags", " name like :name", $bind);

        if(!$r || count($r) == 0)
            $r = array();

        return $r;
    }


    function getTagsById($fileId) {
        $bind = array(
            ":fileId" => "$fileId"
        );

        $db = new db();

        $r = $db->run("select ft.file_id as file_id, t.id as tag_id, t.name as tag_name from file_tags ft, tags t where ft.tag_id = t.id and file_id = :fileId", $bind);

        if(!$r || count($r) == 0)
            return array();

        return $r;
    }

    function getTag($tag, $autoAdd=true) {
        $bind = array(
            ":name" => "$tag"
        );

        $db = new db();


        $r = $db->select("tags", "name = :name", $bind);

        if(!$r || count($r) == 0)
        {
            if($autoAdd) {
                $db_tag = array();
                $db_tag["id"] = UUID::Generate();
                $db_tag["name"] = $tag;
                $db->insert("tags", $db_tag);
                $r = $db->select("tags", "name = :name", $bind);
            }
            else
                return null;
        }

        return $r[0];
    }


    function addTag($fileId, $tag) {

        $db_tag = $this->getTag($tag);

        $db = new db();
        $rel = array();
        $rel["file_id"] = $fileId;
        $rel["tag_id"] = $db_tag["id"];
        $r = $db->insert("file_tags", $rel);

        return true;
    }
    function removeTag($fileId, $tag) {
        $db_tag = $this->getTag($tag);
        $bind = array(
            ":fileId" => "$fileId",
            ":tagId" => $db_tag["id"]
        );

        $db = new db();
print 'aa';
        $db->delete("file_tags", "file_id = :fileId and tag_id = :tagId", $bind);

        return true;
    }

    function tagExists($tag, $tag_array) {
        foreach($tag_array as $itm) {
            if ($tag == $itm["tag_name"]) {
                return true;
            }
        }
        return false;
    }
}