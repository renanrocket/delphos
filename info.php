<?php


include "inc/funcoes.inc.php";
class Line_Counter
{
    private $filepath;
    private $files = array();
    public $files_columns = array();
	public $files_lines = array();
	public $files_number = 0;
	public $files_php_number = 0;

    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    public function countLines($extensions = array('php', 'css', 'js'))
    {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->filepath));
        foreach ($it as $file)
        {
           // if ($file->isDir() || $file->isDot())
           if ($file->isDir() )
            {
                continue;
            }
            $parts = explode('.', $file->getFilename());
            $extension = end($parts);
            if (in_array($extension, $extensions))
            {
                $files[$file->getPathname()] = count(file($file->getPathname()));
                $this->files_php_number++;
				
				$path = explode('delphos', $file->getPathname());
				$count = count($path);
				$count--;
				//$this->files_lines[] = array($path[$count], count(file($file->getPathname())), "ln");
                $this->files_lines[] = $path[$count];
                $this->files_columns[] = count(file($file->getPathname()));
            }
			$this->files_number++;
        }
        return $files;
    }

    public function showLines()
    {
        //echo '<pre>';
        $this->countLines();
        //echo '</pre>';
    }

    public function totalLines()
    {
        return array_sum($this->countLines());
    }

}
$path = explode("/", $_SERVER["SCRIPT_FILENAME"]);
$count = count($path);
for($i=0, $path2="";$i<$count; $i++){
	$i!=0?$path2.="/":false;
	if($i+1!=$count){
		$path2 .= $path[$i];
	}
	
}
// Get all files with line count for each into an array
$loc = new Line_Counter($path2);
$loc->showLines();


echo grafico("Doughnut", $loc->files_lines, $loc->files_columns, null, array(1000, 500));
echo '<br>Total arquivos ';
echo $loc->files_number;
echo '<br>Total arquivos php css js ';
echo $loc->files_php_number;
echo '<br>Total linhas de codigo: ';
echo $loc->totalLines();

?>