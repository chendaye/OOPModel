<?php
    /**
     * 命名空间，是一个容器，包含类，变量，函数
     * 在命名空间内部肆意访问，在命名空间外部必须要引入才能使用
     * 一个命名空间可以看做是跟空间下面的一个文件夹
     *
     * 在一个命名空间下面，引用公共空间的成员，要在最前面加前导反斜杠
     * 例如  namespace main; \com\get();  use com\get();
     * 因为命名空间也和文件夹类似，会以相对空间解析
     * 如果不加前导斜杠，会默认在 当前空间下面去找子空间
     * 前导反斜杠表示在跟空间下面去找
     *
     * 在用use 来引入命名空间是  不用再最前面加前导反斜杠 是因为  use  默认是在跟空间下面去查找
     */
    namespace chen\chen{
        class chen{
            //TODO:some thing
        }
    }
    namespace daye\daye {
        class daye{
            //TODO:some thing
        }
    }
    namespace common{
        //TODO:
        use chen\chen\chen;
        use daye\daye\daye;
        $ret = new daye();
    }
?>