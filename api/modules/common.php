<?php 

class common
{

    public static function filter()
    {

        $bind = array();
        $keys = array();

        $decoded = RetrieveIDs($_GET);

        foreach (array_keys($decoded) as $i => $k) {
            if (isset($decoded[$k]) && !empty($decoded[$k])) {
                $keys[$i] = $k . "=:" . $k;
                $bind[":" . $k] = $decoded[$k];
            }
        }

        $where = (count($bind) > 0) ? implode(" AND ", $keys) : null;

        return array(
            "where" => $where,
            "bind" => $bind
        );
    }
}