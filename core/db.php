<?php
/* ================================
   DATABASE LAYER (JSON)
   ================================ */

class Database {
    
    /* ================= READ ================= */
    public static function read($file) {
        $path = DATA_DIR . '/' . $file . '.json';
        
        if(!file_exists($path)) {
            return [];
        }
        
        $content = file_get_contents($path);
        return json_decode($content, true) ?: [];
    }
    
    /* ================= WRITE ================= */
    public static function write($file, $data) {
        $path = DATA_DIR . '/' . $file . '.json';
        
        if(!is_dir(DATA_DIR)) {
            mkdir(DATA_DIR, 0777, true);
        }
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($path, $json);
        
        return true;
    }
    
    /* ================= FIND ================= */
    public static function find($file, $key, $value) {
        $data = self::read($file);
        
        foreach($data as $item) {
            if(isset($item[$key]) && $item[$key] == $value) {
                return $item;
            }
        }
        
        return null;
    }
    
    /* ================= FIND ALL ================= */
    public static function findAll($file, $key, $value) {
        $data = self::read($file);
        $results = [];
        
        foreach($data as $item) {
            if(isset($item[$key]) && $item[$key] == $value) {
                $results[] = $item;
            }
        }
        
        return $results;
    }
    
    /* ================= INSERT ================= */
    public static function insert($file, $item) {
        $data = self::read($file);
        $data[] = $item;
        return self::write($file, $data);
    }
    
    /* ================= UPDATE ================= */
    public static function update($file, $key, $value, $newData) {
        $data = self::read($file);
        
        foreach($data as &$item) {
            if(isset($item[$key]) && $item[$key] == $value) {
                $item = array_merge($item, $newData);
            }
        }
        
        return self::write($file, $data);
    }
    
    /* ================= DELETE ================= */
    public static function delete($file, $key, $value) {
        $data = self::read($file);
        $data = array_filter($data, function($item) use($key, $value) {
            return !isset($item[$key]) || $item[$key] != $value;
        });
        
        return self::write($file, array_values($data));
    }
    
    /* ================= COUNT ================= */
    public static function count($file) {
        $data = self::read($file);
        return count($data);
    }
    
    /* ================= PAGINATION ================= */
    public static function paginate($file, $page = 1, $perPage = ITEMS_PER_PAGE) {
        $data = self::read($file);
        $total = count($data);
        $start = ($page - 1) * $perPage;
        $items = array_slice($data, $start, $perPage);
        
        return [
            'items' => $items,
            'total' => $total,
            'pages' => ceil($total / $perPage),
            'current' => $page
        ];
    }
}

?>