<?php

class Parser {

    public $configPattern = '/[a-z]{1,}(\.[a-z]{1,}){0,}(\s?\=\s?.+)|(\s?\=[[:punct:]]?\s?\w+[[:punct:]]?)/';
    public $configs = [];
    public $file;
    public $configKeys = [];

    /**
    * Comment: Why did you use this way of instantiating the object?
    */
    public static function Parser($filePath) 
    {
        $parser = new self;
        $parser->file = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $parser;
    }

  /**
    * Comment: Why (new self) ?
    * Comment: Can you explain what $configPattern does?
    */
    public function regexParser()
    {
        foreach($this->file as $line) {
            preg_match((new self)->configPattern, $line, $match);
            if(!empty($match)){
                array_push($this->configs, $match[0]);
            }
        }
        return new self;
    }

    
    public function cleaner()
    {
        foreach($this->configs as $key => $config) {
            // $config = each line of the txt file
            $stringLength = strlen($config);
            
            /*
            * Comment: Can you find a cleaner mode of doing this bit?
            */ 
            for($index = 0; $index < $stringLength; $index++) {
                if($config[$index] == '=') {

                    // each line config keys
                    $configKeys = substr($config,0 , $index);
                    $configKeys = trim($configKeys);
                    $configKeys = explode('.', $configKeys);

                    // each line config values
                    $configValues = substr($config, ($index + 1), $stringLength);
                    $configValues = trim($configValues, '"\' ');
                    
                    $configValues = $this->castToType($configValues);
                    $this->mapper($this->configKeys, $configKeys, $configValues);
                }
            }
        }

        var_dump ($this->configKeys);
    }

    public function castToType($value)
    {
        if(is_numeric($value)){
            if(!strpos($value, '.')) {
                $value = intval($value);
                return $value;
            }
        }else {
            if($value == 'false') {
                $value = false;
                return $value;
            }elseif($value == 'true') {
                $value = true;
                return $value;
            }
        }

        return $value;
    }
    /*
     * @param array &$holder: empty array
     * @param array @$keys: array of keys
     * @param mixed $value
     */
    public function mapper(&$holder, $keys, $value)
    {

        foreach ($keys as $key) {
            $holder = &$holder[$key];
        }
        
        $holder = $value;

    }

}

$parser = Parser::Parser('./config.txt');
$parser->regexParser();
echo '<pre>';
$parser->cleaner();
echo '<pre>';
