<html>

<head>
    <meta charset="UTF-8">
    <title>easy_sql</title>
</head>

<body>
<h1>取材于某次真实环境渗透，只说一句话：开发和安全缺一不可</h1>
<!-- sqlmap是没有灵魂的 -->
<form method="get">
    姿势: <input type="text" name="inject" value="1">
    <input type="submit">
</form>

<pre>
<?php
function waf1($inject) {
    return preg_match("/select|update|delete|drop|insert|where|\./i",$inject);
}

function waf2($inject) {
    return strstr($inject, "set") && strstr($inject, "prepare");
}

if(isset($_GET['inject'])) {
    $id = $_GET['inject'];

    if(waf1($id)) {
        echo 'return preg_match("/select|update|delete|drop|insert|where|\./i",$inject);';
    } else {
        if(waf2($id)) {
            echo 'strstr($inject, "set") && strstr($inject, "prepare");';
        } else {
            $mysqli = new mysqli("127.0.0.1","root","root","supersqli");
            //多条sql语句
            $sql = "select * from `words` where id = '$id';";

            if ($mysqli->multi_query($sql)){//使用multi_query()执行一条或多条sql语句
            	do{
            		if ($rs = $mysqli->store_result()){//store_result()方法获取第一条sql语句查询结果
            			while ($row=$rs->fetch_row()){
            				var_dump($row);
            				echo "<br>";
            			}
            			$rs->Close(); //关闭结果集
            			if ($mysqli->more_results()){  //判断是否还有更多结果集
            				echo "<hr>";
            			}
            		}
            	}while($mysqli->next_result()); //next_result()方法获取下一结果集，返回bool值
            }
            $mysqli->close();  //关闭数据库连接
        }
    }
}


?>
</pre>

</body>

</html>
