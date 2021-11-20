<?php
class JString {
    private $str = "";
    
    public function __construct($string = null) {
        $rawString = $this->getString($string);
        $this->str = "".$rawString;
    }
        
    public function __toString() {
        return $this->str;
    }

    private function setStr($string = null) {
        $this->str = $this->getString($string);
    }
    
    private function getString($string = null){
        return $string.""; //if $string is object this will call $string->__toString();
    }
    
    public function length(){
        return mb_strlen($this->str);
    }
    
    public function append($string = null){
        $this->insert( $this->length(), $string );
        return $this;
    }
    
    public function insert($index = 0, $string = null){
        $objString = new JString($string);
        $before = $index == 0 ? "" : mb_substr($this->str, 0, $index);
        $after = mb_substr($this->str, $index, $this->length());
        $this->setStr( $before.$objString.$after );
        return $this;
    }
    
    public function reverse(){
        $this->setStr( implode("", array_reverse($this->getChars())) );
        return $this;
    }
    
    public function getChars($start = 0, $end = 0){
        $ret = array();
        $max = ($end > 0 && $end < $this->length() ) ? $end : $this->length();
        
        for($i = $start; $i < $max; $i++){
            $ret[] = new JString(mb_substr($this->str, $i, 1));
        }
        return $ret;
    }
    
    public function charAt($position){
        $ret = "";
        $chars = $this->getChars();
        if(isset($chars[$position])){
            $ret = $chars[$position];
        }
        $this->setStr($ret);
        return $this;
    }
    
    public function equals($string = null){
        return $this->str == $this->getString($string);
    }
    
    public function startsWith($string = null){
        return ($this->indexOf($string) == 0);
    }
    
    public function endsWith($string = null){
        $revInput = new JString($string);
        $revInput->reverse();
        $this->reverse();
        return $this->startsWith($revInput);
    }
    
    public function indexOf($string = null, $offset = 0){
        $ret = -1;
        $rawString = $this->getString($string);
        if($rawString != ""){
            $result = mb_strpos($this->str, $rawString, $offset);
            if($result !== false){
                $ret = $result;
            }
        }
        return $ret;
    }
    
    public function lastIndexOf($string = null, $offset = 0){
        $revInput = new JString($string);
        $revInput->reverse();
        $revOrig = $this->reverse();
        $result = $revOrig->indexOf($revInput, $offset);
        if($result != -1){
            $ret = $revOrig->length() - 1 - $result;
        }else{
            $ret = $result;
        }
        return $ret;
    }
    
    public function substring($start = 0, $end = 0){
        $this->setStr( ($start == $end) ? "" : mb_substr($this->str, $start, ($end > 0) ? $end - $start : null) );
        return $this;
    }
    
    public function concat($string1 = null){
        $this->setStr( $this->str.$this->getString($string1) );
        return $this;
    }
    
    public function replace($what = null, $with = null){
        $this->setStr( str_replace($this->getString($what), $this->getString($with), $this->str) );
        return $this;
    }
    
    public function replaceNewLines($with = null) {
        $this->setStr( $this->replace("\r\n", $with)->replace("\r", $with)->replace("\n", $with) );
        return $this;
    }
    
    public function trim(){
        $this->setStr( trim($this->str) );
        return $this;
    }
    
    public function toLowerCase(){
        $this->setStr( mb_strtolower($this->str) );
        return $this;
    }
    
    public function toUpperCase(){
        $this->setStr( mb_strtoupper($this->str) );
        return $this;
    }
    
    public function contains($string = null){
        return $this->indexOf($string) != -1;
    }
    
    public function isEmpty(){
        return $this->length() == 0;
    }
    
    public function replaceFirst($what = null, $with = null){
        if($this->contains($what)){
            $objWhat = new JString($what);
            $objWith = new JString($with);

            $before = new JString($this->str);
            $after = new JString($this->str);
            
            $startIndex = $this->indexOf($objWhat);
            $endIndex = $startIndex + $objWhat->length();

            $before->substring(0, $startIndex);
            $after->substring($endIndex);

            $this->setStr( $before . $objWith . $after );
        }

        return $this;
    }
    
    public function replaceLast($what = null, $with = null){
        $objWhat = new JString($what);
        $objWith = new JString($with);
        
        return $this->reverse()->replaceFirst($objWhat->reverse(), $objWith->reverse())->reverse();
    }
    
    public function split($string = null){
        $rawString = $this->getString($string);
        $retRaw = $rawString != "" ? explode($rawString, $this->str) : array();
        $ret = array();
        foreach($retRaw as $current){
            $ret[] = new JString($current);
        }
        return $ret;
    }
    
    
    public function splitPlain($string = null) {
        $result = [];
        $data = $this->split($string);
        foreach ($data as $current) {
            $result[] = $current . "";
        }
        return $result;
    }
    
    public function htmlSpecialChars(){
        $this->setStr( htmlspecialchars($this->str) );
        return $this;
    }
    
    public function htmlSpecialCharsDecode(){
        $this->setStr( htmlspecialchars_decode($this->str) );
        return $this;
    }
    
    public function stripTags(){
        $this->setStr( strip_tags($this->str) );
        return $this;
    }
    
    public function stripSlashes(){
        $this->setStr( stripslashes($this->str) );
        return $this;
    }
    
    public function toCamelCase() {
        $ret = $this->toLowerCase();
        while(true) {
            $i = $ret->indexOf("_");
            if($i == -1) {
                break;
            }
            $objBefore = new JString($ret);
            $objAfter = new JString($ret);
            $before = $objBefore->substring(0, $i);
            $toUp = $i < $ret->length() - 1 ? $ret->charAt($i + 1)->toUpperCase() : "";
            $after = $objAfter->substring($i + 2);
            $this->setStr( $before . $toUp . $after );
        }
        return $this;
    }
    
    public function fromCamelCase() {
        $ret = new JString();
        $chars = $this->getChars();

        foreach ($chars as $currentChar) {
        	$currentCharObj = new JString($currentChar);
            if($currentCharObj->toLowerCase() == $currentChar) {
                $ret->append($currentChar);
            } else {
                $ret->append("_")->append($currentCharObj->toLowerCase());
            }
        }
        $this->setStr( $ret );
        return $this;
    }
    
    public function trimTo($count) {
        if ($count >= $this->length()) {
            return $this;
        } else {
        	$tmpString = new JString($this->str);
        	$tmpString->substring(0, $count);

        	$index = $tmpString->lastIndexOf(" ");
            return $this->substring(0, $index)->append(" ...");
        }
    }
    
    public function recursiveReplace($what, $to) {
        $result = new JString($this);
        if (!static::from($to)->contains($what)) {
            while ($result->contains($what)) {
                $result = $result->replace($what, $to);
            }
        }
        $this->setStr($result);
        return $this;
    }
    
    public static function from($string) {
        return new JString($string);
    }
}
?>


