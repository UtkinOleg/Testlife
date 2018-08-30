<?
class Cache {

    function read($fileName) {
	$fileName = 'cache/'.$fileName;
	if (file_exists($fileName)) {
	    $variable = file_get_contents($fileName);
	    return unserialize($variable);
	} else {
	    return null;
	}
}

    function write($fileName,$variable) {
        $fileName = 'cache/'.$fileName;
        file_put_contents($fileName, serialize($variable));
    }

    function delete($fileName) {
        $fileName = 'cache/'.$fileName;
        @unlink($fileName);
    }
}

?>