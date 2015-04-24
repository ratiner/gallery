<?php
class listing
{


    function search($tags) {

        $fileEngine = new file();

        $list = array();


        foreach($tags as $tag) {
            if(!$tag)
                continue;

            $files = $fileEngine->getFilesByTag($tag);


            foreach ($files as $file) {
                $fileName = basename(Config::$upload_dir . $file["path"]);
                $path = dirname($file["path"]);

                $list[] = $this->add_file($fileName, $path, Config::$upload_dir .'/'. $path);
            }
        }

        return $this->orderSearch(array_unique($list), $tags);
    }

    function orderSearch($list, $tags) {
        foreach($list as $item) {
            foreach($item->db_tags as $db_tag) {
                foreach($tags as $tag) {
                    if($db_tag["tag_name"] == $tag)
                        $item->tag_rating++;
                }
            }
        }

        usort($list, array($this, "sort_by_tagRating"));
        return $list;
    }

    function _list($path)
    {
        $fullpath = Config::$upload_dir . $path;
        $contents = scandir($fullpath);

        $folders = array();
        $files = array();

        foreach ($contents as $item) {
            if ($item === '.' or $item === '..') continue;

            if (is_dir($fullpath . '/' . $item))
                $folders[] = $this->add_folder($item, $path, $fullpath);
            else
                $files[] = $this->add_file($item, $path, $fullpath);
        }



        usort($folders, array($this, "sort_by_name"));
        usort($files, array($this, "sort_by_name"));

        return array_merge($folders, $files);
    }


    function add_file($item, $path, $fullpath) {
        $file = new listingItem();
        $file->name = $item;
        $file->path = ltrim($path, '/') . '/'. ltrim($item, '/');
        $file->path = ltrim($file->path, '/');
        $file->type = "file";
        $file->size = filesize($fullpath . '/' . $item);

        $tagEngine = new tag();
        $file->db_tags = $tagEngine->getTagsByPath($file->path);
        return $file;
    }

    function add_folder($item, $path, $fullpath) {
        $folder = new listingItem();
        $folder->name = $item;
        $folder->path = ltrim($path, '/') . '/'. ltrim($item, '/');
        $folder->path = ltrim($folder->path, '/');
        $folder->type = "folder";
        return $folder;
    }


    function sort_by_name($a, $b) {
        return strnatcasecmp($a->name, $b->name);
    }

    function sort_by_tagRating($a, $b) {
        return $a->tag_rating < $b->tag_rating;
    }
}

class listingItem {
    public $name = "";
    public $path = "";
    public $size = -1;
    public $db_md5 = "";
    public $db_desc = "";
    public $db_tags = array();
    public $type = "";
    public $tag_rating = 0;
    public function __toString() {
        return $this->path;
    }

}
