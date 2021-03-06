<?php
/**
 *企业应用程序分层
 * 注册表模式管理应用程序数据
 * 表现层：管理和响应用户请求，并把数据呈现给用户
 * 业务逻辑层
 *
 * 整个面向对象的核心在于四个字：各司其职
 *
 * 注册表：该模式用于让数据对进程中所有的类都有效。通过序列化操作注册表对象可用于存储跨回话甚至跨应用实例的数据
 * 前端控制器：在规模较大的系统中，该模式可用于尽可能灵活的管理各种不同的命令和视图
 * 应用控制器：创建一个类来管理视图逻辑和命令选择
 * 模板视图：创建模板来显示和处理用户界面，在显示标记中加入动态内容，尽量少使用进行业务操作的代码
 * 页面控制器：满足和前端控制器相同的需求，但较为轻量级，灵活性也小一些，如果要快速得到结果且系统也不太复杂的话，可用此模式来处理页面逻辑
 * 事务脚本：如果想快速完成某个任务，可用此模式，它用过程式的代码来实现持续逻辑
 * 领域模型：和事务脚本相反，该模式可为业务参与者和过程构建基于对象的模型
 *
 * 系统模式大部分是来使 程序中不同的层 独立工作的
 * 与类的使命类似，企业应用系统中的层也是如此
 *
 * 系统分层：
 * 视图层 生成指向控制层的请求
 * 命令与控制层  解释请求并查询调用业务逻辑层
 * 业务逻辑层  处理业务逻辑
 * 数据层  处理数据的获取与请求
 *
 * 一个请求执行完后返回结果 ：
 * 业务逻辑层  返回结果给命令控制层
 * 命令控制层  获取结果并选择适当的视图来展示结果
 *
 * 层的结构并不是一成不变的，其中一些层可以合并，并且层与层之间的交互策略因系统的复杂程度而不同
 * 但归根结底， 模式强调 灵活性和重用性， 应用需要根据灵活性和重用性进行扩展
 *
 * 具体来说：
 * 视图层：包括系统用户实际看到和交互的页面，负责显示用户请求的结果、传递新的请求给系统
 *
 * 命令和控制层：处理、解释、用户的请求，委托业务逻辑层来实现请求，然后选择合适的视图展示给用户
 * 视图层和命令控制层常常合并为表现层，不过不管怎样 显示的任务都要严格的与请求处理和业务逻辑的调用分离开
 *
 * 业务逻辑层： 根据买了控制层分发的请求执行业务操作
 *
 * 数据层： 负责保存和获取系统中的持久信息
 *
 * 分层的目的： 解耦
 * 通过分离各层 当添加新的接口道系统时，系统内部只需要做很小的改动
 */

/**
 * 问题：
 * 假设有一个管理事件列表的系统
 * 终端用户需要一个漂亮的HTML接口
 * 系统管理员需要一个命令行接口来构建自动化系统
 * 可能需要支持手机和其他设备的访问版本
 *
 * 如果把底层的逻辑和HTML代码混写在一起，要实现上面提的需求就不得不重写代码
 * 另一方面，如果是分层的系统，就可以直接调用新的显示方案，而不用重新考虑业务逻辑和数据层
 * 如果持久性策略改变（换数据库），也能够在对其他层影响最小的情况下更换存储模型
 *
 * 系统分层的了一个原因是测试：web程序测试很难，测试工作必须运行在完全部署的系统上，但是这会有破坏真实系统的风险
 * 在分层系统中，任何需要与其他层直接打交道的类通常都扩展自 抽象父类 或者实现统一接口；而父类支持多态，测试环境中一个完整的层可以被一个虚拟的对象代替
 *
 * 即使系统只有一个简单的接口，分层仍然非常有用，
 * 通过创建独立分工的层，可以构建一个易于扩展调试的系统
 * 将具有同样功能的代码放在同一个地方可以减少代码重复，而不是将所有层混在一起
 * 这样新功能添加到系统会相对简单，因为改变是纵向的不是横向的
 *
 * 混在一起：大量重复的代码，重复的功能，添加新功能必须重写所有代码
 *
 * 在分层系统中，一个新功能可能需要一个新的组件接口，额外的请求处理，更多的业务逻辑和对存储机制的修改
 * 这些修改时纵向的  并不是在一个层中
 * 在没分层的系统中，如果要增加新功能，则可能要记住很多个和数据表相关的页面，新的接口可能会在十几个地方呗调用
 * 因此要为系统增加这部分代码，这就是横向修改
 */
?>