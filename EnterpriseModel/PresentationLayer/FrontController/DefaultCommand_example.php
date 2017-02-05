<?php
/**
 * 一个具体的默认命令类,如果请求没有指明调用哪一个特定的Command对象，CommandResolver就会提供此默认对象
 * 抽象基类实现了execute()方法，且该方法向下调用由子类实现的doExecute()方法
 * Class DefaultCommand
 * @package Angular\Command
 */
class DefaultCommand extends Command {
    public function doExecute(Request $request)
    {
        $request->addFeedback('welcome');
        include ("Angular/view/main.php");  //mian.php 是视图文件
    }
}
?>