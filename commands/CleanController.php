<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\helpers\FileHelper;

/**
 * Removes content of assets and runtime directories
 *
 * @author Joachim Werner <joachim.werner@diggin-data.de> 
 */
class CleanController extends Controller
{
    public $assetsPaths = ['@app/web/assets'];
    public $runtimePaths = ['@runtime'];
    // {{{ getHelp
    public function getHelp()
    {
        $out = "The clean command allows you to clean up various temporary data Yii and an application are generating.\n\n";
        return $out . parent::getHelp();
    } // }}}
    // {{{ actionAssets
    /**
     * Removes temporary assets
     */
    public function actionAssets()
    {
        foreach((array)$this->assetsPaths as $path) {
            $this->cleanDir($path);
        }
        $this->stdout('Done.' . PHP_EOL);
    } // }}}
    // {{{ actionRuntime
    /**
     * Removes runtime content
     */
    public function actionRuntime()
    {
        foreach((array)$this->runtimePaths as $path) {
            $this->cleanDir($path);
        }
        $this->stdout('Done.' . PHP_EOL);
    } // }}}
    // {{{ actionEmptySubDirs
    /**
     * Removes empty sub-dirs
     *
     * @param string $path Parent directory to loop through
     * @param bool $root Is $path the root dir? If yes, it won't get deleted
     */
    public  function actionEmptySubDirs($path=null, $root=true)
    {
        static $deleted = 0;
        $empty=true;

        if($path===null && $root===true) {
            echo "Error! path is empty\n";
            return 1;
        }
        if(substr($path, 0, 1)==='@' && $root===true) {
            $path2 = \Yii::getAlias($path);
            if(!is_dir($path2)) {
                echo "Error! $path is not a valid path\n";
                return 1;
            }
        }
        $path = $path2;

        foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
        {
            echo $file.'...';
            if(is_dir($file))
                echo 'DIR'."\n";
            $empty &= is_dir($file) && $this->actionEmptySubDirs($file, false);
        }
        $result = $empty && (!$root) && rmdir($path);
        if($result===true)
            $deleted++;
        if($result)
            echo basename($path) . ' removed' . "\n";
        else
            echo basename($path).' not removed' . "\n";
        if($root)
            echo 'Total deleted dirs: '.$deleted."\n";
        return $result;
    } // }}}
    // {{{ cleanDir
    /**
     * Do the cleaning of a dir
     *
     * @var string $dir Dir alias to be removed
     */
    private function cleanDir($dir)
    {
        $iterator = new \DirectoryIterator(\Yii::getAlias($dir));
        foreach($iterator as $sub) {
            if(!$sub->isDot() && $sub->isDir()) {
                $this->stdout('Removed ' . $sub->getPathname() . PHP_EOL);
                FileHelper::removeDirectory($sub->getPathname());
            }
        }
    } // }}}
}
