<?php
namespace Demo\Controller;

use Think\Controller;
use Think\Model;


class SmallGameController extends Controller
{
    /**
     *
     */
    public $sockets = array();
    public $master;
    public $users;

    public function index()
    {
        $array1 = C("MODULE_ALLOW_LIST");
        //echo var_dump(stream_get_transports());
        //echo $_SERVER['HTTP_HOST'] . $_SERVER['SERVER_PORT'] . "<br />";
/*        $fp = fsockopen($_SERVER['HTTP_HOST'],$_SERVER['SERVER_PORT']);
        if (!$fp) {
            echo "wrong";
        }
        else {
            $out = "GET /Home/Index/index?id=5 HTTP/1.1\r\n";
            $out .= "Host: localhost\r\n";
            $out .= "Connection: Close\r\n\r\n";
            echo $out . "<br />";
            fwrite($fp, $out);
            while (!feof($fp)) {
                echo fgets($fp, 128);
            }
            fclose($fp);
        }*/
        //phpinfo();

        $host = I('server.HTTP_HOST');
        $port =  9050;
        echo $host . " " . I('server.SERVER_PORT') . "\n";

        // create a streaming socket, of type TCP/IP
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        echo $sock . "\n";

        // set the option to reuse the port
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, TRUE);

        // "bind" the socket to the address to "localhost", on port $port
        // so this means that all connections on this port are now our resposibility to send/recv data, disconnect, etc.
        socket_bind($sock, $host, $port);

        // start listen for connections
        socket_listen($sock);

        // create a list of all the clients that will be connected to us..
        // add the listening socket to this list
        $clients = array($sock);
        //$this->master = $server;
        //$this->sockets[] = $this->master;
        echo 111;
        socket_select($this->sockets, $write, $except, NULL);

       // while(true) {
            /*$changes = $this->sockets;

            socket_select($this->sockets, $write, $except, NULL);

            foreach($changes as $sock) {
                if ($sock == $server) {
                    $client = socket_accept($this->master);
                    echo $client;
                    $this->users = array(
                        'socket'=>$client,
                        'shou'=>false
                    );
                }
            }*/

       // }




    }

    public function _before_index() {
        echo $_SERVER['HTTP_HOST']."<br />";
        echo "before";
    }

    public function send($str = "aaa") {
        echo $this->users['socket'];

        socket_write($this->users['socket'], $str, strlen($str));


    }

    public function server() {
        $address = "127.0.0.1";

//端口

        $port = 10005;

//创建一个套接字

        if(	($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))	===false){

            echo "创建一个套接字 失败" . "\n";

        }

//启动套接字

        if(socket_bind($sock, $address,$port)===false){

            echo "启动套接字 失败" . socket_strerror(socket_last_error($sock)) . "\n";

        }


//监听端口

        if(socket_listen($sock,5) === false){

            echo "监听端口 失败" . socket_strerror(socket_last_error($sock)) . "\n";

        }


        do {

//似乎是接收客户端传来的消息

            if(($msgsock=socket_accept($sock))===false){

                echo "socket_accepty() failed :reason:".socket_strerror(socket_last_error($sock)) . "\n";

                break;

            }

//echo "读取客户端传来的消息"."\n";

            $buf = socket_read($msgsock, 8192);

            $talkback = "我已经成功接到客户端的信息了。现在我还回信息给客户端"."\n";

            if(false=== socket_write($msgsock, $talkback)){

                echo "socket_write() failed reason:" . socket_strerror(socket_last_error($sock)) ."\n";

            }else{

                echo "return info msg ku fu duan success"."\n";

            }

            socket_close($msgsock);

        }while (true);

        socket_close($sock);
    }

    public function client($msg = "default msg!") {
        $host = "127.0.0.1";
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        echo "create finished." . "<br/>";

        /* set socket receive timeout to 1 second */
        //socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 0));

        /* connect to socket */
        socket_connect($socket, $host, 9051);
        echo "connect finished." . "<br/>";

        /* record start time */
      /*  list($start_usec, $start_sec) = explode(" ", microtime());
        $start_time = ((float) $start_usec + (float) $start_sec);*/

/*        socket_write($socket, $msg, strlen($msg), 0);
        echo "send finished." . "<br/>";*/

        //读取指定长度的数据
        while($buffer = socket_read($socket, 1024, PHP_NORMAL_READ)) {
            if($buffer == "NO DATA") {
                printf("NO DATA");
                break;
            }else{
                // 输出 buffer
                printf("Buffer Data: " . $buffer . "");
                break;
            }
        }
        printf("Writing to Socket". "<br/>");

        //写数据到socket缓存
        if(!socket_write($socket, $msg . "\n")){
            printf("Write failed");
        }

        while($buffer = socket_read($socket, 1024, PHP_NORMAL_READ)){
            printf("Data sent was: SOME DATA Response was:" . $buffer . "");
        }

        printf("Done Reading from Socket");

        /*$data = @socket_read($socket, 255);
        if($data) {
            list($end_usec, $end_sec) = explode(" ", microtime());
            $end_time = ((float) $end_usec + (float) $end_sec);

            $total_time = $end_time - $start_time;

            echo $data . "<br />";
            echo $total_time;
        }
        socket_write($socket, $msg, strlen($msg), 0);*/

        //socket_close($socket);
    }

    public function testU() {
        echo U("Demo/SmallGame/index?id=5");

    }


    public function testModel() {
        $test = M("Test");
        $field = $test->getDbFields();
        dump( $field );
        $data = array();
        $data['note'] = '我草';


        $data = $test->create($data, Model::MODEL_INSERT);
        dump($data);
        $test->status = 1;
        $test->create_time = time();
        $test->add();

        header("Content-Type:text/html;charset=gb2312");
        echo "你好";
        echo iconv("UTF-8", "GB2312", '你好');
    }

    public function testInput() {
        $this->display('SmallGame/input_name');
    }

    public function doNameInput() {
        header("Content-Type:text/html;charset=gb2312");
        $name = I('get.name');
        echo iconv("UTF-8", "GB2312", $name);
        echo $name . '<br />';
        echo md5($name) . '<br />';

        $char = 'E';
        echo ($char - 'B');

    }

    public function testRedirect() {
        $smallGame = A('SmallGame');
        $smallGame->testDestination();

        $ch = curl_init();

        $data = array('name' => 'Foo', 'age' => 25);


        curl_exec($ch);
    }

    public function testDestination() {
        echo 111;

    }

}