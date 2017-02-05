<?php
/**
 * 用PageController类添加一个新的场景到系统中
 */
namespace Angular\Controller;
use League\Flysystem\Exception;

class AddVenueController extends PageController {
    public function process()
    {
        // TODO: Implement process() method.
        try{
            $request = $this->getRequest();
            $name = $request->getProperty('venue_name');
            if(is_null($request->getProperty('submitted'))){
                $request->addFeedback('choose a name for the venue');
                $this->forward('add_venue_.php)');
            }elseif(is_null($name)){
                $request->addFeedback('name is a required field');
                $this->forward('add_venue.php');
            }
            //创建对象可将它添加到数据库
            $venue = new \Venue(null, $name);
            $this->forward("ListVenues.php");
       }catch (Exception $e){
            $this->forward('error.php');
        }
    }
}
$controller = new AddVenueController();
$controller->process();
/**
 * AddVenueController 类实现了process()方法
 * process 负责检查用户提交的数据，如果用户并未填写数据或者填写有误，就加载默认视图
 * 如果成功添加一个用户就调用 forward 方法，进行转向；把用户传递到 ListVenues 页面控制器
 *
 * 视图的工作就是现实数据，并且提供一个可以生成新请求的机制
 * 请求生成后就会发送给PageController,而不是返回给视图
 *
 * 效果：
 * 页面控制器的优点是非常简单
 * 如果将视图与控制器分离开，会复杂一点但是更容易理解
 *
 * 可能让人迷惑的是，当页面控制器执行完某个操作的时候，会加载一个视图
 * 有时会需要同一段代码来加载另一个页面控制器
 */
?>